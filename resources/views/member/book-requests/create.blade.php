@extends('layouts.member')

@section('title', 'Ajukan Buku')

@section('content')
    <x-page-header title="Ajukan Buku Baru" subtitle="Tuliskan judul dan detail catalog dengan teks. Anggota lain dapat memberi vote pada usulan Anda.">
        <x-slot:actions>
            <a href="{{ route('member.book-requests.index') }}" class="btn btn-ghost">
                <x-icon name="arrow-left" class="h-4 w-4" />
                Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_22rem]">
        <form method="POST" action="{{ route('member.book-requests.store') }}" class="surface-card space-y-5 p-6">
            @csrf

            <div>
                <label for="title" class="field-label">Judul buku</label>
                <input id="title" name="title" type="text" required maxlength="255" value="{{ old('title') }}"
                    class="field-input" placeholder="Contoh: Laut Bercerita" />
                @error('title') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <label for="genre" class="field-label">Genre</label>
                    <input id="genre" name="genre" type="text" maxlength="255" value="{{ old('genre') }}"
                        class="field-input" placeholder="Contoh: Roman" />
                    @error('genre') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="publisher" class="field-label">Penerbit</label>
                    <input id="publisher" name="publisher" type="text" maxlength="255" value="{{ old('publisher') }}"
                        class="field-input" placeholder="Contoh: Gramedia" />
                    @error('publisher') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="author" class="field-label">Pengarang</label>
                    <input id="author" name="author" type="text" maxlength="255" value="{{ old('author') }}"
                        class="field-input" placeholder="Contoh: Leila S. Chudori" />
                    @error('author') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="reason" class="field-label">Alasan usulan</label>
                <textarea id="reason" name="reason" rows="5" required minlength="10" maxlength="2000"
                    class="field-input resize-y" placeholder="Mengapa buku ini penting untuk koleksi perpustakaan?">{{ old('reason') }}</textarea>
                <p class="mt-1 text-[0.7rem] text-muted">Minimal 10 karakter. Jelaskan manfaat atau kebutuhan Anda terhadap buku ini.</p>
                @error('reason') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-end gap-2 border-t border-line pt-4">
                <a href="{{ route('member.book-requests.index') }}" class="btn btn-ghost">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <x-icon name="bookmark" class="h-4 w-4" />
                    Kirim Usulan
                </button>
            </div>
        </form>

        <aside class="space-y-4">
            <div class="surface-card p-5">
                <h3 class="text-sm font-bold text-ink">Alur pengajuan</h3>
                <ol class="mt-3 space-y-3 text-xs text-muted">
                    <li class="flex gap-2"><span class="grid h-5 w-5 shrink-0 place-items-center rounded-full bg-accent/15 text-[0.65rem] font-bold text-accent-bright">1</span> Tuliskan judul buku beserta genre, penerbit, dan pengarangnya.</li>
                    <li class="flex gap-2"><span class="grid h-5 w-5 shrink-0 place-items-center rounded-full bg-accent/15 text-[0.65rem] font-bold text-accent-bright">2</span> Anggota lain dapat melihat usulan Anda dan memberikan vote disertai alasan.</li>
                    <li class="flex gap-2"><span class="grid h-5 w-5 shrink-0 place-items-center rounded-full bg-accent/15 text-[0.65rem] font-bold text-accent-bright">3</span> Jika buku sudah pernah diajukan, cukup beri vote tanpa menginput ulang.</li>
                </ol>
            </div>

            <div class="surface-card p-5 text-xs leading-relaxed text-muted">
                <div class="flex items-start gap-2">
                    <x-icon name="alert" class="mt-0.5 h-4 w-4 shrink-0 text-warning" />
                    <p>Semua kolom diisi menggunakan teks bebas. Pastikan ejaan judul dan detail buku sudah benar.</p>
                </div>
            </div>
        </aside>
    </div>
@endsection
