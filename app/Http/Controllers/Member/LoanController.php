<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLoanRequest;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use App\Services\LoanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoanController extends Controller
{
    public function __construct(private LoanService $loanService) {}

    public function index(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();
        $status = $request->string('status')->toString();

        return view('member.loans.index', [
            'loans' => Loan::query()
                ->whereBelongsTo($user)
                ->with('book')
                ->when($status !== '', fn ($query) => $query->where('status', $status))
                ->latest()
                ->paginate(10)
                ->withQueryString(),
            'unpaidFines' => Loan::query()
                ->whereBelongsTo($user)
                ->where('fine_paid', false)
                ->sum('fine_amount'),
        ]);
    }

    public function store(StoreLoanRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $book = Book::findOrFail($request->integer('book_id'));

        $this->loanService->create($user, $book, false, null, $request->string('note')->toString() ?: null);

        return redirect()->route('member.loans.index')
            ->with('success', 'Permintaan peminjaman dikirim. Tunggu persetujuan admin.');
    }

    public function cancel(Loan $loan): RedirectResponse
    {
        abort_unless($loan->user_id === Auth::id(), 403);
        abort_unless($loan->status === Loan::STATUS_PENDING, 403);

        $this->loanService->reject($loan);

        return back()->with('success', 'Permintaan peminjaman dibatalkan.');
    }
}
