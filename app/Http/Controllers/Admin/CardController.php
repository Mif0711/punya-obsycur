<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateCardRequest;
use App\Models\MemberCard;
use App\Models\User;
use App\Services\MemberCardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CardController extends Controller
{
    public function __construct(private MemberCardService $memberCards) {}

    public function index(Request $request): View
    {
        return view('admin.cards.index', [
            'cards' => MemberCard::query()
                ->with('user')
                ->when($request->string('q')->toString(), function ($query, string $q): void {
                    $query->where('card_number', 'like', '%'.$q.'%')
                        ->orWhereHas('user', fn ($userQuery) => $userQuery
                            ->whereLike('name', '%'.$q.'%')
                            ->orWhereLike('username', '%'.$q.'%'));
                })
                ->latest()
                ->paginate(12)
                ->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.cards.create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['password']);
        $data['password'] = $request->string('password')->toString();

        $user = User::create($data);

        if ($user->isMember()) {
            $this->memberCards->issue($user);
        }

        return redirect()->route('admin.cards.index')->with('success', 'Akun berhasil dibuat. Kartu anggota dibuat otomatis.');
    }

    public function edit(MemberCard $card): View
    {
        return view('admin.cards.edit', [
            'card' => $card->load('user'),
        ]);
    }

    public function update(UpdateCardRequest $request, MemberCard $card): RedirectResponse
    {
        $this->memberCards->update($card, $request->validated());

        return redirect()->route('admin.cards.index')->with('success', 'Kartu anggota berhasil diperbarui.');
    }

    public function markLost(MemberCard $card): RedirectResponse
    {
        $this->memberCards->markLost($card);

        return back()->with('success', 'Kartu ditandai hilang. Status akun pengguna dinonaktifkan.');
    }

    public function replace(MemberCard $card): RedirectResponse
    {
        $replacement = $this->memberCards->replace($card);

        return back()->with('success', 'Kartu baru diterbitkan: '.$replacement->card_number.'. Akun pengguna diaktifkan kembali.');
    }
}
