@extends('layouts.admin')

@section('title', 'Buat Akun & Kartu')

@section('content')
    <x-page-header title="Buat Akun Baru" subtitle="Akun pengguna otomatis mendapat kartu anggota. Akun admin tidak memerlukan kartu.">
        <x-slot:actions>
            <a href="{{ route('admin.cards.index') }}" class="btn btn-ghost">
                <x-icon name="arrow-left" class="h-4 w-4" />
                Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_22rem]">
        <form method="POST" action="{{ route('admin.cards.store') }}" class="surface-card space-y-5 p-6">
            @csrf

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="username" class="field-label">Username</label>
                    <input id="username" name="username" type="text" value="{{ old('username') }}" required maxlength="50" class="field-input" placeholder="huruf, angka, titik, garis bawah" />
                    @error('username') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="name" class="field-label">Nama Lengkap</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required maxlength="150" class="field-input" />
                    @error('name') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="password" class="field-label">Kata Sandi</label>
                    <input id="password" name="password" type="password" required minlength="8" class="field-input" />
                    @error('password') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="field-label">Ulangi Kata Sandi</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" class="field-input" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="role" class="field-label">Peran</label>
                    <select id="role" name="role" required class="field-input">
                        <option value="user" @selected(old('role', 'user') === 'user')>Pengguna (anggota)</option>
                        <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                    </select>
                    @error('role') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="phone" class="field-label">Telepon</label>
                    <input id="phone" name="phone" type="text" value="{{ old('phone') }}" maxlength="30" class="field-input" placeholder="08…" />
                    @error('phone') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="address" class="field-label">Alamat</label>
                <textarea id="address" name="address" rows="3" maxlength="500" class="field-input resize-y">{{ old('address') }}</textarea>
                @error('address') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
            </div>

            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-line bg-white/[0.02] p-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked((bool) old('is_active', true))
                    class="h-4 w-4 rounded border-line bg-canvas text-accent focus:ring-accent/40" />
                <span>
                    <span class="block text-sm font-semibold text-ink">Akun aktif</span>
                    <span class="block text-xs text-muted">Anggota nonaktif tidak dapat masuk ke portal.</span>
                </span>
            </label>

            <div class="flex items-center justify-end gap-2 border-t border-line pt-4">
                <a href="{{ route('admin.cards.index') }}" class="btn btn-ghost">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <x-icon name="check" class="h-4 w-4" />
                    Simpan Akun
                </button>
            </div>
        </form>

        <aside class="space-y-4">
            <section class="surface-card p-5">
                <div class="flex items-center gap-2">
                    <span class="grid h-9 w-9 place-items-center rounded-lg border border-accent/25 bg-accent/10 text-accent">
                        <x-icon name="card" class="h-4 w-4" />
                    </span>
                    <div>
                        <h3 class="text-sm font-bold text-ink">Kartu Otomatis</h3>
                        <p class="text-xs text-muted">Khusus peran pengguna</p>
                    </div>
                </div>
                <p class="mt-3 text-xs leading-relaxed text-muted">
                    Nomor kartu dibuat dengan format <span class="kbd">PPS-TAHUN-XXXXXX</span> dan langsung berstatus aktif.
                </p>
            </section>

            <section class="surface-card p-5">
                <h3 class="text-sm font-bold text-ink">Kebijakan kartu</h3>
                <ul class="mt-3 space-y-2.5 text-xs text-muted">
                    <li class="flex gap-2"><x-icon name="alert" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-danger" /> Kartu hilang → akun dinonaktifkan.</li>
                    <li class="flex gap-2"><x-icon name="refresh" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-success" /> Penggantian kartu → akun aktif kembali.</li>
                    <li class="flex gap-2"><x-icon name="check" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-accent" /> Admin tidak menerima kartu anggota.</li>
                </ul>
            </section>
        </aside>
    </div>
@endsection
