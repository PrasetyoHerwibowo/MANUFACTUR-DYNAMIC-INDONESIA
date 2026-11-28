@props([
    'label',
    'value',
    'icon' => 'fas fa-chart-line',
    'gradient' => 'stat-card-gradient-1',
    'note' => null,
])

<div class="{{ $gradient }} flex h-full transform flex-col justify-between rounded-2xl p-5 text-white shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
    <div class="mb-4 flex items-center justify-between gap-2.5">
        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white/20 backdrop-blur">
            <i aria-hidden="true" class="{{ $icon }} text-base"></i>
        </div>

        @if ($note)
            <span title="{{ $note }}"
                  class="inline-flex h-6 max-w-full items-center truncate whitespace-nowrap rounded-full bg-white/20 px-2.5 text-[11px] font-semibold leading-none backdrop-blur">
                {{ $note }}
            </span>
        @endif
    </div>

    <div>
        <p class="truncate text-2xl font-bold tracking-tight">{{ $value }}</p>
        <p class="mt-0.5 truncate text-xs font-medium text-white/80">{{ $label }}</p>
    </div>
</div>
