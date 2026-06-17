<?php

namespace Database\Factories;

use App\Models\Application;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'scholarship_id' => \App\Models\Scholarship::factory(),
            'user_id' => \App\Models\User::factory(),
            'registration_number' => 'REG-' . fake()->unique()->numerify('######'),
            'snapshot_profile' => ['name' => fake()->name()],
            'status' => fake()->randomElement(['draft', 'submitted', 'under_review']),
            'is_renewal' => false,
            'submitted_at' => now(),
        ];
    }
}
