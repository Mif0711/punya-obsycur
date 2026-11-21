<?php

namespace Database\Factories;

use App\Models\Genre;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Genre>
 */
class GenreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(['Fiksi', 'Nonfiksi', 'Sains', 'Sejarah', 'Biografi', 'Puisi', 'Cerpen', 'Teknologi']).'-'.fake()->unique()->numberBetween(1, 9999),
            'description' => fake()->sentence(12),
        ];
    }
}
