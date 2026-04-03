<?php

namespace Database\Seeders;

use App\Models\OrganisationCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $supplierCategory = OrganisationCategory::where('type', 'supplier')->first();
        $customerCategory = OrganisationCategory::where('type', 'customer')->first();

        DB::transaction(function () use ($supplierCategory, $customerCategory) {
            $admin = User::updateOrCreate(
                ['email' => 'admin@snappyquote.test'],
                [
                    'name' => 'Admin User',
                    'phone' => '9000000001',
                    'is_active' => true,
                    'email_verified_at' => now(),
                    'password' => 'password',
                ]
            );
            $admin->syncRoles(['admin']);

            $supplier = User::updateOrCreate(
                ['email' => 'supplier@snappyquote.test'],
                [
                    'name' => 'Supplier User',
                    'phone' => '9000000002',
                    'is_active' => true,
                    'email_verified_at' => now(),
                    'password' => 'password',
                ]
            );
            $supplier->syncRoles(['supplier']);
            $supplier->supplierProfile()->updateOrCreate(
                ['user_id' => $supplier->id],
                [
                    'company_name' => 'Snappy Supplies',
                    'address' => '12 Market Street, Mumbai',
                    'website' => 'https://supplier.snappyquote.test',
                    'review_link' => 'https://reviews.snappyquote.test/supplier',
                    'social_link' => 'https://facebook.com/snappysupplies',
                ]
            );
            if ($supplierCategory) {
                $supplier->organisationCategories()->sync([$supplierCategory->id]);
            }

            $customer = User::updateOrCreate(
                ['email' => 'customer@snappyquote.test'],
                [
                    'name' => 'Customer User',
                    'phone' => '9000000003',
                    'is_active' => true,
                    'email_verified_at' => now(),
                    'password' => 'password',
                ]
            );
            $customer->syncRoles(['customer']);
            $customer->customerProfile()->updateOrCreate(
                ['user_id' => $customer->id],
                [
                    'county' => 'Maharashtra',
                    'school_name' => 'Snappy Public School',
                ]
            );
            if ($customerCategory) {
                $customer->organisationCategories()->sync([$customerCategory->id]);
            }
        });
    }
}
