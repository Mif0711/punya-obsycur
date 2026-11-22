@extends('layouts.member')

@section('title', 'Usulan Buku')

@section('content')
    <x-page-header title="Usulan Buku Baru" subtitle="Lihat usulan anggota lain dan beri vote disertai alasan. Jika buku belum ada, ajukan usulan baru.">
        <x-slot:actions>
            <a href="{{ route('member.book-requests.create') }}" class="btn btn-primary">
                <x-icon name="plus" class="h-4 w-4" />
                Ajukan Buku
            </a>
        </x-slot:actions>
    </x-page-header>

    @if ($requests->isEmpty())
        <x-empty-state title="Belum ada usulan" description="Jadilah yang pertama mengajukan buku untuk koleksi perpustakaan." icon="bookmark">
            <x-slot:action>
                <a href="{{ route('member.book-requests.create') }}" class="btn btn-primary btn-sm">Buat Usulan Pertama</a>
            </x-slot:action>
        </x-empty-state>
    @else
        <div class="stagger grid gap-4 lg:grid-cols-2">
            @foreach ($requests as $bookRequest)
                @php
                    $isOwner = $bookRequest->user_id === auth()->id();
                    $hasVoted = $bookRequest->votes->contains('user_id', auth()->id());
                @endphp

                <article class="surface-card flex h-full flex-col p-5">
                    <div class="flex flex-wrap items-center gap-2">
                        <x-status-badge :status="$bookRequest->status" />
                        @if ($isOwner)
                            <span class="badge border-accent/25 bg-accent/10 text-accent-bright">Usulan Anda</span>
                        @endif
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
                            @foreach ($bookRequest->votes as $vote)
                                <div class="rounded-lg border border-line bg-white/[0.02] px-3 py-2 text-xs">
                                    <p class="font-semibold text-body">{{ $vote->user?->name ?? 'Anggota' }}</p>
                                    <p class="mt-0.5 leading-relaxed text-muted">{{ $vote->comment }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-4 border-t border-line/70 pt-3">
                        @if ($isOwner)
                            <p class="text-xs text-muted">Anda tidak dapat memberi vote pada usulan sendiri.</p>
                        @elseif ($hasVoted)
                            <form method="POST" action="{{ route('member.book-requests.vote', $bookRequest) }}">
                                @csrf
                                <button type="submit" class="btn btn-ghost btn-sm">
                                    <x-icon name="x" class="h-3.5 w-3.5" />
                                    Batalkan Vote
                                </button>
                            </form>
                        @else
                            <details class="group rounded-xl border border-line bg-white/[0.02]">
                                <summary class="flex cursor-pointer list-none items-center justify-between rounded-xl px-4 py-2.5 text-xs font-semibold text-accent-bright transition hover:bg-white/5">
                                    <span class="flex items-center gap-2">
                                        <x-icon name="star" class="h-3.5 w-3.5" />
                                        Vote buku ini
                                    </span>
                                    <x-icon name="chevron-down" class="h-3.5 w-3.5 transition group-open:rotate-180" />
                                </summary>
                                <form method="POST" action="{{ route('member.book-requests.vote', $bookRequest) }}" class="space-y-2 border-t border-line px-4 py-3">
                                    @csrf
                                    <label for="vote-comment-{{ $bookRequest->id }}" class="sr-only">Alasan vote</label>
                                    <textarea id="vote-comment-{{ $bookRequest->id }}" name="comment" rows="2" required minlength="3" maxlength="1000"
                                        class="field-input resize-y" placeholder="Tuliskan alasan Anda memilih buku ini...">{{ old('comment') }}</textarea>
                                    @error('comment') <p class="text-xs text-danger">{{ $message }}</p> @enderror
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <x-icon name="check" class="h-3.5 w-3.5" />
                                        Kirim Vote
                                    </button>
                                </form>
                            </details>
                        @endif

                        @if ($isOwner)
                            <form method="POST" action="{{ route('member.book-requests.destroy', $bookRequest) }}"
                                class="mt-2" data-confirm="Hapus usulan ini? Jika sudah ada vote, satu vote akan dikurangi dan usulan tetap disimpan.">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <x-icon name="trash" class="h-3.5 w-3.5" />
                                    Hapus Usulan
                                </button>
                            </form>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        {{ $requests->links('partials.pagination') }}
    @endif
@endsection
