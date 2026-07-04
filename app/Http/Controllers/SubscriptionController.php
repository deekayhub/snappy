<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class SubscriptionController extends Controller
{
    public function index()
    {
        $plans = Plan::active()->ordered()->get();
        $user = request()->user();
        $subscription = $user->subscription('default');
        $portalUrl = $user->stripe_id ? $user->billingPortalUrl(route('subscription.index')) : null;

        return view('subscription.index', compact('plans', 'subscription', 'portalUrl'));
    }

    public function preview(Request $request, Plan $plan)
    {
        $user = $request->user();
        $subscription = $user->subscription('default');
        $currentPlan = $user->currentPlan();

        if ($plan->is_free) {
            return view('subscription.preview', compact('plan', 'currentPlan', 'subscription'));
        }

        if (!$plan->stripe_price_id) {
            return redirect()->route('subscription.index')
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

        return view('subscription.preview', compact('plan', 'currentPlan', 'subscription', 'prorationPreview'));
    }

    public function checkout(Request $request, Plan $plan)
    {
        $user = $request->user();

        if ($plan->is_free) {
            if ($user->subscribed('default')) {
                $user->subscription('default')->cancelNow();
            }
            return redirect()->route('subscription.index')
                ->with('success', 'You are now on the Free plan.');
        }

        if (!$plan->stripe_price_id) {
            return redirect()->route('subscription.index')
                ->with('error', 'This plan is not yet configured for billing.');
        }

        if (!$user->subscribed('default')) {
            $checkout = $user->newSubscription('default', $plan->stripe_price_id)
                ->checkout([
                    'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('subscription.index'),
                ]);

            return redirect($checkout->url);
        }

        $subscription = $user->subscription('default');

        if ($subscription->stripe_price === $plan->stripe_price_id) {
            return redirect()->route('subscription.index')
                ->with('info', 'You are already on this plan.');
        }

        try {
            $subscription->swap($plan->stripe_price_id);
        } catch (\Exception $e) {
            Log::error('Subscription swap failed', ['error' => $e->getMessage()]);
            return redirect()->route('subscription.index')
                ->with('error', 'Failed to change subscription: ' . $e->getMessage());
        }

        $subscription->refresh();

        if ($subscription->stripe_status === 'incomplete' || $subscription->stripe_status === 'past_due') {
            return redirect()->route('subscription.index')
                ->with('warning', 'Subscription changed to ' . $plan->name . ', but your payment method needs attention.')
                ->with('portal_url', $user->billingPortalUrl(route('subscription.index')));
        }

        return redirect()->route('subscription.index')
            ->with('success', 'Subscription changed to ' . $plan->name . ' successfully.');
    }

    public function cancel(Request $request)
    {
        $user = $request->user();

        if ($user->subscribed('default')) {
            $user->subscription('default')->cancel();
        }

        return redirect()->route('subscription.index')
            ->with('success', 'Subscription cancelled successfully. Access continues until the end of the billing period.');
    }

    public function resume(Request $request)
    {
        $user = $request->user();

        if ($user->subscribed('default') && $user->subscription('default')->onGracePeriod()) {
            $user->subscription('default')->resume();
        }

        return redirect()->route('subscription.index')
            ->with('success', 'Subscription resumed successfully.');
    }

    public function success(Request $request)
    {
        return redirect()->route('subscription.index')
            ->with('success', 'We received your checkout response from Stripe. Your subscription will appear active once Stripe confirms it through the webhook.');
    }

    public function invoices(Request $request)
    {
        $invoices = $request->user()->invoices();

        return view('subscription.invoices', compact('invoices'));
    }

    public function downloadInvoice(Request $request, string $invoiceId)
    {
        return $request->user()->downloadInvoice($invoiceId, [
            'vendor' => config('app.name'),
            'product' => 'Subscription',
        ]);
    }
}
