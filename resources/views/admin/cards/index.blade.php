@extends('layouts.admin')

@section('title', 'Kartu Baru')

@section('content')
    <x-page-header title="Kartu Anggota" subtitle="Kelola kartu perpustakaan, status kehilangan, dan penggantian kartu.">
        <x-slot:actions>
            <a href="{{ route('admin.cards.create') }}" class="btn btn-primary">
                <x-icon name="plus" class="h-4 w-4" />
                Buat Akun &amp; Kartu
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="surface-card flex items-start gap-3 border-accent/20 bg-accent/[0.04] px-5 py-4 text-xs text-body">
        <x-icon name="card" class="mt-0.5 h-4 w-4 shrink-0 text-accent" />
        <p>Setiap akun pengguna baru otomatis mendapat kartu anggota. Jika kartu hilang, tandai hilang untuk menonaktifkan akun, lalu terbitkan kartu pengganti untuk mengaktifkannya kembali.</p>
    </div>

    <form method="GET" class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="relative flex-1">
            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-muted">
                <x-icon name="search" class="h-4 w-4" />
            </span>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nomor kartu, nama, atau username…" class="field-input pl-9" data-search-input />
        </div>
        <button type="submit" class="btn btn-ghost">Cari</button>
        @if (request('q'))
            <a href="{{ route('admin.cards.index') }}" class="btn btn-ghost">Reset</a>
        @endif
    </form>

    @if ($cards->isEmpty())
        <x-empty-state title="Belum ada kartu" description="Buat akun pengguna untuk menerbitkan kartu anggota pertama." icon="card">
            <x-slot:action>
                <a href="{{ route('admin.cards.create') }}" class="btn btn-primary">Buat Akun</a>
            </x-slot:action>
        </x-empty-state>
    @else
        <div class="stagger grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($cards as $card)
                <article class="surface-card surface-card-hover relative overflow-hidden p-5">
                    <div class="pointer-events-none absolute -right-10 -top-10 h-28 w-28 rounded-full bg-gradient-to-br from-accent/20 to-violet/10 blur-2xl"></div>
                    <div class="relative flex items-start justify-between gap-3">
                        <div>
                            <p class="text-[0.62rem] font-semibold uppercase tracking-[0.2em] text-muted">Perpusa Member Card</p>
                            <p class="mt-1 font-mono text-sm font-bold text-ink">{{ $card->card_number }}</p>
                        </div>
                        <x-status-badge :status="$card->status" />
                    </div>

                    <div class="relative mt-4 flex items-center gap-3 rounded-xl border border-line bg-white/[0.02] p-3">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-gradient-to-br from-accent to-violet text-sm font-bold text-white">
                            {{ strtoupper(substr($card->user?->name ?? 'A', 0, 1)) }}
                        </span>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-ink">{{ $card->user?->name }}</p>
                            <p class="truncate text-xs text-muted">@ {{ $card->user?->username }} · {{ $card->user?->phone ?: 'tanpa telepon' }}</p>
                        </div>
                    </div>

                    <dl class="relative mt-3 grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <dt class="text-muted">Terbit</dt>
                            <dd class="text-body">{{ $card->issued_at?->translatedFormat('d M Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-muted">Status akun</dt>
                            <dd class="{{ $card->user?->is_active ? 'text-success' : 'text-danger' }}">
                                {{ $card->user?->is_active ? 'Aktif' : 'Nonaktif' }}
                            </dd>
                        </div>
                    </dl>

                    <div class="relative mt-4 flex flex-wrap items-center gap-2 border-t border-line/70 pt-3">
                        <a href="{{ route('admin.cards.edit', $card) }}" class="btn btn-ghost btn-sm">
                            <x-icon name="edit" class="h-3.5 w-3.5" />
                            Ubah
                        </a>
                        @if ($card->status === 'active')
                            <form method="POST" action="{{ route('admin.cards.lost', $card) }}" data-confirm="Tandai kartu ini hilang? Akun pengguna akan dinonaktifkan.">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <x-icon name="alert" class="h-3.5 w-3.5" />
                                    Tandai Hilang
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.cards.replace', $card) }}" data-confirm="Terbitkan kartu pengganti dan aktifkan kembali akun ini?">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">
                                    <x-icon name="refresh" class="h-3.5 w-3.5" />
                                    Terbitkan Pengganti
                                </button>
                            </form>
                        @endif
                        <span class="ml-auto text-[0.68rem] text-muted">
                            {{ $card->note ?: 'Kartu aktif' }}
                        </span>
                    </div>
                </article>
            @endforeach
        </div>

        {{ $cards->links('partials.pagination') }}
    @endif
@endsection
