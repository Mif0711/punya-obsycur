<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Loan;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class BookRecommendationService
{
    /**
     * Score books using all eight recommendation signals requested by the library.
     *
     * @return Collection<int, Book>
     */
    public function recommend(User $user, int $limit = 6): Collection
    {
        /** @var array<int, array{score: float, reasons: array<int, string>}> $scores */
        $scores = [];

        $add = function (int $bookId, float $score, string $reason) use (&$scores): void {
            $scores[$bookId] ??= ['score' => 0, 'reasons' => []];
            $scores[$bookId]['score'] += $score;

            if (! in_array($reason, $scores[$bookId]['reasons'], true)) {
                $scores[$bookId]['reasons'][] = $reason;
            }
        };

        $borrowedBookIds = $user->loans()->pluck('book_id')->all();

        $historyGenreIds = Book::query()
            ->whereIn('id', $borrowedBookIds)
            ->pluck('genre_id')
            ->unique()
            ->all();

        foreach ($historyGenreIds as $genreId) {
            Book::query()
                ->where('genre_id', $genreId)
                ->whereNotIn('id', $borrowedBookIds)
                ->limit(10)
                ->pluck('id')
                ->each(fn (int $id) => $add($id, 12, 'Sesuai riwayat bacaan Anda'));
        }

        $searchTerms = $user->searchLogs()->latest()->limit(10)->pluck('query')->all();

        if ($searchTerms !== []) {
            foreach ($searchTerms as $term) {
                Book::query()
                    ->whereLike('title', '%'.$term.'%')
                    ->whereNotIn('id', $borrowedBookIds)
                    ->limit(5)
                    ->pluck('id')
                    ->each(fn (int $id) => $add($id, 6, 'Sesuai pencarian Anda'));
            }

            Genre::query()
                ->where(function ($query) use ($searchTerms): void {
                    foreach ($searchTerms as $term) {
                        $query->orWhereLike('name', '%'.$term.'%');
                    }
                })
                ->pluck('id')
                ->each(function (int $genreId) use ($add, $borrowedBookIds): void {
                    Book::query()
                        ->where('genre_id', $genreId)
                        ->whereNotIn('id', $borrowedBookIds)
                        ->limit(5)
                        ->pluck('id')
                        ->each(fn (int $id) => $add($id, 5, 'Sesuai pencarian Anda'));
                });
        }

        if ($borrowedBookIds !== []) {
            $peerIds = Loan::query()
                ->whereIn('book_id', $borrowedBookIds)
                ->where('user_id', '!=', $user->id)
                ->distinct()
                ->pluck('user_id')
                ->all();

            foreach ($peerIds as $peerId) {
                Loan::query()
                    ->where('user_id', $peerId)
                    ->whereNotIn('book_id', $borrowedBookIds)
                    ->orderByDesc('id')
                    ->limit(5)
                    ->pluck('book_id')
                    ->each(fn (int $id) => $add($id, 8, 'Disukai pembaca dengan selera serupa'));
            }
        }

        $topRatedBookIds = Review::query()
            ->selectRaw('book_id, AVG(rating) as average_review_rating')
            ->where('rating', '>=', 4)
            ->groupBy('book_id')
            ->orderByDesc('average_review_rating')
            ->limit(10)
            ->pluck('book_id')
            ->all();

        foreach ($topRatedBookIds as $bookId) {
            $add($bookId, 7, 'Rating pembaca tinggi');
        }

        $currentTheme = $this->currentTheme();
        $themedBookIds = Book::query()
            ->whereNotNull('themes')
            ->pluck('themes', 'id')
            ->filter(fn (array $themes, int $id): bool => in_array($currentTheme, $themes, true))
            ->keys()
            ->all();

        foreach ($themedBookIds as $bookId) {
            $add($bookId, 5, 'Sesuai tema/momen saat ini');
        }

        /** @var Collection<int, Book> $candidates */
        $candidates = Book::query()
            ->with(['genre', 'author', 'publisher'])
            ->whereNotIn('id', $borrowedBookIds)
            ->orderByDesc('average_rating')
            ->limit(300)
            ->get();

        foreach ($candidates as $book) {
            if ((float) $book->average_rating >= 4) {
                $add($book->id, 6, 'Rating pembaca tinggi');
            }

            if ($book->editor_pick) {
                $add($book->id, 7, 'Pilihan editor / pustakawan');
            }

            if ($book->award_winner) {
                $add($book->id, 7, 'Pemenang penghargaan sastra');
            }

            if ($book->public_figure_pick) {
                $add($book->id, 6, 'Daftar bacaan tokoh publik');
            }

            if ($book->social_trend) {
                $add($book->id, 6, 'Tren media sosial & adaptasi');
            }

            if (in_array($currentTheme, $book->themes, true)) {
                $add($book->id, 5, 'Sesuai tema/momen saat ini');
            }
        }

        return $candidates
            ->filter(fn (Book $book): bool => ($scores[$book->id]['score'] ?? 0) > 0)
            ->sortByDesc(fn (Book $book): float => $scores[$book->id]['score'])
            ->take($limit)
            ->values()
            ->map(function (Book $book) use ($scores): Book {
                $book->setAttribute('recommendation_score', $scores[$book->id]['score']);
                $book->setAttribute('recommendation_reasons', $scores[$book->id]['reasons']);

                return $book;
            });
    }

    /**
     * A simple seasonal theme based on the current month.
     */
    private function currentTheme(): string
    {
        return match ((int) now()->format('m')) {
            1 => 'Awal Tahun',
            2 => 'Cinta',
            4 => 'Bumi',
            5 => 'Keluarga',
            8 => 'Kemerdekaan',
            12 => 'Liburan',
            default => 'Sepanjang Tahun',
        };
    }
}
