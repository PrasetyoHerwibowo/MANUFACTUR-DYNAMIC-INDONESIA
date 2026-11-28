@props([
    'status',
    'label' => null,
])

@php
    /** Warna & ikon untuk status pesanan maupun status pembayaran. */
    $badges = [
        'menunggu_pembayaran' => [
            'text' => 'Menunggu Pembayaran',
            'class' => 'bg-amber-100 text-amber-800',
            'icon' => 'fas fa-hourglass-half',
        ],
        'lunas' => [
            'text' => 'Lunas',
            'class' => 'bg-emerald-100 text-emerald-800',
            'icon' => 'fas fa-check-circle',
        ],
        'gagal' => [
            'text' => 'Gagal',
            'class' => 'bg-red-100 text-red-800',
            'icon' => 'fas fa-times-circle',
        ],
        'dibatalkan' => [
            'text' => 'Dibatalkan',
            'class' => 'bg-zinc-200 text-zinc-700',
            'icon' => 'fas fa-ban',
        ],
    ];

    $badge = $badges[$status] ?? [
        'text' => ucfirst(str_replace('_', ' ', (string) $status)),
        'class' => 'bg-zinc-100 text-zinc-700',
        'icon' => 'fas fa-circle',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-1 text-[11px] font-bold leading-none '.$badge['class']]) }}>
    <i aria-hidden="true" class="{{ $badge['icon'] }} text-[10px]"></i>
    {{ $label ?? $badge['text'] }}
</span>
