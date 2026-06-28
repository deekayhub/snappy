<?php

namespace App\Http\Controllers;

use App\Models\CategoryField;
use App\Models\CustomerJob;
use App\Models\OrganisationCategory;
use App\Models\Quote;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Support\Arr;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerPanelController extends Controller
{
    public function dashboard(Request $request): View
    {
        $user = $request->user();
        $jobsBaseQuery = $user->customerJobs();

        $stats = [
            'jobs_posted' => (clone $jobsBaseQuery)->count(),
            'open_jobs' => (clone $jobsBaseQuery)->where('status', 'open')->count(),
            'closed_jobs' => (clone $jobsBaseQuery)->where('status', '!=', 'open')->count(),
            'quotes_received' => Quote::query()
                ->whereIn('customer_job_id', $user->customerJobs()->select('id'))
                ->count(),
        ];

        $recentJobs = $user->customerJobs()
            ->withCount('quotes')
            ->latest()
            ->take(6)
            ->get();

        $recentQuotes = Quote::query()
            ->whereIn('customer_job_id', $user->customerJobs()->select('id'))
            ->with([
                'job:id,title,user_id',
                'supplier:id,name,email',
                'supplier.supplierProfile:id,user_id,company_name',
            ])
            ->latest()
            ->take(6)
            ->get();

        return view('customer-panel.dashboard', compact('stats', 'recentJobs', 'recentQuotes'));
    }

    public function jobs(Request $request): View
    {
        $jobs = $request->user()
            ->customerJobs()
            ->with(['categoryId', 'dynamicFieldValues.categoryFields'])
            ->withCount('quotes')
            ->latest()
            ->paginate(9);

        $categories = OrganisationCategory::where('type', 'supplier')->get();

        return view('customer-panel.jobs.index', compact('jobs', 'categories'));
    }

    public function getCategoryFields(Request $request, $id)
    {
        $itemIndex = max(1, (int) $request->integer('item_index', 1));
        $fields = CategoryField::where('category_id', $id)
            ->where('status', 1)
            ->orderBy('sort_order')
            ->get();

        $html = view('customer-panel.jobs.dynamic-fields', [
            'fields' => $fields,
            'itemIndex' => $itemIndex,
            'itemValues' => [],
        ])->render();

        return response()->json([
            'html' => $html
        ]);
         
    }

    public function store(Request $request): JsonResponse
    {
        if (! $request->user()?->hasRole('customer')) {
            return response()->json([
                'success' => false,
                'message' => 'Only customer accounts can post a job.'
            ], 403);
        }

        $validated = $this->validateJob($request);
        $job = $request->user()->customerJobs()->create(Arr::except($validated, ['dynamic_fields', 'dynamic_fields_existing']));

        $this->syncDynamicFields($request, $job, $validated['dynamic_fields'] ?? []);

        User::role('supplier')->where('is_active', true)->chunk(100, function ($suppliers) use ($job) {
            foreach ($suppliers as $supplier) {
                if ($supplier->hasFeature('instant_job_alerts')) {
                    UserNotification::create([
                        'user_id' => $supplier->id,
                        'type' => 'new_job',
                        'message' => "New job posted: {$job->title}",
                        'action_url' => route('supplier-panel.jobs'),
                    ]);
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Your job has been posted successfully.'
        ]);
    }

    public function editJob(Request $request, CustomerJob $job): JsonResponse
    {
        if ((int) $job->user_id !== (int) $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not allowed to edit this job.'
            ], 403);
        }

        $job->loadMissing([
            'categoryId',
            'dynamicFieldValues.categoryFields',
            'quotes',
        ]);

        $fields = CategoryField::where('category_id', $job->category)
            ->where('status', 1)
            ->orderBy('sort_order')
            ->get();

        $groupedDynamicFieldValues = $this->groupDynamicFieldValues($job);

        $dynamicFieldsHtml = '';
        $renderableItems = empty($groupedDynamicFieldValues) ? [[]] : $groupedDynamicFieldValues;

        foreach ($renderableItems as $index => $itemValues) {
            $dynamicFieldsHtml .= view('customer-panel.jobs.dynamic-fields', [
                'fields' => $fields,
                'itemIndex' => $index + 1,
                'itemValues' => $itemValues,
            ])->render();
        }

        return response()->json([
            'success' => true,
            'job' => [
                'id' => $job->id,
                'title' => $job->title,
                'category' => $job->category,
                'category_label' => $job->categoryId?->name ?? 'General',
                'organisation_name' => $job->organisation_name,
                'location' => $job->location,
                'budget' => $job->budget,
                'needed_by' => $job->needed_by?->format('Y-m-d H:i'),
                'description' => $job->description,
                'status' => $job->status,
            ],
            'view_html' => view('customer-panel.jobs.job-details', [
                'job' => $job,
                'groupedDynamicFieldValues' => $groupedDynamicFieldValues,
            ])->render(),
            'edit_fields_html' => $dynamicFieldsHtml,
            'item_count' => count($renderableItems),
        ]);
    }

    public function updateJob(Request $request, CustomerJob $job): JsonResponse
    {
        if ((int) $job->user_id !== (int) $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not allowed to update this job.'
            ], 403);
        }

        $validated = $this->validateJob($request);
        $job->update(Arr::except($validated, ['dynamic_fields', 'dynamic_fields_existing']));

        $this->syncDynamicFields($request, $job, $validated['dynamic_fields'] ?? []);

        return response()->json([
            'success' => true,
            'message' => 'Job updated successfully.'
        ]);
    }

    public function destroyJob(Request $request, CustomerJob $job): JsonResponse
    {
        if ((int) $job->user_id !== (int) $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not allowed to delete this job.'
            ], 403);
        }

        $job->delete();

        return response()->json([
            'success' => true,
            'message' => 'Job deleted successfully.'
        ]);
    }

    public function quotes(Request $request): View
    {
        $jobs = $request->user()
            ->customerJobs()
            ->with([
                'categoryId',
                'quotes' => fn ($quoteQuery) => $quoteQuery->latest(),
                'quotes.supplier' => fn ($supplierQuery) => $supplierQuery
                    ->select('id', 'name', 'email')
                    ->withAvg('ratedSupplierQuotes as supplier_average_rating', 'customer_rating')
                    ->withCount('ratedSupplierQuotes as supplier_ratings_count'),
                'quotes.supplier.supplierProfile:id,user_id,company_name',
            ])
            ->latest()
            ->get();

        return view('customer-panel.quotes.index', compact('jobs'));
    }

    public function profile(Request $request): View
    {
        $organisation = OrganisationCategory::query()
            ->orderBy('name')
            ->get();

        $user = $request->user()->load(['customerProfile', 'organisationCategories']);

        return view('customer-panel.profile.index', compact('user', 'organisation'));
    }

    private function validateJob(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'organisation_name' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'needed_by' => ['required', 'date', 'after_or_equal:today'],
            'description' => ['nullable', 'string'],
            'dynamic_fields' => ['nullable', 'array'],
            'dynamic_fields.*' => ['nullable', 'array'],
            'dynamic_fields.*.*' => ['nullable'],
            'dynamic_fields_existing' => ['nullable', 'array'],
        ]);
    }

    private function syncDynamicFields(Request $request, CustomerJob $job, array $dynamicFields): void
    {
        $job->dynamicFieldValues()->delete();

        if (empty($dynamicFields) || empty($job->category)) {
            return;
        }

        $fields = CategoryField::where('category_id', $job->category)
            ->where('status', 1)
            ->get()
            ->keyBy('id');

        foreach ($dynamicFields as $fieldId => $items) {
            $field = $fields->get((int) $fieldId);

            if (! $field) {
                continue;
            }

            foreach ((array) $items as $itemIndex => $value) {
                $storedValue = $this->normalizeDynamicFieldValue(
                    $request,
                    $field,
                    (int) $fieldId,
                    $itemIndex,
                    $value
                );

                if ($storedValue === null || $storedValue === '') {
                    continue;
                }

                $job->dynamicFieldValues()->create([
                    'job_id' => $job->id,
                    'category_id' => $job->category,
                    'field_id' => $fieldId,
                    'user_id' => Auth::id(),
                    'item_no' => (int) $itemIndex,
                    'field_value' => $storedValue,
                ]);
            }
        }
    }

    private function normalizeDynamicFieldValue(Request $request, CategoryField $field, int $fieldId, int|string $itemIndex, mixed $value): mixed
    {
        if ($field->field_type === 'file') {
            $uploadedFiles = $request->file("dynamic_fields.$fieldId.$itemIndex", []);
            $uploadedFiles = is_array($uploadedFiles) ? $uploadedFiles : array_filter([$uploadedFiles]);

            if (! empty($uploadedFiles)) {
                $savedFiles = [];

                foreach ($uploadedFiles as $file) {
                    if (! $file) {
                        continue;
                    }

                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $file->getClientOriginalExtension();
                    $originalName = str_replace(' ', '_', $originalName);

                    $fileName = $request->input('category', 'job')
                        . '_' . $request->user()->id
                        . '_' . $itemIndex
                        . '_' . time()
                        . '_' . $originalName
                        . '.' . $extension;

                    $file->move(public_path('assets/jobimage'), $fileName);
                    $savedFiles[] = 'assets/jobimage/' . $fileName;
                }

                return empty($savedFiles) ? null : json_encode($savedFiles);
            }

            $existingValue = $request->input("dynamic_fields_existing.$fieldId.$itemIndex");

            return $existingValue ?: null;
        }

        if ($field->field_type === 'checkbox') {
            $selectedValues = array_values(array_filter((array) $value, fn ($item) => $item !== null && $item !== ''));

            return empty($selectedValues) ? null : json_encode($selectedValues);
        }

        if (is_array($value)) {
            $value = Arr::first(array_filter($value, fn ($item) => $item !== null && $item !== ''));
        }

        return $value ?: null;
    }

    private function groupDynamicFieldValues(CustomerJob $job): array
    {
        return $job->dynamicFieldValues
            ->groupBy('item_no')
            ->map(function ($items) {
                return $items->keyBy('field_id')->all();
            })
            ->all();
    }

    public function suppliers(Request $request)
    {
        if($request->ajax()) {
            $query = User::query()
                    ->role('supplier')
                    ->where('is_active', true)
                    ->with('supplierProfile')
                    ->withAvg([
                        'supplierQuotes as avg_rating' => function ($q) {
                            $q->whereNotNull('customer_rating');
                        }
                    ], 'customer_rating')
                    ->withCount([
                        'supplierQuotes as total_reviews' => function ($q) {
                            $q->whereNotNull('customer_rating');
                        }
                    ]);

            return datatables()->of($query)
                ->addIndexColumn()
                ->addColumn('company_logo', function ($row) {
                    if ($row->supplierProfile && $row->supplierProfile->company_logo) {
                        $url = asset($row->supplierProfile->company_logo);
                        return '<img src="'.$url.'"  style="width:50px;height:50px;object-fit:cover;border-radius:6px;" />';
                    }
                    return '<img src="https://placehold.net/default.png"  style="width:50px;height:50px;object-fit:cover;border-radius:6px;" />';
                })
                ->addColumn('company_name', function ($row) {
                    return $row->supplierProfile ? $row->supplierProfile->company_name : 'N/A';
                })
                ->addColumn('avg_rating', function ($row) {
                    return  number_format($row->avg_rating ?? 0, 1) . ' / 5 (' . ($row->total_reviews ?? 0) . ' reviews)';
                })
                ->addColumn('actions', function ($row) {
                    return '<button type="button" class="supplier-action-btn view btn p-2" data-id="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="View"><i class="fa fa-eye"></i></button>';
                })
                 ->rawColumns(['actions', 'company_logo'])
                ->make(true);
        }

        return view('customer-panel.supplier.index');
    }

    public function suppliersDetails($id)
    {
        $suppliers = User::where('is_active', true)
                    ->with('supplierProfile')
                    ->withAvg([
                        'supplierQuotes as avg_rating' => function ($q) {
                            $q->whereNotNull('customer_rating');
                        }
                    ], 'customer_rating')
                    ->withCount([
                        'supplierQuotes as total_reviews' => function ($q) {
                            $q->whereNotNull('customer_rating');
                        }
                    ])
                    ->findOrFail($id);
                     
                    // dd($suppliers->toArray());

        return view('customer-panel.supplier.supplier-details', compact('suppliers'));
    }
}
