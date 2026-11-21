@extends('layouts.admin')

@section('title', $title)

@section('content')
    <x-page-header :title="$title" :subtitle="'Lengkapi informasi '.strtolower($label).' agar mudah ditemukan pembaca.'">
        <x-slot:actions>
            <a href="{{ route('admin.'.$resource.'.index') }}" class="btn btn-ghost">
                <x-icon name="arrow-left" class="h-4 w-4" />
                Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_20rem]">
        <form method="POST" action="{{ route('admin.'.$resource.'.store') }}" class="surface-card space-y-5 p-6">
            @csrf

            <div>
                <label for="name" class="field-label">Nama {{ $label }}</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required maxlength="150" class="field-input" />
                @error('name') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
            </div>

            @if ($resource === 'publishers')
                <div>
                    <label for="city" class="field-label">Kota</label>
                    <input id="city" name="city" type="text" value="{{ old('city') }}" maxlength="100" class="field-input" placeholder="contoh: Jakarta" />
                    @error('city') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
            @endif

            <div>
                <label for="description" class="field-label">Deskripsi / Detail</label>
                <textarea id="description" name="description" rows="6" maxlength="2000" class="field-input resize-y" placeholder="Tuliskan gambaran singkat tentang {{ strtolower($label) }} ini…">{{ old('description') }}</textarea>
                @error('description') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-end gap-2 border-t border-line pt-4">
                <a href="{{ route('admin.'.$resource.'.index') }}" class="btn btn-ghost">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <x-icon name="check" class="h-4 w-4" />
                    Simpan {{ $label }}
                </button>
            </div>
        </form>

        <aside class="surface-card h-fit p-5">
            <h3 class="text-sm font-bold text-ink">Tips pengisian</h3>
            <ul class="mt-3 space-y-2.5 text-xs leading-relaxed text-muted">
                <li class="flex gap-2"><x-icon name="check" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-success" /> Gunakan nama resmi agar pencarian konsisten.</li>
                <li class="flex gap-2"><x-icon name="check" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-success" /> Deskripsi membantu anggota mengenali {{ strtolower($label) }}.</li>
                <li class="flex gap-2"><x-icon name="check" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-success" /> Data ini muncul di halaman detail dan filter katalog.</li>
            </ul>
        </aside>
    </div>
@endsection
