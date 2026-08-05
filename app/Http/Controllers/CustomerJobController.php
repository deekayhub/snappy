<?php

namespace App\Http\Controllers;

use App\Models\CustomerJob;
use App\Models\OrganisationCategory;
use App\Models\Quote;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class CustomerJobController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if (! $request->user()?->hasRole('customer')) {
            return redirect()
                ->route('home')
                ->with('error', 'Only customer accounts can post a job.');
        }
        $categories = OrganisationCategory::where('type', 'supplier')->get();
        return view('customer-jobs.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        if (! $request->user()?->hasRole('customer')) {
            return redirect()
                ->route('home')
                ->with('error', 'Only customer accounts can post a job.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'organisation_name' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'needed_by' => ['nullable', 'date', 'after_or_equal:today'],
            'description' => ['required', 'string', 'min:0'],
        ]);

        $job = $request->user()->customerJobs()->create($validated);

        $query = User::role('supplier')->where('is_active', true);

        if ($job->category) {
            $query->whereHas('organisationCategories', function ($q) use ($job) {
                $q->where('organisation_category_id', $job->category);
            });
        }

        $query->chunk(100, function ($suppliers) use ($job) {
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

        return redirect()
            ->route('customer-panel.dashboard')
            ->with('success', 'Your job has been posted successfully.');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $jobs = CustomerJob::query()
                ->with(['user:id,name,email', 'categoryId', 'quotes.supplier.supplierProfile'])
                ->latest();

            return DataTables::eloquent($jobs)
                ->addIndexColumn()
                ->addColumn('customer_name', fn (CustomerJob $job) => $job->user?->name ?? '-')
                ->addColumn('customer_email', fn (CustomerJob $job) => $job->user?->email ?? '-')
                ->editColumn('budget', fn (CustomerJob $job) => $job->budget ? '£ '.number_format((float) $job->budget, 2) : '-')
                ->editColumn('needed_by', fn (CustomerJob $job) => $job->needed_by?->format('d M Y') ?? '-')
                ->editColumn('status', fn (CustomerJob $job) => '<span class="supplier-status-badge">'.e(ucfirst($job->status)).'</span>')
                ->editColumn('created_at', fn (CustomerJob $job) => $job->created_at?->format('d M Y') ?? '-')
                ->addColumn('quotes_count', fn (CustomerJob $job) => '<span class="job-quote-badge">'.$job->quotes->count().'</span>')
                ->addColumn('winner', function (CustomerJob $job) {
                    $winner = $job->quotes->first(fn (Quote $quote) => in_array($quote->status, ['accepted', 'completed']));

                    if ($winner) {
                        $supplierName = $winner->supplier?->supplierProfile?->company_name
                            ?: ($winner->supplier?->name ?? 'Supplier');

                        return '<span class="winner-badge"><i class="mdi mdi-trophy-outline"></i>'.e($supplierName).'</span>';
                    }

                    return $job->quotes->isNotEmpty()
                        ? '<span class="text-muted">No winner yet</span>'
                        : '<span class="text-muted">—</span>';
                })
                ->addColumn('action', function (CustomerJob $job) {
                    return '<button type="button" class="job-action-btn view" data-id="' . $job->id . '" data-toggle="tooltip" data-placement="top" title="View"><i class="mdi mdi-eye-outline"></i></button>
                            <button type="button" class="job-action-btn delete" data-id="' . $job->id . '" data-toggle="tooltip" data-placement="top" title="Delete"><i class="mdi mdi-trash-can-outline"></i></button>';
                })
                ->rawColumns(['status', 'quotes_count', 'winner', 'action'])
                ->make(true);
        }

        $stats = [
            'total' => CustomerJob::count(),
            'open' => CustomerJob::where('status', 'open')->count(),
            'recent' => CustomerJob::whereDate('created_at', now()->toDateString())->count(),
        ];

        return view('admin.jobs.index', compact('stats'));
    }

    public function destroy(CustomerJob $job)
    {
        $job->delete();

        return response()->json([
            'success' => true,
            'message' => 'Job deleted successfully.',
        ]);
    }

    public function details(CustomerJob $job)
    {
        $job->load([
            'categoryId',
            'user' => fn ($q) => $q->with('customerProfile')->select('id', 'name', 'email', 'phone'),
            'dynamicFieldValues',
            'quotes.supplier.supplierProfile',
        ]);

        $grouped = $job->dynamicFieldValues
            ->sortBy([
                ['item_no', 'asc'],
                ['field_id', 'asc'],
            ])
            ->groupBy('item_no')
            ->map(function ($items) {
                return $items->values()->map(function ($item) {
                    $rawValue = $item->field_value;
                    $parsedValue = is_string($rawValue) ? json_decode($rawValue, true) : $rawValue;

                    if (json_last_error() !== JSON_ERROR_NONE && is_string($rawValue)) {
                        $parsedValue = $rawValue;
                    }

                    return [
                        'label' => $item->categoryFields?->field_label ?: ($item->categoryFields?->field_name ?? 'Field'),
                        'value' => is_array($parsedValue) ? implode(', ', $parsedValue) : $parsedValue,
                    ];
                })->toArray();
            })
            ->values()
            ->toArray();

        $quotes = $job->quotes->map(function (Quote $quote) {
            $company = $quote->supplier?->supplierProfile?->company_name
                ?: ($quote->supplier?->name ?? 'Supplier');

            return [
                'id' => $quote->id,
                'supplier_id' => $quote->supplier_user_id,
                'supplier_name' => $quote->supplier?->name ?? 'Supplier',
                'company_name' => $quote->supplier?->supplierProfile?->company_name,
                'email' => $quote->supplier?->email,
                'total_price' => '£ '.number_format((float) $quote->total_price, 2),
                'status' => $quote->status,
                'sent_at' => $quote->sent_at?->format('d M Y') ?: '-',
                'is_winner' => in_array($quote->status, ['accepted', 'completed']),
            ];
        })->values();

        return response()->json([
            'id' => $job->id,
            'title' => $job->title,
            'status' => ucfirst($job->status),
            'category' => $job->categoryId?->name ?? 'General',
            'organisation_name' => $job->organisation_name ?: '-',
            'location' => $job->location ?: '-',
            'budget' => $job->budget ? '£ '.number_format((float) $job->budget, 2) : '-',
            'needed_by' => $job->needed_by?->format('d M Y') ?? '-',
            'description' => $job->description,
            'posted_at' => $job->created_at?->format('d M Y') ?? '-',
            'posted_by' => [
                'name' => $job->user?->name ?? '-',
                'email' => $job->user?->email ?? '-',
                'school' => $job->user?->customerProfile?->school_name ?? '-',
            ],
            'quotes_count' => $job->quotes->count(),
            'quotes' => $quotes,
            'dynamic_fields' => $grouped,
        ]);
    }
}
