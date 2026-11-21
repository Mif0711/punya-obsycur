@props(['id', 'placement' => 'down', 'variant' => 'header'])

@php
    /** @var \App\Models\User $accountUser */
    $accountUser = auth()->user();
    $initial = strtoupper(substr($accountUser->name, 0, 1));
    $isSidebar = $variant === 'sidebar';
@endphp

<div class="relative {{ $isSidebar ? 'w-full' : '' }}" data-account-menu>
    <button type="button" data-menu-toggle="{{ $id }}" aria-haspopup="menu" aria-expanded="false"
        @class([
            'group flex items-center transition',
            'w-full gap-3 rounded-xl border border-transparent p-2 text-left hover:border-line hover:bg-white/[0.04]' => $isSidebar,
            'gap-2 rounded-full border border-line bg-white/[0.03] py-1 pl-1 pr-2 hover:bg-white/[0.06]' => ! $isSidebar,
        ])>
        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-gradient-to-br from-accent to-violet text-xs font-bold text-white shadow-[0_0_18px_-8px_rgba(6,182,212,0.9)]">
            {{ $initial }}
        </span>
        <span @class([
            'min-w-0 flex-1' => $isSidebar,
            'hidden min-w-0 xl:block xl:max-w-[4.5rem]' => ! $isSidebar,
        ])>
            <span class="block truncate text-sm font-semibold text-ink">{{ $accountUser->name }}</span>
            <span @class(['hidden' => ! $isSidebar, 'block truncate text-[0.7rem] text-muted' => $isSidebar])>
                {{ '@'.$accountUser->username }}
            </span>
        </span>
        <x-icon name="chevron-down" class="h-3.5 w-3.5 shrink-0 text-muted transition-transform duration-200 group-aria-expanded:rotate-180" />
    </button>

    <div id="{{ $id }}" role="menu" @class([
        'absolute z-50 w-60 rounded-xl border border-line bg-surface p-1.5 shadow-[0_20px_50px_-20px_rgba(0,0,0,0.95)]',
        'bottom-full left-0 mb-2' => $placement === 'up',
        'right-0 top-full mt-2' => $placement === 'down',
        'hidden',
    ])>
        <div class="border-b border-line px-2.5 py-2">
            <p class="truncate text-sm font-semibold text-ink">{{ $accountUser->name }}</p>
            <p class="truncate text-[0.7rem] text-muted">
                {{ '@'.$accountUser->username }} · {{ $accountUser->isAdmin() ? 'Admin' : 'Anggota' }}
            </p>
        </div>

        <a href="{{ route('account.edit') }}" role="menuitem"
            class="mt-1 flex items-center gap-2 rounded-lg px-2.5 py-2 text-sm text-body transition hover:bg-white/5 hover:text-ink">
            <x-icon name="settings" class="h-4 w-4 text-accent" />
            Pengaturan akun
        </a>

        <form method="POST" action="{{ route('logout') }}" role="none">
            @csrf
            <button type="submit" role="menuitem"
                class="flex w-full items-center gap-2 rounded-lg px-2.5 py-2 text-left text-sm text-body transition hover:bg-danger/10 hover:text-danger">
                <x-icon name="logout" class="h-4 w-4" />
                Keluar
            </button>
        </form>
    </div>
</div>
