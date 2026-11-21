@extends('layouts.admin')

@section('title', 'Permintaan')

@section('content')
    <x-page-header title="Permintaan" subtitle="Tinjau permintaan peminjaman dan booking buku dari anggota.">
        <x-slot:actions>
            <a href="{{ route('admin.loans.index') }}" class="btn btn-ghost">
                <x-icon name="swap" class="h-4 w-4" />
                Semua Peminjaman
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 xl:grid-cols-2">
        <section class="space-y-4">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="flex items-center gap-2 text-base font-bold text-ink">
                        <x-icon name="swap" class="h-4 w-4 text-accent" />
                        Permintaan Peminjaman
                    </h2>
                    <p class="text-xs text-muted">Menunggu persetujuan admin.</p>
                </div>
                <span class="badge border-warning/25 bg-warning/10 text-warning">{{ $loanRequests->count() }} menunggu</span>
            </div>

            @forelse ($loanRequests as $loan)
                <article class="surface-card p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="truncate font-semibold text-ink">{{ $loan->book?->title }}</p>
                            <p class="text-xs text-muted">{{ $loan->user?->name }} · @ {{ $loan->user?->username }}</p>
                            <p class="mt-1 text-[0.7rem] text-muted">Diajukan {{ $loan->created_at?->diffForHumans() }}</p>
                        </div>
                        <x-status-badge status="pending" />
                    </div>
                    @if ($loan->note)
                        <p class="mt-3 rounded-lg border border-line bg-white/[0.02] px-3 py-2 text-xs text-body">{{ $loan->note }}</p>
                    @endif
                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        <form method="POST" action="{{ route('admin.requests.loans.approve', $loan) }}">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">
                                <x-icon name="check" class="h-3.5 w-3.5" />
                                Setujui
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.requests.loans.reject', $loan) }}" data-confirm="Tolak permintaan peminjaman ini?">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">
                                <x-icon name="x" class="h-3.5 w-3.5" />
                                Tolak
                            </button>
                        </form>
                        <a href="{{ route('admin.loans.show', $loan) }}" class="btn btn-ghost btn-sm ml-auto">Detail</a>
                    </div>
                </article>
            @empty
                <x-empty-state title="Tidak ada permintaan peminjaman" description="Semua permintaan peminjaman sudah diproses." icon="inbox" />
            @endforelse
        </section>

        <section class="space-y-4">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="flex items-center gap-2 text-base font-bold text-ink">
                        <x-icon name="calendar" class="h-4 w-4 text-violet" />
                        Permintaan Booking
                    </h2>
                    <p class="text-xs text-muted">Antrean booking buku oleh anggota.</p>
                </div>
                <span class="badge border-accent/25 bg-accent/10 text-accent-bright">{{ $reservations->count() }} menunggu</span>
            </div>

            @forelse ($reservations as $reservation)
                <article class="surface-card p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="truncate font-semibold text-ink">{{ $reservation->book?->title }}</p>
                            <p class="text-xs text-muted">{{ $reservation->user?->name }} · @ {{ $reservation->user?->username }}</p>
                            <p class="mt-1 text-[0.7rem] text-muted">Diajukan {{ $reservation->created_at?->diffForHumans() }}</p>
                        </div>
                        <x-status-badge status="pending" />
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        <form method="POST" action="{{ route('admin.requests.reservations.approve', $reservation) }}">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">
                                <x-icon name="check" class="h-3.5 w-3.5" />
                                Setujui
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.requests.reservations.reject', $reservation) }}" data-confirm="Batalkan booking ini?">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">
                                <x-icon name="x" class="h-3.5 w-3.5" />
                                Tolak
                            </button>
                        </form>
                    </div>
                </article>
            @empty
                <x-empty-state title="Tidak ada booking" description="Belum ada permintaan booking yang menunggu." icon="calendar" />
            @endforelse
        </section>
    </div>

    <section class="space-y-4">
        <div>
            <h2 class="text-base font-bold text-ink">Riwayat Booking Terbaru</h2>
            <p class="text-xs text-muted">Booking yang sudah diproses dalam 20 data terakhir.</p>
        </div>

        @if ($recentReservations->isEmpty())
            <x-empty-state title="Belum ada riwayat" description="Riwayat booking akan tampil di sini setelah diproses." icon="clock" />
        @else
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Anggota</th>
                            <th>Buku</th>
                            <th>Status</th>
                            <th>Diproses</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentReservations as $reservation)
                            <tr>
                                <td>
                                    <p class="font-semibold text-ink">{{ $reservation->user?->name }}</p>
                                    <p class="text-xs text-muted">@ {{ $reservation->user?->username }}</p>
                                </td>
                                <td>{{ $reservation->book?->title }}</td>
                                <td><x-status-badge :status="$reservation->status" /></td>
                                <td class="text-xs text-muted">{{ $reservation->updated_at?->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
