@extends('layouts.member')

@section('title', 'Katalog Buku')

@section('content')
    <x-page-header title="Katalog Buku" subtitle="Telusuri koleksi berdasarkan judul, pengarang, penerbit, atau genre.">
        <x-slot:actions>
            <a href="{{ route('member.book-requests.create') }}" class="btn btn-ghost">
                <x-icon name="bookmark" class="h-4 w-4" />
                Usulkan Buku
            </a>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" class="surface-card space-y-3 p-4">
        <div class="flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-muted">
                    <x-icon name="search" class="h-4 w-4" />
                </span>
                <input type="search" name="q" value="{{ $query }}" placeholder="Cari judul, penulis, penerbit, genre… (tekan / )" class="field-input pl-9" data-search-input />
            </div>
            <button type="submit" class="btn btn-primary">
                <x-icon name="search" class="h-4 w-4" />
                Cari
            </button>
            @if (request()->hasAny(['q', 'genre_id', 'publisher_id', 'author_id', 'availability']))
                <a href="{{ route('member.books.index') }}" class="btn btn-ghost">Reset</a>
            @endif
        </div>

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <select name="genre_id" class="field-input text-sm">
                <option value="">Semua genre</option>
                @foreach ($genres as $genre)
                    <option value="{{ $genre->id }}" @selected((int) request('genre_id') === $genre->id)>{{ $genre->name }}</option>
                @endforeach
            </select>
            <select name="publisher_id" class="field-input text-sm">
                <option value="">Semua penerbit</option>
                @foreach ($publishers as $publisher)
                    <option value="{{ $publisher->id }}" @selected((int) request('publisher_id') === $publisher->id)>{{ $publisher->name }}</option>
                @endforeach
            </select>
            <select name="author_id" class="field-input text-sm">
                <option value="">Semua pengarang</option>
                @foreach ($authors as $author)
                    <option value="{{ $author->id }}" @selected((int) request('author_id') === $author->id)>{{ $author->name }}</option>
                @endforeach
            </select>
            <select name="availability" class="field-input text-sm">
                <option value="">Semua ketersediaan</option>
                <option value="available" @selected(request('availability') === 'available')>Hanya yang tersedia</option>
            </select>
        </div>
    </form>

    <div class="flex items-center justify-between gap-3">
        <p class="text-xs text-muted">
            Menampilkan <span class="font-semibold text-body">{{ $books->firstItem() ?? 0 }}–{{ $books->lastItem() ?? 0 }}</span>
            dari <span class="font-semibold text-body">{{ $books->total() }}</span> buku
        </p>
        @if ($query !== '')
            <p class="text-xs text-muted">Hasil untuk “<span class="text-body">{{ $query }}</span>”</p>
        @endif
    </div>

    @if ($books->isEmpty())
        <x-empty-state title="Buku tidak ditemukan" description="Coba kata kunci lain atau reset filter pencarian." icon="search">
            <x-slot:action>
                <a href="{{ route('member.books.index') }}" class="btn btn-ghost">Reset Pencarian</a>
            </x-slot:action>
        </x-empty-state>
    @else
        <div class="stagger grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($books as $book)
                <x-book-card :book="$book">
                    <x-slot:actions>
                        @if (! $book->is_borrowed && ! $book->is_reserved)
                            <span class="badge border-success/25 bg-success/10 text-success">Tersedia</span>
                        @else
                            <span class="badge border-danger/25 bg-danger/10 text-danger">Tidak tersedia</span>
                        @endif
                    </x-slot:actions>
                </x-book-card>
            @endforeach
        </div>

        {{ $books->links('partials.pagination') }}
    @endif
@endsection
