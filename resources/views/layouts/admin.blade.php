<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Admin') · {{ config('app.name', 'Perpusa') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full antialiased">
    <div class="app-shell min-h-screen">
        {{-- Mobile overlay --}}
        <div data-sidebar-overlay data-sidebar-close class="fixed inset-0 z-40 hidden bg-black/60 backdrop-blur-sm lg:hidden" aria-hidden="true"></div>

        {{-- Sidebar --}}
        <aside data-sidebar class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col border-r border-line bg-[#0a0e18]/95 backdrop-blur-xl transition-transform duration-300 lg:translate-x-0">
            <div class="flex items-center gap-3 border-b border-line px-5 py-5">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br from-accent to-violet text-white shadow-[0_0_24px_-6px_rgba(6,182,212,0.7)]">
                    <x-icon name="library" class="h-5 w-5" />
                </span>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold tracking-wide text-ink">Perpusa</p>
                    <p class="truncate text-[0.68rem] uppercase tracking-[0.2em] text-muted">Panel Admin</p>
                </div>
                <button type="button" data-sidebar-close class="grid h-8 w-8 place-items-center rounded-lg text-muted transition hover:bg-white/5 hover:text-ink lg:hidden" aria-label="Tutup menu">
                    <x-icon name="x" class="h-4 w-4" />
                </button>
            </div>

            <nav class="flex-1 space-y-6 overflow-y-auto px-4 py-5">
                <div class="space-y-1">
                    <p class="px-3 pb-1 text-[0.62rem] font-bold uppercase tracking-[0.22em] text-muted/70">Utama</p>
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'nav-link-active' : '' }}">
                        <x-icon name="dashboard" class="h-4.5 w-4.5" />
                        Dashboard
                    </a>
                </div>

                <div class="space-y-1">
                    <p class="px-3 pb-1 text-[0.62rem] font-bold uppercase tracking-[0.22em] text-muted/70">Menu Buku</p>
                    <a href="{{ route('admin.genres.index') }}" class="nav-link {{ request()->routeIs('admin.genres.*') ? 'nav-link-active' : '' }}">
                        <x-icon name="tag" class="h-4.5 w-4.5" />
                        Genre
                    </a>
                    <a href="{{ route('admin.books.index') }}" class="nav-link {{ request()->routeIs('admin.books.*') ? 'nav-link-active' : '' }}">
                        <x-icon name="book" class="h-4.5 w-4.5" />
                        Buku
                        <x-nav-badge key="admin.books" :count="$navBadges['admin.books'] ?? 0" tone="accent" />
                    </a>
                    <a href="{{ route('admin.publishers.index') }}" class="nav-link {{ request()->routeIs('admin.publishers.*') ? 'nav-link-active' : '' }}">
                        <x-icon name="inbox" class="h-4.5 w-4.5" />
                        Penerbit
                    </a>
                    <a href="{{ route('admin.authors.index') }}" class="nav-link {{ request()->routeIs('admin.authors.*') ? 'nav-link-active' : '' }}">
                        <x-icon name="user" class="h-4.5 w-4.5" />
                        Pengarang
                    </a>
                </div>

                <div class="space-y-1">
                    <p class="px-3 pb-1 text-[0.62rem] font-bold uppercase tracking-[0.22em] text-muted/70">Sirkulasi</p>
                    <a href="{{ route('admin.loans.index') }}" class="nav-link {{ request()->routeIs('admin.loans.*') ? 'nav-link-active' : '' }}">
                        <x-icon name="swap" class="h-4.5 w-4.5" />
                        Peminjaman
                        <x-nav-badge key="admin.loans" :count="$navBadges['admin.loans'] ?? 0" />
                    </a>
                    <a href="{{ route('admin.requests.index') }}" class="nav-link {{ request()->routeIs('admin.requests.*') ? 'nav-link-active' : '' }}">
                        <x-icon name="inbox" class="h-4.5 w-4.5" />
                        Permintaan
                        <x-nav-badge key="admin.requests" :count="$navBadges['admin.requests'] ?? 0" />
                    </a>
                </div>

                <div class="space-y-1">
                    <p class="px-3 pb-1 text-[0.62rem] font-bold uppercase tracking-[0.22em] text-muted/70">Layanan</p>
                    <a href="{{ route('admin.book-requests.index') }}" class="nav-link {{ request()->routeIs('admin.book-requests.*') ? 'nav-link-active' : '' }}">
                        <x-icon name="bookmark" class="h-4.5 w-4.5" />
                        Buku Baru
                        <x-nav-badge key="admin.book-requests" :count="$navBadges['admin.book-requests'] ?? 0" />
                    </a>
                    <a href="{{ route('admin.cards.index') }}" class="nav-link {{ request()->routeIs('admin.cards.*') ? 'nav-link-active' : '' }}">
                        <x-icon name="card" class="h-4.5 w-4.5" />
                        Kartu Baru
                        <x-nav-badge key="admin.cards" :count="$navBadges['admin.cards'] ?? 0" />
                    </a>
                </div>

                <div class="space-y-1">
                    <p class="px-3 pb-1 text-[0.62rem] font-bold uppercase tracking-[0.22em] text-muted/70">Sistem</p>
                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'nav-link-active' : '' }}">
                        <x-icon name="users" class="h-4.5 w-4.5" />
                        Pengguna
                        <x-nav-badge key="admin.users" :count="$navBadges['admin.users'] ?? 0" tone="accent" />
                    </a>
                    <a href="{{ route('admin.import-export.index') }}" class="nav-link {{ request()->routeIs('admin.import-export.*') ? 'nav-link-active' : '' }}">
                        <x-icon name="upload" class="h-4.5 w-4.5" />
                        Import / Ekspor
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'nav-link-active' : '' }}">
                        <x-icon name="settings" class="h-4.5 w-4.5" />
                        Pengaturan
                    </a>
                </div>
            </nav>

            <div class="border-t border-line p-3">
                <div class="rounded-xl border border-line bg-white/[0.02] p-1">
                    <x-account-menu id="admin-account-menu" variant="sidebar" placement="up" />
                </div>
            </div>
        </aside>

        {{-- Main column --}}
        <div class="lg:pl-72">
            <header class="sticky top-0 z-30 border-b border-line bg-canvas/80 backdrop-blur-xl">
                <div class="flex items-center gap-3 px-4 py-3.5 sm:px-6 lg:px-8">
                    <button type="button" data-sidebar-open class="grid h-9 w-9 place-items-center rounded-lg border border-line text-body transition hover:bg-white/5 hover:text-ink lg:hidden" aria-label="Buka menu">
                        <x-icon name="menu" class="h-5 w-5" />
                    </button>

                    <div class="min-w-0 flex-1">
                        <p class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-muted">Panel Admin</p>
                        <p class="truncate text-sm font-semibold text-ink">@yield('title', 'Dashboard')</p>
                    </div>

                    <form method="GET" action="{{ route('admin.books.index') }}" class="hidden items-center gap-2 md:flex">
                        <div class="relative">
                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-muted">
                                <x-icon name="search" class="h-4 w-4" />
                            </span>
                            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari buku…"
                                class="field-input w-56 pl-9 py-2 text-sm" data-search-input />
                        </div>
                    </form>

                    <a href="{{ route('admin.loans.create') }}" class="btn btn-primary btn-sm hidden sm:inline-flex">
                        <x-icon name="plus" class="h-4 w-4" />
                        Pinjam Baru
                    </a>
                </div>
            </header>

            <main class="page-enter px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                <div class="mx-auto max-w-[92rem] space-y-6">
                    @include('partials.flash')
                    @yield('content')
                </div>
            </main>

            <footer class="px-4 pb-6 text-center text-[0.7rem] text-muted/60 sm:px-6 lg:px-8">
                Perpusa · Sistem Perpustakaan Digital · {{ now()->year }}
            </footer>
        </div>
    </div>
</body>
</html>
