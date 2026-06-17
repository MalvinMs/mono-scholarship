<?php

namespace Database\Factories;

use App\Models\Scholarship;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Scholarship>
 */
class ScholarshipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $year = fake()->year();
        return [
            'name' => fake()->sentence(3),
            'slug' => fn (array $attributes) => \Illuminate\Support\Str::slug($attributes['name'] . '-' . uniqid()),
            'description' => fake()->paragraph(),
            'academic_year' => $year . '/' . ($year + 1),
            'fund_amount' => fake()->numberBetween(1000000, 10000000),
            'quota_primary' => fake()->numberBetween(10, 200),
            'quota_reserve' => fake()->numberBetween(0, 20),
            'date_start' => now()->subDays(fake()->numberBetween(0, 30)),
            'date_end' => now()->addDays(fake()->numberBetween(30, 90)),
            'status' => fake()->randomElement(['draft', 'open', 'closed', 'announced']),
            'is_verification_enabled' => true,
            'min_gpa_renewal' => 3.50,
            'scoring_display_mode' => 'absolute',
            'otp_channel' => 'whatsapp',
        ];
    }
}
