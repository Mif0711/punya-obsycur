<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\MemberCardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(private MemberCardService $memberCards) {}

    public function index(Request $request): View
    {
        return view('admin.users.index', [
            'users' => User::query()
                ->with('memberCard')
                ->when($request->string('q')->toString(), function ($query, string $q): void {
                    $query->whereLike('name', '%'.$q.'%')
                        ->orWhereLike('username', '%'.$q.'%');
                })
                ->latest()
                ->paginate(12)
                ->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['password']);
        $data['password'] = $request->string('password')->toString();

        $user = User::create($data);

        if ($user->isMember()) {
            $this->memberCards->issue($user);
        }

        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil dibuat.');
    }

    public function edit(User $user): View
    {
        $user->load('memberCard');

        return view('admin.users.edit', ['user' => $user]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $password = $data['password'] ?? null;
        $previousRole = $user->role;

        unset($data['password']);

        $user->fill($data);

        if ($password !== null && $password !== '') {
            $user->password = $password;
        }

        $user->save();

        if ($user->isMember() && $previousRole !== User::ROLE_USER && ! $user->memberCard()->exists()) {
            $this->memberCards->issue($user);
        } elseif ($user->isAdmin() && $previousRole === User::ROLE_USER) {
            $user->memberCard()->delete();
        }

        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil diperbarui.');
    }
}
