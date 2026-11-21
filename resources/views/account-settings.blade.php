@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.member')

@section('title', 'Pengaturan Akun')

@section('content')
    <x-page-header title="Pengaturan Akun" subtitle="Perbarui username dan kata sandi yang digunakan untuk masuk ke portal Perpusa." />

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_20rem]">
        <form method="POST" action="{{ route('account.update') }}" class="surface-card space-y-5 p-6">
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
                <span class="badge ml-auto border-accent/25 bg-accent/10 text-accent">
                    {{ $user->isAdmin() ? 'Admin' : 'Anggota' }}
                </span>
            </div>

            <div>
                <label for="username" class="field-label">Username baru</label>
                <input id="username" name="username" type="text" value="{{ old('username', $user->username) }}" required maxlength="50"
                    autocomplete="username" class="field-input" placeholder="Username" />
                <p class="mt-1 text-xs text-muted">Username dipakai untuk masuk. Huruf besar akan disimpan sebagai huruf kecil.</p>
                @error('username') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="current_password" class="field-label">Kata sandi saat ini</label>
                <div class="relative">
                    <input id="current_password" name="current_password" type="password" required autocomplete="current-password"
                        class="field-input pr-10" placeholder="Masukkan kata sandi saat ini" />
                    <button type="button" data-password-toggle="current_password" aria-label="Tampilkan kata sandi saat ini"
                        class="absolute right-1.5 top-1/2 grid h-8 w-8 -translate-y-1/2 place-items-center rounded-lg text-muted transition hover:bg-white/5 hover:text-ink">
                        <x-icon name="eye" class="h-4 w-4" />
                    </button>
                </div>
                <p class="mt-1 text-xs text-muted">Diperlukan untuk setiap perubahan akun.</p>
                @error('current_password') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="password" class="field-label">Kata sandi baru</label>
                    <div class="relative">
                        <input id="password" name="password" type="password" autocomplete="new-password"
                            class="field-input pr-10" placeholder="Kosongkan jika tidak diubah" />
                        <button type="button" data-password-toggle="password" aria-label="Tampilkan kata sandi baru"
                            class="absolute right-1.5 top-1/2 grid h-8 w-8 -translate-y-1/2 place-items-center rounded-lg text-muted transition hover:bg-white/5 hover:text-ink">
                            <x-icon name="eye" class="h-4 w-4" />
                        </button>
                    </div>
                    @error('password') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="field-label">Ulangi kata sandi baru</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                        class="field-input" placeholder="Ulangi kata sandi baru" />
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-line pt-5">
                <p class="text-xs text-muted">Perubahan langsung berlaku untuk sesi ini.</p>
                <button type="submit" class="btn btn-primary">
                    <x-icon name="check" class="h-4 w-4" />
                    Simpan Perubahan
                </button>
            </div>
        </form>

        <aside class="space-y-4">
            <section class="surface-card p-5">
                <h3 class="text-sm font-bold text-ink">Akun Anda</h3>
                <dl class="mt-4 space-y-3 text-xs">
                    <div class="flex items-center justify-between gap-3 border-b border-line/60 pb-2.5">
                        <dt class="text-muted">Nama</dt>
                        <dd class="text-right font-semibold text-body">{{ $user->name }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3 border-b border-line/60 pb-2.5">
                        <dt class="text-muted">Username</dt>
                        <dd class="text-right font-semibold text-body">{{ '@'.$user->username }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3 border-b border-line/60 pb-2.5">
                        <dt class="text-muted">Peran</dt>
                        <dd class="text-right font-semibold text-body">{{ $user->isAdmin() ? 'Admin' : 'Anggota' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-muted">Status</dt>
                        <dd class="text-right font-semibold text-success">Aktif</dd>
                    </div>
                </dl>
            </section>

            <section class="surface-card p-5">
                <h3 class="text-sm font-bold text-ink">Tips keamanan</h3>
                <ul class="mt-3 space-y-3 text-xs text-muted">
                    <li class="flex gap-2">
                        <x-icon name="lock" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-accent" />
                        Gunakan kata sandi minimal 8 karakter dengan kombinasi huruf dan angka.
                    </li>
                    <li class="flex gap-2">
                        <x-icon name="user" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-accent" />
                        Username tidak boleh sama dengan akun lain di perpustakaan.
                    </li>
                    <li class="flex gap-2">
                        <x-icon name="alert" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-warning" />
                        Kata sandi saat ini tetap diperlukan meski hanya mengubah username.
                    </li>
                </ul>
            </section>
        </aside>
    </div>
@endsection
