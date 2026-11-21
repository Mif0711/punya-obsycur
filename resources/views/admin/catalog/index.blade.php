@extends('layouts.admin')

@section('title', $title)

@section('content')
    <x-page-header :title="$title" :subtitle="'Kelola data '.strtolower($label).' yang dipakai di katalog perpustakaan.'">
        <x-slot:actions>
            <a href="{{ route('admin.'.$resource.'.create') }}" class="btn btn-primary">
                <x-icon name="plus" class="h-4 w-4" />
                Tambah {{ $label }}
            </a>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="relative flex-1">
            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-muted">
                <x-icon name="search" class="h-4 w-4" />
            </span>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari {{ strtolower($label) }}…" class="field-input pl-9" data-search-input />
        </div>
        <button type="submit" class="btn btn-ghost">Cari</button>
        @if (request('q'))
            <a href="{{ route('admin.'.$resource.'.index') }}" class="btn btn-ghost">Reset</a>
        @endif
    </form>

    @if ($items->isEmpty())
        <x-empty-state :title="'Belum ada '.strtolower($label)" :description="'Tambahkan '.strtolower($label).' pertama untuk mulai melengkapi katalog.'" icon="tag">
            <x-slot:action>
                <a href="{{ route('admin.'.$resource.'.create') }}" class="btn btn-primary">Tambah {{ $label }}</a>
            </x-slot:action>
        </x-empty-state>
    @else
        <div class="stagger grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($items as $item)
                <article class="surface-card surface-card-hover flex h-full flex-col p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-[0.65rem] font-semibold uppercase tracking-[0.18em] text-muted">{{ $label }}</p>
                            <h3 class="mt-1 truncate text-base font-bold text-ink">{{ $item->name }}</h3>
                            @if ($resource === 'publishers' && $item->city)
                                <p class="mt-0.5 inline-flex items-center gap-1 text-xs text-muted">
                                    <x-icon name="home" class="h-3.5 w-3.5" />
                                    {{ $item->city }}
                                </p>
                            @endif
                        </div>
                        <span class="badge shrink-0 border-accent/25 bg-accent/10 text-accent-bright">
                            {{ $item->books_count }} buku
                        </span>
                    </div>

                    <p class="mt-3 line-clamp-3 flex-1 text-sm leading-relaxed text-muted">
                        {{ $item->description ?: 'Belum ada deskripsi untuk '.strtolower($label).' ini.' }}
                    </p>

                    <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-line/70 pt-3">
                        <a href="{{ route('admin.'.$resource.'.show', $item) }}" class="btn btn-ghost btn-sm">
                            <x-icon name="eye" class="h-3.5 w-3.5" />
                            Detail
                        </a>
                        <a href="{{ route('admin.'.$resource.'.edit', $item) }}" class="btn btn-ghost btn-sm ml-auto" aria-label="Ubah {{ $item->name }}">
                            <x-icon name="edit" class="h-3.5 w-3.5" />
                        </a>
                        <form method="POST" action="{{ route('admin.'.$resource.'.destroy', $item) }}" data-confirm="Hapus {{ $item->name }}? Buku terkait juga akan terhapus.">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" aria-label="Hapus {{ $item->name }}">
                                <x-icon name="trash" class="h-3.5 w-3.5" />
                            </button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>

        {{ $items->links('partials.pagination') }}
    @endif
@endsection
