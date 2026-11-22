<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk · {{ config('app.name', 'Perpusa') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="portal-opening min-h-full bg-portal-bg text-body antialiased">
    <div class="portal-scene">
        <div class="portal-grid" aria-hidden="true"></div>
        <div class="portal-aurora" aria-hidden="true"></div>
        <div class="portal-stars" aria-hidden="true"></div>

        <main class="relative z-10 mx-auto grid w-full max-w-6xl items-center gap-10 px-6 py-10 lg:grid-cols-[0.9fr_1.1fr] lg:gap-12 lg:px-10">
            <!-- Circular portal with the login form -->
            <section class="portal-form-panel lg:order-2">
                <div class="relative flex w-full items-center justify-center">
                    <div class="relative">
                        <div class="portal-form-orb">
                            <svg class="portal-outline" viewBox="0 0 100 100" aria-hidden="true">
                                <circle class="portal-outline-path" cx="50" cy="50" r="48" pathLength="100"></circle>
                            </svg>
                            <div class="portal-water" aria-hidden="true">
                                <span class="portal-water-vortex"></span>
                            </div>
                            <div class="portal-form-stage">
                                <form method="POST" action="{{ route('login.store') }}" class="portal-form-inner space-y-2.5 sm:space-y-3" data-login-form>
                                    @csrf

                                    <div class="text-center">
                                        <p class="text-[0.58rem] font-semibold uppercase tracking-[0.32em] text-portal-glow/80">Perpusa Portal</p>
                                        <h1 class="mt-1 text-lg font-bold tracking-tight text-white sm:text-2xl">Masuk</h1>
                                        <p class="mt-1 hidden text-[0.72rem] leading-relaxed text-white/45 sm:block">
                                            Gunakan username dan kata sandi akun perpustakaan Anda.
                                        </p>
                                    </div>

                                    <div>
                                        <label for="username" class="sr-only">Username</label>
                                        <div class="relative">
                                            <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-white/30">
                                                <x-icon name="user" class="h-4 w-4" />
                                            </span>
                                            <input id="username" name="username" type="text" value="{{ old('username') }}" required autofocus autocomplete="username"
                                                @class(['portal-input portal-input-has-icon', 'portal-input-error' => $errors->has('username')])
                                                placeholder="Username" />
                                        </div>
                                    </div>

                                    <div>
                                        <label for="password" class="sr-only">Kata sandi</label>
                                        <div class="relative">
                                            <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-white/30">
                                                <x-icon name="lock" class="h-4 w-4" />
                                            </span>
                                            <input id="password" name="password" type="password" required autocomplete="current-password"
                                                @class(['portal-input portal-input-has-icon portal-input-has-toggle', 'portal-input-error' => $errors->has('password')])
                                                placeholder="Kata sandi" />
                                            <button type="button" data-password-toggle="password" aria-label="Tampilkan kata sandi"
                                                class="absolute right-2 top-1/2 grid h-8 w-8 -translate-y-1/2 place-items-center rounded-full text-white/35 transition hover:bg-white/5 hover:text-white/70">
                                                <x-icon name="eye" class="h-4 w-4" />
                                            </button>
                                        </div>
                                    </div>

                                    <label class="flex items-center justify-center gap-2 text-[0.72rem] text-white/50">
                                        <input type="checkbox" name="remember" value="1"
                                            class="h-3.5 w-3.5 rounded border-white/15 bg-white/5 text-accent focus:ring-accent/40" />
                                        Ingat saya
                                    </label>

                                    <button type="submit" class="btn btn-primary w-full rounded-full py-2 text-sm">
                                        Masuk
                                        <x-icon name="arrow-right" class="h-4 w-4" />
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="portal-float portal-float--top" aria-hidden="true">
                            <span class="grid h-6 w-6 place-items-center rounded-full bg-portal-glow/15 text-portal-glow">
                                <x-icon name="sparkles" class="h-3.5 w-3.5" />
                            </span>
                            8 sinyal rekomendasi
                        </div>
                        <div class="portal-float portal-float--bottom" aria-hidden="true">
                            <span class="grid h-6 w-6 place-items-center rounded-full bg-violet/20 text-violet">
                                <x-icon name="card" class="h-3.5 w-3.5" />
                            </span>
                            Kartu anggota otomatis
                        </div>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="mt-7 w-full rounded-2xl border border-portal-outer/30 bg-portal-outer/10 px-4 py-3 text-sm text-rose-200" role="alert" aria-live="polite">
                        <div class="flex items-start gap-2">
                            <x-icon name="alert" class="mt-0.5 h-4 w-4 shrink-0" />
                            <div class="space-y-1">
                                @foreach ($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

            </section>

            <!-- Supporting content so the page feels alive around the portal -->
            <section class="portal-copy relative z-10 max-w-lg lg:order-1">
                <span class="badge border-portal-glow/25 bg-portal-glow/10 text-portal-glow">
                    <x-icon name="sparkles" class="h-3.5 w-3.5" />
                    Perpustakaan modern
                </span>

                <h2 class="mt-5 text-3xl font-bold leading-tight tracking-tight text-white sm:text-5xl">
                    Satu pintu untuk
                    <span class="bg-gradient-to-r from-portal-glow via-white to-portal-outer bg-clip-text text-transparent">semua bacaan</span>
                    Anda.
                </h2>
                <p class="mt-4 text-sm leading-relaxed text-white/55">
                    Kelola katalog, peminjaman, denda, kartu anggota, dan rekomendasi cerdas dalam satu portal perpustakaan.
                </p>

                <div class="mt-8 space-y-3">
                    <div class="flex items-center gap-3 rounded-2xl border border-white/8 bg-white/[0.03] px-4 py-3">
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-portal-glow/25 bg-portal-glow/10 text-portal-glow">
                            <x-icon name="book" class="h-4 w-4" />
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-white">Katalog digital</p>
                            <p class="text-xs text-white/45">Cari dan jelajahi koleksi kapan saja.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 rounded-2xl border border-white/8 bg-white/[0.03] px-4 py-3">
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-violet/30 bg-violet/15 text-violet">
                            <x-icon name="swap" class="h-4 w-4" />
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-white">Peminjaman &amp; denda</p>
                            <p class="text-xs text-white/45">Pengajuan, persetujuan, dan pengembalian tercatat rapi.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 rounded-2xl border border-white/8 bg-white/[0.03] px-4 py-3">
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-portal-outer/30 bg-portal-outer/10 text-portal-outer">
                            <x-icon name="trending" class="h-4 w-4" />
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-white">Rekomendasi cerdas</p>
                            <p class="text-xs text-white/45">Delapan sinyal untuk menemukan bacaan berikutnya.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-7 flex flex-wrap gap-2 text-[0.68rem] text-white/35">
                    <span class="badge border-white/10 bg-white/5 text-white/50">CSV import &amp; ekspor</span>
                    <span class="badge border-white/10 bg-white/5 text-white/50">Kartu anggota</span>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
