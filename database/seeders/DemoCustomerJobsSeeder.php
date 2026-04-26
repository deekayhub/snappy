<?php

namespace Database\Seeders;

use App\Models\CustomerJob;
use App\Models\OrganisationCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoCustomerJobsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customerIds = User::query()
            ->role('customer')
            ->pluck('id');

        if ($customerIds->isEmpty()) {
            return;
        }

        $supplierCategoryNames = OrganisationCategory::query()
            ->where('type', 'supplier')
            ->pluck('name');

        CustomerJob::factory()
            ->count(10)
            ->make()
            ->each(function (CustomerJob $job) use ($customerIds, $supplierCategoryNames) {
                $job->user_id = $customerIds->random();

                if ($supplierCategoryNames->isNotEmpty()) {
                    $job->category = $supplierCategoryNames->random();
                }

                $job->save();
            });
    }
}
