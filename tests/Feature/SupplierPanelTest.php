<?php

namespace Tests\Feature;

use App\Models\Plan;
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
        [$user] = $this->createSupplier();

        $this->actingAs($user)->get(route('supplier-panel.dashboard'))->assertOk();
        $this->actingAs($user)->get(route('supplier-panel.profile'))->assertOk();
    }

    public function test_supplier_sidebar_links_to_the_supplier_subscription_page(): void
    {
        [$user] = $this->createSupplier();
        $this->seedSupplierPlans();

        $this->actingAs($user)
            ->get(route('supplier-panel.dashboard'))
            ->assertOk()
            ->assertSee(route('supplier-panel.subscription.index'));
    }

    public function test_supplier_subscription_and_invoices_pages_are_accessible_inside_the_panel(): void
    {
        [$user] = $this->createSupplier();
        $this->seedSupplierPlans();

        $this->actingAs($user)
            ->get(route('supplier-panel.subscription.index'))
            ->assertOk()
            ->assertSee(route('supplier-panel.subscription.invoices'));

        $this->actingAs($user)
            ->get(route('supplier-panel.subscription.invoices'))
            ->assertOk()
            ->assertSee(route('supplier-panel.subscription.index'));
    }

    private function createSupplier(): array
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

        return [$user, $category];
    }

    private function seedSupplierPlans(): void
    {
        Plan::create([
            'name' => 'Basic',
            'slug' => 'basic',
            'description' => 'A free starter plan.',
            'features' => ['Browse jobs'],
            'price' => 0,
            'duration_months' => 1,
            'is_active' => true,
            'is_free' => true,
            'sort_order' => 1,
        ]);

        Plan::create([
            'name' => 'Bronze',
            'slug' => 'bronze',
            'description' => 'A paid supplier plan.',
            'features' => ['Submit more quotes'],
            'price' => 2500,
            'stripe_price_id' => 'price_bronze_test',
            'duration_months' => 1,
            'is_active' => true,
            'is_free' => false,
            'sort_order' => 2,
        ]);
    }
}
