<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Publisher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => ucwords(fake()->unique()->words(3, true)).' '.fake()->unique()->numberBetween(1, 999),
            'genre_id' => Genre::factory(),
            'publisher_id' => Publisher::factory(),
            'author_id' => Author::factory(),
            'isbn' => fake()->unique()->isbn13(),
            'description' => fake()->paragraph(),
            'average_rating' => fake()->randomFloat(1, 3, 5),
            'themes' => fake()->randomElements(['Ramadan', 'Musim Hujan', 'Liburan', 'Back to School'], 2),
        ];
    }

    public function editorPick(): static
    {
        return $this->state(fn (array $attributes): array => ['editor_pick' => true]);
    }

    public function awardWinner(): static
    {
        return $this->state(fn (array $attributes): array => [
            'award_winner' => true,
            'award_name' => 'Kusala Sastra Khatulistiwa',
        ]);
    }
}
