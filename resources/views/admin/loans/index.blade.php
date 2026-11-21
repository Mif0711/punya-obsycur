@extends('layouts.admin')

@section('title', 'Peminjaman')

@section('content')
    <x-page-header title="Peminjaman" subtitle="Pantau peminjaman berjalan, keterlambatan, dan status denda anggota.">
        <x-slot:actions>
            <a href="{{ route('admin.loans.create') }}" class="btn btn-primary">
                <x-icon name="plus" class="h-4 w-4" />
                Buat Peminjaman
            </a>
        </x-slot:actions>
    </x-page-header>

    <section class="stagger grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Peminjaman Berjalan" :value="number_format($stats['ongoing'], 0, ',', '.')" hint="Disetujui & dipinjam" icon="swap" tone="accent" />
        <x-stat-card label="Terlambat" :value="number_format($stats['overdue'], 0, ',', '.')" hint="Melewati tenggat" icon="alert" tone="danger" />
        <x-stat-card label="Total Denda" :value="'Rp '.number_format((float) $stats['total_fines'], 0, ',', '.')" hint="Akumulasi denda tercatat" icon="wallet" tone="warning" />
        <x-stat-card label="Denda Belum Lunas" :value="'Rp '.number_format((float) $stats['unpaid_fines'], 0, ',', '.')" hint="Perlu ditagih" icon="chart" tone="violet" />
    </section>

    <form method="GET" class="surface-card flex flex-col gap-3 p-4 sm:flex-row sm:items-center">
        <div class="relative flex-1">
            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-muted">
                <x-icon name="search" class="h-4 w-4" />
            </span>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari anggota atau judul buku…" class="field-input pl-9" data-search-input />
        </div>
        <select name="status" class="field-input sm:w-48">
            <option value="">Semua status</option>
            @foreach (['pending' => 'Menunggu', 'approved' => 'Disetujui', 'borrowed' => 'Dipinjam', 'returned' => 'Dikembalikan', 'rejected' => 'Ditolak'] as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary">Terapkan</button>
        @if (request()->hasAny(['q', 'status']))
            <a href="{{ route('admin.loans.index') }}" class="btn btn-ghost">Reset</a>
        @endif
    </form>

    @if ($loans->isEmpty())
        <x-empty-state title="Tidak ada peminjaman" description="Belum ada peminjaman yang cocok dengan filter saat ini." icon="swap" />
    @else
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Anggota</th>
                        <th>Buku</th>
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
                                <a href="{{ route('admin.loans.show', $loan) }}" class="font-semibold text-ink hover:text-accent">{{ $loan->user?->name }}</a>
                                <p class="text-xs text-muted">@ {{ $loan->user?->username }}</p>
                            </td>
                            <td>
                                <p class="max-w-[14rem] truncate font-medium text-body">{{ $loan->book?->title }}</p>
                                <p class="text-xs text-muted">{{ $loan->loan_date?->translatedFormat('d M Y') }}</p>
                            </td>
                            <td>
                                <p class="{{ $loan->isOverdue() ? 'font-semibold text-danger' : 'text-body' }}">{{ $loan->due_date?->translatedFormat('d M Y') }}</p>
                                @if ($loan->isOverdue())
                                    <p class="text-[0.7rem] text-danger">Terlambat</p>
                                @endif
                            </td>
                            <td>
                                <x-status-badge :status="$loan->status" />
                            </td>
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
                                        <form method="POST" action="{{ route('admin.loans.approve', $loan) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">Setujui</button>
                                        </form>
                                    @endif
                                    @if ($loan->status === 'approved')
                                        <form method="POST" action="{{ route('admin.loans.borrow', $loan) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-ghost btn-sm">Dipinjam</button>
                                        </form>
                                    @endif
                                    @if (in_array($loan->status, ['approved', 'borrowed'], true))
                                        <form method="POST" action="{{ route('admin.loans.return', $loan) }}" data-confirm="Tandai buku ini sudah dikembalikan?">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">Kembali</button>
                                        </form>
                                    @endif
                                    @if ((float) $loan->fine_amount > 0 && ! $loan->fine_paid)
                                        <form method="POST" action="{{ route('admin.loans.pay-fine', $loan) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-ghost btn-sm">Lunasi</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('admin.loans.show', $loan) }}" class="btn btn-ghost btn-sm" aria-label="Detail peminjaman">
                                        <x-icon name="eye" class="h-3.5 w-3.5" />
                                    </a>
                                    <form method="POST" action="{{ route('admin.loans.destroy', $loan) }}" data-confirm="Hapus data peminjaman ini?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" aria-label="Hapus peminjaman">
                                            <x-icon name="trash" class="h-3.5 w-3.5" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $loans->links('partials.pagination') }}
    @endif
@endsection
