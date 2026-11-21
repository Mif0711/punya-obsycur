@extends('layouts.member')

@section('title', $book->title)

@section('content')
    <div class="flex flex-wrap items-center gap-2 text-xs text-muted">
        <a href="{{ route('member.books.index') }}" class="hover:text-accent">Katalog</a>
        <x-icon name="chevron-right" class="h-3 w-3" />
        <a href="{{ route('member.books.index', ['genre_id' => $book->genre_id]) }}" class="hover:text-accent">{{ $book->genre?->name }}</a>
        <x-icon name="chevron-right" class="h-3 w-3" />
        <span class="text-body">{{ $book->title }}</span>
    </div>

    <div class="grid gap-6 lg:grid-cols-[20rem_minmax(0,1fr)]">
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

            <div class="surface-card space-y-2 p-4">
                @if ($activeLoan)
                    <div class="rounded-xl border border-accent/25 bg-accent/10 px-3 py-2.5 text-xs text-accent-bright">
                        <p class="font-semibold">Peminjaman aktif</p>
                        <p class="mt-0.5">Status: {{ ucfirst($activeLoan->status) }} · tenggat {{ $activeLoan->due_date?->translatedFormat('d M Y') }}</p>
                    </div>
                    @if ($activeLoan->status === 'pending')
                        <form method="POST" action="{{ route('member.loans.cancel', $activeLoan) }}" data-confirm="Batalkan permintaan peminjaman ini?">
                            @csrf
                            <button type="submit" class="btn btn-danger w-full">Batalkan Permintaan</button>
                        </form>
                    @elseif (in_array($activeLoan->status, ['approved', 'borrowed'], true))
                        <p class="text-xs text-muted">Pengembalian buku dilakukan oleh petugas perpustakaan.</p>
                    @endif
                @elseif ($available)
                    <form method="POST" action="{{ route('member.loans.store') }}" class="space-y-2">
                        @csrf
                        <input type="hidden" name="book_id" value="{{ $book->id }}">
                        <button type="submit" class="btn btn-primary w-full">
                            <x-icon name="swap" class="h-4 w-4" />
                            Ajukan Peminjaman
                        </button>
                    </form>
                    <p class="text-center text-[0.7rem] text-muted">Permintaan akan ditinjau admin.</p>
                @else
                    <form method="POST" action="{{ route('member.reservations.store') }}">
                        @csrf
                        <input type="hidden" name="book_id" value="{{ $book->id }}">
                        <button type="submit" class="btn btn-ghost w-full">
                            <x-icon name="calendar" class="h-4 w-4" />
                            Booking Buku Ini
                        </button>
                    </form>
                    <p class="text-center text-[0.7rem] text-muted">Anda akan masuk antrean saat buku tersedia kembali.</p>
                @endif
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
                        @if ($book->social_trend) <span class="badge border-violet/25 bg-violet/10 text-violet-300">Sedang Tren</span> @endif
                    </div>
                </div>

                <p class="mt-4 whitespace-pre-line text-sm leading-relaxed text-body">{{ $book->description ?: 'Belum ada deskripsi untuk buku ini.' }}</p>

                @if (! empty($book->themes))
                    <div class="mt-4 flex flex-wrap gap-1.5">
                        @foreach ($book->themes as $theme)
                            <span class="badge border-line bg-white/5 text-muted">#{{ $theme }}</span>
                        @endforeach
                    </div>
                @endif

                @if ($book->award_name || $book->public_figure_name || $book->social_trend_source)
                    <div class="mt-5 grid gap-2 rounded-xl border border-line bg-white/[0.02] p-4 text-xs text-muted sm:grid-cols-3">
                        @if ($book->award_name)
                            <div><p class="font-semibold text-body">Penghargaan</p><p>{{ $book->award_name }}</p></div>
                        @endif
                        @if ($book->public_figure_name)
                            <div><p class="font-semibold text-body">Rekomendasi tokoh</p><p>{{ $book->public_figure_name }}</p></div>
                        @endif
                        @if ($book->social_trend_source)
                            <div><p class="font-semibold text-body">Sumber tren</p><p>{{ $book->social_trend_source }}</p></div>
                        @endif
                    </div>
                @endif
            </section>

            <section class="surface-card p-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-bold text-ink">Ulasan Pembaca</h2>
                        <p class="text-xs text-muted">{{ $book->reviews->count() }} ulasan tercatat</p>
                    </div>
                    <span class="inline-flex items-center gap-1 rounded-full border border-warning/25 bg-warning/10 px-3 py-1 text-sm font-bold text-warning">
                        <x-icon name="star" class="h-4 w-4" />
                        {{ number_format((float) $book->average_rating, 1) }}
                    </span>
                </div>

                <div class="mt-4 space-y-3">
                    @forelse ($book->reviews as $review)
                        <article class="rounded-xl border border-line bg-white/[0.02] p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2">
                                    <span class="grid h-8 w-8 place-items-center rounded-full bg-gradient-to-br from-accent/30 to-violet/30 text-xs font-bold text-white">
                                        {{ strtoupper(substr($review->user?->name ?? 'A', 0, 1)) }}
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-ink">{{ $review->user?->name ?? 'Anggota' }}</p>
                                        <p class="text-[0.7rem] text-muted">{{ $review->created_at?->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-0.5 text-warning">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <x-icon name="star" class="h-3.5 w-3.5 {{ $i <= $review->rating ? 'text-warning' : 'text-muted/40' }}" />
                                    @endfor
                                </div>
                            </div>
                            @if ($review->review)
                                <p class="mt-2 text-sm leading-relaxed text-body">{{ $review->review }}</p>
                            @endif
                        </article>
                    @empty
                        <p class="rounded-xl border border-dashed border-line px-4 py-8 text-center text-sm text-muted">Belum ada ulasan. Jadilah yang pertama setelah selesai meminjam.</p>
                    @endforelse
                </div>

                <form method="POST" action="{{ route('member.reviews.store', $book) }}" class="mt-5 space-y-3 border-t border-line pt-5">
                    @csrf
                    <div>
                        <span class="field-label">Rating Anda</span>
                        <div class="flex items-center gap-1" data-star-input>
                            <input type="hidden" name="rating" value="{{ old('rating', $userReview?->rating ?? 5) }}">
                            @for ($i = 1; $i <= 5; $i++)
                                <button type="button" data-star="{{ $i }}" class="rounded p-0.5 transition hover:scale-110" aria-label="{{ $i }} bintang">
                                    <x-icon name="star" class="h-6 w-6 {{ $i <= ($userReview?->rating ?? 5) ? 'text-warning' : 'text-muted' }}" />
                                </button>
                            @endfor
                        </div>
                    </div>
                    <div>
                        <label for="review" class="field-label">Ulasan (opsional)</label>
                        <textarea id="review" name="review" rows="3" maxlength="2000" class="field-input resize-y" placeholder="Ceritakan kesan Anda…">{{ old('review', $userReview?->review) }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">{{ $userReview ? 'Perbarui Ulasan' : 'Kirim Ulasan' }}</button>
                    <p class="text-[0.7rem] text-muted">Ulasan hanya dapat dikirim setelah peminjaman buku ini selesai.</p>
                </form>
            </section>

            @if ($relatedBooks->isNotEmpty())
                <section class="space-y-4">
                    <h2 class="text-base font-bold text-ink">Buku lain di genre ini</h2>
                    <div class="stagger grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        @foreach ($relatedBooks as $related)
                            <x-book-card :book="$related" compact />
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>
@endsection
