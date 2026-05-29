<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $plans = Plan::active()->ordered()->get();
        $subscription = request()->user()->subscription('default');

        return view('subscription.index', compact('plans', 'subscription'));
    }

    public function checkout(Request $request, Plan $plan)
    {
        $user = $request->user();
        $currentPlan = $user->currentPlan();

        if ($plan->is_free) {
            if ($user->subscribed('default')) {
                $user->subscription('default')->cancelNow();
            }
            return redirect()->route('subscription.index')
                ->with('success', 'You are now on the Basic (Free) plan.');
        }

        if (!$plan->stripe_price_id) {
            return redirect()->route('subscription.index')
                ->with('error', 'This plan is not yet configured for billing. Please run "php artisan stripe:sync-plans" to set up Stripe products.');
        }

        if ($currentPlan?->slug === 'bronze' && $plan->stripe_price_id !== $currentPlan->stripe_price_id) {
            if ($user->subscribed('default')) {
                $user->subscription('default')->cancelNow();
            }

            $checkout = $user->newSubscription('default', $plan->stripe_price_id)
                ->checkout([
                    'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('subscription.index'),
                ]);

            return redirect($checkout->url)
                ->with('info', 'Your Bronze subscription was ended first, and a new checkout was created for the selected plan.');
        }

        if ($user->subscribed('default')) {
            return $this->swap($request, $plan);
        }

        $checkout = $user->newSubscription('default', $plan->stripe_price_id)
            ->checkout([
                'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('subscription.index'),
            ]);

        return redirect($checkout->url);
    }

    protected function swap(Request $request, Plan $plan)
    {
        $user = $request->user();
        $subscription = $user->subscription('default');

        if (!$plan->stripe_price_id) {
            return redirect()->route('subscription.index')
                ->with('error', 'This plan is not yet configured for billing.');
        }

        if ($subscription->stripe_price === $plan->stripe_price_id) {
            return redirect()->route('subscription.index')
                ->with('info', 'You are already on this plan.');
        }

        $subscription->swap($plan->stripe_price_id);

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
