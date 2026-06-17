<?php

namespace Database\Factories;

use App\Models\ApplicationAnswer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ApplicationAnswer>
 */
class ApplicationAnswerFactory extends Factory
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
            'qualification_id' => \App\Models\Qualification::factory(),
            'computed_score' => fake()->numberBetween(0, 50),
        ];
    }
}
