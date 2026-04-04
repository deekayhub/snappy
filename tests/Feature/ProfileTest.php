<?php

namespace Tests\Feature;

use App\Models\OrganisationCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        Role::create(['name' => 'customer', 'guard_name' => 'web']);
        $category = OrganisationCategory::create(['name' => 'TEAM', 'type' => 'customer']);
        $user = User::factory()->create();
        $user->assignRole('customer');
        $user->organisationCategories()->attach($category);

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_customer_profile_information_can_be_updated(): void
    {
        Role::create(['name' => 'customer', 'guard_name' => 'web']);
        $category = OrganisationCategory::create(['name' => 'TEAM', 'type' => 'customer']);
        $user = User::factory()->create();
        $user->assignRole('customer');
        $user->organisationCategories()->attach($category);

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->patch('/profile', [
                'name' => 'Test User',
                'phone' => '1234567890',
                'customer_organisation' => [$category->id],
                'county' => 'Leeds',
                'school_name' => 'North Academy',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('1234567890', $user->phone);
        $this->assertSame('Leeds', $user->customerProfile?->county);
        $this->assertSame('North Academy', $user->customerProfile?->school_name);
    }

    public function test_supplier_profile_route_redirects_to_supplier_profile_panel(): void
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

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertRedirect(route('supplier-panel.profile'));
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
