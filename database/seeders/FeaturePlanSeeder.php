<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class FeaturePlanSeeder extends Seeder
{
    public function run(): void
    {
        $free = Plan::where('slug', 'free')->first();
        $threeMonths = Plan::where('slug', '3_months')->first() ?? Plan::where('slug', '1_months')->first();
        $sixMonths = Plan::where('slug', '6_months')->first();

        $features = Feature::pluck('id', 'slug');

        // Free plan features
        if ($free) {
            $free->featureModels()->sync([
                $features['basic_profile'],
                $features['view_job_details'],
                $features['limited_quotes_month'],
                $features['limited_quotes_year'],
                $features['basic_quote_template'],
            ]);
        }

        // 3 Months plan features
        if ($threeMonths) {
            $threeMonths->featureModels()->sync([
                $features['enhanced_profile'],
                $features['instant_job_alerts'],
                $features['priority_support'],
                $features['sms_notifications'],
                $features['recommended_badge'],
                $features['professional_quote'],
                $features['unlimited_quotes'],
                $features['analytics_dashboard'],
                $features['early_access_jobs'],
            ]);
        }

        // 6 Month plan features
        if ($sixMonths) {
            $sixMonths->featureModels()->sync([
                $features['enhanced_profile'],
                $features['instant_job_alerts'],
                $features['priority_support'],
                $features['sms_notifications'],
                $features['recommended_badge'],
                $features['professional_quote'],
                $features['unlimited_quotes'],
                $features['analytics_dashboard'],
                $features['early_access_jobs'],
            ]);
        }
    }
}
