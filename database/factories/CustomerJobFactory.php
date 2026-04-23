<?php

namespace Database\Factories;

use App\Models\CustomerJob;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CustomerJob>
 */
class CustomerJobFactory extends Factory
{
    protected $model = CustomerJob::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->randomElement([
            'Custom Team Jerseys',
            'Sports Day Medals and Trophies',
            'Indoor Court Signage',
            'School Uniform Supply',
            'Club Training Kit',
            'Promotional Sports Bottles',
            'Tournament Banner Printing',
        ]);

        return [
            'title' => $title,
            'category' => fake()->randomElement([
                'sportswear',
                'sports equipment',
                'trophies & awards',
                'signage',
                'gifts & promotional items',
                'school uniforms & supplies',
                'other',
            ]),
            'organisation_name' => fake()->company(),
            'location' => fake()->city(),
            'budget' => fake()->randomFloat(2, 300, 15000),
            'delivery_in_uk' => fake()->boolean(85),
            'personalisation_required' => fake()->boolean(45),
            'personalisation_mode' => fake()->randomElement(['same', 'different', null]),
            'supplier_target_type' => fake()->randomElement(['all', 'count']),
            'supplier_target_count' => fake()->numberBetween(3, 15),
            'needed_by' => fake()->dateTimeBetween('+5 days', '+90 days'),
            'description' => fake()->paragraphs(2, true),
            'notes' => fake()->optional()->paragraph(),
            'status' => fake()->randomElement(['open', 'open', 'open', 'closed']),
        ];
    }
}
