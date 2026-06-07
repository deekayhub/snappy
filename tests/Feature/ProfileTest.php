<?php

namespace Tests\Feature;

use App\Models\OrganisationCategory;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        [$user] = $this->createCustomerUser();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk()->assertSee('Profile picture');
    }

    public function test_customer_profile_information_can_be_updated(): void
    {
        [$user, $category] = $this->createCustomerUser();
        $picture = UploadedFile::fake()->image('customer-profile.jpg', 800, 800)->size(5000);

        $this->actingAs($user)
            ->get(route('customer-panel.profile'))
            ->assertOk()
            ->assertSee('Profile picture');

        $response = $this
            ->actingAs($user)
            ->from(route('profile.edit'))
            ->patch('/profile', [
                'name' => 'Test User',
                'phone' => '1234567890',
                'customer_organisation' => [$category->id],
                'county' => 'Leeds',
                'school_name' => 'North Academy',
                'profile_picture' => $picture,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('profile.edit'));

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('1234567890', $user->phone);
        $this->assertSame('Leeds', $user->customerProfile?->county);
        $this->assertSame('North Academy', $user->customerProfile?->school_name);
        $this->assertNotNull($user->profile_picture);
        $this->assertStringStartsWith('profile-pictures/', $user->profile_picture);
        $this->assertFileExists(public_path($user->profile_picture));
    }

    public function test_supplier_profile_route_redirects_to_supplier_profile_panel(): void
    {
        [$user] = $this->createSupplierUser();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertRedirect(route('supplier-panel.profile'));
    }

    public function test_supplier_profile_picture_can_be_updated_from_the_supplier_panel(): void
    {
        [$user, $category] = $this->createSupplierUser();
        $picture = UploadedFile::fake()->image('supplier-profile.jpg', 900, 900)->size(4500);

        $this->actingAs($user)
            ->get(route('supplier-panel.profile'))
            ->assertOk()
            ->assertSee('Profile picture');

        $response = $this
            ->actingAs($user)
            ->from(route('supplier-panel.profile'))
            ->post('/profile', [
                'name' => 'Supplier Name',
                'phone' => '5555555',
                'company_name' => 'Snappy Supplier',
                'address' => 'Test address',
                'supplier_organisation' => [$category->id],
                'website' => 'https://example.com',
                'review_link' => 'https://reviews.example.com',
                'social_link' => 'https://social.example.com',
                'profile_picture' => $picture,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('supplier-panel.profile'));

        $user->refresh();

        $this->assertSame('Supplier Name', $user->name);
        $this->assertNotNull($user->profile_picture);
        $this->assertFileExists(public_path($user->profile_picture));
    }

    public function test_admin_profile_picture_can_be_updated_from_the_admin_panel(): void
    {
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole('admin');
        $picture = UploadedFile::fake()->image('admin-profile.jpg', 720, 720)->size(3000);

        $this->actingAs($user)
            ->get(route('admin.profile'))
            ->assertOk()
            ->assertSee('Profile picture');

        $response = $this
            ->actingAs($user)
            ->from(route('admin.profile'))
            ->post('/profile', [
                'name' => 'Admin User',
                'phone' => '111222333',
                'profile_picture' => $picture,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.profile'));

        $user->refresh();

        $this->assertSame('Admin User', $user->name);
        $this->assertSame('111222333', $user->phone);
        $this->assertNotNull($user->profile_picture);
        $this->assertFileExists(public_path($user->profile_picture));
    }

    public function test_profile_picture_validation_rejects_unsupported_file_types(): void
    {
        [$user, $category] = $this->createCustomerUser();
        $file = UploadedFile::fake()->create('document.gif', 250, 'image/gif');

        $response = $this
            ->actingAs($user)
            ->from(route('profile.edit'))
            ->post('/profile', [
                'name' => 'Test User',
                'phone' => '1234567890',
                'customer_organisation' => [$category->id],
                'county' => 'Leeds',
                'school_name' => 'North Academy',
                'profile_picture' => $file,
            ]);

        $response
            ->assertSessionHasErrors('profile_picture')
            ->assertRedirect(route('profile.edit'));
    }

    public function test_profile_picture_validation_rejects_files_over_ten_mb(): void
    {
        [$user, $category] = $this->createCustomerUser();
        $file = UploadedFile::fake()->image('large-profile.jpg', 2200, 2200)->size(11000);

        $response = $this
            ->actingAs($user)
            ->from(route('profile.edit'))
            ->post('/profile', [
                'name' => 'Test User',
                'phone' => '1234567890',
                'customer_organisation' => [$category->id],
                'county' => 'Leeds',
                'school_name' => 'North Academy',
                'profile_picture' => $file,
            ]);

        $response
            ->assertSessionHasErrors('profile_picture')
            ->assertRedirect(route('profile.edit'));
    }

    public function test_profile_picture_is_replaced_when_a_new_one_is_uploaded(): void
    {
        [$user, $category] = $this->createCustomerUser();
        $firstPicture = UploadedFile::fake()->image('first-profile.jpg', 700, 700)->size(2500);
        $secondPicture = UploadedFile::fake()->image('second-profile.jpg', 700, 700)->size(2600);

        $this->actingAs($user)->post('/profile', [
            'name' => 'Test User',
            'phone' => '1234567890',
            'customer_organisation' => [$category->id],
            'county' => 'Leeds',
            'school_name' => 'North Academy',
            'profile_picture' => $firstPicture,
        ]);

        $user->refresh();
        $firstPath = $user->profile_picture;
        $this->assertNotNull($firstPath);
        $this->assertFileExists(public_path($firstPath));

        $this->actingAs($user)->post('/profile', [
            'name' => 'Test User',
            'phone' => '1234567890',
            'customer_organisation' => [$category->id],
            'county' => 'Leeds',
            'school_name' => 'North Academy',
            'profile_picture' => $secondPicture,
        ]);

        $user->refresh();
        $secondPath = $user->profile_picture;

        $this->assertNotSame($firstPath, $secondPath);
        $this->assertFileDoesNotExist(public_path($firstPath));
        $this->assertFileExists(public_path($secondPath));
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

    private function createCustomerUser(): array
    {
        Role::create(['name' => 'customer', 'guard_name' => 'web']);
        $category = OrganisationCategory::create(['name' => 'TEAM', 'type' => 'customer']);
        $user = User::factory()->create();
        $user->assignRole('customer');
        $user->organisationCategories()->attach($category);

        return [$user, $category];
    }

    private function createSupplierUser(): array
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
}
