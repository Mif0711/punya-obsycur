<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Reservation;
use App\Models\User;
use App\Services\BookRecommendationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(BookRecommendationService $recommendations): View
    {
        /** @var User $user */
        $user = Auth::user();
        $user->loadMissing('memberCard');

        $loans = Loan::query()
            ->whereBelongsTo($user)
            ->with('book')
            ->latest()
            ->limit(5)
            ->get();

        return view('member.dashboard', [
            'user' => $user,
            'memberCard' => $user->memberCard,
            'loans' => $loans,
            'reservations' => Reservation::query()
                ->whereBelongsTo($user)
                ->with('book')
                ->latest()
                ->limit(5)
                ->get(),
            'stats' => [
                'ongoing' => Loan::query()->whereBelongsTo($user)->ongoing()->count(),
                'returned' => Loan::query()->whereBelongsTo($user)->where('status', Loan::STATUS_RETURNED)->count(),
                'overdue' => Loan::query()->whereBelongsTo($user)->overdue()->count(),
                'unpaid_fines' => Loan::query()->whereBelongsTo($user)->where('fine_paid', false)->sum('fine_amount'),
            ],
            'recommendedBooks' => $recommendations->recommend($user, 6),
        ]);
    }
}
