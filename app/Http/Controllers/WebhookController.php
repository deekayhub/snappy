<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;

class WebhookController extends CashierWebhookController
{
    protected function handleCheckoutSessionCompleted(array $payload)
    {
        $session = $payload['data']['object'];

        Log::info('Stripe Checkout Session completed', [
            'session_id' => $session['id'],
            'customer' => $session['customer'] ?? null,
            'mode' => $session['mode'] ?? null,
            'payment_status' => $session['payment_status'] ?? null,
            'subscription' => $session['subscription'] ?? null,
        ]);

        return $this->successMethod();
    }

    protected function handleInvoicePaymentSucceeded(array $payload)
    {
        $invoice = $payload['data']['object'];
        $customerId = $invoice['customer'] ?? null;
        $subscriptionId = $invoice['subscription'] ?? null;

        Log::info('Stripe invoice payment succeeded', [
            'invoice_id' => $invoice['id'],
            'customer' => $customerId,
            'subscription' => $subscriptionId,
            'amount_paid' => $invoice['amount_paid'] ?? null,
            'currency' => $invoice['currency'] ?? null,
        ]);

        if ($subscriptionId && $customerId) {
            $user = Cashier::findBillable($customerId);

            if ($user) {
                $subscription = $user->subscriptions()
                    ->where('stripe_id', $subscriptionId)
                    ->first();

                if ($subscription && $subscription->stripe_status === 'incomplete') {
                    $subscription->update(['stripe_status' => 'active']);
                    Log::info('Subscription activated after successful payment', [
                        'subscription_id' => $subscriptionId,
                        'user_id' => $user->id,
                    ]);
                }
            }
        }

        return $this->successMethod();
    }

    protected function handleCustomerSubscriptionUpdated(array $payload)
    {
        $subscriptionData = $payload['data']['object'];
        $subscriptionId = $subscriptionData['id'];
        $customerId = $subscriptionData['customer'] ?? null;
        $status = $subscriptionData['status'] ?? null;
        $priceId = $subscriptionData['items']['data'][0]['price']['id'] ?? null;

        Log::info('Stripe subscription updated', [
            'subscription_id' => $subscriptionId,
            'customer' => $customerId,
            'status' => $status,
            'price' => $priceId,
        ]);

        if ($customerId) {
            $user = Cashier::findBillable($customerId);

            if ($user) {
                $subscription = $user->subscriptions()
                    ->where('stripe_id', $subscriptionId)
                    ->first();

                if ($subscription) {
                    $updateData = ['stripe_status' => $status];

                    if ($priceId) {
                        $updateData['stripe_price'] = $priceId;
                    }

                    if (isset($subscriptionData['canceled_at'])) {
                        $updateData['ends_at'] = $subscriptionData['canceled_at']
                            ? now()->createFromTimestamp($subscriptionData['canceled_at'])
                            : null;
                    }

                    $subscription->update($updateData);

                    Log::info('Local subscription synced after Stripe update', [
                        'subscription_id' => $subscriptionId,
                        'user_id' => $user->id,
                    ]);
                }
            }
        }

        return $this->successMethod();
    }

    protected function handleInvoicePaymentFailed(array $payload)
    {
        $invoice = $payload['data']['object'];
        $customerId = $invoice['customer'] ?? null;
        $subscriptionId = $invoice['subscription'] ?? null;

        Log::warning('Stripe invoice payment failed', [
            'invoice_id' => $invoice['id'],
            'customer' => $customerId,
            'subscription' => $subscriptionId,
            'amount_due' => $invoice['amount_due'] ?? null,
            'attempt_count' => $invoice['attempt_count'] ?? null,
            'next_payment_attempt' => $invoice['next_payment_attempt'] ?? null,
        ]);

        if ($subscriptionId && $customerId) {
            $user = Cashier::findBillable($customerId);

            if ($user) {
                $subscription = $user->subscriptions()
                    ->where('stripe_id', $subscriptionId)
                    ->first();

                if ($subscription) {
                    $subscription->update(['stripe_status' => 'past_due']);
                    Log::info('Subscription marked as past_due after failed payment', [
                        'subscription_id' => $subscriptionId,
                        'user_id' => $user->id,
                    ]);
                }
            }
        }

        return $this->successMethod();
    }
}
