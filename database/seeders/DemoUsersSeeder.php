<?php

namespace Database\Seeders;

use App\Models\OrganisationCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $supplierCategoryIds = OrganisationCategory::query()
            ->where('type', 'supplier')
            ->pluck('id');

        $customerCategoryIds = OrganisationCategory::query()
            ->where('type', 'customer')
            ->pluck('id');

        DB::transaction(function () use ($supplierCategoryIds, $customerCategoryIds) {
            $suppliers = User::factory()
                ->count(10)
                ->create([
                    'is_active' => true,
                    'phone' => fake()->numerify('9#########'),
                ]);

            foreach ($suppliers as $supplier) {
                $supplier->syncRoles(['supplier']);
                $supplier->supplierProfile()->create([
                    'company_name' => fake()->company().' Supplies',
                    'address' => fake()->address(),
                    'website' => fake()->url(),
                    'review_link' => fake()->url(),
                    'social_link' => fake()->url(),
                ]);

                if ($supplierCategoryIds->isNotEmpty()) {
                    $supplier->organisationCategories()->sync(
                        $supplierCategoryIds->random(rand(1, min(3, $supplierCategoryIds->count())))->values()->all()
                    );
                }
            }

            $customers = User::factory()
                ->count(10)
                ->create([
                    'is_active' => true,
                    'phone' => fake()->numerify('8#########'),
                ]);

            foreach ($customers as $customer) {
                $customer->syncRoles(['customer']);
                $customer->customerProfile()->create([
                    'county' => fake()->state(),
                    'school_name' => fake()->company().' School',
                ]);

                if ($customerCategoryIds->isNotEmpty()) {
                    $customer->organisationCategories()->sync(
                        $customerCategoryIds->random(rand(1, min(2, $customerCategoryIds->count())))->values()->all()
                    );
                }
            }
        });
    }
}
