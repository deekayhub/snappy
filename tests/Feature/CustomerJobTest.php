<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CustomerJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_must_login_before_accessing_post_job_page(): void
    {
        $response = $this->get(route('customer.jobs.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_customer_can_post_a_job(): void
    {
        Role::create(['name' => 'customer', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole('customer');

        $response = $this->actingAs($user)->post(route('customer.jobs.store'), [
            'title' => 'Need new basketball uniforms',
            'category' => 'Sportswear',
            'organisation_name' => 'Northside School',
            'location' => 'Leeds',
            'budget' => '1200',
            'needed_by' => now()->addWeek()->toDateString(),
            'description' => 'We need home and away kits for 24 students before the summer tournament.',
        ]);

        $response->assertRedirect(route('customer.jobs.create'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('customer_jobs', [
            'user_id' => $user->id,
            'title' => 'Need new basketball uniforms',
            'category' => 'Sportswear',
            'organisation_name' => 'Northside School',
            'location' => 'Leeds',
            'status' => 'open',
        ]);
    }
}
