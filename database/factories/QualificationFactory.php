<?php

namespace Database\Factories;

use App\Models\Qualification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Qualification>
 */
class QualificationFactory extends Factory
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
            'name' => fake()->sentence(4),
            'description' => fake()->optional()->sentence(),
            'type' => fake()->randomElement(['single_choice', 'multi_choice', 'numeric_range', 'file_upload', 'text']),
            'is_required' => true,
            'is_file_upload_required' => false,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}
