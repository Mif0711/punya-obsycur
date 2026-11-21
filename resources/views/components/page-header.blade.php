@props(['title', 'subtitle' => null, 'eyebrow' => null])

<header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div>
        @if ($eyebrow)
            <p class="mb-1 text-[0.68rem] font-semibold uppercase tracking-[0.2em] text-accent">{{ $eyebrow }}</p>
        @endif
        <h1 class="text-xl font-bold tracking-tight text-ink sm:text-2xl">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-1 max-w-2xl text-sm text-muted">{{ $subtitle }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>
    @endisset
</header>
