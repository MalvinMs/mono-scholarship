<?php

namespace Database\Factories;

use App\Models\QualificationRange;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QualificationRange>
 */
class QualificationRangeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'qualification_id' => \App\Models\Qualification::factory(),
            'range_min' => fake()->randomFloat(2, 0, 5),
            'range_max' => fake()->randomFloat(2, 5, 10),
            'value' => fake()->numberBetween(0, 50),
            'label' => fake()->optional()->words(2, true),
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
