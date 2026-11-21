@extends('layouts.admin')

@section('title', 'Ubah Buku')

@section('content')
    <x-page-header :title="'Ubah: '.$book->title" subtitle="Perbarui metadata dan penanda rekomendasi buku.">
        <x-slot:actions>
            <a href="{{ route('admin.books.show', $book) }}" class="btn btn-ghost">
                <x-icon name="eye" class="h-4 w-4" />
                Detail
            </a>
            <a href="{{ route('admin.books.index') }}" class="btn btn-ghost">
                <x-icon name="arrow-left" class="h-4 w-4" />
                Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    @include('admin.books._form', [
        'action' => route('admin.books.update', $book),
        'method' => 'PUT',
        'submitLabel' => 'Simpan Perubahan',
    ])
@endsection
