<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Loan;
use App\Models\Publisher;
use App\Models\SearchLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $query = $request->string('q')->toString();
        $availability = $request->string('availability')->toString();

        $books = Book::query()
            ->with(['genre', 'author', 'publisher'])
            ->withAvailability()
            ->when($query !== '', function ($builder) use ($query): void {
                $builder->where(function ($inner) use ($query): void {
                    $inner->whereLike('title', '%'.$query.'%')
                        ->orWhereHas('author', fn ($authorQuery) => $authorQuery->whereLike('name', '%'.$query.'%'))
                        ->orWhereHas('publisher', fn ($publisherQuery) => $publisherQuery->whereLike('name', '%'.$query.'%'))
                        ->orWhereHas('genre', fn ($genreQuery) => $genreQuery->whereLike('name', '%'.$query.'%'));
                });
            })
            ->when($request->integer('genre_id'), fn ($builder, int $genreId) => $builder->where('genre_id', $genreId))
            ->when($request->integer('publisher_id'), fn ($builder, int $publisherId) => $builder->where('publisher_id', $publisherId))
            ->when($request->integer('author_id'), fn ($builder, int $authorId) => $builder->where('author_id', $authorId))
            ->when($availability === 'available', fn ($builder) => $builder->available())
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        if (mb_strlen($query) >= 2) {
            SearchLog::create([
                'user_id' => Auth::id(),
                'query' => $query,
                'created_at' => now(),
            ]);
        }

        return view('member.catalog.index', [
            'books' => $books,
            'genres' => Genre::orderBy('name')->get(['id', 'name']),
            'publishers' => Publisher::orderBy('name')->get(['id', 'name']),
            'authors' => Author::orderBy('name')->get(['id', 'name']),
            'query' => $query,
        ]);
    }

    public function show(Book $book): View
    {
        /** @var User $user */
        $user = Auth::user();

        return view('member.catalog.show', [
            'book' => $book->load(['genre', 'author', 'publisher', 'reviews.user']),
            'available' => Book::whereKey($book->id)->available()->exists(),
            'activeLoan' => Loan::query()
                ->whereBelongsTo($user)
                ->where('book_id', $book->id)
                ->whereIn('status', [Loan::STATUS_PENDING, Loan::STATUS_APPROVED, Loan::STATUS_BORROWED])
                ->first(),
            'userReview' => $book->reviews->firstWhere('user_id', $user->id),
            'relatedBooks' => Book::query()
                ->with(['author', 'genre'])
                ->where('genre_id', $book->genre_id)
                ->whereKeyNot($book->id)
                ->limit(4)
                ->get(),
        ]);
    }
}
