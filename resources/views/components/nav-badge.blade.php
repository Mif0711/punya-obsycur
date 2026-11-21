@props([
    'key' => '',
    'count' => 0,
    'tone' => 'danger',
])

@php
    $count = (int) $count;
@endphp

@if ($count > 0)
    @php
        $toneClass = $tone === 'accent'
            ? 'bg-accent text-white shadow-[0_0_12px_-4px_rgba(6,182,212,0.9)]'
            : 'bg-danger text-white shadow-[0_0_12px_-4px_rgba(244,63,94,0.9)]';
    @endphp
    <span data-nav-badge="{{ $key }}" data-count="{{ $count }}" class="ml-auto inline-flex min-w-[1.25rem] shrink-0 items-center justify-center rounded-full px-1.5 py-0.5 text-[0.65rem] font-bold leading-none {{ $toneClass }}" title="{{ $count }} notifikasi baru" aria-label="{{ $count }} notifikasi baru">{{ $count > 99 ? '99+' : $count }}</span>
@endif
