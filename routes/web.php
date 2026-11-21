<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Member;
use App\Http\Middleware\MarkNavAsSeen;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! Auth::check()) {
        return redirect()->route('login');
    }

    /** @var User $user */
    $user = Auth::user();

    return redirect($user->isAdmin() ? route('admin.dashboard') : route('member.dashboard'));
})->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'show'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:login')->name('login.store');
});

Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'can:active'])->group(function (): void {
    Route::get('/account/settings', [AccountController::class, 'edit'])->name('account.edit');
    Route::put('/account/settings', [AccountController::class, 'update'])->name('account.update');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'can:admin', 'can:active', MarkNavAsSeen::class])->group(function (): void {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('genres', Admin\GenreController::class);
    Route::resource('publishers', Admin\PublisherController::class);
    Route::resource('authors', Admin\AuthorController::class);
    Route::resource('books', Admin\BookController::class)->except(['edit']);
    Route::get('books/{book}/edit', [Admin\BookController::class, 'edit'])->name('books.edit');

    Route::get('loans', [Admin\LoanController::class, 'index'])->name('loans.index');
    Route::get('loans/create', [Admin\LoanController::class, 'create'])->name('loans.create');
    Route::post('loans', [Admin\LoanController::class, 'store'])->name('loans.store');
    Route::get('loans/{loan}', [Admin\LoanController::class, 'show'])->name('loans.show');
    Route::delete('loans/{loan}', [Admin\LoanController::class, 'destroy'])->name('loans.destroy');
    Route::post('loans/{loan}/approve', [Admin\LoanController::class, 'approve'])->name('loans.approve');
    Route::post('loans/{loan}/borrow', [Admin\LoanController::class, 'markBorrowed'])->name('loans.borrow');
    Route::post('loans/{loan}/return', [Admin\LoanController::class, 'returnBook'])->name('loans.return');
    Route::post('loans/{loan}/pay-fine', [Admin\LoanController::class, 'payFine'])->name('loans.pay-fine');

    Route::get('requests', [Admin\RequestController::class, 'index'])->name('requests.index');
    Route::post('requests/loans/{loan}/approve', [Admin\RequestController::class, 'approveLoan'])->name('requests.loans.approve');
    Route::post('requests/loans/{loan}/reject', [Admin\RequestController::class, 'rejectLoan'])->name('requests.loans.reject');
    Route::post('requests/reservations/{reservation}/approve', [Admin\RequestController::class, 'approveReservation'])->name('requests.reservations.approve');
    Route::post('requests/reservations/{reservation}/reject', [Admin\RequestController::class, 'rejectReservation'])->name('requests.reservations.reject');

    Route::get('book-requests', [Admin\BookRequestController::class, 'index'])->name('book-requests.index');
    Route::delete('book-requests', [Admin\BookRequestController::class, 'destroyAll'])->name('book-requests.destroy-all');

    Route::get('users', [Admin\UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [Admin\UserController::class, 'create'])->name('users.create');
    Route::post('users', [Admin\UserController::class, 'store'])->name('users.store');
    Route::get('users/{user}/edit', [Admin\UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [Admin\UserController::class, 'update'])->name('users.update');

    Route::get('cards', [Admin\CardController::class, 'index'])->name('cards.index');
    Route::get('cards/create', [Admin\CardController::class, 'create'])->name('cards.create');
    Route::post('cards', [Admin\CardController::class, 'store'])->name('cards.store');
    Route::get('cards/{card}/edit', [Admin\CardController::class, 'edit'])->name('cards.edit');
    Route::put('cards/{card}', [Admin\CardController::class, 'update'])->name('cards.update');
    Route::post('cards/{card}/lost', [Admin\CardController::class, 'markLost'])->name('cards.lost');
    Route::post('cards/{card}/replace', [Admin\CardController::class, 'replace'])->name('cards.replace');

    Route::get('import-export', [Admin\ImportExportController::class, 'index'])->name('import-export.index');
    Route::post('import-export/import', [Admin\ImportExportController::class, 'import'])->name('import-export.import');
    Route::get('import-export/export/{type}', [Admin\ImportExportController::class, 'export'])->name('import-export.export');

    Route::get('settings', [Admin\SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [Admin\SettingController::class, 'update'])->name('settings.update');
});

Route::prefix('member')->name('member.')->middleware(['auth', 'can:member', 'can:active', MarkNavAsSeen::class])->group(function (): void {
    Route::get('/', [Member\DashboardController::class, 'index'])->name('dashboard');

    Route::get('books', [Member\CatalogController::class, 'index'])->name('books.index');
    Route::get('books/{book}', [Member\CatalogController::class, 'show'])->name('books.show');
    Route::post('books/{book}/reviews', [Member\ReviewController::class, 'store'])->name('reviews.store');

    Route::get('loans', [Member\LoanController::class, 'index'])->name('loans.index');
    Route::post('loans', [Member\LoanController::class, 'store'])->name('loans.store');
    Route::post('loans/{loan}/cancel', [Member\LoanController::class, 'cancel'])->name('loans.cancel');

    Route::get('reservations', [Member\ReservationController::class, 'index'])->name('reservations.index');
    Route::post('reservations', [Member\ReservationController::class, 'store'])->name('reservations.store');
    Route::post('reservations/{reservation}/cancel', [Member\ReservationController::class, 'cancel'])->name('reservations.cancel');

    Route::post('book-requests/{bookRequest}/vote', [Member\BookRequestController::class, 'vote'])->name('book-requests.vote');
    Route::resource('book-requests', Member\BookRequestController::class)->only(['index', 'create', 'store', 'destroy']);

    Route::get('recommendations', [Member\RecommendationController::class, 'index'])->name('recommendations.index');
});
