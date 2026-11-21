@props(['label', 'value', 'hint' => null, 'icon' => 'chart', 'tone' => 'accent', 'suffix' => null])

@php
    $tones = [
        'accent' => 'from-accent/20 to-transparent text-accent-bright border-accent/20',
        'violet' => 'from-violet/20 to-transparent text-violet-300 border-violet/20',
        'success' => 'from-success/20 to-transparent text-success border-success/20',
        'warning' => 'from-warning/20 to-transparent text-warning border-warning/20',
        'danger' => 'from-danger/20 to-transparent text-danger border-danger/20',
    ];
    $toneClass = $tones[$tone] ?? $tones['accent'];
@endphp

<div {{ $attributes->merge(['class' => 'surface-card relative overflow-hidden p-5']) }}>
    <div class="pointer-events-none absolute inset-x-0 -top-16 h-32 bg-gradient-to-b {{ $toneClass }} opacity-40 blur-2xl"></div>
    <div class="relative flex items-start justify-between gap-4">
        <div>
            <p class="text-[0.68rem] font-semibold uppercase tracking-[0.16em] text-muted">{{ $label }}</p>
            <p class="mt-2 text-2xl font-bold tracking-tight text-ink">
                {{ $value }}@if ($suffix)<span class="ml-1 text-sm font-medium text-muted">{{ $suffix }}</span>@endif
            </p>
            @if ($hint)
                <p class="mt-1 text-xs text-muted">{{ $hint }}</p>
            @endif
        </div>
        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl border {{ $toneClass }}">
            <x-icon :name="$icon" class="h-5 w-5" />
        </span>
    </div>
</div>
