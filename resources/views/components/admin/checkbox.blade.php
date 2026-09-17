@props([
    'name',
    'label' => null,
    'checked' => false,
    'hint' => null,
])

@php
    $inputId = $attributes->get('id', 'check-'.str_replace(['[', ']', '.'], ['-', '', '-'], $name));
@endphp

<label for="{{ $inputId }}" {{ $attributes->merge(['class' => 'flex cursor-pointer items-start gap-3 rounded-xl border border-stone-200 bg-stone-50/70 px-4 py-3 transition hover:border-brand-300']) }}>
    <input type="hidden" name="{{ $name }}" value="0">
    <input
        type="checkbox"
        name="{{ $name }}"
        id="{{ $inputId }}"
        value="1"
        @checked(old($name, $checked))
        class="mt-0.5 size-4 shrink-0 rounded border-stone-300 text-brand-600 focus:ring-brand-300"
    >

    <span class="text-sm">
        <span class="block font-semibold text-stone-700">{{ $label }}</span>
        @if ($hint)
            <span class="mt-0.5 block text-xs text-stone-500">{{ $hint }}</span>
        @endif
    </span>
</label>
