<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\CustomerJob;
use App\Models\OrganisationCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierPanelController extends Controller
{
    private const ENDING_SOON_THRESHOLD_HOURS = 24;

    public function dashboard(Request $request): View
    {
        $user = $request->user()->load(['supplierProfile', 'organisationCategories']);
        $jobs = CustomerJob::with('categoryId:id,name')->latest()->take(6)->get();
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
            'ending_soon' => CustomerJob::where('status', 'open')
                ->whereBetween('needed_by', [now(), now()->addHours(self::ENDING_SOON_THRESHOLD_HOURS)])
                ->count(),
            'ended_jobs' => CustomerJob::where(function ($query) {
                $query->where('status', '!=', 'open')
                    ->orWhereDate('needed_by', '<', now()->toDateString());
            })->count(),
            'submitted_quotes' => $request->user()->supplierQuotes()->count(),
        ];

        return view('supplier-panel.dashboard', compact('user', 'stats', 'jobs', 'recentQuotes', 'supplierAverageRating', 'supplierRatingsCount'));
    }

    public function jobs(Request $request): View
    {
        $query = CustomerJob::query()
            ->with(['categoryId', 'dynamicFieldValues', 'user:id,name,email', 'quotes' => fn ($quoteQuery) => $quoteQuery->where('supplier_user_id', $request->user()->id)]);

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
        $jobs->getCollection()->transform(function ($job) {

            $grouped = $job->dynamicFieldValues
                ->sortBy([
                    ['item_no', 'asc'],
                    ['field_id', 'asc']
                ])
                ->groupBy('item_no')
                ->map(function ($items) {
                    return $items->values()->map(function ($item) {
                        $rawValue = $item->field_value;
                        $parsedValue = is_string($rawValue) ? json_decode($rawValue, true) : $rawValue;

                        if (json_last_error() !== JSON_ERROR_NONE && is_string($rawValue)) {
                            $parsedValue = $rawValue;
                        }

                        $item->setAttribute('parsed_value', $parsedValue);

                        return $item;
                    })->toArray();
                })
                ->values()
                ->toArray();

            /*
            overwrite relation
            */
            $job->setRelation('dynamicFieldValues', collect($grouped));

            return $job;
        });
         
        // dd($jobs->toArray());
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

        $user = $request->user()->load(['supplierProfile', 'organisationCategories', 'notificationPreference']);

        return view('supplier-panel.profile.index', compact('user', 'organisation'));
    }

    public function updateNotificationPreferences(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'email_alerts' => ['boolean'],
            'sms_alerts' => ['boolean'],
            'phone_number' => ['nullable', 'string', 'max:20'],
        ]);

        $user = $request->user();

        $prefs = $user->notificationPreference()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'email_alerts' => $validated['email_alerts'] ?? false,
                'sms_alerts' => $validated['sms_alerts'] ?? false,
                'phone_number' => $validated['phone_number'] ?? null,
            ]
        );

        return redirect()->route('supplier-panel.profile')
            ->with('success', 'Notification preferences saved.');
    }

    public function subscriptionIndex(Request $request): View
    {
        $plans = Plan::active()->ordered()->get();
        $subscription = $request->user()->subscription('default');

        return view('supplier-panel.subscription.index', compact('plans', 'subscription'));
    }

    public function subscriptionCheckout(Request $request, Plan $plan)
    {
        $user = $request->user();
        $currentPlan = $user->currentPlan();

        if ($plan->is_free) {
            if ($user->subscribed('default')) {
                $user->subscription('default')->cancelNow();
            }

            return redirect()->route('supplier-panel.subscription.index')
                ->with('success', 'You are now on the Basic (Free) plan.');
        }

        if (! $plan->stripe_price_id) {
            return redirect()->route('supplier-panel.subscription.index')
                ->with('error', 'This plan is not yet configured for billing. Please run "php artisan stripe:sync-plans" to set up Stripe products.');
        }

        if ($currentPlan?->slug === 'bronze' && $plan->stripe_price_id !== $currentPlan->stripe_price_id) {
            if ($user->subscribed('default')) {
                $user->subscription('default')->cancelNow();
            }

            $checkout = $user->newSubscription('default', $plan->stripe_price_id)
                ->checkout([
                    'success_url' => route('supplier-panel.subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('supplier-panel.subscription.index'),
                ]);

            return redirect($checkout->url)
                ->with('info', 'Your Bronze subscription was ended first, and a new checkout was created for the selected plan.');
        }

        if ($user->subscribed('default')) {
            return $this->subscriptionSwap($request, $plan);
        }

        $checkout = $user->newSubscription('default', $plan->stripe_price_id)
            ->checkout([
                'success_url' => route('supplier-panel.subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('supplier-panel.subscription.index'),
            ]);

        return redirect($checkout->url);
    }

    protected function subscriptionSwap(Request $request, Plan $plan)
    {
        $user = $request->user();
        $subscription = $user->subscription('default');

        if (! $plan->stripe_price_id) {
            return redirect()->route('supplier-panel.subscription.index')
                ->with('error', 'This plan is not yet configured for billing.');
        }

        if ($subscription->stripe_price === $plan->stripe_price_id) {
            return redirect()->route('supplier-panel.subscription.index')
                ->with('info', 'You are already on this plan.');
        }

        $subscription->swap($plan->stripe_price_id);

        return redirect()->route('supplier-panel.subscription.index')
            ->with('success', 'Subscription changed to ' . $plan->name . ' successfully.');
    }

    public function subscriptionCancel(Request $request)
    {
        $user = $request->user();

        if ($user->subscribed('default')) {
            $user->subscription('default')->cancel();
        }

        return redirect()->route('supplier-panel.subscription.index')
            ->with('success', 'Subscription cancelled successfully. Access continues until the end of the billing period.');
    }

    public function subscriptionResume(Request $request)
    {
        $user = $request->user();

        if ($user->subscribed('default') && $user->subscription('default')->onGracePeriod()) {
            $user->subscription('default')->resume();
        }

        return redirect()->route('supplier-panel.subscription.index')
            ->with('success', 'Subscription resumed successfully.');
    }

    public function subscriptionSuccess(Request $request)
    {
        return redirect()->route('supplier-panel.subscription.index')
            ->with('success', 'We received your checkout response from Stripe. Your subscription will appear active once Stripe confirms it through the webhook.');
    }

    public function subscriptionInvoices(Request $request): View
    {
        $invoices = $request->user()->invoices();

        return view('supplier-panel.subscription.invoices', compact('invoices'));
    }

    public function downloadSubscriptionInvoice(Request $request, string $invoiceId)
    {
        return $request->user()->downloadInvoice($invoiceId, [
            'vendor' => config('app.name'),
            'product' => 'Subscription',
        ]);
    }

    public function analytics(Request $request): View
    {
        $user = $request->user();

        $monthlyQuoteStats = $user->supplierQuotes()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->take(12)
            ->get();

        $categoryBreakdown = \App\Models\CustomerJob::query()
            ->selectRaw("COALESCE(category, 'General') as name, COUNT(*) as total")
            ->groupBy('name')
            ->orderByDesc('total')
            ->take(8)
            ->get();

        $winRate = $user->supplierQuotes()
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accepted,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
            ")
            ->first();

        $totalEarnings = $user->supplierQuotes()
            ->whereIn('status', ['accepted', 'completed'])
            ->sum('total_price');

        return view('supplier-panel.analytics.index', compact(
            'monthlyQuoteStats',
            'categoryBreakdown',
            'winRate',
            'totalEarnings'
        ));
    }

    public function earlyJobs(Request $request): View
    {
        $query = \App\Models\CustomerJob::query()
            ->with(['categoryId', 'dynamicFieldValues', 'user:id,name,email', 'quotes' => fn ($q) => $q->where('supplier_user_id', $request->user()->id)]);

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
        $jobs->getCollection()->transform(function ($job) {
            $grouped = $job->dynamicFieldValues
                ->sortBy([
                    ['item_no', 'asc'],
                    ['field_id', 'asc']
                ])
                ->groupBy('item_no')
                ->map(function ($items) {
                    return $items->values()->map(function ($item) {
                        $rawValue = $item->field_value;
                        $parsedValue = is_string($rawValue) ? json_decode($rawValue, true) : $rawValue;
                        if (json_last_error() !== JSON_ERROR_NONE && is_string($rawValue)) {
                            $parsedValue = $rawValue;
                        }
                        $item->setAttribute('parsed_value', $parsedValue);
                        return $item;
                    })->toArray();
                })
                ->values()
                ->toArray();
            $job->setRelation('dynamicFieldValues', collect($grouped));
            return $job;
        });

        $categories = \App\Models\OrganisationCategory::query()->where('type', 'supplier')->orderBy('name')->get();

        return view('supplier-panel.jobs.index', compact('jobs', 'sort', 'categories'));
    }
}
