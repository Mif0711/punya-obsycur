@extends('layouts.admin')

@section('title', $title)

@section('content')
    <x-page-header :title="$title" :subtitle="'Perbarui informasi '.strtolower($label).' di katalog.'">
        <x-slot:actions>
            <a href="{{ route('admin.'.$resource.'.index') }}" class="btn btn-ghost">
                <x-icon name="arrow-left" class="h-4 w-4" />
                Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_20rem]">
        <form method="POST" action="{{ route('admin.'.$resource.'.update', $item) }}" class="surface-card space-y-5 p-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="field-label">Nama {{ $label }}</label>
                <input id="name" name="name" type="text" value="{{ old('name', $item->name) }}" required maxlength="150" class="field-input" />
                @error('name') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
            </div>

            @if ($resource === 'publishers')
                <div>
                    <label for="city" class="field-label">Kota</label>
                    <input id="city" name="city" type="text" value="{{ old('city', $item->city) }}" maxlength="100" class="field-input" />
                    @error('city') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
            @endif

            <div>
                <label for="description" class="field-label">Deskripsi / Detail</label>
                <textarea id="description" name="description" rows="6" maxlength="2000" class="field-input resize-y">{{ old('description', $item->description) }}</textarea>
                @error('description') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-end gap-2 border-t border-line pt-4">
                <a href="{{ route('admin.'.$resource.'.show', $item) }}" class="btn btn-ghost">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <x-icon name="check" class="h-4 w-4" />
                    Simpan Perubahan
                </button>
            </div>
        </form>

        <aside class="surface-card h-fit p-5">
            <h3 class="text-sm font-bold text-ink">Informasi</h3>
            <dl class="mt-3 space-y-3 text-xs">
                <div class="flex items-center justify-between border-b border-line/60 pb-2">
                    <dt class="text-muted">Dibuat</dt>
                    <dd class="text-body">{{ $item->created_at?->translatedFormat('d M Y') }}</dd>
                </div>
                <div class="flex items-center justify-between border-b border-line/60 pb-2">
                    <dt class="text-muted">Diperbarui</dt>
                    <dd class="text-body">{{ $item->updated_at?->translatedFormat('d M Y') }}</dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt class="text-muted">Jumlah buku</dt>
                    <dd class="text-body">{{ $item->books()->count() }}</dd>
                </div>
            </dl>
        </aside>
    </div>
@endsection
