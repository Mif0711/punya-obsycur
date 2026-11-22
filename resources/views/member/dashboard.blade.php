@extends('layouts.member')

@section('title', 'Dashboard')

@section('content')
    <section class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_22rem]">
        <div class="relative overflow-hidden rounded-2xl border border-line bg-gradient-to-br from-surface-2 via-surface to-canvas p-6 sm:p-8">
            <div class="pointer-events-none absolute -right-10 -top-16 h-48 w-48 rounded-full bg-accent/10 blur-3xl"></div>
            <div class="relative">
                <p class="inline-flex items-center gap-2 rounded-full border border-accent/25 bg-accent/10 px-3 py-1 text-[0.65rem] font-semibold uppercase tracking-[0.18em] text-accent-bright">
                    <x-icon name="sparkles" class="h-3.5 w-3.5" />
                    Ruang baca Anda
                </p>
                <h1 class="mt-4 text-2xl font-bold tracking-tight text-ink sm:text-3xl">
                    Halo, {{ $user->name }}.
                </h1>
                <p class="mt-2 max-w-xl text-sm leading-relaxed text-muted">
                    @if ($stats['overdue'] > 0)
                        Anda memiliki <span class="font-semibold text-danger">{{ $stats['overdue'] }} buku terlambat</span>. Segera kembalikan agar tidak terkena denda tambahan.
                    @elseif ($stats['ongoing'] > 0)
                        Anda sedang meminjam <span class="font-semibold text-accent-bright">{{ $stats['ongoing'] }} buku</span>. Selamat membaca!
                    @else
                        Belum ada peminjaman aktif. Jelajahi katalog dan temukan bacaan berikutnya.
                    @endif
                </p>
                <div class="mt-5 flex flex-wrap gap-2">
                    <a href="{{ route('member.books.index') }}" class="btn btn-primary">
                        <x-icon name="search" class="h-4 w-4" />
                        Jelajahi Katalog
                    </a>
                    <a href="{{ route('member.recommendations.index') }}" class="btn btn-ghost">
                        <x-icon name="sparkles" class="h-4 w-4" />
                        Rekomendasi Saya
                    </a>
                </div>
            </div>
        </div>

        <div class="relative overflow-hidden rounded-2xl border border-accent/20 bg-gradient-to-br from-[#0d2030] via-[#121a2e] to-[#1b1230] p-5">
            <div class="pointer-events-none absolute -right-8 -top-10 h-32 w-32 rounded-full bg-violet/20 blur-2xl"></div>
            <div class="relative flex items-start justify-between">
                <div>
                    <p class="text-[0.62rem] font-semibold uppercase tracking-[0.24em] text-white/45">Kartu Anggota</p>
                    <p class="mt-1 text-sm font-bold text-white">Perpusa Library</p>
                </div>
                <x-icon name="card" class="h-5 w-5 text-accent-bright" />
            </div>
            <p class="relative mt-6 font-mono text-base font-bold tracking-wider text-white">
                {{ $memberCard?->card_number ?? 'KARTU BELUM TERBIT' }}
            </p>
            <div class="relative mt-4 flex items-end justify-between gap-3">
                <div>
                    <p class="text-[0.6rem] uppercase tracking-[0.2em] text-white/40">Nama</p>
                    <p class="text-sm font-semibold text-white">{{ $user->name }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[0.6rem] uppercase tracking-[0.2em] text-white/40">Status</p>
                    <x-status-badge :status="$memberCard?->status ?? 'pending'" :label="$memberCard?->status === 'active' ? 'Aktif' : null" />
                </div>
            </div>
            <p class="relative mt-3 text-[0.65rem] leading-relaxed text-white/45">
                @if ($user->nickname)
                    Panggilan: {{ $user->nickname }}<br>
                @endif
                {{ $user->birth_place ?: '—' }}{{ $user->birth_date ? ', '.$user->birth_date->translatedFormat('d M Y') : '' }}
                @if ($user->school)
                    <br>Sekolah: {{ $user->school }}
                @endif
            </p>
            <p class="relative mt-2 text-[0.65rem] text-white/35">
                Terbit {{ $memberCard?->issued_at?->translatedFormat('d M Y') ?? '—' }} · Tunjukkan kartu saat meminjam.
            </p>
        </div>
    </section>

    <section class="stagger grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Sedang Dipinjam" :value="number_format($stats['ongoing'], 0, ',', '.')" hint="Buku aktif" icon="swap" tone="accent" />
        <x-stat-card label="Sudah Dikembalikan" :value="number_format($stats['returned'], 0, ',', '.')" hint="Total riwayat" icon="check" tone="success" />
        <x-stat-card label="Terlambat" :value="number_format($stats['overdue'], 0, ',', '.')" hint="Perlu dikembalikan" icon="alert" tone="danger" />
        <x-stat-card label="Denda Belum Lunas" :value="'Rp '.number_format((float) $stats['unpaid_fines'], 0, ',', '.')" hint="Bayar ke petugas" icon="wallet" tone="warning" />
    </section>

    <div class="grid gap-6 xl:grid-cols-2">
        <section class="space-y-4">
            <div class="flex items-end justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-ink">Peminjaman Terakhir</h2>
                    <p class="text-xs text-muted">Pantau tenggat dan status pengembalian.</p>
                </div>
                <a href="{{ route('member.loans.index') }}" class="text-xs font-semibold text-accent hover:text-accent-bright">Semua →</a>
            </div>

            @if ($loans->isEmpty())
                <x-empty-state title="Belum ada peminjaman" description="Mulai dari katalog dan ajukan peminjaman buku pertama Anda." icon="swap">
                    <x-slot:action>
                        <a href="{{ route('member.books.index') }}" class="btn btn-primary btn-sm">Jelajahi Katalog</a>
                    </x-slot:action>
                </x-empty-state>
            @else
                <div class="surface-card divide-y divide-line/70">
                    @foreach ($loans as $loan)
                        <div class="flex items-center gap-3 p-4">
                            <div class="h-14 w-10 shrink-0 overflow-hidden rounded-md border border-line bg-surface-2">
                                @if ($loan->book?->cover_path)
                                    <img src="{{ asset('storage/'.$loan->book->cover_path) }}" alt="" class="h-full w-full object-cover" loading="lazy">
                                @else
                                    <div class="grid h-full w-full place-items-center text-[0.55rem] font-bold text-muted">PPS</div>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('member.books.show', $loan->book) }}" class="truncate text-sm font-semibold text-ink hover:text-accent">{{ $loan->book?->title }}</a>
                                <p class="text-xs text-muted">Tenggat {{ $loan->due_date?->translatedFormat('d M Y') }}</p>
                            </div>
                            <x-status-badge :status="$loan->isOverdue() ? 'overdue' : $loan->status" />
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="space-y-4">
            <div class="flex items-end justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-ink">Booking Buku</h2>
                    <p class="text-xs text-muted">Buku yang Anda antrekan.</p>
                </div>
                <a href="{{ route('member.reservations.index') }}" class="text-xs font-semibold text-accent hover:text-accent-bright">Semua →</a>
            </div>

            @if ($reservations->isEmpty())
                <x-empty-state title="Belum ada booking" description="Booking memungkinkan Anda mengantre buku yang sedang dipinjam." icon="calendar" />
            @else
                <div class="surface-card divide-y divide-line/70">
                    @foreach ($reservations as $reservation)
                        <div class="flex items-center gap-3 p-4">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg border border-line bg-white/[0.02] text-violet">
                                <x-icon name="calendar" class="h-4 w-4" />
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-ink">{{ $reservation->book?->title }}</p>
                                <p class="text-xs text-muted">
                                    @if ($reservation->reserved_until)
                                        Hingga {{ $reservation->reserved_until->translatedFormat('d M Y') }}
                                    @else
                                        Diajukan {{ $reservation->created_at?->diffForHumans() }}
                                    @endif
                                </p>
                            </div>
                            <x-status-badge :status="$reservation->status" />
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>

    <section class="space-y-4">
        <div class="flex items-end justify-between gap-3">
            <div>
                <h2 class="flex items-center gap-2 text-base font-bold text-ink">
                    <x-icon name="sparkles" class="h-4 w-4 text-accent" />
                    Rekomendasi untuk Anda
                </h2>
                <p class="text-xs text-muted">Dipilih dari riwayat, rating, tren, penghargaan, dan tema musiman.</p>
            </div>
            <a href="{{ route('member.recommendations.index') }}" class="text-xs font-semibold text-accent hover:text-accent-bright">Lihat semua →</a>
        </div>

        @if ($recommendedBooks->isEmpty())
            <x-empty-state title="Rekomendasi belum tersedia" description="Pinjam beberapa buku atau lakukan pencarian agar sistem mengenal selera Anda." icon="sparkles" />
        @else
            <div class="stagger grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($recommendedBooks as $book)
                    <x-book-card :book="$book" show-reasons compact />
                @endforeach
            </div>
        @endif
    </section>
@endsection
