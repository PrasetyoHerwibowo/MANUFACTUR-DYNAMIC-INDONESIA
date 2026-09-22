@props([
    'label',
    'value',
    'icon' => 'fas fa-chart-line',
    'gradient' => 'stat-card-gradient-1',
    'note' => null,
])

<div class="{{ $gradient }} transform rounded-2xl p-5 text-white shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
    <div class="mb-3 flex items-center justify-between">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 backdrop-blur">
            <i aria-hidden="true" class="{{ $icon }} text-lg"></i>
        </div>

        @if ($note)
            <span class="rounded-full bg-white/20 px-2.5 py-0.5 text-[11px] font-semibold backdrop-blur">{{ $note }}</span>
        @endif
    </div>

    <p class="text-2xl font-bold">{{ $value }}</p>
    <p class="text-xs font-medium text-white/80">{{ $label }}</p>
</div>
