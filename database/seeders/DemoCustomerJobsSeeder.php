<?php

namespace Database\Seeders;

use App\Models\CustomerJob;
use App\Models\JobItem;
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

                $itemCount = random_int(1, 3);

                for ($i = 0; $i < $itemCount; $i++) {
                    JobItem::create([
                        'customer_job_id' => $job->id,
                        'item_name' => fake()->randomElement([
                            'Trophies',
                            'Medals',
                            'Glass awards',
                            'Sports bottles',
                            'Team shirts',
                            'Presentation boxes',
                        ]),
                        'quantity' => fake()->numberBetween(10, 250),
                        'sku_codes' => fake()->boolean(50) ? [fake()->bothify('SKU-####')] : [],
                        'image_paths' => [],
                        'item_link' => fake()->boolean(30) ? fake()->url() : null,
                        'allow_similar_quote' => fake()->boolean(50),
                    ]);
                }
            });
    }
}
