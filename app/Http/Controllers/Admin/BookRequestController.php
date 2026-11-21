<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BookRequestController extends Controller
{
    public function index(): View
    {
        return view('admin.book-requests.index', [
            'requests' => BookRequest::query()
                ->with(['user', 'votes.user'])
                ->latest()
                ->paginate(12),
        ]);
    }

    public function destroyAll(): RedirectResponse
    {
        BookRequest::query()->delete();

        return back()->with('success', 'Semua pengajuan buku beserta vote-nya dihapus.');
    }
}
