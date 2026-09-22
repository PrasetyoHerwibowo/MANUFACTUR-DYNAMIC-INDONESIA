@props([
    'name',
    'label' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => '— Pilih —',
    'hint' => null,
    'required' => false,
])

@php
    $inputId = $attributes->get('id', 'field-'.str_replace(['[', ']', '.'], ['-', '', '-'], $name));
    $hasError = $errors->has($name);
    $current = old($name, $selected);
@endphp

<div>
    @if ($label)
        <label for="{{ $inputId }}" class="mb-1.5 block text-sm font-semibold text-stone-700 dark:text-gray-300">
            {{ $label }}@if ($required)<span class="text-red-500"> *</span>@endif
        </label>
    @endif

    <select
        name="{{ $name }}"
        id="{{ $inputId }}"
        @if ($required) required @endif
        {{ $attributes->merge([
            'class' => 'w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-stone-800 shadow-sm outline-none transition focus:ring-2 dark:bg-gray-900 dark:text-gray-100 '
                .($hasError
                    ? 'border-red-300 focus:border-red-400 focus:ring-red-100 dark:border-red-500/60 dark:focus:border-red-400 dark:focus:ring-red-500/20'
                    : 'border-stone-300 focus:border-coffee-500 focus:ring-coffee-200 dark:border-gray-600 dark:focus:border-coffee-500 dark:focus:ring-coffee-900'),
        ]) }}
    >
        @if ($placeholder !== null)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected((string) $current === (string) $optionValue)>{{ $optionLabel }}</option>
        @endforeach
    </select>

    @error($name)
        <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
    @enderror

    @if ($hint)
        <p class="mt-1.5 text-xs text-stone-500 dark:text-gray-400">{{ $hint }}</p>
    @endif
</div>
