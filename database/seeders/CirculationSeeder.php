<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class CirculationSeeder extends Seeder
{
    /**
     * Seed loan history and reviews so recommendations have signals to work with.
     */
    public function run(): void
    {
        if (Loan::query()->exists()) {
            return;
        }

        $budi = User::where('username', 'budi')->firstOrFail();
        $sinta = User::where('username', 'sinta')->firstOrFail();
        $rina = User::where('username', 'rina')->firstOrFail();

        $history = [
            [$budi, 'Laskar Pelangi', 40, 33],
            [$budi, 'Filosofi Teras', 25, 18],
            [$budi, 'Sapiens', 12, 5],
            [$sinta, 'Laskar Pelangi', 30, 23],
            [$sinta, 'Bumi Manusia', 20, 13],
            [$sinta, 'Negeri 5 Menara', 10, 3],
            [$rina, 'Pulang', 35, 28],
            [$rina, 'Laut Bercerita', 15, 8],
        ];

        foreach ($history as [$user, $title, $borrowedDaysAgo, $returnedDaysAgo]) {
            $book = Book::where('title', $title)->firstOrFail();

            Loan::create([
                'user_id' => $user->id,
                'book_id' => $book->id,
                'loan_date' => today()->subDays($borrowedDaysAgo),
                'due_date' => today()->subDays($borrowedDaysAgo)->addDays(7),
                'return_date' => today()->subDays($returnedDaysAgo),
                'status' => Loan::STATUS_RETURNED,
            ]);
        }

        Loan::create([
            'user_id' => $budi->id,
            'book_id' => Book::where('title', 'Atomic Habits')->firstOrFail()->id,
            'loan_date' => today()->subDays(3),
            'due_date' => today()->addDays(4),
            'status' => Loan::STATUS_BORROWED,
        ]);

        $reviews = [
            [$budi, 'Laskar Pelangi', 5, 'Kisah yang menghangatkan hati dan memotivasi.'],
            [$budi, 'Filosofi Teras', 5, 'Banyak pelajaran untuk mengelola emosi.'],
            [$sinta, 'Laskar Pelangi', 5, 'Wajib dibaca semua generasi.'],
            [$sinta, 'Bumi Manusia', 5, 'Karya sejarah yang luar biasa.'],
            [$rina, 'Pulang', 4, 'Menyentuh dan membuat saya merenung.'],
        ];

        foreach ($reviews as [$user, $title, $rating, $review]) {
            $book = Book::where('title', $title)->firstOrFail();

            Review::updateOrCreate(
                ['user_id' => $user->id, 'book_id' => $book->id],
                ['rating' => $rating, 'review' => $review],
            );
        }
    }
}
