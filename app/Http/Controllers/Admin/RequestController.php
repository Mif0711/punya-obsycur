<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Reservation;
use App\Models\User;
use App\Services\LoanService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RequestController extends Controller
{
    public function __construct(private LoanService $loanService) {}

    public function index(): View
    {
        return view('admin.requests.index', [
            'loanRequests' => Loan::query()
                ->pending()
                ->with(['user', 'book'])
                ->oldest()
                ->get(),
            'reservations' => Reservation::query()
                ->pending()
                ->with(['user', 'book'])
                ->oldest()
                ->get(),
            'recentReservations' => Reservation::query()
                ->whereNot('status', Reservation::STATUS_PENDING)
                ->with(['user', 'book'])
                ->latest()
                ->limit(20)
                ->get(),
        ]);
    }

    public function approveLoan(Loan $loan): RedirectResponse
    {
        /** @var User $admin */
        $admin = Auth::user();

        try {
            $this->loanService->approve($loan, $admin);
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }

        return back()->with('success', 'Permintaan peminjaman disetujui.');
    }

    public function rejectLoan(Loan $loan): RedirectResponse
    {
        $this->loanService->reject($loan);

        return back()->with('success', 'Permintaan peminjaman ditolak.');
    }

    public function approveReservation(Reservation $reservation): RedirectResponse
    {
        try {
            Book::whereKey($reservation->book_id)->available()->firstOrFail();
        } catch (ModelNotFoundException) {
            return back()->withErrors(['book_id' => 'Buku tidak tersedia untuk booking ini.']);
        }

        $reservation->update([
            'status' => Reservation::STATUS_APPROVED,
            'processed_by' => Auth::id(),
            'reserved_until' => today()->addDays(3),
        ]);

        return back()->with('success', 'Booking buku disetujui.');
    }

    public function rejectReservation(Reservation $reservation): RedirectResponse
    {
        $reservation->update([
            'status' => Reservation::STATUS_CANCELLED,
            'processed_by' => Auth::id(),
        ]);

        return back()->with('success', 'Booking buku dibatalkan.');
    }
}
