<?php

namespace Database\Factories;

use App\Models\BookRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BookRequest>
 */
class BookRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'genre' => fake()->optional()->word(),
            'publisher' => fake()->optional()->company(),
            'author' => fake()->optional()->name(),
            'reason' => fake()->paragraph(),
            'status' => BookRequest::STATUS_PENDING,
        ];
    }
}
