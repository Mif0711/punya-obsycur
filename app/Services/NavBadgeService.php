<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BookRequest;
use App\Models\Loan;
use App\Models\MemberCard;
use App\Models\NavNotification;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Carbon;

class NavBadgeService
{
    /**
     * Map route names to the navigation key whose badge should be cleared.
     *
     * @var array<string, string>
     */
    public const ROUTE_KEYS = [
        'admin.books.index' => 'admin.books',
        'admin.loans.index' => 'admin.loans',
        'admin.requests.index' => 'admin.requests',
        'admin.book-requests.index' => 'admin.book-requests',
        'admin.cards.index' => 'admin.cards',
        'admin.users.index' => 'admin.users',
        'member.books.index' => 'member.books',
        'member.loans.index' => 'member.loans',
        'member.reservations.index' => 'member.reservations',
        'member.book-requests.index' => 'member.book-requests',
        'member.recommendations.index' => 'member.recommendations',
    ];

    /**
     * Get the unseen count for every navigation item of the current user.
     *
     * @return array<string, int>
     */
    public function countsFor(User $user): array
    {
        $seen = NavNotification::query()
            ->where('user_id', $user->id)
            ->pluck('last_seen_at', 'nav_key');

        if ($user->isAdmin()) {
            /** @var Carbon|null $adminBooksSeen */
            $adminBooksSeen = $seen->get('admin.books');
            /** @var Carbon|null $loansSeen */
            $loansSeen = $seen->get('admin.loans');
            /** @var Carbon|null $requestsSeen */
            $requestsSeen = $seen->get('admin.requests');
            /** @var Carbon|null $bookRequestsSeen */
            $bookRequestsSeen = $seen->get('admin.book-requests');
            /** @var Carbon|null $cardsSeen */
            $cardsSeen = $seen->get('admin.cards');
            /** @var Carbon|null $usersSeen */
            $usersSeen = $seen->get('admin.users');

            return [
                'admin.books' => $this->newBooks($adminBooksSeen),
                'admin.loans' => $this->overdueLoans($loansSeen),
                'admin.requests' => $this->pendingRequests($requestsSeen),
                'admin.book-requests' => $this->pendingBookRequests($bookRequestsSeen),
                'admin.cards' => $this->lostCards($cardsSeen),
                'admin.users' => $this->newUsers($user, $usersSeen),
            ];
        }

        /** @var Carbon|null $memberBooksSeen */
        $memberBooksSeen = $seen->get('member.books');
        /** @var Carbon|null $memberLoansSeen */
        $memberLoansSeen = $seen->get('member.loans');
        /** @var Carbon|null $reservationsSeen */
        $reservationsSeen = $seen->get('member.reservations');
        /** @var Carbon|null $memberBookRequestsSeen */
        $memberBookRequestsSeen = $seen->get('member.book-requests');
        /** @var Carbon|null $recommendationsSeen */
        $recommendationsSeen = $seen->get('member.recommendations');

        return [
            'member.books' => $this->newBooks($memberBooksSeen),
            'member.loans' => $this->memberLoanUpdates($user, $memberLoansSeen),
            'member.reservations' => $this->memberReservationUpdates($user, $reservationsSeen),
            'member.book-requests' => $this->memberBookRequestUpdates($user, $memberBookRequestsSeen),
            'member.recommendations' => $this->newRecommendations($user, $recommendationsSeen),
        ];
    }

    /**
     * Record that the user has opened the given navigation item.
     */
    public function markSeen(User $user, string $navKey): void
    {
        NavNotification::query()->updateOrCreate(
            ['user_id' => $user->id, 'nav_key' => $navKey],
            ['last_seen_at' => now()],
        );
    }

    private function lastSeen(?Carbon $lastSeen, int $days): Carbon
    {
        return $lastSeen ?? now()->subDays($days);
    }

    private function newBooks(?Carbon $lastSeen): int
    {
        return Book::query()
            ->when(
                $lastSeen,
                fn ($query) => $query->where('created_at', '>', $lastSeen),
                fn ($query) => $query->where('created_at', '>=', now()->subDays(7)),
            )
            ->count();
    }

    private function overdueLoans(?Carbon $lastSeen): int
    {
        return Loan::query()
            ->whereIn('status', [Loan::STATUS_APPROVED, Loan::STATUS_BORROWED])
            ->where('due_date', '<', today())
            ->when(
                $lastSeen,
                fn ($query) => $query->where('due_date', '>=', $lastSeen->toDateString()),
            )
            ->count();
    }

    private function pendingRequests(?Carbon $lastSeen): int
    {
        $loans = Loan::query()
            ->where('status', Loan::STATUS_PENDING)
            ->when(
                $lastSeen,
                fn ($query) => $query->where('created_at', '>', $lastSeen),
            )
            ->count();

        $reservations = Reservation::query()
            ->where('status', Reservation::STATUS_PENDING)
            ->when(
                $lastSeen,
                fn ($query) => $query->where('created_at', '>', $lastSeen),
            )
            ->count();

        return $loans + $reservations;
    }

    private function pendingBookRequests(?Carbon $lastSeen): int
    {
        return BookRequest::query()
            ->where('status', BookRequest::STATUS_PENDING)
            ->when(
                $lastSeen,
                fn ($query) => $query->where('created_at', '>', $lastSeen),
            )
            ->count();
    }

    private function lostCards(?Carbon $lastSeen): int
    {
        return MemberCard::query()
            ->where('status', MemberCard::STATUS_LOST)
            ->when(
                $lastSeen,
                fn ($query) => $query->where('updated_at', '>', $lastSeen),
            )
            ->count();
    }

    private function newUsers(User $currentUser, ?Carbon $lastSeen): int
    {
        return User::query()
            ->whereKeyNot($currentUser->id)
            ->when(
                $lastSeen,
                fn ($query) => $query->where('created_at', '>', $lastSeen),
                fn ($query) => $query->where('created_at', '>=', now()->subDays(7)),
            )
            ->count();
    }

    private function memberLoanUpdates(User $user, ?Carbon $lastSeen): int
    {
        $since = $this->lastSeen($lastSeen, 30);

        return Loan::query()
            ->where('user_id', $user->id)
            ->where(function ($query) use ($since): void {
                $query->whereIn('status', [
                    Loan::STATUS_APPROVED,
                    Loan::STATUS_BORROWED,
                    Loan::STATUS_RETURNED,
                    Loan::STATUS_REJECTED,
                ])
                    ->where('updated_at', '>', $since)
                    ->orWhere(function ($overdue) use ($since): void {
                        $overdue->whereIn('status', [Loan::STATUS_APPROVED, Loan::STATUS_BORROWED])
                            ->where('due_date', '<', today())
                            ->where('due_date', '>=', $since->toDateString());
                    });
            })
            ->distinct()
            ->count('id');
    }

    private function memberReservationUpdates(User $user, ?Carbon $lastSeen): int
    {
        return Reservation::query()
            ->where('user_id', $user->id)
            ->whereIn('status', [
                Reservation::STATUS_APPROVED,
                Reservation::STATUS_FULFILLED,
                Reservation::STATUS_CANCELLED,
            ])
            ->where('updated_at', '>', $this->lastSeen($lastSeen, 30))
            ->count();
    }

    private function memberBookRequestUpdates(User $user, ?Carbon $lastSeen): int
    {
        return BookRequest::query()
            ->where('user_id', $user->id)
            ->whereIn('status', [BookRequest::STATUS_APPROVED, BookRequest::STATUS_REJECTED])
            ->where('updated_at', '>', $this->lastSeen($lastSeen, 30))
            ->count();
    }

    private function newRecommendations(User $user, ?Carbon $lastSeen): int
    {
        $since = $this->lastSeen($lastSeen, 7);

        $genreIds = Book::query()
            ->whereIn('id', $user->loans()->pluck('book_id'))
            ->pluck('genre_id')
            ->filter()
            ->unique()
            ->all();

        return Book::query()
            ->where('created_at', '>', $since)
            ->when(
                $genreIds !== [],
                fn ($query) => $query->whereIn('genre_id', $genreIds),
                fn ($query) => $query->where('editor_pick', true),
            )
            ->count();
    }
}
