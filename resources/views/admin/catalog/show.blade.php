@extends('layouts.admin')

@section('title', $title)

@section('content')
    <div class="flex flex-wrap items-center gap-2 text-xs text-muted">
        <a href="{{ route('admin.'.$resource.'.index') }}" class="hover:text-accent">Daftar {{ $label }}</a>
        <x-icon name="chevron-right" class="h-3 w-3" />
        <span class="text-body">{{ $item->name }}</span>
    </div>

    <x-page-header :title="$item->name" :subtitle="$item->description ?: 'Belum ada deskripsi untuk '.strtolower($label).' ini.'">
        <x-slot:actions>
            <a href="{{ route('admin.'.$resource.'.edit', $item) }}" class="btn btn-ghost">
                <x-icon name="edit" class="h-4 w-4" />
                Ubah
            </a>
            <a href="{{ route('admin.'.$resource.'.index') }}" class="btn btn-ghost">
                <x-icon name="arrow-left" class="h-4 w-4" />
                Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    <section class="surface-card p-6">
        <div class="flex flex-wrap items-center gap-3">
            <span class="grid h-12 w-12 place-items-center rounded-xl border border-accent/25 bg-accent/10 text-accent">
                <x-icon name="tag" class="h-5 w-5" />
            </span>
            <div class="flex-1">
                <p class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-muted">{{ $label }}</p>
                <h2 class="text-lg font-bold text-ink">{{ $item->name }}</h2>
            </div>
            @if ($resource === 'publishers' && $item->city)
                <span class="badge border-line bg-white/5 text-body">
                    <x-icon name="home" class="h-3 w-3" />
                    {{ $item->city }}
                </span>
            @endif
            <span class="badge border-accent/25 bg-accent/10 text-accent-bright">{{ $books->total() }} buku terkait</span>
        </div>
        <p class="mt-4 text-sm leading-relaxed text-body">{{ $item->description ?: 'Belum ada deskripsi.' }}</p>
    </section>

    <section class="space-y-4">
        <div class="flex items-end justify-between gap-3">
            <div>
                <h3 class="text-base font-bold text-ink">Buku dalam {{ strtolower($label) }} ini</h3>
                <p class="text-xs text-muted">Klik buku untuk membuka detail di katalog admin.</p>
            </div>
            <a href="{{ route('admin.books.create') }}" class="btn btn-ghost btn-sm">
                <x-icon name="plus" class="h-3.5 w-3.5" />
                Tambah Buku
            </a>
        </div>

        @if ($books->isEmpty())
            <x-empty-state title="Belum ada buku" description="Belum ada buku yang terhubung dengan {{ strtolower($label) }} ini." icon="book" />
        @else
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>ISBN</th>
                            <th>Rating</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($books as $book)
                            <tr>
                                <td>
                                    <p class="font-semibold text-ink">{{ $book->title }}</p>
                                    <p class="text-xs text-muted">{{ $book->author?->name ?? '—' }}</p>
                                </td>
                                <td class="font-mono text-xs text-muted">{{ $book->isbn ?: '—' }}</td>
                                <td>
                                    <span class="inline-flex items-center gap-1 text-warning">
                                        <x-icon name="star" class="h-3.5 w-3.5" />
                                        {{ number_format((float) $book->average_rating, 1) }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('admin.books.show', $book) }}" class="btn btn-ghost btn-sm">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $books->links('partials.pagination') }}
        @endif
    </section>
@endsection
