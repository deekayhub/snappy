<?php

namespace App\Http\Controllers;

use App\Models\CustomerJob;
use App\Models\OrganisationCategory;
use App\Models\Quote;
use App\Models\User;
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
        $request->user()->customerJobs()->create($validated);

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

        return response()->json([
            'success' => true,
            'job' => [
                'id' => $job->id,
                'title' => $job->title,
                'category' => $job->category,
                'organisation_name' => $job->organisation_name,
                'location' => $job->location,
                'budget' => $job->budget,
                'needed_by' => $job->needed_by?->format('Y-m-d'),
                'description' => $job->description,
            ],
        ]);
    }

    public function updateJob(Request $request, CustomerJob $job): JsonResponse
    {
        // dd($request->all());
        if ((int) $job->user_id !== (int) $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not allowed to update this job.'
            ], 403);
        }

        $validated = $this->validateJob($request);
        $job->update($validated);

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
            'needed_by' => ['nullable', 'date', 'after_or_equal:today'],
            'description' => ['required', 'string'],
        ]);
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
