<?php

namespace App\Http\Controllers\Admin;

use \App\Models\CustomerJob;
use \App\Models\Quote;
use \App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{

    public function index()
    {
        // Counts
        $customerCount = User::role('customer')->count();
        $supplierCount = User::role('supplier')->count();
        $jobs = CustomerJob::with(['user', 'categoryId'])->get(); 

        // Date ranges
        $startOfThisWeek = Carbon::now()->startOfWeek();
        $startOfLastWeek = Carbon::now()->subWeek()->startOfWeek();
        $endOfLastWeek = Carbon::now()->subWeek()->endOfWeek();

        // Jobs
        $thisWeekJobs = CustomerJob::where('created_at', '>=', $startOfThisWeek)->count();
        $lastWeekJobs = CustomerJob::whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])->count();

        $jobPercentage = $lastWeekJobs > 0 
            ? (($thisWeekJobs - $lastWeekJobs) / $lastWeekJobs) * 100 
            : 100;

        // Customers
        $thisWeekCustomers = User::role('customer')->where('created_at', '>=', $startOfThisWeek)->count();
        $lastWeekCustomers = User::role('customer')->whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])->count();

        $customerPercentage = $lastWeekCustomers > 0 
            ? (($thisWeekCustomers - $lastWeekCustomers) / $lastWeekCustomers) * 100 
            : 100;

        // Suppliers
        $thisWeekSuppliers = User::role('supplier')->where('created_at', '>=', $startOfThisWeek)->count();
        $lastWeekSuppliers = User::role('supplier')->whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])->count();

        $supplierPercentage = $lastWeekSuppliers > 0 
            ? (($thisWeekSuppliers - $lastWeekSuppliers) / $lastWeekSuppliers) * 100 
            : 100;

        // Open jobs
        $openJobs = CustomerJob::where('status', 'open')->count();

        $totalJobs = $jobs->count();
        $openJobShare = $totalJobs > 0 ? round(($openJobs / $totalJobs) * 100, 1) : 0;

        // Quotes
        $totalQuotes = Quote::count();
        $thisWeekQuotes = Quote::where('created_at', '>=', $startOfThisWeek)->count();
        $lastWeekQuotes = Quote::whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])->count();

        $quotePercentage = $lastWeekQuotes > 0
            ? (($thisWeekQuotes - $lastWeekQuotes) / $lastWeekQuotes) * 100
            : 100;

        // Revenue (won value)
        $totalRevenue = Quote::whereIn('status', ['accepted', 'completed'])->sum('total_price');
        $thisWeekRevenue = Quote::where('created_at', '>=', $startOfThisWeek)
            ->whereIn('status', ['accepted', 'completed'])
            ->sum('total_price');
        $lastWeekRevenue = Quote::whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])
            ->whereIn('status', ['accepted', 'completed'])
            ->sum('total_price');

        if ($lastWeekRevenue > 0) {
            $revenuePercentage = (($thisWeekRevenue - $lastWeekRevenue) / $lastWeekRevenue) * 100;
        } elseif ($thisWeekRevenue > 0) {
            $revenuePercentage = 100;
        } else {
            $revenuePercentage = 0;
        }

        // Quote Overview chart (current year, Jan-Dec)
        $monthlyQuotesRaw = Quote::query()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $monthlyQuotes = collect();
        for ($monthNo = 1; $monthNo <= 12; $monthNo++) {
            $date = Carbon::create(now()->year, $monthNo, 1);
            $month = $date->format('Y-m');
            $monthlyQuotes->push([
                'label' => $date->format('M'),
                'total' => (int) ($monthlyQuotesRaw[$month] ?? 0),
            ]);
        }

        // Jobs overview trend (last week vs current week)
        $startOfThisWeek = now()->startOfWeek();
        $startOfLastWeek = now()->subWeek()->startOfWeek();
        $endOfLastWeek = now()->subWeek()->endOfWeek();

        $jobsCurrentWeek = CustomerJob::where('created_at', '>=', $startOfThisWeek)->count();
        $jobsLastWeek = CustomerJob::whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])->count();

        if ($jobsLastWeek > 0) {
            $jobsWeekPercentage = (($jobsCurrentWeek - $jobsLastWeek) / $jobsLastWeek) * 100;
        } elseif ($jobsCurrentWeek > 0) {
            $jobsWeekPercentage = 100;
        } else {
            $jobsWeekPercentage = 0;
        }

        // Jobs overview chart (month-wise bars for the current year)
        $monthlyJobsRaw = CustomerJob::query()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $monthlyJobsChart = collect();
        for ($monthNo = 1; $monthNo <= 12; $monthNo++) {
            $date = Carbon::create(now()->year, $monthNo, 1);
            $month = $date->format('Y-m');
            $monthlyJobsChart->push([
                'label' => $date->format('M'),
                'total' => (int) ($monthlyJobsRaw[$month] ?? 0),
            ]);
        }

        // Type By Amount doughnut (quote value by status)
        $quoteAmountRaw = Quote::query()
            ->selectRaw('status, SUM(total_price) as amount')
            ->groupBy('status')
            ->pluck('amount', 'status');

        $quoteAmountByStatus = collect(['submitted', 'accepted', 'completed', 'rejected'])
            ->map(fn ($status) => [
                'label' => ucfirst($status),
                'value' => (float) ($quoteAmountRaw[$status] ?? 0),
                'color' => match ($status) {
                    'submitted' => '#1F3BB3',
                    'accepted' => '#FDD0C7',
                    'completed' => '#52CDFF',
                    'rejected' => '#ef4444',
                },
            ])
            ->values();

        return view('admin.dashboard', compact(
            'customerCount',
            'supplierCount',
            'jobs',
            'jobPercentage',
            'customerPercentage',
            'supplierPercentage',
            'openJobs',
            'openJobShare',
            'totalJobs',
            'totalQuotes',
            'quotePercentage',
            'totalRevenue',
            'revenuePercentage',
            'monthlyJobsChart',
            'jobsCurrentWeek',
            'jobsWeekPercentage',
            'quoteAmountByStatus'
        ));
    }
}
