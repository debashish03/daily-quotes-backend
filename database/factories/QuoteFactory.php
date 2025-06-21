<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quote>
 */
class QuoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content' => $this->faker->sentence(10),
            'author' => $this->faker->name,
            'is_published' => true,
            'view_count' => $this->faker->numberBetween(0, 1000),
            'share_count' => $this->faker->numberBetween(0, 500)
        ];
    }
}
