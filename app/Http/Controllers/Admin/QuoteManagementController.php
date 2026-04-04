<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerJob;
use Illuminate\View\View;

class QuoteManagementController extends Controller
{
    public function index(): View
    {
        $stats = [
            'jobs_available_for_quotes' => CustomerJob::count(),
            'active_jobs' => CustomerJob::where('status', 'open')->count(),
            'ending_soon' => CustomerJob::whereDate('needed_by', '>=', now()->toDateString())
                ->whereDate('needed_by', '<=', now()->addDays(3)->toDateString())
                ->count(),
        ];

        return view('admin.quotes.index', compact('stats'));
    }
}
