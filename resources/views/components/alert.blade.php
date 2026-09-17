@props(['type' => 'success'])

@php
    $styles = [
        'success' => 'border-accent-300 bg-accent-100 text-accent-700',
        'error' => 'border-red-200 bg-red-50 text-red-700',
        'info' => 'border-sky-200 bg-sky-50 text-sky-700',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
    ][$type] ?? 'border-stone-200 bg-stone-50 text-stone-700';
@endphp

<div {{ $attributes->merge(['class' => 'rounded-xl border px-4 py-3 text-sm font-medium '.$styles]) }} role="alert">
    {{ $slot }}
</div>
