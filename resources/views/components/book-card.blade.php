@props(['book', 'showReasons' => false, 'compact' => false, 'href' => null])

@php
    $detailUrl = $href ?? route('member.books.show', $book);
@endphp

<article class="surface-card surface-card-hover group flex h-full flex-col overflow-hidden">
    <a href="{{ $detailUrl }}" class="block {{ $compact ? 'p-3 pb-0' : 'p-4 pb-0' }}">
        <div class="book-cover {{ $compact ? 'text-[0.7rem]' : '' }}">
            <div class="absolute inset-0 opacity-90 [background:radial-gradient(120%_120%_at_10%_0%,rgba(139,92,246,.45),transparent_55%),linear-gradient(160deg,#1b2542,#0c1322_70%)]"></div>
            @if ($book->cover_path)
                <img src="{{ asset('storage/'.$book->cover_path) }}" alt="Sampul {{ $book->title }}" loading="lazy">
            @endif
            <div class="relative z-10">
                <p class="text-[0.65rem] font-semibold uppercase tracking-[0.18em] text-white/60">{{ $book->genre?->name ?? 'Katalog' }}</p>
                <h3 class="mt-1 line-clamp-2 font-semibold leading-snug text-white drop-shadow">{{ $book->title }}</h3>
            </div>
            @if ($book->average_rating >= 4)
                <span class="absolute right-2 top-2 z-10 inline-flex items-center gap-1 rounded-full bg-black/55 px-2 py-1 text-[0.65rem] font-semibold text-warning backdrop-blur">
                    <x-icon name="star" class="h-3 w-3" />
                    {{ number_format((float) $book->average_rating, 1) }}
                </span>
            @endif
        </div>
    </a>

    <div class="flex flex-1 flex-col gap-3 {{ $compact ? 'p-3' : 'p-4' }}">
        <div class="flex flex-wrap items-center gap-1.5">
            @if ($book->editor_pick)
                <span class="badge border-accent/30 bg-accent/10 text-accent-bright">Pilihan Editor</span>
            @endif
            @if ($book->award_winner)
                <span class="badge border-warning/30 bg-warning/10 text-warning">Penghargaan</span>
            @endif
            @if ($book->social_trend)
                <span class="badge border-violet/30 bg-violet/10 text-violet-300">Sedang Tren</span>
            @endif
        </div>

        <p class="text-sm text-muted">
            {{ $book->author?->name ?? 'Pengarang tidak diketahui' }}
        </p>

        @if ($showReasons && ! empty($book->recommendation_reasons))
            <ul class="space-y-1 text-xs text-body/80">
                @foreach (array_slice($book->recommendation_reasons, 0, 3) as $reason)
                    <li class="flex items-start gap-1.5">
                        <x-icon name="sparkles" class="mt-0.5 h-3 w-3 shrink-0 text-accent" />
                        {{ $reason }}
                    </li>
                @endforeach
            </ul>
        @endif

        <div class="mt-auto flex items-center justify-between gap-2 pt-1">
            <a href="{{ $detailUrl }}" class="text-xs font-semibold text-accent transition hover:text-accent-bright">
                Lihat detail
            </a>
            @isset($actions)
                <div class="flex items-center gap-2">{{ $actions }}</div>
            @endisset
        </div>
    </div>
</article>
