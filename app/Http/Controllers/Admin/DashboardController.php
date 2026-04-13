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
        $jobs = CustomerJob::with(['user'])->get(); 

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

        return view('admin.dashboard', compact(
            'customerCount',
            'supplierCount',
            'jobs',
            'jobPercentage',
            'customerPercentage',
            'supplierPercentage'
        ));
    }
}
