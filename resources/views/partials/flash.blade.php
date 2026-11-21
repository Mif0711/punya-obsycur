@if (session('success'))
    <div data-flash class="flash mb-5 flex items-start gap-3 rounded-xl border border-success/30 bg-success/10 px-4 py-3 text-sm text-success">
        <x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0" />
        <p class="flex-1">{{ session('success') }}</p>
        <button type="button" data-flash-close class="text-success/70 transition hover:text-success" aria-label="Tutup notifikasi">
            <x-icon name="x" class="h-4 w-4" />
        </button>
    </div>
@endif

@if (session('error'))
    <div data-flash class="flash mb-5 flex items-start gap-3 rounded-xl border border-danger/30 bg-danger/10 px-4 py-3 text-sm text-danger">
        <x-icon name="alert" class="mt-0.5 h-4 w-4 shrink-0" />
        <p class="flex-1">{{ session('error') }}</p>
        <button type="button" data-flash-close class="text-danger/70 transition hover:text-danger" aria-label="Tutup notifikasi">
            <x-icon name="x" class="h-4 w-4" />
        </button>
    </div>
@endif

@if ($errors->any())
    <div data-flash class="flash mb-5 rounded-xl border border-danger/30 bg-danger/10 px-4 py-3 text-sm text-danger">
        <div class="flex items-start gap-3">
            <x-icon name="alert" class="mt-0.5 h-4 w-4 shrink-0" />
            <div class="flex-1">
                <p class="font-semibold">Ada {{ $errors->count() }} hal yang perlu diperbaiki:</p>
                <ul class="mt-2 list-inside list-disc space-y-1 text-danger/90">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" data-flash-close class="text-danger/70 transition hover:text-danger" aria-label="Tutup notifikasi">
                <x-icon name="x" class="h-4 w-4" />
            </button>
        </div>
    </div>
@endif
