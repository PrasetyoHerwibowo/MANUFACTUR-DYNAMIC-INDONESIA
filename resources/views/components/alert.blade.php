@props(['type' => 'success'])

@php
    $styles = [
        'success' => 'border-accent-300 bg-accent-100 text-accent-700 dark:border-accent-700/50 dark:bg-accent-700/15 dark:text-accent-300',
        'error' => 'border-red-200 bg-red-50 text-red-700 dark:border-red-500/40 dark:bg-red-500/10 dark:text-red-300',
        'info' => 'border-sky-200 bg-sky-50 text-sky-700 dark:border-sky-500/40 dark:bg-sky-500/10 dark:text-sky-300',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-500/40 dark:bg-amber-500/10 dark:text-amber-300',
    ][$type] ?? 'border-stone-200 bg-stone-50 text-stone-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300';
@endphp

<div {{ $attributes->merge(['class' => 'flex items-start gap-3 rounded-xl border px-4 py-3 text-sm font-medium '.$styles]) }} role="alert">
    {{ $slot }}
</div>
