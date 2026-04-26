<?php

namespace App\Http\Controllers;

use App\Models\CustomerJob;
use App\Models\OrganisationCategory;
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

        $request->user()->customerJobs()->create($validated);

        return back()
            ->with('success', 'Your job has been posted successfully.');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $jobs = CustomerJob::query()
                ->with('user:id,name,email')
                ->latest();

            return DataTables::eloquent($jobs)
                ->addIndexColumn()
                ->addColumn('customer_name', fn (CustomerJob $job) => $job->user?->name ?? '-')
                ->addColumn('customer_email', fn (CustomerJob $job) => $job->user?->email ?? '-')
                ->editColumn('budget', fn (CustomerJob $job) => $job->budget ? '£ '.number_format((float) $job->budget, 2) : '-')
                ->editColumn('needed_by', fn (CustomerJob $job) => $job->needed_by?->format('d M Y') ?? '-')
                ->editColumn('status', fn (CustomerJob $job) => '<span class="supplier-status-badge">'.e(ucfirst($job->status)).'</span>')
                ->editColumn('created_at', fn (CustomerJob $job) => $job->created_at?->format('d M Y') ?? '-')
                ->rawColumns(['status'])
                ->make(true);
        }

        $stats = [
            'total' => CustomerJob::count(),
            'open' => CustomerJob::where('status', 'open')->count(),
            'recent' => CustomerJob::whereDate('created_at', now()->toDateString())->count(),
        ];

        return view('admin.jobs.index', compact('stats'));
    }
}
