<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::create([
            'name' => 'Basic',
            'slug' => 'basic',
            'description' => 'Perfect for new suppliers exploring the platform. Get started with a basic profile and limited quotes.',
            'features' => [
                'Basic Profile (No reviews, rating or Social Links)',
                'View Job Details',
                '1 Quote Per Month',
                'Maximum 6 Quotes per year',
                'Basic Quote Template',
            ],
            'price' => 0,
            'duration_months' => 0,
            'is_free' => true,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        Plan::create([
            'name' => 'Bronze',
            'slug' => 'bronze',
            'description' => 'For suppliers needing a monthly boost. Flexible 1-month commitment.',
            'features' => [
                'Enhanced Supplier Profile',
                'Instant Job Alerts',
                'Priority Customer Support',
                'SMS Notifications (Optional)',
                'Recommended Supplier Badge (After 10 wins)',
                'Professional Quote Template',
                'Unlimited Quote Submissions',
                'Advanced Analytics Dashboard',
                'Early Access to jobs',
            ],
            'price' => 3200,
            'duration_months' => 1,
            'is_free' => false,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Plan::create([
            'name' => 'Silver',
            'slug' => 'silver',
            'description' => 'Great value for growing suppliers. Save with a 3-month plan.',
            'features' => [
                'Enhanced Supplier Profile',
                'Instant Job Alerts',
                'Priority Customer Support',
                'SMS Notifications (Optional)',
                'Recommended Supplier Badge (After 10 wins)',
                'Professional Quote Template',
                'Unlimited Quote Submissions',
                'Advanced Analytics Dashboard',
                'Early Access to jobs',
            ],
            'price' => 9000,
            'duration_months' => 3,
            'is_free' => false,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        Plan::create([
            'name' => 'Gold',
            'slug' => 'gold',
            'description' => 'Our most popular plan. Best value for established suppliers with a 6-month commitment.',
            'features' => [
                'Enhanced Supplier Profile',
                'Instant Job Alerts',
                'Priority Customer Support',
                'SMS Notifications (Optional)',
                'Recommended Supplier Badge (After 10 wins)',
                'Professional Quote Template',
                'Unlimited Quote Submissions',
                'Advanced Analytics Dashboard',
                'Early Access to jobs',
            ],
            'price' => 16200,
            'duration_months' => 6,
            'is_free' => false,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        Plan::create([
            'name' => 'Platinum',
            'slug' => 'platinum',
            'description' => 'Ultimate value for established businesses. Full year access at the best price.',
            'features' => [
                'Enhanced Supplier Profile',
                'Instant Job Alerts',
                'Priority Customer Support',
                'SMS Notifications (Optional)',
                'Recommended Supplier Badge (After 10 wins)',
                'Professional Quote Template',
                'Unlimited Quote Submissions',
                'Advanced Analytics Dashboard',
                'Early Access to jobs',
            ],
            'price' => 30000,
            'duration_months' => 12,
            'is_free' => false,
            'is_active' => true,
            'sort_order' => 4,
        ]);
    }
}
