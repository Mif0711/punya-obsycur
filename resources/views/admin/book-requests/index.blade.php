@extends('layouts.admin')

@section('title', 'Buku Baru')

@section('content')
    <x-page-header title="Pengajuan Buku Baru" subtitle="Daftar usulan anggota beserta vote dan alasan dari anggota lain.">
        <x-slot:actions>
            <form method="POST" action="{{ route('admin.book-requests.destroy-all') }}" data-confirm="Hapus semua pengajuan beserta vote-nya?">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <x-icon name="trash" class="h-4 w-4" />
                    Hapus Semua
                </button>
            </form>
        </x-slot:actions>
    </x-page-header>

    @if ($requests->isEmpty())
        <x-empty-state title="Belum ada pengajuan" description="Usulan buku dari anggota akan muncul di sini." icon="bookmark" />
    @else
        <div class="stagger grid gap-4 lg:grid-cols-2">
            @foreach ($requests as $bookRequest)
                <article class="surface-card flex h-full flex-col p-5">
                    <div class="flex flex-wrap items-center gap-2">
                        <x-status-badge :status="$bookRequest->status" />
                        <span class="badge border-line bg-white/5 text-body">
                            <x-icon name="star" class="h-3.5 w-3.5" />
                            {{ $bookRequest->votes->count() }} vote
                        </span>
                        <span class="ml-auto text-[0.7rem] text-muted">{{ $bookRequest->created_at?->diffForHumans() }}</span>
                    </div>

                    <h3 class="mt-3 text-base font-bold text-ink">{{ $bookRequest->subjectLabel() }}</h3>
                    <p class="text-xs text-muted">Diajukan oleh {{ $bookRequest->user?->name ?? 'Anggota' }}</p>

                    <div class="mt-2 flex flex-wrap gap-1.5">
                        @if ($bookRequest->genre)
                            <span class="badge border-line bg-white/5 text-body">Genre: {{ $bookRequest->genre }}</span>
                        @endif
                        @if ($bookRequest->publisher)
                            <span class="badge border-line bg-white/5 text-body">Penerbit: {{ $bookRequest->publisher }}</span>
                        @endif
                        @if ($bookRequest->author)
                            <span class="badge border-line bg-white/5 text-body">Pengarang: {{ $bookRequest->author }}</span>
                        @endif
                    </div>

                    <p class="mt-3 rounded-xl border border-line bg-white/[0.02] px-4 py-3 text-sm leading-relaxed text-body">
                        {{ $bookRequest->reason }}
                    </p>

                    @if ($bookRequest->votes->isNotEmpty())
                        <div class="mt-3 space-y-2">
                            <p class="text-[0.7rem] font-semibold uppercase tracking-wider text-muted">Alasan vote</p>
                            @foreach ($bookRequest->votes as $vote)
                                <div class="rounded-lg border border-line bg-white/[0.02] px-3 py-2 text-xs">
                                    <p class="font-semibold text-body">{{ $vote->user?->name ?? 'Anggota' }}</p>
                                    <p class="mt-0.5 leading-relaxed text-muted">{{ $vote->comment }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </article>
            @endforeach
        </div>

        {{ $requests->links('partials.pagination') }}
    @endif
@endsection
