<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Publisher;
use Illuminate\Database\Seeder;

class FakeBooksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genreIds = Genre::query()->pluck('id')->all();
        $publisherIds = Publisher::query()->pluck('id')->all();
        $authorIds = Author::query()->pluck('id')->all();

        Book::factory()
            ->count(500)
            ->sequence(fn (): array => [
                'genre_id' => fake()->randomElement($genreIds),
                'publisher_id' => fake()->randomElement($publisherIds),
                'author_id' => fake()->randomElement($authorIds),
            ])
            ->create();
    }
}
