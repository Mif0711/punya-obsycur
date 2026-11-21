@extends('layouts.admin')

@section('title', 'Pengaturan')

@section('content')
    <x-page-header title="Pengaturan Sirkulasi" subtitle="Atur denda harian, toleransi keterlambatan, dan durasi peminjaman." />

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_22rem]">
        <form method="POST" action="{{ route('admin.settings.update') }}" class="surface-card space-y-5 p-6">
            @csrf
            @method('PUT')

            <div>
                <label for="fine_per_day" class="field-label">Denda per hari (Rp)</label>
                <input id="fine_per_day" name="fine_per_day" type="number" min="0" max="1000000" value="{{ old('fine_per_day', $finePerDay) }}" required class="field-input" />
                <p class="mt-1 text-xs text-muted">Dikenakan untuk setiap hari keterlambatan setelah masa toleransi.</p>
                @error('fine_per_day') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="grace_days" class="field-label">Toleransi keterlambatan (hari)</label>
                <input id="grace_days" name="grace_days" type="number" min="0" max="30" value="{{ old('grace_days', $graceDays) }}" required class="field-input" />
                <p class="mt-1 text-xs text-muted">Jumlah hari setelah tenggat sebelum denda mulai dihitung.</p>
                @error('grace_days') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="loan_days" class="field-label">Durasi peminjaman (hari)</label>
                <input id="loan_days" name="loan_days" type="number" min="1" max="60" value="{{ old('loan_days', $loanDays) }}" required class="field-input" />
                <p class="mt-1 text-xs text-muted">Tenggat dihitung dari tanggal pinjam atau tanggal persetujuan.</p>
                @error('loan_days') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-end gap-2 border-t border-line pt-4">
                <button type="submit" class="btn btn-primary">
                    <x-icon name="check" class="h-4 w-4" />
                    Simpan Pengaturan
                </button>
            </div>
        </form>

        <aside class="space-y-4">
            <section class="surface-card p-5">
                <h3 class="text-sm font-bold text-ink">Simulasi denda</h3>
                <p class="mt-1 text-xs text-muted">Contoh untuk keterlambatan 7 hari:</p>
                @php
                    $exampleLateDays = max(0, 7 - $graceDays);
                    $exampleFine = $exampleLateDays * $finePerDay;
                @endphp
                <dl class="mt-4 space-y-2.5 text-xs">
                    <div class="flex justify-between border-b border-line/60 pb-2"><dt class="text-muted">Hari terlambat</dt><dd class="text-body">7 hari</dd></div>
                    <div class="flex justify-between border-b border-line/60 pb-2"><dt class="text-muted">Toleransi</dt><dd class="text-body">{{ $graceDays }} hari</dd></div>
                    <div class="flex justify-between border-b border-line/60 pb-2"><dt class="text-muted">Hari kena denda</dt><dd class="text-body">{{ $exampleLateDays }} hari</dd></div>
                    <div class="flex justify-between"><dt class="text-muted">Total denda</dt><dd class="font-semibold text-warning">Rp {{ number_format($exampleFine, 0, ',', '.') }}</dd></div>
                </dl>
            </section>

            <section class="surface-card p-5">
                <h3 class="text-sm font-bold text-ink">Catatan</h3>
                <ul class="mt-3 space-y-2.5 text-xs text-muted">
                    <li class="flex gap-2"><x-icon name="clock" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-accent" /> Pengaturan berlaku untuk peminjaman baru dan perhitungan denda berikutnya.</li>
                    <li class="flex gap-2"><x-icon name="wallet" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-warning" /> Denda dicatat dan ditandai lunas secara manual oleh admin.</li>
                    <li class="flex gap-2"><x-icon name="alert" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-danger" /> Perubahan tidak mengubah denda yang sudah tercatat.</li>
                </ul>
            </section>
        </aside>
    </div>
@endsection
