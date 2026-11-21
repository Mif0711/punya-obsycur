@extends('layouts.admin')

@section('title', 'Tambah Buku')

@section('content')
    <x-page-header title="Tambah Buku" subtitle="Lengkapi metadata buku dan tandai sinyal rekomendasinya.">
        <x-slot:actions>
            <a href="{{ route('admin.books.index') }}" class="btn btn-ghost">
                <x-icon name="arrow-left" class="h-4 w-4" />
                Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    @include('admin.books._form', [
        'action' => route('admin.books.store'),
        'method' => 'POST',
        'submitLabel' => 'Simpan Buku',
    ])
@endsection
