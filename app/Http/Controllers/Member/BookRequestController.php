<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequestRequest;
use App\Http\Requests\VoteBookRequestRequest;
use App\Models\BookRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BookRequestController extends Controller
{
    public function index(): View
    {
        return view('member.book-requests.index', [
            'requests' => BookRequest::query()
                ->with(['user', 'votes.user'])
                ->latest()
                ->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('member.book-requests.create');
    }

    public function store(StoreBookRequestRequest $request): RedirectResponse
    {
        BookRequest::create($request->validatedData() + [
            'user_id' => Auth::id(),
            'status' => BookRequest::STATUS_PENDING,
        ]);

        return redirect()->route('member.book-requests.index')
            ->with('success', 'Pengajuan buku dikirim. Anggota lain kini dapat memberikan vote.');
    }

    public function vote(VoteBookRequestRequest $request, BookRequest $bookRequest): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $existingVote = $bookRequest->votes()->whereBelongsTo($user)->first();

        if ($existingVote !== null) {
            $existingVote->delete();

            return back()->with('success', 'Vote Anda dibatalkan.');
        }

        $bookRequest->votes()->create([
            'user_id' => $user->id,
            'comment' => $request->string('comment')->toString(),
        ]);

        return back()->with('success', 'Vote dan alasan Anda tersimpan.');
    }

    public function destroy(BookRequest $bookRequest): RedirectResponse
    {
        abort_unless($bookRequest->user_id === Auth::id(), 403);

        if ($bookRequest->votes()->exists()) {
            $vote = $bookRequest->votes()
                ->where('user_id', Auth::id())
                ->first()
                ?? $bookRequest->votes()->latest()->first();

            $vote?->delete();

            return back()->with('success', 'Satu vote berkurang. Usulan tetap disimpan.');
        }

        $bookRequest->delete();

        return back()->with('success', 'Usulan dihapus.');
    }
}
