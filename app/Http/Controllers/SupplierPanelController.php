<?php

namespace App\Http\Controllers;

use App\Models\CustomerJob;
use App\Models\OrganisationCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierPanelController extends Controller
{
    public function dashboard(Request $request): View
    {
        $user = $request->user()->load(['supplierProfile', 'organisationCategories']);
        $jobs = CustomerJob::query()->latest()->take(6)->get();

        $stats = [
            'available_jobs' => CustomerJob::count(),
            'active_jobs' => CustomerJob::where('status', 'open')->count(),
            'ending_soon' => CustomerJob::whereDate('needed_by', '>=', now()->toDateString())
                ->whereDate('needed_by', '<=', now()->addDays(3)->toDateString())
                ->count(),
            'ended_jobs' => CustomerJob::where(function ($query) {
                $query->where('status', '!=', 'open')
                    ->orWhereDate('needed_by', '<', now()->toDateString());
            })->count(),
        ];

        return view('supplier-panel.dashboard', compact('user', 'stats', 'jobs'));
    }

    public function jobs(Request $request): View
    {
        $query = CustomerJob::query()->with('user:id,name,email');

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($jobQuery) use ($search) {
                $jobQuery
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('organisation_name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $sort = $request->string('sort', 'newest')->toString();
        match ($sort) {
            'oldest' => $query->oldest(),
            'budget_high' => $query->orderByDesc('budget'),
            'budget_low' => $query->orderBy('budget'),
            'ending_soon' => $query->orderBy('needed_by'),
            default => $query->latest(),
        };

        $jobs = $query->paginate(9)->withQueryString();

        return view('supplier-panel.jobs.index', compact('jobs', 'sort'));
    }

    public function reports(): View
    {
        $jobsByCategory = CustomerJob::query()
            ->selectRaw("COALESCE(category, 'Uncategorised') as category_name, COUNT(*) as total")
            ->groupBy('category_name')
            ->orderByDesc('total')
            ->take(6)
            ->get();

        $jobsByLocation = CustomerJob::query()
            ->selectRaw("COALESCE(location, 'Unspecified') as location_name, COUNT(*) as total")
            ->groupBy('location_name')
            ->orderByDesc('total')
            ->take(6)
            ->get();

        return view('supplier-panel.reports.index', compact('jobsByCategory', 'jobsByLocation'));
    }

    public function activity(): View
    {
        $recentJobs = CustomerJob::query()->with('user:id,name')->latest()->take(10)->get();

        return view('supplier-panel.activity.index', compact('recentJobs'));
    }

    public function profile(Request $request): View
    {
        $organisation = OrganisationCategory::query()
            ->where('type', 'supplier')
            ->orderBy('name')
            ->get();

        $user = $request->user()->load(['supplierProfile', 'organisationCategories']);

        return view('supplier-panel.profile.index', compact('user', 'organisation'));
    }
}
