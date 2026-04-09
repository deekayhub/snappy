<?php

namespace App\Http\Controllers;

use App\Models\Quote;
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

        return view('customer-panel.jobs.index', compact('jobs'));
    }

    public function quotes(Request $request): View
    {
        $jobs = $request->user()
            ->customerJobs()
            ->with([
                'quotes' => fn ($quoteQuery) => $quoteQuery->latest(),
                'quotes.supplier:id,name,email',
                'quotes.supplier.supplierProfile:id,user_id,company_name',
            ])
            ->latest()
            ->get();

        return view('customer-panel.quotes.index', compact('jobs'));
    }
}
