@extends('layouts.member')

@section('title', 'Booking Buku')

@section('content')
    <x-page-header title="Booking Buku" subtitle="Antrean buku yang sedang dipinjam anggota lain. Admin akan menghubungi Anda saat buku tersedia.">
        <x-slot:actions>
            <a href="{{ route('member.books.index') }}" class="btn btn-primary">
                <x-icon name="search" class="h-4 w-4" />
                Cari Buku
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="flex items-start gap-3 rounded-xl border border-accent/25 bg-accent/10 px-4 py-3 text-xs text-accent-bright">
        <x-icon name="calendar" class="mt-0.5 h-4 w-4 shrink-0" />
        <p>Booking menjaga posisi antrean Anda. Buka halaman detail buku yang sedang dipinjam, lalu pilih <strong>Booking Buku Ini</strong>.</p>
    </div>

    @if ($reservations->isEmpty())
        <x-empty-state title="Belum ada booking" description="Saat buku yang Anda inginkan sedang dipinjam, ajukan booking agar masuk antrean." icon="calendar">
            <x-slot:action>
                <a href="{{ route('member.books.index') }}" class="btn btn-primary btn-sm">Jelajahi Katalog</a>
            </x-slot:action>
        </x-empty-state>
    @else
        <div class="stagger grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($reservations as $reservation)
                <article class="surface-card flex h-full flex-col overflow-hidden">
                    <div class="flex items-start gap-3 p-4">
                        <div class="h-20 w-14 shrink-0 overflow-hidden rounded-lg border border-line bg-surface-2">
                            @if ($reservation->book?->cover_path)
                                <img src="{{ asset('storage/'.$reservation->book->cover_path) }}" alt="Sampul {{ $reservation->book->title }}" class="h-full w-full object-cover" loading="lazy">
                            @else
                                <div class="grid h-full w-full place-items-center text-[0.6rem] font-bold uppercase tracking-wider text-muted">PPS</div>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <a href="{{ route('member.books.show', $reservation->book) }}" class="line-clamp-2 text-sm font-semibold text-ink hover:text-accent">
                                    {{ $reservation->book?->title }}
                                </a>
                                <x-status-badge :status="$reservation->status" />
                            </div>
                            <p class="mt-1 text-[0.7rem] text-muted">Diajukan {{ $reservation->created_at?->diffForHumans() }}</p>
                        </div>
                    </div>

                    <div class="mt-auto space-y-2 border-t border-line/70 px-4 py-3 text-xs">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-muted">Berlaku hingga</span>
                            <span class="font-semibold text-body">
                                {{ $reservation->reserved_until?->translatedFormat('d M Y') ?? 'Menunggu konfirmasi' }}
                            </span>
                        </div>
                        @if ($reservation->processor)
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-muted">Diproses oleh</span>
                                <span class="text-body">{{ $reservation->processor->name }}</span>
                            </div>
                        @endif
                    </div>

                    @if (in_array($reservation->status, ['pending', 'approved'], true))
                        <div class="border-t border-line/70 p-3">
                            <form method="POST" action="{{ route('member.reservations.cancel', $reservation) }}" data-confirm="Batalkan booking buku ini?">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm w-full">
                                    <x-icon name="x" class="h-3.5 w-3.5" />
                                    Batalkan Booking
                                </button>
                            </form>
                        </div>
                    @endif
                </article>
            @endforeach
        </div>

        {{ $reservations->links('partials.pagination') }}
    @endif
@endsection
