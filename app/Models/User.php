<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable([
    'username',
    'name',
    'nickname',
    'birth_place',
    'birth_date',
    'school',
    'password',
    'role',
    'is_active',
    'phone',
    'parent_phone',
    'address',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_USER = 'user';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'birth_date' => 'date',
        ];
    }

    /**
     * Normalize usernames so login is case-insensitive.
     */
    protected function username(): Attribute
    {
        return Attribute::make(set: fn (string $value): string => Str::lower($value));
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isMember(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    /**
     * Contact options in the order they should be tried when following up on an overdue book.
     *
     * @return array<int, array{label: string, value: string}>
     */
    public function contactChannels(): array
    {
        return collect([
            ['label' => 'WhatsApp', 'value' => $this->phone],
            ['label' => 'Orang tua', 'value' => $this->parent_phone],
            ['label' => 'Alamat', 'value' => $this->address],
        ])
            ->filter(fn (array $channel): bool => filled($channel['value']))
            ->values()
            ->all();
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    #[Scope]
    protected function members(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_USER);
    }

    public function memberCard(): HasOne
    {
        return $this->hasOne(MemberCard::class);
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

    /** @return HasMany<BookRequest, $this> */
    public function bookRequests(): HasMany
    {
        return $this->hasMany(BookRequest::class);
    }

    /** @return HasMany<Review, $this> */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /** @return HasMany<SearchLog, $this> */
    public function searchLogs(): HasMany
    {
        return $this->hasMany(SearchLog::class);
    }

    /** @return HasMany<Loan, $this> */
    public function approvedLoans(): HasMany
    {
        return $this->hasMany(Loan::class, 'approved_by');
    }
}
