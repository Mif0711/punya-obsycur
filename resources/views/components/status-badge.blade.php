@props(['status' => 'pending', 'label' => null])

@php
    $map = [
        'pending' => ['label' => 'Menunggu', 'class' => 'bg-warning/10 text-warning border-warning/30'],
        'approved' => ['label' => 'Disetujui', 'class' => 'bg-accent/10 text-accent-bright border-accent/30'],
        'borrowed' => ['label' => 'Dipinjam', 'class' => 'bg-violet/10 text-violet-300 border-violet/30'],
        'returned' => ['label' => 'Dikembalikan', 'class' => 'bg-success/10 text-success border-success/30'],
        'rejected' => ['label' => 'Ditolak', 'class' => 'bg-danger/10 text-danger border-danger/30'],
        'cancelled' => ['label' => 'Dibatalkan', 'class' => 'bg-white/5 text-muted border-line'],
        'fulfilled' => ['label' => 'Selesai', 'class' => 'bg-success/10 text-success border-success/30'],
        'active' => ['label' => 'Aktif', 'class' => 'bg-success/10 text-success border-success/30'],
        'lost' => ['label' => 'Hilang', 'class' => 'bg-danger/10 text-danger border-danger/30'],
        'replaced' => ['label' => 'Diganti', 'class' => 'bg-violet/10 text-violet-300 border-violet/30'],
        'title' => ['label' => 'Judul Buku', 'class' => 'bg-accent/10 text-accent-bright border-accent/30'],
        'genre' => ['label' => 'Genre', 'class' => 'bg-violet/10 text-violet-300 border-violet/30'],
        'publisher' => ['label' => 'Penerbit', 'class' => 'bg-warning/10 text-warning border-warning/30'],
        'author' => ['label' => 'Pengarang', 'class' => 'bg-success/10 text-success border-success/30'],
        'paid' => ['label' => 'Lunas', 'class' => 'bg-success/10 text-success border-success/30'],
        'unpaid' => ['label' => 'Belum lunas', 'class' => 'bg-danger/10 text-danger border-danger/30'],
        'overdue' => ['label' => 'Terlambat', 'class' => 'bg-danger/10 text-danger border-danger/30'],
    ];

    $state = $map[$status] ?? ['label' => ucfirst(str_replace('_', ' ', $status)), 'class' => 'bg-white/5 text-body border-line'];
@endphp

<span {{ $attributes->merge(['class' => 'badge '.$state['class']]) }}>
    <span class="h-1.5 w-1.5 rounded-full bg-current opacity-80"></span>
    {{ $label ?? $state['label'] }}
</span>
