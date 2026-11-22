@extends('layouts.admin')

@section('title', 'Tambah Pengguna')

@section('content')
    <x-page-header title="Tambah Pengguna" subtitle="Buat akun pengguna atau admin. Peran default adalah Pengguna (anggota).">
        <x-slot:actions>
            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">
                <x-icon name="arrow-left" class="h-4 w-4" />
                Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_22rem]">
        <form method="POST" action="{{ route('admin.users.store') }}" class="surface-card space-y-5 p-6">
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
                    <label for="nickname" class="field-label">Nama Panggilan</label>
                    <input id="nickname" name="nickname" type="text" value="{{ old('nickname') }}" maxlength="100" class="field-input" placeholder="Nama yang dipanggil sehari-hari" />
                    @error('nickname') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="school" class="field-label">Sekolah</label>
                    <input id="school" name="school" type="text" value="{{ old('school') }}" maxlength="150" class="field-input" placeholder="Nama sekolah" />
                    @error('school') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="birth_place" class="field-label">Tempat Lahir</label>
                    <input id="birth_place" name="birth_place" type="text" value="{{ old('birth_place') }}" maxlength="150" class="field-input" placeholder="Kota kelahiran" />
                    @error('birth_place') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="birth_date" class="field-label">Tanggal Lahir</label>
                    <input id="birth_date" name="birth_date" type="date" value="{{ old('birth_date') }}" class="field-input" />
                    @error('birth_date') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
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

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <label for="role" class="field-label">Peran</label>
                    <select id="role" name="role" required class="field-input">
                        <option value="user" @selected(old('role', 'user') === 'user')>Pengguna (anggota)</option>
                        <option value="admin" @selected(old('role', 'user') === 'admin')>Admin</option>
                    </select>
                    <p class="mt-1 text-xs text-muted">Default: Pengguna (anggota).</p>
                    @error('role') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="phone" class="field-label">No. WhatsApp</label>
                    <input id="phone" name="phone" type="text" value="{{ old('phone') }}" maxlength="30" class="field-input" placeholder="08…" />
                    @error('phone') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="parent_phone" class="field-label">No. Orang Tua</label>
                    <input id="parent_phone" name="parent_phone" type="text" value="{{ old('parent_phone') }}" maxlength="30" class="field-input" placeholder="08…" />
                    <p class="mt-1 text-xs text-muted">Dihubungi saat anggota tidak merespons.</p>
                    @error('parent_phone') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="address" class="field-label">Alamat</label>
                <textarea id="address" name="address" rows="3" maxlength="500" class="field-input resize-y" placeholder="Alamat lengkap">{{ old('address') }}</textarea>
                <p class="mt-1 text-xs text-muted">Cadangan bila WhatsApp dan nomor orang tua tidak dapat dihubungi.</p>
                @error('address') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
            </div>

            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-line bg-white/[0.02] p-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked((bool) old('is_active', true))
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
                    Simpan Akun
                </button>
            </div>
        </form>

        <aside class="space-y-4">
            <section class="surface-card p-5">
                <div class="flex items-center gap-2">
                    <span class="grid h-9 w-9 place-items-center rounded-lg border border-accent/25 bg-accent/10 text-accent">
                        <x-icon name="users" class="h-4 w-4" />
                    </span>
                    <div>
                        <h3 class="text-sm font-bold text-ink">Peran Akun</h3>
                        <p class="text-xs text-muted">Pengguna atau admin</p>
                    </div>
                </div>
                <ul class="mt-3 space-y-2.5 text-xs text-muted">
                    <li class="flex gap-2"><x-icon name="user" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-accent" /> <span><strong class="text-body">Pengguna</strong> masuk ke portal anggota dan mendapat kartu otomatis.</span></li>
                    <li class="flex gap-2"><x-icon name="settings" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-violet" /> <span><strong class="text-body">Admin</strong> masuk ke panel admin dan tidak memerlukan kartu.</span></li>
                </ul>
            </section>

            <section class="surface-card p-5">
                <h3 class="text-sm font-bold text-ink">Kartu Anggota</h3>
                <p class="mt-2 text-xs leading-relaxed text-muted">
                    Kartu untuk peran pengguna diterbitkan otomatis dengan format <span class="kbd">PPS-TAHUN-XXXXXX</span>.
                    Kelola kartu melalui menu <strong class="text-body">Kartu Baru</strong>.
                </p>
            </section>
        </aside>
    </div>
@endsection
