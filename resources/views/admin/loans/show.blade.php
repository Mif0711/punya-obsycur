@extends('layouts.admin')

@section('title', 'Detail Peminjaman')

@section('content')
    <div class="flex flex-wrap items-center gap-2 text-xs text-muted">
        <a href="{{ route('admin.loans.index') }}" class="hover:text-accent">Peminjaman</a>
        <x-icon name="chevron-right" class="h-3 w-3" />
        <span class="text-body">#{{ $loan->id }}</span>
    </div>

    <x-page-header :title="'Peminjaman #'.$loan->id" :subtitle="$loan->book?->title">
        <x-slot:actions>
            <x-status-badge :status="$loan->status" />
            <a href="{{ route('admin.loans.index') }}" class="btn btn-ghost">
                <x-icon name="arrow-left" class="h-4 w-4" />
                Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_22rem]">
        <div class="space-y-6">
            <section class="surface-card p-6">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="h-24 w-16 shrink-0 overflow-hidden rounded-lg border border-line bg-surface-2">
                            @if ($loan->book?->cover_path)
                                <img src="{{ asset('storage/'.$loan->book->cover_path) }}" alt="" class="h-full w-full object-cover">
                            @else
                                <div class="grid h-full w-full place-items-center text-xs font-bold text-muted">PPS</div>
                            @endif
                        </div>
                        <div>
                            <p class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-muted">Buku</p>
                            <h2 class="text-lg font-bold text-ink">{{ $loan->book?->title }}</h2>
                            <p class="text-sm text-muted">{{ $loan->book?->author?->name }}</p>
                            <p class="mt-1 font-mono text-xs text-muted">{{ $loan->book?->isbn ?: 'Tanpa ISBN' }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-muted">Anggota</p>
                        <p class="font-semibold text-ink">{{ $loan->user?->name }}</p>
                        <p class="text-xs text-muted">@ {{ $loan->user?->username }}</p>
                        <p class="text-xs text-muted">{{ $loan->user?->phone ?: '—' }}</p>
                    </div>
                </div>
            </section>

            <section class="surface-card p-6">
                <h3 class="text-sm font-bold text-ink">Linimasa</h3>
                <ol class="mt-4 space-y-4">
                    <li class="flex gap-3">
                        <span class="mt-1 grid h-6 w-6 shrink-0 place-items-center rounded-full bg-accent/15 text-accent">
                            <x-icon name="calendar" class="h-3.5 w-3.5" />
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-ink">Tanggal pinjam</p>
                            <p class="text-xs text-muted">{{ $loan->loan_date?->translatedFormat('l, d F Y') }}</p>
                        </div>
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-1 grid h-6 w-6 shrink-0 place-items-center rounded-full bg-warning/15 text-warning">
                            <x-icon name="clock" class="h-3.5 w-3.5" />
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-ink">Tenggat pengembalian</p>
                            <p class="text-xs {{ $loan->isOverdue() ? 'text-danger' : 'text-muted' }}">{{ $loan->due_date?->translatedFormat('l, d F Y') }}</p>
                        </div>
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-1 grid h-6 w-6 shrink-0 place-items-center rounded-full bg-success/15 text-success">
                            <x-icon name="check" class="h-3.5 w-3.5" />
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-ink">Tanggal kembali</p>
                            <p class="text-xs text-muted">{{ $loan->return_date?->translatedFormat('l, d F Y') ?? 'Belum dikembalikan' }}</p>
                        </div>
                    </li>
                </ol>

                @if ($loan->note)
                    <div class="mt-5 rounded-xl border border-line bg-white/[0.02] p-4">
                        <p class="text-[0.65rem] font-semibold uppercase tracking-[0.16em] text-muted">Catatan</p>
                        <p class="mt-1 whitespace-pre-line text-sm text-body">{{ $loan->note }}</p>
                    </div>
                @endif
            </section>
        </div>

        <aside class="space-y-4">
            <section class="surface-card p-5">
                <h3 class="text-sm font-bold text-ink">Denda</h3>
                <p class="mt-2 text-2xl font-bold {{ (float) $loan->fine_amount > 0 ? 'text-warning' : 'text-ink' }}">
                    Rp {{ number_format((float) $loan->fine_amount, 0, ',', '.') }}
                </p>
                <p class="text-xs text-muted">
                    @if ((float) $loan->fine_amount > 0)
                        {{ $loan->fine_paid ? 'Sudah dibayar' : 'Belum dibayar' }}
                    @else
                        Tidak ada denda tercatat
                    @endif
                </p>
                @if ((float) $loan->fine_amount > 0 && ! $loan->fine_paid)
                    <form method="POST" action="{{ route('admin.loans.pay-fine', $loan) }}" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-success w-full">Tandai Lunas</button>
                    </form>
                @endif
            </section>

            <section class="surface-card space-y-2 p-5">
                <h3 class="text-sm font-bold text-ink">Aksi</h3>
                @if ($loan->status === 'pending')
                    <form method="POST" action="{{ route('admin.loans.approve', $loan) }}">
                        @csrf
                        <button type="submit" class="btn btn-success w-full">Setujui Peminjaman</button>
                    </form>
                @endif
                @if ($loan->status === 'approved')
                    <form method="POST" action="{{ route('admin.loans.borrow', $loan) }}">
                        @csrf
                        <button type="submit" class="btn btn-ghost w-full">Tandai Dipinjam</button>
                    </form>
                @endif
                @if (in_array($loan->status, ['approved', 'borrowed'], true))
                    <form method="POST" action="{{ route('admin.loans.return', $loan) }}" data-confirm="Tandai buku sudah dikembalikan?">
                        @csrf
                        <button type="submit" class="btn btn-ghost w-full">Tandai Dikembalikan</button>
                    </form>
                @endif
                <p class="pt-1 text-[0.7rem] text-muted">
                    Disetujui oleh: {{ $loan->approver?->name ?? 'Belum disetujui' }}
                </p>
                <form method="POST" action="{{ route('admin.loans.destroy', $loan) }}" data-confirm="Hapus data peminjaman ini?">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-full">Hapus Peminjaman</button>
                </form>
            </section>
        </aside>
    </div>
@endsection
