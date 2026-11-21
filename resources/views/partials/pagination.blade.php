@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="pagination mt-6">
        @if ($paginator->onFirstPage())
            <span class="inline-flex min-w-9 items-center justify-center rounded-lg border border-line px-2.5 py-1.5 text-xs text-muted opacity-40">
                <x-icon name="chevron-left" class="h-4 w-4" />
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Halaman sebelumnya" class="inline-flex min-w-9 items-center justify-center rounded-lg border border-line px-2.5 py-1.5 text-xs text-body transition hover:border-accent hover:text-white">
                <x-icon name="chevron-left" class="h-4 w-4" />
            </a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="inline-flex min-w-9 items-center justify-center rounded-lg border border-line px-2.5 py-1.5 text-xs text-muted">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page" class="inline-flex min-w-9 items-center justify-center rounded-lg border border-transparent bg-gradient-to-br from-accent to-violet px-2.5 py-1.5 text-xs font-bold text-white">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="inline-flex min-w-9 items-center justify-center rounded-lg border border-line px-2.5 py-1.5 text-xs text-body transition hover:border-accent hover:text-white">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Halaman berikutnya" class="inline-flex min-w-9 items-center justify-center rounded-lg border border-line px-2.5 py-1.5 text-xs text-body transition hover:border-accent hover:text-white">
                <x-icon name="chevron-right" class="h-4 w-4" />
            </a>
        @else
            <span class="inline-flex min-w-9 items-center justify-center rounded-lg border border-line px-2.5 py-1.5 text-xs text-muted opacity-40">
                <x-icon name="chevron-right" class="h-4 w-4" />
            </span>
        @endif
    </nav>
@endif
