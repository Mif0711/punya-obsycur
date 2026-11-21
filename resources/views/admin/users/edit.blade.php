@extends('layouts.admin')

@section('title', 'Ubah Pengguna')

@section('content')
    <x-page-header title="Ubah Pengguna" subtitle="Perbarui data akun, peran, kata sandi, dan status pengguna.">
        <x-slot:actions>
            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">
                <x-icon name="arrow-left" class="h-4 w-4" />
                Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_22rem]">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="surface-card space-y-5 p-6">
            @csrf
            @method('PUT')

            <div class="flex items-center gap-3 border-b border-line pb-5">
                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-gradient-to-br from-accent to-violet text-base font-bold text-white shadow-[0_0_24px_-8px_rgba(6,182,212,0.9)]">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </span>
                <div class="min-w-0">
                    <p class="truncate text-base font-bold text-ink">{{ $user->name }}</p>
                    <p class="truncate text-sm text-muted">{{ '@'.$user->username }}</p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="username" class="field-label">Username</label>
                    <input id="username" name="username" type="text" value="{{ old('username', $user->username) }}" required maxlength="50" class="field-input" placeholder="huruf, angka, titik, garis bawah" />
                    @error('username') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="name" class="field-label">Nama Lengkap</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required maxlength="150" class="field-input" />
                    @error('name') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="role" class="field-label">Peran</label>
                    <select id="role" name="role" required class="field-input">
                        <option value="user" @selected(old('role', $user->role) === 'user')>Pengguna (anggota)</option>
                        <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                    </select>
                    <p class="mt-1 text-xs text-muted">Peran pengguna mendapat kartu anggota otomatis.</p>
                    @error('role') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="phone" class="field-label">Telepon</label>
                    <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}" maxlength="30" class="field-input" placeholder="08…" />
                    @error('phone') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="address" class="field-label">Alamat</label>
                <textarea id="address" name="address" rows="3" maxlength="500" class="field-input resize-y">{{ old('address', $user->address) }}</textarea>
                @error('address') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="password" class="field-label">Kata Sandi Baru</label>
                    <input id="password" name="password" type="password" minlength="8" class="field-input" placeholder="Kosongkan jika tidak diubah" />
                    @error('password') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="field-label">Ulangi Kata Sandi Baru</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" minlength="8" class="field-input" placeholder="Ulangi kata sandi baru" />
                </div>
            </div>

            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-line bg-white/[0.02] p-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked((bool) old('is_active', $user->is_active))
                    class="h-4 w-4 rounded border-line bg-canvas text-accent focus:ring-accent/40" />
                <span>
                    <span class="block text-sm font-semibold text-ink">Akun aktif</span>
                    <span class="block text-xs text-muted">Akun nonaktif tidak dapat masuk ke portal.</span>
                </span>
            </label>

            <div class="flex items-center justify-end gap-2 border-t border-line pt-4">
                <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <x-icon name="check" class="h-4 w-4" />
                    Simpan Perubahan
                </button>
            </div>
        </form>

        <aside class="space-y-4">
            <section class="surface-card p-5">
                <h3 class="text-sm font-bold text-ink">Ringkasan Akun</h3>
                <dl class="mt-4 space-y-3 text-xs">
                    <div class="flex justify-between border-b border-line/60 pb-2.5"><dt class="text-muted">Username</dt><dd class="font-semibold text-body">{{ '@'.$user->username }}</dd></div>
                    <div class="flex justify-between border-b border-line/60 pb-2.5"><dt class="text-muted">Peran</dt><dd class="font-semibold text-body">{{ $user->isAdmin() ? 'Admin' : 'Pengguna' }}</dd></div>
                    <div class="flex justify-between border-b border-line/60 pb-2.5"><dt class="text-muted">Kartu</dt><dd class="font-semibold text-body">{{ $user->memberCard?->card_number ?: '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-muted">Bergabung</dt><dd class="font-semibold text-body">{{ $user->created_at?->translatedFormat('d M Y') ?: '—' }}</dd></div>
                </dl>
            </section>

            <section class="surface-card p-5">
                <h3 class="text-sm font-bold text-ink">Perubahan Peran</h3>
                <ul class="mt-3 space-y-2.5 text-xs text-muted">
                    <li class="flex gap-2"><x-icon name="card" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-accent" /> Pengguna yang baru ditetapkan akan mendapat kartu anggota.</li>
                    <li class="flex gap-2"><x-icon name="trash" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-danger" /> Kartu anggota dihapus bila akun diubah menjadi admin.</li>
                    <li class="flex gap-2"><x-icon name="lock" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-violet" /> Kata sandi kosong berarti tidak diubah.</li>
                </ul>
            </section>
        </aside>
    </div>
@endsection
