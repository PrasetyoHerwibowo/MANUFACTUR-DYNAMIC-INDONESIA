@props([
    'name',
    'label' => null,
    'current' => null,
    'hint' => null,
    'required' => false,
    'multiple' => false,
    'accept' => 'image/*',
])

@php
    $inputId = $attributes->get('id', 'file-'.str_replace(['[', ']', '.'], ['-', '', '-'], $name));
    $previewId = $inputId.'-preview';
    $hasError = $errors->has($name) || $errors->has($name.'.*');
@endphp

<div>
    @if ($label)
        <label for="{{ $inputId }}" class="mb-1.5 block text-sm font-semibold text-stone-700 dark:text-gray-300">
            {{ $label }}@if ($required)<span class="text-red-500"> *</span>@endif
        </label>
    @endif

    <div class="rounded-xl border border-dashed bg-stone-50/70 p-4 dark:bg-gray-900 {{ $hasError ? 'border-red-300 dark:border-red-500/60' : 'border-stone-300 dark:border-gray-600' }}">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
            <div class="h-24 w-32 shrink-0 overflow-hidden rounded-lg border border-stone-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                <img
                    id="{{ $previewId }}"
                    src="{{ $current ?: \App\Support\ImageUploader::placeholder() }}"
                    alt="Pratinjau foto"
                    class="h-full w-full object-cover"
                >
            </div>

            <div class="flex-1">
                <input
                    type="file"
                    name="{{ $name }}"
                    id="{{ $inputId }}"
                    accept="{{ $accept }}"
                    @if ($multiple) multiple @endif
                    data-preview="#{{ $previewId }}"
                    {{ $attributes->merge(['class' => 'block w-full cursor-pointer rounded-lg border border-stone-300 bg-white text-sm text-stone-600 file:mr-3 file:cursor-pointer file:rounded-l-lg file:border-0 file:bg-coffee-600 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-white hover:file:bg-coffee-700 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300']) }}
                >
                @if ($hint)
                    <p class="mt-2 text-xs text-stone-500 dark:text-gray-400">{{ $hint }}</p>
                @endif
            </div>
        </div>
    </div>

    @error($name)
        <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
    @enderror
    @error($name.'.*')
        <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
    @enderror
</div>
