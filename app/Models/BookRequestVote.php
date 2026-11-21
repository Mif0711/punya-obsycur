<?php

namespace App\Models;

use Database\Factories\BookRequestVoteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'comment'])]
class BookRequestVote extends Model
{
    /** @use HasFactory<BookRequestVoteFactory> */
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bookRequest(): BelongsTo
    {
        return $this->belongsTo(BookRequest::class);
    }
}
