@extends('layouts.admin')

@section('title', $book->title)

@section('content')
    <div class="flex flex-wrap items-center gap-2 text-xs text-muted">
        <a href="{{ route('admin.books.index') }}" class="hover:text-accent">Menu Buku</a>
        <x-icon name="chevron-right" class="h-3 w-3" />
        <span class="text-body">{{ $book->title }}</span>
    </div>

    <div class="grid gap-6 lg:grid-cols-[18rem_minmax(0,1fr)]">
        <aside class="space-y-4">
            <div class="surface-card overflow-hidden">
                <div class="book-cover rounded-none">
                    @if ($book->cover_path)
                        <img src="{{ asset('storage/'.$book->cover_path) }}" alt="Sampul {{ $book->title }}">
                    @endif
                    <div class="relative z-10">
                        <p class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-white/60">{{ $book->genre?->name }}</p>
                        <h2 class="mt-1 text-lg font-bold leading-tight text-white">{{ $book->title }}</h2>
                    </div>
                </div>
                <div class="space-y-3 p-4">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-muted">Ketersediaan</span>
                        @if ($available)
                            <span class="badge border-success/25 bg-success/10 text-success">Tersedia</span>
                        @else
                            <span class="badge border-danger/25 bg-danger/10 text-danger">Tidak tersedia</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-muted">Rating</span>
                        <span class="inline-flex items-center gap-1 font-semibold text-warning">
                            <x-icon name="star" class="h-3.5 w-3.5" />
                            {{ number_format((float) $book->average_rating, 1) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-muted">ISBN</span>
                        <span class="font-mono text-body">{{ $book->isbn ?: '—' }}</span>
                    </div>
                </div>
            </div>

            <div class="grid gap-2">
                <a href="{{ route('admin.books.edit', $book) }}" class="btn btn-primary">
                    <x-icon name="edit" class="h-4 w-4" />
                    Ubah Buku
                </a>
                <form method="POST" action="{{ route('admin.books.destroy', $book) }}" data-confirm="Hapus “{{ $book->title }}” dari katalog?">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-full">
                        <x-icon name="trash" class="h-4 w-4" />
                        Hapus Buku
                    </button>
                </form>
            </div>
        </aside>

        <div class="space-y-6">
            <section class="surface-card p-6">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-accent">{{ $book->genre?->name }}</p>
                        <h1 class="mt-1 text-2xl font-bold tracking-tight text-ink">{{ $book->title }}</h1>
                        <p class="mt-1 text-sm text-muted">{{ $book->author?->name }} · {{ $book->publisher?->name }}</p>
                    </div>
                    <div class="flex flex-wrap gap-1.5">
                        @if ($book->editor_pick) <span class="badge border-accent/25 bg-accent/10 text-accent-bright">Pilihan Editor</span> @endif
                        @if ($book->award_winner) <span class="badge border-warning/25 bg-warning/10 text-warning">Penghargaan</span> @endif
                        @if ($book->public_figure_pick) <span class="badge border-success/25 bg-success/10 text-success">Tokoh Publik</span> @endif
                        @if ($book->social_trend) <span class="badge border-violet/25 bg-violet/10 text-violet-300">Tren</span> @endif
                    </div>
                </div>

                <p class="mt-4 whitespace-pre-line text-sm leading-relaxed text-body">{{ $book->description ?: 'Belum ada deskripsi untuk buku ini.' }}</p>
            </section>

            <div class="grid gap-4 sm:grid-cols-2">
                <section class="surface-card p-5">
                    <h3 class="text-sm font-bold text-ink">Detail Bibliografi</h3>
                    <dl class="mt-3 space-y-2.5 text-xs">
                        <div class="flex justify-between gap-3 border-b border-line/60 pb-2"><dt class="text-muted">Pengarang</dt><dd class="text-right text-body">{{ $book->author?->name }}</dd></div>
                        <div class="flex justify-between gap-3 border-b border-line/60 pb-2"><dt class="text-muted">Penerbit</dt><dd class="text-right text-body">{{ $book->publisher?->name }}</dd></div>
                        <div class="flex justify-between gap-3 border-b border-line/60 pb-2"><dt class="text-muted">Genre</dt><dd class="text-right text-body">{{ $book->genre?->name }}</dd></div>
                        <div class="flex justify-between gap-3 border-b border-line/60 pb-2"><dt class="text-muted">ISBN</dt><dd class="text-right font-mono text-body">{{ $book->isbn ?: '—' }}</dd></div>
                    </dl>
                </section>

                <section class="surface-card p-5">
                    <h3 class="text-sm font-bold text-ink">Sinyal Rekomendasi</h3>
                    <dl class="mt-3 space-y-2.5 text-xs">
                        <div class="flex justify-between gap-3 border-b border-line/60 pb-2"><dt class="text-muted">Nama penghargaan</dt><dd class="text-right text-body">{{ $book->award_name ?: '—' }}</dd></div>
                        <div class="flex justify-between gap-3 border-b border-line/60 pb-2"><dt class="text-muted">Tokoh publik</dt><dd class="text-right text-body">{{ $book->public_figure_name ?: '—' }}</dd></div>
                        <div class="flex justify-between gap-3 border-b border-line/60 pb-2"><dt class="text-muted">Sumber tren</dt><dd class="text-right text-body">{{ $book->social_trend_source ?: '—' }}</dd></div>
                        <div>
                            <dt class="mb-1.5 text-muted">Tema</dt>
                            <dd class="flex flex-wrap gap-1">
                                @forelse ($book->themes as $theme)
                                    <span class="badge border-line bg-white/5 text-body">{{ $theme }}</span>
                                @empty
                                    <span class="text-muted">—</span>
                                @endforelse
                            </dd>
                        </div>
                    </dl>
                </section>
            </div>
        </div>
    </div>
@endsection
