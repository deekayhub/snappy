<?php

namespace App\Http\Controllers\Admin;

use \App\Models\CustomerJob;
use \App\Models\Quote;
use \App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $customerCount = User::role('customer')->count();
        $supplierCount = User::role('supplier')->count();
        $jobs = CustomerJob::with(['user'])->get(); 
        // dd($jobs->toArray());
        // dd($customerCount, $supplierCount, $jobs->toArray(), $quotes->toArray());
        return view('admin.dashboard', compact('customerCount', 'supplierCount', 'jobs'));
    }
}
