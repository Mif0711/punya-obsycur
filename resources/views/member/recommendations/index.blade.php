@extends('layouts.member')

@section('title', 'Rekomendasi Saya')

@section('content')
    <x-page-header eyebrow="Dipersonalisasi" title="Rekomendasi untuk Anda" subtitle="Dihitung dari delapan sinyal: riwayat bacaan, pencarian, kemiripan pembaca, rating, pilihan editor, penghargaan, tokoh publik, serta tren dan tema musiman.">
        <x-slot:actions>
            <a href="{{ route('member.books.index') }}" class="btn btn-ghost">
                <x-icon name="book" class="h-4 w-4" />
                Katalog Lengkap
            </a>
        </x-slot:actions>
    </x-page-header>

    @php
        $signals = [
            ['icon' => 'swap', 'label' => 'Riwayat bacaan'],
            ['icon' => 'search', 'label' => 'Riwayat pencarian'],
            ['icon' => 'users', 'label' => 'Pembaca serupa'],
            ['icon' => 'star', 'label' => 'Rating & ulasan'],
            ['icon' => 'sparkles', 'label' => 'Pilihan editor'],
            ['icon' => 'chart', 'label' => 'Penghargaan'],
            ['icon' => 'user', 'label' => 'Tokoh publik'],
            ['icon' => 'trending', 'label' => 'Tren & tema'],
        ];
    @endphp

    <section class="surface-card p-4">
        <div class="flex flex-wrap items-center gap-2">
            <span class="mr-1 text-[0.65rem] font-semibold uppercase tracking-[0.18em] text-muted">Sinyal aktif</span>
            @foreach ($signals as $signal)
                <span class="inline-flex items-center gap-1.5 rounded-full border border-line bg-white/[0.03] px-2.5 py-1 text-[0.7rem] font-medium text-body">
                    <x-icon :name="$signal['icon']" class="h-3.5 w-3.5 text-accent" />
                    {{ $signal['label'] }}
                </span>
            @endforeach
        </div>
    </section>

    @if ($recommendedBooks->isEmpty())
        <x-empty-state title="Rekomendasi belum tersedia" description="Pinjam beberapa buku atau lakukan pencarian agar sistem dapat mengenali selera Anda. Rekomendasi akan muncul di sini." icon="sparkles">
            <x-slot:action>
                <a href="{{ route('member.books.index') }}" class="btn btn-primary btn-sm">Mulai Jelajahi</a>
            </x-slot:action>
        </x-empty-state>
    @else
        <div class="flex items-center justify-between gap-3">
            <p class="text-xs text-muted">
                <span class="font-semibold text-body">{{ $recommendedBooks->count() }}</span> buku dipilih khusus untuk Anda.
            </p>
            <span class="hidden items-center gap-1.5 text-[0.7rem] text-muted sm:inline-flex">
                <x-icon name="refresh" class="h-3.5 w-3.5" />
                Diperbarui mengikuti aktivitas terbaru
            </span>
        </div>

        <div class="stagger grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($recommendedBooks as $book)
                <x-book-card :book="$book" show-reasons />
            @endforeach
        </div>
    @endif
@endsection
