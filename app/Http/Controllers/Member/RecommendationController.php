<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\BookRecommendationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RecommendationController extends Controller
{
    public function index(BookRecommendationService $recommendations): View
    {
        /** @var User $user */
        $user = Auth::user();

        return view('member.recommendations.index', [
            'recommendedBooks' => $recommendations->recommend($user, 12),
        ]);
    }
}
