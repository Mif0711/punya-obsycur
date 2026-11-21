@extends('layouts.admin')

@section('title', 'Menu Buku')

@section('content')
    <x-page-header title="Menu Buku" subtitle="Kelola seluruh koleksi, metadata, dan penanda rekomendasi.">
        <x-slot:actions>
            <a href="{{ route('admin.books.create') }}" class="btn btn-primary">
                <x-icon name="plus" class="h-4 w-4" />
                Tambah Buku
            </a>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" class="surface-card grid gap-3 p-4 sm:grid-cols-[minmax(0,1fr)_12rem_12rem_auto]">
        <div class="relative">
            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-muted">
                <x-icon name="search" class="h-4 w-4" />
            </span>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari judul buku…" class="field-input pl-9" data-search-input />
        </div>

        <select name="genre_id" class="field-input">
            <option value="">Semua genre</option>
            @foreach ($genres as $genre)
                <option value="{{ $genre->id }}" @selected((int) request('genre_id') === $genre->id)>{{ $genre->name }}</option>
            @endforeach
        </select>

        <select name="flag" class="field-input">
            <option value="">Semua label</option>
            <option value="editor" @selected(request('flag') === 'editor')>Pilihan Editor</option>
            <option value="award" @selected(request('flag') === 'award')>Penghargaan</option>
            <option value="figure" @selected(request('flag') === 'figure')>Tokoh Publik</option>
            <option value="trend" @selected(request('flag') === 'trend')>Sedang Tren</option>
        </select>

        <div class="flex items-center gap-2">
            <button type="submit" class="btn btn-primary flex-1">Filter</button>
            @if (request()->hasAny(['q', 'genre_id', 'flag']))
                <a href="{{ route('admin.books.index') }}" class="btn btn-ghost">Reset</a>
            @endif
        </div>
    </form>

    @if ($books->isEmpty())
        <x-empty-state title="Katalog masih kosong" description="Tambahkan buku manual atau impor dari CSV." icon="book">
            <x-slot:action>
                <a href="{{ route('admin.books.create') }}" class="btn btn-primary">Tambah Buku</a>
            </x-slot:action>
        </x-empty-state>
    @else
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Buku</th>
                        <th>Genre</th>
                        <th>Penerbit</th>
                        <th>Rating</th>
                        <th>Label</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($books as $book)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="h-14 w-10 shrink-0 overflow-hidden rounded-md border border-line bg-surface-2">
                                        @if ($book->cover_path)
                                            <img src="{{ asset('storage/'.$book->cover_path) }}" alt="" class="h-full w-full object-cover" loading="lazy">
                                        @else
                                            <div class="grid h-full w-full place-items-center text-[0.6rem] font-bold text-muted">PPS</div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('admin.books.show', $book) }}" class="font-semibold text-ink hover:text-accent">{{ $book->title }}</a>
                                        <p class="text-xs text-muted">{{ $book->author?->name ?? '—' }}</p>
                                        <p class="font-mono text-[0.68rem] text-muted/70">{{ $book->isbn ?: 'Tanpa ISBN' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge border-line bg-white/5 text-body">{{ $book->genre?->name }}</span></td>
                            <td class="text-xs">{{ $book->publisher?->name }}</td>
                            <td>
                                <span class="inline-flex items-center gap-1 text-warning">
                                    <x-icon name="star" class="h-3.5 w-3.5" />
                                    {{ number_format((float) $book->average_rating, 1) }}
                                </span>
                            </td>
                            <td>
                                <div class="flex max-w-[12rem] flex-wrap gap-1">
                                    @if ($book->editor_pick) <span class="badge border-accent/25 bg-accent/10 text-accent-bright">Editor</span> @endif
                                    @if ($book->award_winner) <span class="badge border-warning/25 bg-warning/10 text-warning">Award</span> @endif
                                    @if ($book->public_figure_pick) <span class="badge border-success/25 bg-success/10 text-success">Tokoh</span> @endif
                                    @if ($book->social_trend) <span class="badge border-violet/25 bg-violet/10 text-violet-300">Tren</span> @endif
                                    @if (! $book->editor_pick && ! $book->award_winner && ! $book->public_figure_pick && ! $book->social_trend)
                                        <span class="text-xs text-muted">—</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.books.show', $book) }}" class="btn btn-ghost btn-sm" aria-label="Detail {{ $book->title }}">
                                        <x-icon name="eye" class="h-3.5 w-3.5" />
                                    </a>
                                    <a href="{{ route('admin.books.edit', $book) }}" class="btn btn-ghost btn-sm" aria-label="Ubah {{ $book->title }}">
                                        <x-icon name="edit" class="h-3.5 w-3.5" />
                                    </a>
                                    <form method="POST" action="{{ route('admin.books.destroy', $book) }}" data-confirm="Hapus “{{ $book->title }}” dari katalog?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" aria-label="Hapus {{ $book->title }}">
                                            <x-icon name="trash" class="h-3.5 w-3.5" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $books->links('partials.pagination') }}
    @endif
@endsection
