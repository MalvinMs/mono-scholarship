<?php

namespace Database\Factories;

use App\Models\QualificationOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QualificationOption>
 */
class QualificationOptionFactory extends Factory
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
            'label' => fake()->words(3, true),
            'value' => fake()->numberBetween(0, 50),
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
