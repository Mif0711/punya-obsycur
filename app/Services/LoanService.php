<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Reservation;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoanService
{
    public function __construct(private FineCalculator $fineCalculator) {}

    /**
     * Create a loan request for a member or a direct loan from the admin panel.
     */
    public function create(User $user, Book $book, bool $approved, ?User $admin = null, ?string $note = null): Loan
    {
        return DB::transaction(function () use ($user, $book, $approved, $admin, $note): Loan {
            $lockedBook = Book::whereKey($book->id)->lockForUpdate()->firstOrFail();
            $this->assertBookAvailable($lockedBook);

            $loanDate = today();
            $loanDays = Setting::integer(Setting::KEY_LOAN_DAYS, 7);

            return Loan::create([
                'user_id' => $user->id,
                'book_id' => $lockedBook->id,
                'approved_by' => $approved ? $admin?->id : null,
                'loan_date' => $loanDate,
                'due_date' => $loanDate->copy()->addDays($loanDays),
                'status' => $approved ? Loan::STATUS_APPROVED : Loan::STATUS_PENDING,
                'note' => $note,
            ]);
        });
    }

    public function approve(Loan $loan, User $admin): Loan
    {
        return DB::transaction(function () use ($loan, $admin): Loan {
            $book = Book::whereKey($loan->book_id)->lockForUpdate()->firstOrFail();
            $this->assertBookAvailable($book, $loan->id);

            $loanDate = today();
            $loan->update([
                'status' => Loan::STATUS_APPROVED,
                'approved_by' => $admin->id,
                'loan_date' => $loanDate,
                'due_date' => $loanDate->copy()->addDays(Setting::integer(Setting::KEY_LOAN_DAYS, 7)),
            ]);

            return $loan->refresh();
        });
    }

    public function reject(Loan $loan): Loan
    {
        $loan->update(['status' => Loan::STATUS_REJECTED]);

        return $loan->refresh();
    }

    public function markBorrowed(Loan $loan): Loan
    {
        $loan->update(['status' => Loan::STATUS_BORROWED]);

        return $loan->refresh();
    }

    public function returnBook(Loan $loan): Loan
    {
        $fine = $this->fineCalculator->calculate($loan->refresh()->setAttribute('return_date', today()));

        $loan->update([
            'status' => Loan::STATUS_RETURNED,
            'return_date' => today(),
            'fine_amount' => $fine['amount'],
        ]);

        return $loan->refresh();
    }

    public function markFinePaid(Loan $loan): Loan
    {
        $loan->update(['fine_paid' => true]);

        return $loan->refresh();
    }

    private function assertBookAvailable(Book $book, ?int $ignoreLoanId = null): void
    {
        $borrowed = $book->loans()
            ->whereIn('status', [Loan::STATUS_APPROVED, Loan::STATUS_BORROWED])
            ->when($ignoreLoanId, fn ($query) => $query->whereKeyNot($ignoreLoanId))
            ->count();

        $reserved = $book->reservations()
            ->where('status', Reservation::STATUS_APPROVED)
            ->count();

        if ($borrowed > 0 || $reserved > 0) {
            throw ValidationException::withMessages([
                'book_id' => 'Buku sedang tidak tersedia saat ini.',
            ]);
        }
    }
}
