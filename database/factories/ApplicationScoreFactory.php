<?php

namespace Database\Factories;

use App\Models\ApplicationScore;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ApplicationScore>
 */
class ApplicationScoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'application_id' => \App\Models\Application::factory(),
            'scholarship_id' => \App\Models\Scholarship::factory(),
            'total_score' => fake()->numberBetween(0, 100),
            'max_possible_score' => 100,
            'is_final' => false,
            'calculated_at' => now(),
        ];
    }
}
