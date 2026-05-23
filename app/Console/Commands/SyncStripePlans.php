<?php

namespace App\Console\Commands;

use App\Models\Plan;
use Illuminate\Console\Command;
use Stripe\StripeClient;

class SyncStripePlans extends Command
{
    protected $signature = 'stripe:sync-plans';
    protected $description = 'Create or update Stripe products and prices for all active plans';

    public function handle()
    {
        $stripe = new StripeClient(config('cashier.secret'));

        $plans = Plan::active()->where('is_free', false)->get();

        foreach ($plans as $plan) {
            $this->info("Processing: {$plan->name}");

            $productData = [
                'name' => $plan->name . ' Plan',
                'description' => $plan->description,
            ];

            $product = $stripe->products->create($productData);

            $price = $stripe->prices->create([
                'product' => $product->id,
                'unit_amount' => $plan->price,
                'currency' => 'gbp',
                'recurring' => [
                    'interval' => 'month',
                    'interval_count' => $plan->duration_months,
                ],
            ]);

            $plan->update(['stripe_price_id' => $price->id]);

            $this->info("  Created: Product={$product->id}, Price={$price->id}");
        }

        $this->info('All plans synced with Stripe successfully!');
    }
}
