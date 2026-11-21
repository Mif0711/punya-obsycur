@extends('layouts.member')

@section('title', 'Peminjaman Saya')

@section('content')
    <x-page-header title="Peminjaman Saya" subtitle="Pantau permintaan, tenggat pengembalian, dan status denda Anda. Pengembalian buku dilakukan oleh petugas.">
        <x-slot:actions>
            <a href="{{ route('member.books.index') }}" class="btn btn-primary">
                <x-icon name="search" class="h-4 w-4" />
                Jelajahi Katalog
            </a>
        </x-slot:actions>
    </x-page-header>

    @if ((float) $unpaidFines > 0)
        <div class="flex items-start gap-3 rounded-xl border border-warning/30 bg-warning/10 px-4 py-3 text-sm text-warning">
            <x-icon name="wallet" class="mt-0.5 h-4 w-4 shrink-0" />
            <div>
                <p class="font-semibold">Denda belum lunas: Rp {{ number_format((float) $unpaidFines, 0, ',', '.') }}</p>
                <p class="mt-0.5 text-xs text-warning/80">Silakan lunasi kepada petugas perpustakaan saat mengembalikan buku.</p>
            </div>
        </div>
    @endif

    <form method="GET" class="surface-card flex flex-col gap-3 p-4 sm:flex-row sm:items-center">
        <select name="status" class="field-input sm:w-56">
            <option value="">Semua status</option>
            @foreach (['pending' => 'Menunggu', 'approved' => 'Disetujui', 'borrowed' => 'Dipinjam', 'returned' => 'Dikembalikan', 'rejected' => 'Ditolak'] as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary">Terapkan</button>
        @if (request('status'))
            <a href="{{ route('member.loans.index') }}" class="btn btn-ghost">Reset</a>
        @endif
    </form>

    @if ($loans->isEmpty())
        <x-empty-state title="Belum ada peminjaman" description="Ajukan peminjaman dari halaman detail buku, lalu tunggu persetujuan admin." icon="swap">
            <x-slot:action>
                <a href="{{ route('member.books.index') }}" class="btn btn-primary btn-sm">Cari Buku</a>
            </x-slot:action>
        </x-empty-state>
    @else
        <div class="table-wrap hidden md:block">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Buku</th>
                        <th>Tanggal Pinjam</th>
                        <th>Tenggat</th>
                        <th>Status</th>
                        <th>Denda</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($loans as $loan)
                        <tr>
                            <td>
                                <a href="{{ route('member.books.show', $loan->book) }}" class="font-semibold text-ink hover:text-accent">
                                    {{ $loan->book?->title }}
                                </a>
                                <p class="text-xs text-muted">Dipinjam {{ $loan->created_at?->diffForHumans() }}</p>
                                @if ($loan->note)
                                    <p class="mt-1 max-w-[16rem] truncate text-[0.7rem] text-muted">{{ $loan->note }}</p>
                                @endif
                            </td>
                            <td class="text-xs text-muted">{{ $loan->loan_date?->translatedFormat('d M Y') ?? '—' }}</td>
                            <td>
                                <p class="{{ $loan->isOverdue() ? 'font-semibold text-danger' : 'text-body' }}">
                                    {{ $loan->due_date?->translatedFormat('d M Y') ?? '—' }}
                                </p>
                                @if ($loan->isOverdue())
                                    <p class="text-[0.7rem] text-danger">Terlambat</p>
                                @endif
                            </td>
                            <td><x-status-badge :status="$loan->isOverdue() ? 'overdue' : $loan->status" /></td>
                            <td>
                                @if ((float) $loan->fine_amount > 0)
                                    <p class="font-semibold text-warning">Rp {{ number_format((float) $loan->fine_amount, 0, ',', '.') }}</p>
                                    <x-status-badge :status="$loan->fine_paid ? 'paid' : 'unpaid'" class="mt-1" />
                                @else
                                    <span class="text-xs text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex flex-wrap items-center justify-end gap-1.5">
                                    @if ($loan->status === 'pending')
                                        <form method="POST" action="{{ route('member.loans.cancel', $loan) }}" data-confirm="Batalkan permintaan peminjaman ini?">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm">Batalkan</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('member.books.show', $loan->book) }}" class="btn btn-ghost btn-sm" aria-label="Lihat buku">
                                        <x-icon name="eye" class="h-3.5 w-3.5" />
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="stagger grid gap-3 md:hidden">
            @foreach ($loans as $loan)
                <article class="surface-card space-y-3 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <a href="{{ route('member.books.show', $loan->book) }}" class="font-semibold text-ink hover:text-accent">{{ $loan->book?->title }}</a>
                            <p class="text-xs text-muted">Diajukan {{ $loan->created_at?->diffForHumans() }}</p>
                        </div>
                        <x-status-badge :status="$loan->isOverdue() ? 'overdue' : $loan->status" />
                    </div>
                    <div class="flex items-center justify-between gap-3 text-xs text-muted">
                        <span>Pinjam {{ $loan->loan_date?->translatedFormat('d M Y') ?? '—' }}</span>
                        <span class="{{ $loan->isOverdue() ? 'font-semibold text-danger' : '' }}">Tenggat {{ $loan->due_date?->translatedFormat('d M Y') ?? '—' }}</span>
                    </div>
                    @if ((float) $loan->fine_amount > 0)
                        <div class="flex items-center justify-between rounded-lg border border-warning/20 bg-warning/5 px-3 py-2 text-xs">
                            <span class="text-muted">Denda</span>
                            <span class="font-semibold text-warning">Rp {{ number_format((float) $loan->fine_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex flex-wrap items-center gap-2 border-t border-line/70 pt-3">
                        @if ($loan->status === 'pending')
                            <form method="POST" action="{{ route('member.loans.cancel', $loan) }}" data-confirm="Batalkan permintaan peminjaman ini?">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">Batalkan</button>
                            </form>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        {{ $loans->links('partials.pagination') }}
    @endif
@endsection
