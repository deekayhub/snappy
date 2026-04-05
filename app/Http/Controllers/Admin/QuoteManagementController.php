<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerJob;
use App\Models\Quote;
use Illuminate\View\View;

class QuoteManagementController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_quotes' => Quote::count(),
            'submitted_quotes' => Quote::where('status', 'submitted')->count(),
            'accepted_quotes' => Quote::where('status', 'accepted')->count(),
            'jobs_with_quotes' => CustomerJob::has('quotes')->count(),
        ];

        $quotes = Quote::query()
            ->with(['job.user', 'supplier.supplierProfile'])
            ->latest()
            ->get();

        return view('admin.quotes.index', compact('stats', 'quotes'));
    }
}
