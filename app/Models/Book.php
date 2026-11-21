<?php

namespace App\Models;

use Database\Factories\BookFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'title',
    'genre_id',
    'publisher_id',
    'author_id',
    'isbn',
    'description',
    'cover_path',
    'average_rating',
    'editor_pick',
    'award_winner',
    'award_name',
    'public_figure_pick',
    'public_figure_name',
    'social_trend',
    'social_trend_source',
    'themes',
])]
class Book extends Model
{
    /** @use HasFactory<BookFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'average_rating' => 'decimal:1',
            'editor_pick' => 'boolean',
            'award_winner' => 'boolean',
            'public_figure_pick' => 'boolean',
            'social_trend' => 'boolean',
        ];
    }

    /**
     * Normalize theme tags into a clean list.
     */
    protected function themes(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value): array => array_values(array_filter(
                array_map('trim', explode(',', (string) $value)),
            )),
            set: fn (?array $value): ?string => empty($value) ? null : implode(',', $value),
        );
    }

    #[Scope]
    protected function available(Builder $query): Builder
    {
        return $query->whereRaw(
            '(SELECT COUNT(*) FROM loans WHERE loans.book_id = books.id AND loans.status IN (?, ?)) '
            .'+ (SELECT COUNT(*) FROM reservations WHERE reservations.book_id = books.id AND reservations.status = ?) = 0',
            [Loan::STATUS_APPROVED, Loan::STATUS_BORROWED, Reservation::STATUS_APPROVED],
        );
    }

    #[Scope]
    protected function withAvailability(Builder $query): Builder
    {
        return $query->withExists([
            'loans as is_borrowed' => fn (Builder $loanQuery) => $loanQuery
                ->whereIn('status', [Loan::STATUS_APPROVED, Loan::STATUS_BORROWED]),
            'reservations as is_reserved' => fn (Builder $reservationQuery) => $reservationQuery
                ->where('status', Reservation::STATUS_APPROVED),
        ]);
    }

    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    /** @return HasMany<Loan, $this> */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    /** @return HasMany<Reservation, $this> */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /** @return HasMany<Review, $this> */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
