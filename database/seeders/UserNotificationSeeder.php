<?php

namespace Database\Seeders;

use App\Models\UserNotification;
use Illuminate\Database\Seeder;

class UserNotificationSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = \App\Models\User::role('supplier')->where('is_active', true)->get();

        foreach ($suppliers as $supplier) {
            if ($supplier->hasFeature('instant_job_alerts')) {
                UserNotification::create([
                    'user_id' => $supplier->id,
                    'type' => 'welcome',
                    'message' => 'Welcome to SnappyQuotes! Instant job alerts are active on your plan.',
                    'action_url' => route('supplier-panel.jobs'),
                    'is_read' => false,
                ]);
            }
        }
    }
}
