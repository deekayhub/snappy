<?php

namespace Tests\Feature;

use App\Models\OrganisationCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SupplierPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_supplier_dashboard_and_profile_pages_are_accessible(): void
    {
        Role::create(['name' => 'supplier', 'guard_name' => 'web']);
        $category = OrganisationCategory::create(['name' => 'SPORTSWEAR', 'type' => 'supplier']);
        $user = User::factory()->create();
        $user->assignRole('supplier');
        $user->supplierProfile()->create([
            'company_name' => 'Snappy Supplier',
            'address' => 'Test address',
        ]);
        $user->organisationCategories()->attach($category);

        $this->actingAs($user)->get(route('supplier-panel.dashboard'))->assertOk();
        $this->actingAs($user)->get(route('supplier-panel.profile'))->assertOk();
    }
}
