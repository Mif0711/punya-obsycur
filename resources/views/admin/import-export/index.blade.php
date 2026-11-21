@extends('layouts.admin')

@section('title', 'Import / Ekspor')

@section('content')
    <x-page-header title="Import / Ekspor CSV" subtitle="Pindahkan data katalog dan akun anggota menggunakan berkas CSV native." />

    <div class="grid gap-6 xl:grid-cols-2">
        <section class="surface-card p-6">
            <div class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-xl border border-accent/25 bg-accent/10 text-accent">
                    <x-icon name="download" class="h-5 w-5" />
                </span>
                <div>
                    <h2 class="text-base font-bold text-ink">Ekspor Data</h2>
                    <p class="text-xs text-muted">Unduh data terbaru dalam format CSV.</p>
                </div>
            </div>

            <div class="mt-5 space-y-3">
                @foreach (['books' => ['label' => 'Katalog Buku', 'icon' => 'book', 'hint' => 'Judul, genre, penerbit, pengarang, ISBN, label rekomendasi, tema.'], 'users' => ['label' => 'Akun Pengguna', 'icon' => 'users', 'hint' => 'Username, nama, peran, status aktif, kontak, alamat.']] as $type => $meta)
                    <div class="flex flex-col gap-3 rounded-xl border border-line bg-white/[0.02] p-4 sm:flex-row sm:items-center">
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg border border-line bg-canvas text-muted">
                            <x-icon :name="$meta['icon']" class="h-4 w-4" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-ink">{{ $meta['label'] }}</p>
                            <p class="text-xs text-muted">{{ $meta['hint'] }}</p>
                        </div>
                        <a href="{{ route('admin.import-export.export', $type) }}" class="btn btn-ghost btn-sm shrink-0">
                            <x-icon name="download" class="h-3.5 w-3.5" />
                            Unduh CSV
                        </a>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="surface-card p-6">
            <div class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-xl border border-violet/25 bg-violet/10 text-violet">
                    <x-icon name="upload" class="h-5 w-5" />
                </span>
                <div>
                    <h2 class="text-base font-bold text-ink">Import Data</h2>
                    <p class="text-xs text-muted">Unggah CSV dengan urutan kolom yang sesuai.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.import-export.import') }}" enctype="multipart/form-data" class="mt-5 space-y-4">
                @csrf

                <div>
                    <label for="type" class="field-label">Jenis data</label>
                    <select id="type" name="type" required class="field-input">
                        <option value="books" @selected(old('type') === 'books')>Katalog buku</option>
                        <option value="users" @selected(old('type') === 'users')>Akun pengguna</option>
                    </select>
                    @error('type') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="file" class="field-label">Berkas CSV</label>
                    <input id="file" name="file" type="file" accept=".csv,text/csv" required
                        class="field-input text-xs file:mr-3 file:rounded-md file:border-0 file:bg-white/10 file:px-3 file:py-1.5 file:text-xs file:text-body" />
                    <p class="mt-1 text-[0.7rem] text-muted">Maksimal 4 MB. Baris yang gagal akan dilewati dan dilaporkan.</p>
                    @error('file') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="btn btn-primary w-full">
                    <x-icon name="upload" class="h-4 w-4" />
                    Mulai Import
                </button>
            </form>

            <div class="mt-5 rounded-xl border border-warning/25 bg-warning/10 px-4 py-3 text-xs text-warning">
                <div class="flex items-start gap-2">
                    <x-icon name="alert" class="mt-0.5 h-4 w-4 shrink-0" />
                    <p>Header CSV harus sama persis dengan template. Untuk akun pengguna baru, kartu anggota dibuat otomatis.</p>
                </div>
            </div>
        </section>
    </div>

    <section class="space-y-4">
        <div>
            <h2 class="text-base font-bold text-ink">Template Kolom</h2>
            <p class="text-xs text-muted">Gunakan urutan berikut pada baris pertama berkas CSV.</p>
        </div>

        <div class="grid gap-4 xl:grid-cols-2">
            @foreach ($headers as $type => $columns)
                <div class="surface-card p-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold text-ink">{{ $type === 'books' ? 'Katalog Buku' : 'Akun Pengguna' }}</h3>
                        <span class="badge border-line bg-white/5 text-muted">{{ count($columns) }} kolom</span>
                    </div>
                    <div class="mt-3 overflow-x-auto rounded-lg border border-line bg-canvas/60 p-3">
                        <code class="whitespace-nowrap font-mono text-[0.7rem] text-accent-bright">{{ implode(',', $columns) }}</code>
                    </div>
                    <ul class="mt-3 space-y-1.5 text-[0.7rem] text-muted">
                        @if ($type === 'books')
                            <li>· <span class="text-body">themes</span> dipisahkan dengan tanda <span class="kbd">|</span></li>
                            <li>· <span class="text-body">editor_pick</span>, <span class="text-body">award_winner</span>, <span class="text-body">public_figure_pick</span>, <span class="text-body">social_trend</span> berisi <span class="kbd">1</span> / <span class="kbd">0</span></li>
                            <li>· ISBN kosong akan membuat baris baru setiap import.</li>
                        @else
                            <li>· <span class="text-body">role</span> berisi <span class="kbd">admin</span> atau <span class="kbd">user</span></li>
                            <li>· <span class="text-body">is_active</span> berisi <span class="kbd">1</span> / <span class="kbd">0</span></li>
                            <li>· Password kosong akan diset menjadi <span class="kbd">password</span>.</li>
                        @endif
                    </ul>
                </div>
            @endforeach
        </div>
    </section>
@endsection
