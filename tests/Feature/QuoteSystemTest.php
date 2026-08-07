<?php

namespace Tests\Feature;

use App\Models\CustomerJob;
use App\Models\OrganisationCategory;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class QuoteSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        Role::create(['name' => 'supplier', 'guard_name' => 'web']);
        Role::create(['name' => 'customer', 'guard_name' => 'web']);
    }

    public function test_supplier_can_submit_a_quote_for_a_job(): void
    {
        $customerCategory = OrganisationCategory::create(['name' => 'TEAM', 'type' => 'customer']);
        $supplierCategory = OrganisationCategory::create(['name' => 'SPORTSWEAR', 'type' => 'supplier']);

        $customer = User::factory()->create();
        $customer->assignRole('customer');
        $customer->organisationCategories()->attach($customerCategory);

        $supplier = User::factory()->create();
        $supplier->assignRole('supplier');
        $supplier->supplierProfile()->create([
            'company_name' => 'Snappy Supplier',
            'address' => 'Test address',
        ]);
        $supplier->organisationCategories()->attach($supplierCategory);

        $job = $customer->customerJobs()->create([
            'title' => 'New sports kit',
            'category' => 'Sportswear',
            'description' => 'We need a full sportswear quote for our upcoming season with enough detail for suppliers to price well.',
            'status' => 'open',
        ]);

        $response = $this->actingAs($supplier)->post(route('supplier-panel.quotes.store', $job), [
            'delivery_cost' => 25,
            'discount_offered' => 10,
            'price_for_job' => 200,
            'notes' => 'Delivery within 5 working days.',
        ]);

        $response->assertRedirect(route('supplier-panel.jobs'));

        $this->assertDatabaseHas('quotes', [
            'customer_job_id' => $job->id,
            'supplier_user_id' => $supplier->id,
            'status' => 'submitted',
            'total_price' => 215.00,
        ]);
    }

    public function test_customer_can_view_and_update_quote_status(): void
    {
        $customerCategory = OrganisationCategory::create(['name' => 'TEAM', 'type' => 'customer']);
        $supplierCategory = OrganisationCategory::create(['name' => 'SPORTSWEAR', 'type' => 'supplier']);

        $customer = User::factory()->create();
        $customer->assignRole('customer');
        $customer->organisationCategories()->attach($customerCategory);

        $supplier = User::factory()->create();
        $supplier->assignRole('supplier');
        $supplier->supplierProfile()->create([
            'company_name' => 'Snappy Supplier',
            'address' => 'Test address',
        ]);
        $supplier->organisationCategories()->attach($supplierCategory);

        $job = $customer->customerJobs()->create([
            'title' => 'Club trophies',
            'category' => 'Trophies',
            'description' => 'We need end of season trophies for our club awards night with engraving and delivery included.',
            'status' => 'open',
        ]);

        $quote = Quote::create([
            'customer_job_id' => $job->id,
            'supplier_user_id' => $supplier->id,
            'delivery_cost' => 20,
            'discount_offered' => 5,
            'price_for_job' => 180,
            'total_price' => 195,
            'notes' => 'Includes engraving.',
            'status' => 'submitted',
            'sent_at' => now(),
        ]);

        $this->actingAs($customer)
            ->get(route('customer.quotes.index'))
            ->assertOk()
            ->assertSee('Club trophies')
            ->assertSee('Snappy Supplier');

        $this->actingAs($customer)
            ->patch(route('customer.quotes.status', $quote), [
                'status' => 'accepted',
            ])
            ->assertRedirect();

        $this->assertSame('accepted', $quote->fresh()->status);
        $this->assertSame('closed', $job->fresh()->status);
    }

    public function test_admin_can_view_real_quotes_in_admin_panel(): void
    {
        $customerCategory = OrganisationCategory::create(['name' => 'TEAM', 'type' => 'customer']);
        $supplierCategory = OrganisationCategory::create(['name' => 'SPORTSWEAR', 'type' => 'supplier']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $customer = User::factory()->create();
        $customer->assignRole('customer');
        $customer->organisationCategories()->attach($customerCategory);

        $supplier = User::factory()->create();
        $supplier->assignRole('supplier');
        $supplier->supplierProfile()->create([
            'company_name' => 'Snappy Supplier',
            'address' => 'Test address',
        ]);
        $supplier->organisationCategories()->attach($supplierCategory);

        $job = $customer->customerJobs()->create([
            'title' => 'Team signage',
            'category' => 'Signage',
            'description' => 'We need sponsor and event signage for our team facilities and weekend matchday setup.',
            'status' => 'open',
        ]);

        Quote::create([
            'customer_job_id' => $job->id,
            'supplier_user_id' => $supplier->id,
            'delivery_cost' => 15,
            'discount_offered' => 0,
            'price_for_job' => 250,
            'total_price' => 265,
            'notes' => 'Printed and mounted signage.',
            'status' => 'submitted',
            'sent_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.quotes'))
            ->assertOk()
            ->assertSee('Team signage')
            ->assertSee('Snappy Supplier');
    }
}
