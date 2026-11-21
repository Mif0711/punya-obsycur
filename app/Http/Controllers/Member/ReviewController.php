<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request, Book $book): RedirectResponse
    {
        Review::updateOrCreate(
            ['user_id' => Auth::id(), 'book_id' => $book->id],
            $request->safe()->only(['rating', 'review']),
        );

        $book->update([
            'average_rating' => round((float) $book->reviews()->avg('rating'), 1),
        ]);

        return back()->with('success', 'Ulasan Anda tersimpan.');
    }
}
