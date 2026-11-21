<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLoanRequest;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use App\Services\LoanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoanController extends Controller
{
    public function __construct(private LoanService $loanService) {}

    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $loans = Loan::query()
            ->with(['user', 'book'])
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($request->string('q')->toString(), function ($query, string $q): void {
                $query->where(function ($inner) use ($q): void {
                    $inner->whereHas('user', fn ($userQuery) => $userQuery->whereLike('username', '%'.$q.'%'))
                        ->orWhereHas('book', fn ($bookQuery) => $bookQuery->whereLike('title', '%'.$q.'%'));
                });
            })
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.loans.index', [
            'loans' => $loans,
            'stats' => [
                'ongoing' => Loan::ongoing()->count(),
                'overdue' => Loan::overdue()->count(),
                'total_fines' => Loan::where('fine_amount', '>', 0)->sum('fine_amount'),
                'unpaid_fines' => Loan::where('fine_paid', false)->sum('fine_amount'),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.loans.create', [
            'users' => User::members()->active()->orderBy('name')->get(['id', 'name', 'username']),
            'books' => Book::query()->available()->orderBy('title')->get(['id', 'title']),
        ]);
    }

    public function store(StoreLoanRequest $request): RedirectResponse
    {
        /** @var User $admin */
        $admin = Auth::user();
        $member = User::findOrFail($request->integer('user_id'));
        $book = Book::findOrFail($request->integer('book_id'));

        $loan = $this->loanService->create($member, $book, true, $admin, $request->string('note')->toString() ?: null);

        return redirect()->route('admin.loans.index')->with('success', 'Peminjaman berhasil dibuat ('.$loan->book->title.').');
    }

    public function show(Loan $loan): View
    {
        return view('admin.loans.show', [
            'loan' => $loan->load(['user', 'book', 'approver']),
        ]);
    }

    public function approve(Loan $loan): RedirectResponse
    {
        /** @var User $admin */
        $admin = Auth::user();

        try {
            $this->loanService->approve($loan, $admin);
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }

        return back()->with('success', 'Peminjaman disetujui.');
    }

    public function markBorrowed(Loan $loan): RedirectResponse
    {
        $this->loanService->markBorrowed($loan);

        return back()->with('success', 'Status diperbarui menjadi sedang dipinjam.');
    }

    public function returnBook(Loan $loan): RedirectResponse
    {
        $this->loanService->returnBook($loan);

        return back()->with('success', 'Buku diterima kembali. Denda dihitung otomatis jika terlambat.');
    }

    public function payFine(Loan $loan): RedirectResponse
    {
        $this->loanService->markFinePaid($loan);

        return back()->with('success', 'Denda ditandai sudah lunas.');
    }

    public function destroy(Loan $loan): RedirectResponse
    {
        $loan->delete();

        return redirect()->route('admin.loans.index')->with('success', 'Data peminjaman dihapus.');
    }
}
