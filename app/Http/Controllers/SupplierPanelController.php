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
        $recentQuotes = $request->user()
            ->supplierQuotes()
            ->with('job')
            ->latest()
            ->take(5)
            ->get();

        $ratingSummary = $request->user()
            ->supplierQuotes()
            ->whereNotNull('customer_rating')
            ->selectRaw('AVG(customer_rating) as average_rating, COUNT(customer_rating) as ratings_count')
            ->first();
        $supplierAverageRating = $ratingSummary?->average_rating
            ? round((float) $ratingSummary->average_rating, 1)
            : null;
        $supplierRatingsCount = (int) ($ratingSummary?->ratings_count ?? 0);
            // dd($ratingSummary->toArray());

        $stats = [
            'available_jobs' => CustomerJob::count(),
            'active_jobs' => CustomerJob::where('status', 'open')->count(),
            'ending_soon' => CustomerJob::where('needed_by', '>=', now())
                ->where('needed_by', '<=', now()->addHours(2))
                ->count(),
            'ended_jobs' => CustomerJob::where(function ($query) {
                $query->where('status', '!=', 'open')
                    ->orWhere('needed_by', '<', now());
            })->count(),
            'submitted_quotes' => $request->user()->supplierQuotes()->count(),
        ];

        return view('supplier-panel.dashboard', compact('user', 'stats', 'jobs', 'recentQuotes', 'supplierAverageRating', 'supplierRatingsCount'));
    }

    public function jobs(Request $request): View
    {
        $query = CustomerJob::query()
            ->with(['user:id,name,email', 'quotes' => fn ($quoteQuery) => $quoteQuery->where('supplier_user_id', $request->user()->id)]);

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
        if ($request->filled('category')) {
            $query->where('category', $request->string('category')->toString());
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
        $categories = OrganisationCategory::query()->where('type', 'supplier')->orderBy('name')->get();

        return view('supplier-panel.jobs.index', compact('jobs', 'sort', 'categories'));
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
        $recentQuotes = auth()->user()
            ->supplierQuotes()
            ->with('job')
            ->latest()
            ->take(10)
            ->get();

        return view('supplier-panel.activity.index', compact('recentJobs', 'recentQuotes'));
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
