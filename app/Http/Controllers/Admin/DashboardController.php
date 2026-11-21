<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookRequest;
use App\Models\Loan;
use App\Models\Reservation;
use App\Models\Setting;
use App\Models\User;
use App\Services\BookRecommendationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(BookRecommendationService $recommendations): View
    {
        /** @var User $admin */
        $admin = Auth::user();

        $stats = [
            'books' => Book::count(),
            'members' => User::members()->count(),
            'ongoing_loans' => Loan::ongoing()->count(),
            'overdue_loans' => Loan::overdue()->count(),
            'unpaid_fines' => Loan::where('fine_paid', false)->sum('fine_amount'),
            'pending_requests' => Loan::pending()->count()
                + Reservation::pending()->count()
                + BookRequest::pending()->count(),
        ];

        return view('admin.dashboard', [
            'stats' => $stats,
            'recommendedBooks' => $recommendations->recommend($admin, 6),
            'finePerDay' => Setting::integer(Setting::KEY_FINE_PER_DAY, 5000),
            'graceDays' => Setting::integer(Setting::KEY_GRACE_DAYS, 2),
        ]);
    }
}
