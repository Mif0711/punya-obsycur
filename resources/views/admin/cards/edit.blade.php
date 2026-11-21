@extends('layouts.admin')

@section('title', 'Ubah Kartu Anggota')

@section('content')
    <x-page-header title="Ubah Kartu Anggota" subtitle="Perbarui nomor kartu, tanggal terbit, status, dan catatan internal.">
        <x-slot:actions>
            <a href="{{ route('admin.cards.index') }}" class="btn btn-ghost">
                <x-icon name="arrow-left" class="h-4 w-4" />
                Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_22rem]">
        <form method="POST" action="{{ route('admin.cards.update', $card) }}" class="surface-card space-y-5 p-6">
            @csrf
            @method('PUT')

            <div class="flex items-center gap-3 border-b border-line pb-5">
                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl border border-accent/25 bg-accent/10 text-accent">
                    <x-icon name="card" class="h-5 w-5" />
                </span>
                <div class="min-w-0">
                    <p class="truncate font-mono text-sm font-bold text-ink">{{ $card->card_number }}</p>
                    <p class="truncate text-xs text-muted">Kartu milik {{ $card->user?->name ?? '—' }}</p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="card_number" class="field-label">Nomor Kartu</label>
                    <input id="card_number" name="card_number" type="text" value="{{ old('card_number', $card->card_number) }}" required maxlength="32" pattern="PPS-[0-9]{4}-[A-F0-9]{6}" class="field-input font-mono uppercase" placeholder="PPS-2026-ABCDEF" />
                    <p class="mt-1 text-xs text-muted">Format: PPS-TAHUN-XXXXXX</p>
                    @error('card_number') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="issued_at" class="field-label">Tanggal Terbit</label>
                    <input id="issued_at" name="issued_at" type="date" value="{{ old('issued_at', $card->issued_at?->format('Y-m-d')) }}" required class="field-input" />
                    @error('issued_at') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="status" class="field-label">Status Kartu</label>
                <select id="status" name="status" required class="field-input">
                    <option value="" disabled @selected(! in_array(old('status', $card->status), ['active', 'lost'], true))>Pilih status</option>
                    <option value="active" @selected(old('status', $card->status) === 'active')>Aktif</option>
                    <option value="lost" @selected(old('status', $card->status) === 'lost')>Hilang</option>
                </select>
                <p class="mt-1 text-xs text-muted">Status aktif mengaktifkan akun; status hilang menonaktifkan akun. Untuk kartu pengganti, gunakan tombol di halaman daftar.</p>
                @error('status') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="note" class="field-label">Catatan</label>
                <textarea id="note" name="note" rows="3" maxlength="255" class="field-input resize-y" placeholder="Catatan internal, misalnya lokasi kehilangan kartu">{{ old('note', $card->note) }}</textarea>
                @error('note') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-end gap-2 border-t border-line pt-4">
                <a href="{{ route('admin.cards.index') }}" class="btn btn-ghost">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <x-icon name="check" class="h-4 w-4" />
                    Simpan Perubahan
                </button>
            </div>
        </form>

        <aside class="space-y-4">
            <section class="surface-card p-5">
                <h3 class="text-sm font-bold text-ink">Ringkasan Kartu</h3>
                <dl class="mt-4 space-y-3 text-xs">
                    <div class="flex justify-between border-b border-line/60 pb-2.5"><dt class="text-muted">Anggota</dt><dd class="font-semibold text-body">{{ $card->user?->name ?: '—' }}</dd></div>
                    <div class="flex justify-between border-b border-line/60 pb-2.5"><dt class="text-muted">Username</dt><dd class="font-semibold text-body">{{ $card->user?->username ? '@'.$card->user->username : '—' }}</dd></div>
                    <div class="flex items-center justify-between border-b border-line/60 pb-2.5"><dt class="text-muted">Status</dt><dd><x-status-badge :status="$card->status" /></dd></div>
                    <div class="flex justify-between"><dt class="text-muted">Akun</dt><dd class="font-semibold {{ $card->user?->is_active ? 'text-success' : 'text-danger' }}">{{ $card->user?->is_active ? 'Aktif' : 'Nonaktif' }}</dd></div>
                </dl>
            </section>

            <section class="surface-card p-5">
                <h3 class="text-sm font-bold text-ink">Kebijakan Status</h3>
                <ul class="mt-3 space-y-2.5 text-xs text-muted">
                    <li class="flex gap-2"><x-icon name="alert" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-danger" /> Kartu hilang → akun anggota dinonaktifkan.</li>
                    <li class="flex gap-2"><x-icon name="check" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-success" /> Kartu aktif → akun anggota diaktifkan kembali.</li>
                    <li class="flex gap-2"><x-icon name="refresh" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-accent" /> Penggantian kartu tetap dibuat dari halaman daftar.</li>
                </ul>
            </section>
        </aside>
    </div>
@endsection
