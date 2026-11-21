<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservationRequest;
use App\Models\Book;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();

        return view('member.reservations.index', [
            'reservations' => Reservation::query()
                ->whereBelongsTo($user)
                ->with(['book', 'processor'])
                ->latest()
                ->paginate(10),
        ]);
    }

    public function store(StoreReservationRequest $request): RedirectResponse
    {
        $book = Book::findOrFail($request->integer('book_id'));

        $alreadyReserved = Reservation::query()
            ->whereBelongsTo(Auth::user())
            ->where('book_id', $book->id)
            ->whereIn('status', [Reservation::STATUS_PENDING, Reservation::STATUS_APPROVED])
            ->exists();

        if ($alreadyReserved) {
            return back()->withErrors(['book_id' => 'Anda sudah memiliki booking aktif untuk buku ini.']);
        }

        Reservation::create([
            'user_id' => Auth::id(),
            'book_id' => $book->id,
            'status' => Reservation::STATUS_PENDING,
        ]);

        return back()->with('success', 'Booking buku diajukan. Tunggu persetujuan admin.');
    }

    public function cancel(Reservation $reservation): RedirectResponse
    {
        abort_unless($reservation->user_id === Auth::id(), 403);
        abort_unless(in_array($reservation->status, [Reservation::STATUS_PENDING, Reservation::STATUS_APPROVED], true), 403);

        $reservation->update(['status' => Reservation::STATUS_CANCELLED]);

        return back()->with('success', 'Booking dibatalkan.');
    }
}
