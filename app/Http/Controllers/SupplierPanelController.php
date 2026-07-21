<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\CustomerJob;
use App\Models\OrganisationCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Stripe\StripeClient;

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

    public function reports(Request $request): View
    {
        $user = $request->user();

        $jobsByCategory = OrganisationCategory::query()
            ->select('organisation_categories.name', \Illuminate\Support\Facades\DB::raw('COUNT(customer_jobs.id) as total'))
            ->leftJoin('customer_jobs', 'organisation_categories.id', '=', 'customer_jobs.category')
            ->where('organisation_categories.type', 'supplier')
            ->groupBy('organisation_categories.id', 'organisation_categories.name')
            ->orderByDesc('total')
            ->take(8)
            ->get();

        $jobsByLocation = CustomerJob::query()
            ->selectRaw("COALESCE(NULLIF(location, ''), 'Unspecified') as location_name, COUNT(*) as total")
            ->groupBy('location_name')
            ->orderByDesc('total')
            ->take(8)
            ->get();

        $rawMonthlyTrend = CustomerJob::query()
            ->selectRaw("DATE_FORMAT(customer_jobs.created_at, '%Y-%m') as month, COUNT(*) as total")
            ->join('quotes', 'customer_jobs.id', '=', 'quotes.customer_job_id')
            ->where('quotes.supplier_user_id', $user->id)
            ->where('customer_jobs.created_at', '>=', now()->subMonths(11))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $monthlyTrend = collect();
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $monthlyTrend->push((object) [
                'month' => $month,
                'total' => (int) ($rawMonthlyTrend[$month] ?? 0),
            ]);
        }

        $rawYearlyTrend = CustomerJob::query()
            ->selectRaw("YEAR(customer_jobs.created_at) as year, COUNT(*) as total")
            ->join('quotes', 'customer_jobs.id', '=', 'quotes.customer_job_id')
            ->where('quotes.supplier_user_id', $user->id)
            ->where('customer_jobs.created_at', '>=', now()->subYears(5))
            ->groupBy('year')
            ->orderBy('year')
            ->pluck('total', 'year');

        $yearlyTrend = collect();
        for ($i = 4; $i >= 0; $i--) {
            $year = now()->subYears($i)->format('Y');
            $yearlyTrend->push((object) [
                'year' => $year,
                'total' => (int) ($rawYearlyTrend[$year] ?? 0),
            ]);
        }

        $budgetRanges = [
            'Under £500' => CustomerJob::where('budget', '<', 500)->whereNotNull('budget')->count(),
            '£500 - £1k' => CustomerJob::whereBetween('budget', [500, 1000])->count(),
            '£1k - £5k' => CustomerJob::whereBetween('budget', [1000, 5000])->count(),
            '£5k - £10k' => CustomerJob::whereBetween('budget', [5000, 10000])->count(),
            'Over £10k' => CustomerJob::where('budget', '>', 10000)->count(),
            'Not set' => CustomerJob::whereNull('budget')->count(),
        ];

        $recentJobsCount = CustomerJob::where('created_at', '>=', now()->subDays(30))->count();

        $totalJobs = CustomerJob::count();

        $myQuoteStats = [
            'total' => $user->supplierQuotes()->count(),
            'this_month' => $user->supplierQuotes()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'submitted' => $user->supplierQuotes()->where('status', 'submitted')->count(),
            'accepted' => $user->supplierQuotes()->where('status', 'accepted')->count(),
            'completed' => $user->supplierQuotes()->where('status', 'completed')->count(),
            'rejected' => $user->supplierQuotes()->where('status', 'rejected')->count(),
        ];

        $myQuoteStatusChart = [
            ['status' => 'Submitted', 'count' => $myQuoteStats['submitted'], 'color' => '#0ea5e9'],
            ['status' => 'Accepted', 'count' => $myQuoteStats['accepted'], 'color' => '#22c55e'],
            ['status' => 'Completed', 'count' => $myQuoteStats['completed'], 'color' => '#14b8a6'],
            ['status' => 'Rejected', 'count' => $myQuoteStats['rejected'], 'color' => '#ef4444'],
        ];

        $topJobCategories = OrganisationCategory::query()
            ->select('organisation_categories.name', \Illuminate\Support\Facades\DB::raw('COUNT(quotes.id) as quote_count'))
            ->leftJoin('customer_jobs', 'organisation_categories.id', '=', 'customer_jobs.category')
            ->leftJoin('quotes', function ($join) use ($user) {
                $join->on('customer_jobs.id', '=', 'quotes.customer_job_id')
                    ->where('quotes.supplier_user_id', '=', $user->id);
            })
            ->where('organisation_categories.type', 'supplier')
            ->groupBy('organisation_categories.id', 'organisation_categories.name')
            ->orderByDesc('quote_count')
            ->take(5)
            ->get();

        return view('supplier-panel.reports.index', compact(
            'jobsByCategory',
            'jobsByLocation',
            'monthlyTrend',
            'yearlyTrend',
            'budgetRanges',
            'recentJobsCount',
            'totalJobs',
            'myQuoteStats',
            'myQuoteStatusChart',
            'topJobCategories'
        ));
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
        $user = $request->user();
        $subscription = $user->subscription('default');
        $portalUrl = $user->stripe_id ? $user->billingPortalUrl(route('supplier-panel.subscription.index')) : null;

        $currentPlan = null;
        $stripePriceInfo = null;

            if ($subscription && $subscription->stripe_price) {
                $currentPlan = $plans->firstWhere('stripe_price_id', $subscription->stripe_price);

                if (!$currentPlan) {
                    try {
                        $stripe = new StripeClient(config('cashier.secret'));
                        $price = $stripe->prices->retrieve($subscription->stripe_price);
                        $stripePriceInfo = [
                            'amount' => $price->unit_amount,
                            'currency' => $price->currency,
                            'interval' => $price->recurring->interval,
                            'interval_count' => $price->recurring->interval_count,
                        ];

                        $currentPlan = $plans->firstWhere('stripe_product_id', $price->product);
                    } catch (\Exception $e) {
                        Log::warning('Failed to retrieve Stripe price', ['error' => $e->getMessage()]);
                    }
                }
            }

        return view('supplier-panel.subscription.index', compact('plans', 'subscription', 'portalUrl', 'currentPlan', 'stripePriceInfo'));
    }

    public function subscriptionPreview(Request $request, Plan $plan): View|\Illuminate\Http\RedirectResponse
    {
        $user = $request->user();
        $subscription = $user->subscription('default');
        $currentPlan = $user->currentPlan();

        if ($plan->is_free) {
            return view('supplier-panel.subscription.preview', compact('plan', 'currentPlan', 'subscription'));
        }

        if (! $plan->stripe_price_id) {
            return redirect()->route('supplier-panel.subscription.index')
                ->with('error', 'This plan is not yet configured for billing.');
        }

        $prorationPreview = null;
        if ($subscription && $subscription->valid()) {
            try {
                $stripe = new StripeClient(config('cashier.secret'));
                $upcomingInvoice = $stripe->invoices->createPreview([
                    'customer' => $user->stripe_id,
                    'subscription' => $subscription->stripe_id,
                    'subscription_items' => [
                        [
                            'id' => $subscription->items->first()->stripe_id,
                            'price' => $plan->stripe_price_id,
                        ],
                    ],
                ]);

                $lines = [];
                foreach ($upcomingInvoice->lines->data as $line) {
                    $lines[] = [
                        'description' => $line->description ?? 'Line item',
                        'amount' => $line->amount,
                    ];
                }

                $prorationPreview = [
                    'total' => $upcomingInvoice->total,
                    'subtotal' => $upcomingInvoice->subtotal,
                    'currency' => $upcomingInvoice->currency,
                    'lines' => $lines,
                ];
            } catch (\Exception $e) {
                Log::warning('Failed to fetch proration preview', ['error' => $e->getMessage()]);
            }
        }

        return view('supplier-panel.subscription.preview', compact('plan', 'currentPlan', 'subscription', 'prorationPreview'));
    }

    public function subscriptionCheckout(Request $request, Plan $plan)
    {
        $user = $request->user();

        if ($plan->is_free) {
            if ($user->subscribed('default')) {
                $user->subscription('default')->cancelNow();
            }

            return redirect()->route('supplier-panel.subscription.index')
                ->with('success', 'You are now on the Free plan.');
        }

        if (! $plan->stripe_price_id) {
            return redirect()->route('supplier-panel.subscription.index')
                ->with('error', 'This plan is not yet configured for billing.');
        }

        if (! $user->subscribed('default')) {
            $checkout = $user->newSubscription('default', $plan->stripe_price_id)
                ->checkout([
                    'success_url' => route('supplier-panel.subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('supplier-panel.subscription.index'),
                ]);

            return redirect($checkout->url);
        }

        $subscription = $user->subscription('default');

        if ($subscription->stripe_price === $plan->stripe_price_id) {
            return redirect()->route('supplier-panel.subscription.index')
                ->with('info', 'You are already on this plan.');
        }

        try {
            $subscription->swapAndInvoice($plan->stripe_price_id);

            $invoice = $subscription->latestInvoice();
            $charged = $invoice && $invoice->rawTotal() > 0
                ? ' Your card was charged ' . $invoice->total() . '.'
                : '';
        } catch (IncompletePayment $e) {
            return redirect()->route('cashier.payment', [
                'id' => $e->payment->id,
                'redirect' => route('supplier-panel.subscription.index'),
            ]);
        } catch (\Exception $e) {
            Log::error('Subscription swap failed', ['error' => $e->getMessage()]);
            return redirect()->route('supplier-panel.subscription.index')
                ->with('error', 'Failed to change subscription: ' . $e->getMessage());
        }

        return redirect()->route('supplier-panel.subscription.index')
            ->with('success', 'Subscription changed to ' . $plan->name . ' successfully.' . $charged);
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
