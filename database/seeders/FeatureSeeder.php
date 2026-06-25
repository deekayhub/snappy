<?php

namespace Database\Seeders;

use App\Models\Feature;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            ['slug' => 'basic_profile',           'name' => 'Basic Profile (No reviews, rating or Social Links)',        'sort_order' => 0],
            ['slug' => 'view_job_details',         'name' => 'View Job Details',                                          'sort_order' => 1],
            ['slug' => 'limited_quotes_month',     'name' => '1 Quote Per Month',                                         'sort_order' => 2],
            ['slug' => 'limited_quotes_year',      'name' => 'Maximum 6 Quotes per year',                                 'sort_order' => 3],
            ['slug' => 'basic_quote_template',     'name' => 'Basic Quote Template',                                      'sort_order' => 4],
            ['slug' => 'enhanced_profile',         'name' => 'Enhanced Supplier Profile',                                 'sort_order' => 5],
            ['slug' => 'instant_job_alerts',       'name' => 'Instant Job Alerts',                                        'sort_order' => 6],
            ['slug' => 'priority_support',         'name' => 'Priority Customer Support',                                 'sort_order' => 7],
            ['slug' => 'sms_notifications',        'name' => 'SMS Notifications (Optional)',                              'sort_order' => 8],
            ['slug' => 'recommended_badge',        'name' => 'Recommended Supplier Badge (After 10 wins)',                'sort_order' => 9],
            ['slug' => 'professional_quote',       'name' => 'Professional Quote Template',                               'sort_order' => 10],
            ['slug' => 'unlimited_quotes',         'name' => 'Unlimited Quote Submissions',                               'sort_order' => 11],
            ['slug' => 'analytics_dashboard',      'name' => 'Advanced Analytics Dashboard',                              'sort_order' => 12],
            ['slug' => 'early_access_jobs',        'name' => 'Early Access to jobs',                                      'sort_order' => 13],
        ];

        foreach ($features as $data) {
            Feature::create($data);
        }
    }
}
