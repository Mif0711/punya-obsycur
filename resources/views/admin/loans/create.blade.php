@extends('layouts.admin')

@section('title', 'Buat Peminjaman')

@section('content')
    <x-page-header title="Buat Peminjaman" subtitle="Admin dapat membuat peminjaman yang langsung disetujui untuk anggota.">
        <x-slot:actions>
            <a href="{{ route('admin.loans.index') }}" class="btn btn-ghost">
                <x-icon name="arrow-left" class="h-4 w-4" />
                Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_22rem]">
        <form method="POST" action="{{ route('admin.loans.store') }}" class="surface-card space-y-5 p-6">
            @csrf

            <div>
                <label for="user_id" class="field-label">Anggota</label>
                <select id="user_id" name="user_id" required class="field-input">
                    <option value="">Pilih anggota aktif</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected((int) old('user_id') === $user->id)>{{ $user->name }} ({{ $user->username }})</option>
                    @endforeach
                </select>
                @error('user_id') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="book_id" class="field-label">Buku tersedia</label>
                <select id="book_id" name="book_id" required class="field-input">
                    <option value="">Pilih buku</option>
                    @foreach ($books as $book)
                        <option value="{{ $book->id }}" @selected((int) old('book_id') === $book->id)>{{ $book->title }}</option>
                    @endforeach
                </select>
                @error('book_id') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="note" class="field-label">Catatan (opsional)</label>
                <textarea id="note" name="note" rows="4" maxlength="1000" class="field-input resize-y" placeholder="Catatan untuk petugas atau anggota…">{{ old('note') }}</textarea>
                @error('note') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
            </div>

            <div class="rounded-xl border border-warning/25 bg-warning/10 px-4 py-3 text-xs text-warning">
                <div class="flex items-start gap-2">
                    <x-icon name="alert" class="mt-0.5 h-4 w-4 shrink-0" />
                    <p>Peminjaman yang dibuat admin langsung berstatus <strong>disetujui</strong>. Tenggat dihitung otomatis dari pengaturan durasi peminjaman.</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 border-t border-line pt-4">
                <a href="{{ route('admin.loans.index') }}" class="btn btn-ghost">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <x-icon name="check" class="h-4 w-4" />
                    Buat Peminjaman
                </button>
            </div>
        </form>

        <aside class="surface-card h-fit p-5">
            <h3 class="text-sm font-bold text-ink">Alur peminjaman</h3>
            <ol class="mt-3 space-y-3 text-xs text-muted">
                <li class="flex gap-2"><span class="grid h-5 w-5 shrink-0 place-items-center rounded-full bg-accent/15 text-[0.65rem] font-bold text-accent-bright">1</span> Pilih anggota aktif dan buku yang tersedia.</li>
                <li class="flex gap-2"><span class="grid h-5 w-5 shrink-0 place-items-center rounded-full bg-accent/15 text-[0.65rem] font-bold text-accent-bright">2</span> Sistem mengunci buku dan menghitung tenggat.</li>
                <li class="flex gap-2"><span class="grid h-5 w-5 shrink-0 place-items-center rounded-full bg-accent/15 text-[0.65rem] font-bold text-accent-bright">3</span> Tandai dipinjam, lalu kembali saat buku diterima.</li>
                <li class="flex gap-2"><span class="grid h-5 w-5 shrink-0 place-items-center rounded-full bg-accent/15 text-[0.65rem] font-bold text-accent-bright">4</span> Denda dihitung otomatis bila melewati toleransi.</li>
            </ol>
        </aside>
    </div>
@endsection
