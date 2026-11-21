@props(['title' => 'Belum ada data', 'description' => null, 'icon' => 'inbox'])

<div {{ $attributes->merge(['class' => 'surface-card flex flex-col items-center justify-center gap-3 px-6 py-12 text-center']) }}>
    <span class="grid h-14 w-14 place-items-center rounded-2xl border border-line bg-white/5 text-muted">
        <x-icon :name="$icon" class="h-6 w-6" />
    </span>
    <div>
        <p class="font-semibold text-ink">{{ $title }}</p>
        @if ($description)
            <p class="mt-1 max-w-sm text-sm text-muted">{{ $description }}</p>
        @endif
    </div>
    @isset($action)
        <div class="mt-1">{{ $action }}</div>
    @endisset
</div>
