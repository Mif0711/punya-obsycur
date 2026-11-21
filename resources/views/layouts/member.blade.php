<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · {{ config('app.name', 'Perpusa') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full antialiased">
    <div class="app-shell flex min-h-screen flex-col">
        <header class="sticky top-0 z-30 border-b border-line bg-canvas/85 backdrop-blur-xl">
            <div class="mx-auto flex w-full max-w-7xl items-center gap-4 px-4 py-3 sm:px-6 lg:px-8">
                <a href="{{ route('member.dashboard') }}" class="flex items-center gap-2.5">
                    <span class="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-accent to-violet text-white shadow-[0_0_22px_-8px_rgba(6,182,212,0.8)]">
                        <x-icon name="library" class="h-4.5 w-4.5" />
                    </span>
                    <div class="leading-tight">
                        <p class="text-sm font-bold text-ink">Perpusa</p>
                        <p class="text-[0.62rem] uppercase tracking-[0.2em] text-muted">Perpustakaan</p>
                    </div>
                </a>

                <nav class="ml-4 hidden shrink-0 items-center gap-0.5 xl:flex xl:gap-1">
                    @php
                        $links = [
                            ['route' => 'member.dashboard', 'label' => 'Dashboard', 'active' => 'member.dashboard', 'icon' => 'dashboard', 'badge' => null, 'tone' => 'danger'],
                            ['route' => 'member.books.index', 'label' => 'Katalog', 'active' => 'member.books.*', 'icon' => 'book', 'badge' => 'member.books', 'tone' => 'accent'],
                            ['route' => 'member.loans.index', 'label' => 'Peminjaman', 'active' => 'member.loans.*', 'icon' => 'swap', 'badge' => 'member.loans', 'tone' => 'danger'],
                            ['route' => 'member.reservations.index', 'label' => 'Booking', 'active' => 'member.reservations.*', 'icon' => 'calendar', 'badge' => 'member.reservations', 'tone' => 'danger'],
                            ['route' => 'member.book-requests.index', 'label' => 'Buku Baru', 'active' => 'member.book-requests.*', 'icon' => 'bookmark', 'badge' => 'member.book-requests', 'tone' => 'danger'],
                            ['route' => 'member.recommendations.index', 'label' => 'Rekomendasi', 'active' => 'member.recommendations.*', 'icon' => 'sparkles', 'badge' => 'member.recommendations', 'tone' => 'accent'],
                        ];
                    @endphp
                    @foreach ($links as $link)
                        <a href="{{ route($link['route']) }}"
                            class="inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap rounded-lg px-2.5 py-2 text-sm font-medium transition xl:px-3 {{ request()->routeIs($link['active']) ? 'bg-white/[0.06] text-white shadow-[inset_0_0_0_1px_rgba(6,182,212,0.18)]' : 'text-body hover:bg-white/5 hover:text-ink' }}">
                            <x-icon :name="$link['icon']" class="h-4 w-4 {{ request()->routeIs($link['active']) ? 'text-accent' : 'text-muted' }}" />
                            {{ $link['label'] }}
                            <x-nav-badge :key="$link['badge'] ?? ''" :count="$link['badge'] ? ($navBadges[$link['badge']] ?? 0) : 0" :tone="$link['tone'] ?? 'danger'" />
                        </a>
                    @endforeach
                </nav>

                <div class="ml-auto flex items-center gap-2">
                    <form method="GET" action="{{ route('member.books.index') }}" class="hidden md:block">
                        <div class="relative">
                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-muted">
                                <x-icon name="search" class="h-4 w-4" />
                            </span>
                            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari buku, penulis…"
                                class="field-input w-40 py-2 pl-9 text-sm xl:w-52" data-search-input />
                        </div>
                    </form>

                    <x-account-menu id="member-account-menu" />

                    <button type="button" data-menu-toggle="member-menu" class="grid h-9 w-9 place-items-center rounded-lg border border-line text-body transition hover:bg-white/5 xl:hidden" aria-label="Buka menu" aria-expanded="false">
                        <x-icon name="menu" class="h-5 w-5" />
                    </button>
                </div>
            </div>

            <div id="member-menu" class="hidden border-t border-line bg-surface/95 px-4 py-3 backdrop-blur-xl xl:hidden">
                <nav class="grid gap-1">
                    @foreach ($links as $link)
                        <a href="{{ route($link['route']) }}"
                            class="flex items-center gap-2 rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs($link['active']) ? 'bg-accent/10 text-white' : 'text-body hover:bg-white/5' }}">
                            <x-icon :name="$link['icon']" class="h-4 w-4 {{ request()->routeIs($link['active']) ? 'text-accent' : 'text-muted' }}" />
                            {{ $link['label'] }}
                            <x-nav-badge :key="$link['badge'] ?? ''" :count="$link['badge'] ? ($navBadges[$link['badge']] ?? 0) : 0" :tone="$link['tone'] ?? 'danger'" />
                        </a>
                    @endforeach
                </nav>
                <div class="mt-3 flex items-center justify-between gap-3 border-t border-line pt-3">
                    <a href="{{ route('account.edit') }}" class="flex items-center gap-2 rounded-lg p-1 transition hover:bg-white/5">
                        <span class="grid h-8 w-8 place-items-center rounded-full bg-gradient-to-br from-accent to-violet text-xs font-bold text-white">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                        <div class="leading-tight">
                            <p class="text-sm font-semibold text-ink">{{ auth()->user()->name }}</p>
                            <p class="text-[0.7rem] text-muted">@ {{ auth()->user()->username }}</p>
                        </div>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">
                            <x-icon name="logout" class="h-4 w-4" />
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main class="page-enter mx-auto w-full max-w-7xl flex-1 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
            <div class="space-y-6">
                @include('partials.flash')
                @yield('content')
            </div>
        </main>

        <footer class="border-t border-line/60 px-4 py-5 text-center text-[0.7rem] text-muted/60 sm:px-6 lg:px-8">
            Perpusa · Sistem Perpustakaan Digital · {{ now()->year }}
        </footer>
    </div>
</body>
</html>
