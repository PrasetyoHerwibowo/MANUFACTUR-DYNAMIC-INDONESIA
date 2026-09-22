@props([
    'status',
    'label' => null,
])

@php
    /** Warna & ikon untuk status pesanan maupun status pembayaran. */
    $badges = [
        'menunggu_pembayaran' => [
            'text' => 'Menunggu Pembayaran',
            'class' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-500/15 dark:text-yellow-300',
            'icon' => 'fas fa-hourglass-half',
        ],
        'lunas' => [
            'text' => 'Lunas',
            'class' => 'bg-green-100 text-green-800 dark:bg-green-500/15 dark:text-green-300',
            'icon' => 'fas fa-check-circle',
        ],
        'gagal' => [
            'text' => 'Gagal',
            'class' => 'bg-red-100 text-red-800 dark:bg-red-500/15 dark:text-red-300',
            'icon' => 'fas fa-times-circle',
        ],
        'dibatalkan' => [
            'text' => 'Dibatalkan',
            'class' => 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
            'icon' => 'fas fa-ban',
        ],
    ];

    $badge = $badges[$status] ?? [
        'text' => ucfirst(str_replace('_', ' ', (string) $status)),
        'class' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
        'icon' => 'fas fa-circle',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-bold '.$badge['class']]) }}>
    <i aria-hidden="true" class="{{ $badge['icon'] }} text-[10px]"></i>
    {{ $label ?? $badge['text'] }}
</span>
