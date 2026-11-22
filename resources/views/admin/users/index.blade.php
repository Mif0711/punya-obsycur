@extends('layouts.admin')

@section('title', 'Pengguna')

@section('content')
    <x-page-header title="Pengguna" subtitle="Kelola akun pengguna dan admin yang dapat mengakses portal Perpusa.">
        <x-slot:actions>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <x-icon name="plus" class="h-4 w-4" />
                Tambah Pengguna
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="surface-card flex items-start gap-3 border-accent/20 bg-accent/[0.04] px-5 py-4 text-xs text-body">
        <x-icon name="users" class="mt-0.5 h-4 w-4 shrink-0 text-accent" />
        <p>Akun dengan peran <strong class="font-semibold text-ink">Pengguna</strong> otomatis menerima kartu anggota. Akun admin tidak memerlukan kartu.</p>
    </div>

    <form method="GET" class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="relative flex-1">
            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-muted">
                <x-icon name="search" class="h-4 w-4" />
            </span>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama atau username…" class="field-input pl-9" data-search-input />
        </div>
        <button type="submit" class="btn btn-ghost">Cari</button>
        @if (request('q'))
            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Reset</a>
        @endif
    </form>

    @if ($users->isEmpty())
        <x-empty-state title="Belum ada pengguna" description="Tambahkan akun pengguna atau admin pertama untuk mulai menggunakan portal." icon="users">
            <x-slot:action>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Tambah Pengguna</a>
            </x-slot:action>
        </x-empty-state>
    @else
        <div class="surface-card overflow-hidden">
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Peran</th>
                            <th>Status</th>
                            <th>Kartu Anggota</th>
                            <th>Bergabung</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-2.5">
                                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-gradient-to-br from-accent to-violet text-[0.65rem] font-bold text-white">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </span>
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-ink">{{ $user->name }}</p>
                                            <p class="truncate text-xs text-muted">{{ $user->phone ? 'WA: '.$user->phone : 'Tanpa WhatsApp' }}</p>
                                            <p class="truncate text-xs text-muted">{{ $user->parent_phone ? 'Ortu: '.$user->parent_phone : 'Tanpa no. orang tua' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="font-mono text-xs text-body">{{ '@'.$user->username }}</td>
                                <td>
                                    <span class="badge {{ $user->isAdmin() ? 'border-violet/30 bg-violet/15 text-violet' : 'border-accent/25 bg-accent/10 text-accent-bright' }}">
                                        {{ $user->isAdmin() ? 'Admin' : 'Pengguna' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="inline-flex items-center gap-1.5 text-xs {{ $user->is_active ? 'text-success' : 'text-danger' }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $user->is_active ? 'bg-success' : 'bg-danger' }}"></span>
                                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="font-mono text-xs text-muted">{{ $user->memberCard?->card_number ?: '—' }}</td>
                                <td class="text-xs text-muted">{{ $user->created_at?->translatedFormat('d M Y') ?: '—' }}</td>
                                <td class="text-right">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-ghost btn-sm" aria-label="Ubah {{ $user->name }}">
                                        <x-icon name="edit" class="h-3.5 w-3.5" />
                                        Ubah
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{ $users->links('partials.pagination') }}
    @endif
@endsection
