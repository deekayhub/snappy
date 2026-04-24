<?php

namespace App\Http\Controllers;

use App\Models\CustomerJob;
use App\Models\JobItem;
use App\Models\OrganisationCategory;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            ->with(['jobItems'])
            ->withCount('quotes')
            ->latest()
            ->paginate(10);
        $categories = OrganisationCategory::where('type', 'supplier')->get();
        return view('customer-panel.jobs.index', compact('jobs', 'categories'));
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

        DB::transaction(function () use ($request, $validated) {
            $job = $request->user()->customerJobs()->create($this->buildJobData($validated));
            $this->syncJobItems($request, $job, $validated['items']);
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

        $job->load('jobItems');

        return response()->json([
            'success' => true,
            'job' => [
                'id' => $job->id,
                'title' => $job->title,
                'category' => $job->category,
                'organisation_name' => $job->organisation_name,
                'location' => $job->location,
                'budget' => $job->budget,
                'delivery_in_uk' => (bool) $job->delivery_in_uk,
                'personalisation_required' => (bool) $job->personalisation_required,
                'personalisation_mode' => $job->personalisation_mode,
                'supplier_target_type' => $job->supplier_target_type,
                'supplier_target_count' => $job->supplier_target_count,
                'needed_by' => $job->needed_by?->format('Y-m-d H:i'),
                'notes' => $job->notes,
                'items' => $job->jobItems->map(function (JobItem $item) {
                    return [
                        'id' => $item->id,
                        'item_name' => $item->item_name,
                        'quantity' => $item->quantity,
                        'sku_codes' => implode(', ', $item->sku_codes ?? []),
                        'item_link' => $item->item_link,
                        'allow_similar_quote' => (bool) $item->allow_similar_quote,
                        'image_paths' => $item->image_paths ?? [],
                    ];
                })->values(),
            ],
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

        DB::transaction(function () use ($request, $job, $validated) {
            $job->update($this->buildJobData($validated));
            $this->syncJobItems($request, $job, $validated['items']);
        });

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
        // dd($organisation->toArray());

        return view('customer-panel.profile.index', compact('user', 'organisation'));
    }

    private function validateJob(Request $request): array
    {
        return $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'organisation_name' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'delivery_in_uk' => ['nullable', 'boolean'],
            'personalisation_required' => ['nullable', 'boolean'],
            'personalisation_mode' => ['nullable', 'required_if:personalisation_required,1,true', 'in:same,different'],
            'supplier_target_type' => ['nullable', 'in:all,count'],
            'supplier_target_count' => ['nullable', 'integer', 'min:1', 'required_if:supplier_target_type,count'],
            'needed_by' => ['required', 'date', 'after_or_equal:now'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['nullable', 'integer'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.sku_codes' => ['nullable', 'string', 'max:2000'],
            'items.*.item_link' => ['nullable', 'url', 'max:255'],
            'items.*.allow_similar_quote' => ['nullable', 'boolean'],
            'items.*.existing_image_paths' => ['nullable', 'array'],
            'items.*.existing_image_paths.*' => ['nullable', 'string', 'max:255'],
            'items.*.images' => ['nullable', 'array'],
            'items.*.images.*' => ['nullable', 'file', 'image', 'max:5120'],
        ]);
    }

    private function buildJobData(array $validated): array
    {
        $items = $validated['items'] ?? [];
        $firstItem = $items[0]['item_name'] ?? 'Job request';
        $itemCount = count($items);
        $title = $validated['title'] ?? $firstItem;

        if ($itemCount > 1 && blank($validated['title'] ?? null)) {
            $title = $firstItem.' and '.($itemCount - 1).' more';
        }

        return [
            'title' => Str::limit((string) $title, 255, ''),
            'category' => $validated['category'] ?? null,
            'organisation_name' => $validated['organisation_name'] ?? null,
            'location' => $validated['location'] ?? null,
            'budget' => $validated['budget'] ?? null,
            'delivery_in_uk' => (bool) ($validated['delivery_in_uk'] ?? true),
            'personalisation_required' => (bool) ($validated['personalisation_required'] ?? false),
            'personalisation_mode' => $validated['personalisation_mode'] ?? null,
            'supplier_target_type' => $validated['supplier_target_type'] ?? 'all',
            'supplier_target_count' => $validated['supplier_target_count'] ?? null,
            'needed_by' => $validated['needed_by'],
            'description' => $this->buildJobDescription($validated),
            'notes' => $validated['notes'] ?? null,
            'status' => 'open',
        ];
    }

    private function buildJobDescription(array $validated): string
    {
        $lines = [];
        $items = $validated['items'] ?? [];

        foreach ($items as $index => $item) {
            $skuCodes = $this->normalizeSkuCodes($item['sku_codes'] ?? null);
            $line = ($index + 1).'. '.$item['item_name'].' x '.(int) $item['quantity'];

            if ($skuCodes) {
                $line .= ' | SKU: '.implode(', ', $skuCodes);
            }

            if (! empty($item['item_link'])) {
                $line .= ' | Link: '.$item['item_link'];
            }

            if (! empty($item['allow_similar_quote'])) {
                $line .= ' | Similar quote allowed';
            }

            $lines[] = $line;
        }

        if (! empty($validated['personalisation_required'])) {
            $lines[] = 'Personalisation: '.(($validated['personalisation_mode'] ?? 'same') === 'different' ? 'Different on each item' : 'All the same');
        }

        $lines[] = 'Delivery in UK: '.(! empty($validated['delivery_in_uk']) ? 'Yes' : 'No');
        $lines[] = 'Supplier target: '.(($validated['supplier_target_type'] ?? 'all') === 'count' ? ('Send to '.$validated['supplier_target_count'].' suppliers') : 'All registered suppliers');

        if (! empty($validated['notes'])) {
            $lines[] = 'Notes: '.$validated['notes'];
        }

        return trim(implode("\n", $lines));
    }

    private function syncJobItems(Request $request, CustomerJob $job, array $items): void
    {
        $existingItems = $job->jobItems()->get()->keyBy('id');
        $keepIds = [];

        foreach ($items as $index => $itemData) {
            $existingItem = null;
            $itemId = (int) ($itemData['id'] ?? 0);

            if ($itemId && $existingItems->has($itemId)) {
                $existingItem = $existingItems->get($itemId);
                $keepIds[] = $existingItem->id;
            }

            $existingPaths = array_values(array_filter((array) ($itemData['existing_image_paths'] ?? [])));
            $uploadedPaths = $this->storeJobItemImages($request, $index, $job);
            $imagePaths = array_values(array_unique(array_merge($existingPaths, $uploadedPaths)));

            $jobItem = $existingItem ?: new JobItem();
            $jobItem->customer_job_id = $job->id;
            $jobItem->item_name = $itemData['item_name'];
            $jobItem->quantity = (int) $itemData['quantity'];
            $jobItem->sku_codes = $this->normalizeSkuCodes($itemData['sku_codes'] ?? null);
            $jobItem->item_link = $itemData['item_link'] ?? null;
            $jobItem->allow_similar_quote = (bool) ($itemData['allow_similar_quote'] ?? false);
            $jobItem->image_paths = $imagePaths;
            $jobItem->save();

            $keepIds[] = $jobItem->id;
        }

        $job->jobItems()
            ->when($keepIds, fn ($query) => $query->whereNotIn('id', array_unique($keepIds)))
            ->delete();
    }

    private function storeJobItemImages(Request $request, int $index, CustomerJob $job): array
    {
        $files = data_get($request->file(), "items.$index.images", []);
        $files = is_array($files) ? $files : [$files];
        $paths = [];

        foreach ($files as $file) {
            if (! $file) {
                continue;
            }

            $paths[] = $file->store("job-items/{$job->id}", 'public');
        }

        return $paths;
    }

    private function normalizeSkuCodes(?string $value): array
    {
        if (blank($value)) {
            return [];
        }

        $codes = preg_split('/[\r\n,]+/', $value) ?: [];

        return array_values(array_filter(array_map(static fn ($code) => trim((string) $code), $codes)));
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
                    
                    // return response()->json($query->get()->toArray());

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
}
