@extends('layouts.admin')

@section('title', 'Dashboard')
@section('eyebrow', 'Ringkasan hari ini')

@section('content')
    <section class="relative overflow-hidden rounded-2xl border border-line bg-gradient-to-br from-surface-2 via-surface to-canvas p-6 sm:p-8">
        <div class="pointer-events-none absolute -right-16 -top-20 h-56 w-56 rounded-full bg-accent/10 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-24 left-1/3 h-56 w-56 rounded-full bg-violet/10 blur-3xl"></div>
        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-2xl">
                <p class="inline-flex items-center gap-2 rounded-full border border-accent/25 bg-accent/10 px-3 py-1 text-[0.65rem] font-semibold uppercase tracking-[0.18em] text-accent-bright">
                    <x-icon name="sparkles" class="h-3.5 w-3.5" />
                    Rekomendasi cerdas aktif
                </p>
                <h2 class="mt-4 text-2xl font-bold leading-tight tracking-tight text-ink sm:text-3xl">
                    Selamat datang kembali, {{ auth()->user()->name }}.
                </h2>
                <p class="mt-2 text-sm leading-relaxed text-muted">
                    Ada <span class="font-semibold text-warning">{{ $stats['pending_requests'] }} permintaan</span> menunggu,
                    <span class="font-semibold text-danger">{{ $stats['overdue_loans'] }} peminjaman terlambat</span>, dan
                    denda belum lunas sebesar <span class="font-semibold text-ink">Rp {{ number_format((float) $stats['unpaid_fines'], 0, ',', '.') }}</span>.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.loans.create') }}" class="btn btn-primary">
                    <x-icon name="plus" class="h-4 w-4" />
                    Buat Peminjaman
                </a>
                <a href="{{ route('admin.requests.index') }}" class="btn btn-ghost">
                    <x-icon name="inbox" class="h-4 w-4" />
                    Tinjau Permintaan
                </a>
            </div>
        </div>
    </section>

    <section class="stagger grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Total Buku" :value="number_format($stats['books'], 0, ',', '.')" hint="Judul dalam katalog" icon="book" tone="accent" />
        <x-stat-card label="Anggota Aktif" :value="number_format($stats['members'], 0, ',', '.')" hint="Akun pengguna terdaftar" icon="users" tone="violet" />
        <x-stat-card label="Sedang Dipinjam" :value="number_format($stats['ongoing_loans'], 0, ',', '.')" hint="Peminjaman berjalan" icon="swap" tone="success" />
        <x-stat-card label="Terlambat" :value="number_format($stats['overdue_loans'], 0, ',', '.')" hint="Melewati tenggat" icon="alert" tone="danger" />
    </section>

    <div class="grid gap-6 xl:grid-cols-3">
        <section class="space-y-4 xl:col-span-2">
            <div class="flex items-end justify-between gap-3">
                <div>
                    <h3 class="text-base font-bold text-ink">Rekomendasi untuk Anda</h3>
                    <p class="text-xs text-muted">Dipilih dari 8 sinyal: riwayat, korelasi pembaca, rating, pilihan editor, penghargaan, tokoh publik, tren sosial, dan tema musiman.</p>
                </div>
                <a href="{{ route('admin.books.index') }}" class="hidden text-xs font-semibold text-accent hover:text-accent-bright sm:block">Lihat katalog →</a>
            </div>

            @if ($recommendedBooks->isEmpty())
                <x-empty-state title="Belum ada rekomendasi" description="Tambahkan buku dan riwayat peminjaman untuk mengaktifkan mesin rekomendasi." icon="sparkles" />
            @else
                <div class="stagger grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($recommendedBooks as $book)
                        <x-book-card :book="$book" :href="route('admin.books.show', $book)" show-reasons compact />
                    @endforeach
                </div>
            @endif
        </section>

        <aside class="space-y-6">
            <section class="surface-card p-5">
                <div class="mb-3 flex items-center gap-2">
                    <span class="grid h-8 w-8 place-items-center rounded-lg border border-warning/25 bg-warning/10 text-warning">
                        <x-icon name="wallet" class="h-4 w-4" />
                    </span>
                    <div>
                        <h3 class="text-sm font-bold text-ink">Aturan Sirkulasi</h3>
                        <p class="text-xs text-muted">Berlaku untuk semua peminjaman</p>
                    </div>
                </div>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between gap-3 border-b border-line/60 pb-3">
                        <dt class="text-muted">Denda per hari</dt>
                        <dd class="font-semibold text-ink">Rp {{ number_format($finePerDay, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3 border-b border-line/60 pb-3">
                        <dt class="text-muted">Toleransi keterlambatan</dt>
                        <dd class="font-semibold text-ink">{{ $graceDays }} hari</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-muted">Denda belum lunas</dt>
                        <dd class="font-semibold text-danger">Rp {{ number_format((float) $stats['unpaid_fines'], 0, ',', '.') }}</dd>
                    </div>
                </dl>
                <a href="{{ route('admin.settings.index') }}" class="btn btn-ghost btn-sm mt-4 w-full">
                    <x-icon name="settings" class="h-4 w-4" />
                    Ubah pengaturan
                </a>
            </section>

            <section class="surface-card p-5">
                <h3 class="text-sm font-bold text-ink">Akses Cepat</h3>
                <div class="mt-3 grid grid-cols-2 gap-2">
                    @foreach ([
                        ['route' => 'admin.cards.create', 'label' => 'Kartu Baru', 'icon' => 'card'],
                        ['route' => 'admin.book-requests.index', 'label' => 'Buku Baru', 'icon' => 'bookmark'],
                        ['route' => 'admin.import-export.index', 'label' => 'Import CSV', 'icon' => 'upload'],
                    ] as $action)
                        <a href="{{ route($action['route']) }}" class="flex flex-col gap-2 rounded-xl border border-line bg-white/[0.02] p-3 text-xs font-semibold text-body transition hover:border-accent/40 hover:text-ink">
                            <x-icon :name="$action['icon']" class="h-4 w-4 text-accent" />
                            {{ $action['label'] }}
                        </a>
                    @endforeach
                </div>
            </section>
        </aside>
    </div>
@endsection
