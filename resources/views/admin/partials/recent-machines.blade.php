{{-- Daftar model mesin terbaru (dipakai di dashboard) --}}
<div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
    <div class="flex items-center justify-between border-b border-stone-200 px-5 py-4">
        <h2 class="text-base font-extrabold text-stone-900">Model mesin terbaru</h2>
        <a href="{{ route('admin.machines.index') }}" class="text-xs font-bold text-brand-700">Lihat semua</a>
    </div>

    @if ($recentMachines->isNotEmpty())
        <ul class="divide-y divide-stone-100">
            @foreach ($recentMachines as $machine)
                <li class="flex items-center gap-4 px-5 py-3.5">
                    <img src="{{ $machine->photo }}" alt="{{ $machine->name }}" class="h-12 w-16 rounded-lg object-cover">

                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-bold text-stone-800">{{ $machine->name }}</p>
                        <p class="truncate text-xs text-stone-500">{{ $machine->category?->name }}</p>
                    </div>

                    <a href="{{ route('admin.machines.edit', $machine) }}"
                       class="rounded-lg border border-stone-300 px-3 py-1.5 text-xs font-bold text-stone-600 transition hover:bg-stone-50">
                        Edit
                    </a>
                </li>
            @endforeach
        </ul>
    @else
        <p class="px-5 py-8 text-center text-sm text-stone-500">Belum ada data mesin.</p>
    @endif
</div>

{{-- Daftar pesan terbaru (dipakai di dashboard) --}}
<div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
    <div class="flex items-center justify-between border-b border-stone-200 px-5 py-4">
        <h2 class="text-base font-extrabold text-stone-900">Pesan terbaru</h2>
        <a href="{{ route('admin.messages.index') }}" class="text-xs font-bold text-brand-700">Lihat semua</a>
    </div>

    @if ($recentMessages->isNotEmpty())
        <ul class="divide-y divide-stone-100">
            @foreach ($recentMessages as $message)
                <li class="flex items-start gap-3 px-5 py-3.5">
                    <span class="mt-1 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-50 text-xs font-bold text-brand-700">
                        {{ strtoupper(substr($message->name, 0, 1)) }}
                    </span>

                    <div class="min-w-0 flex-1">
                        <p class="flex items-center gap-2 text-sm font-bold text-stone-800">
                            {{ $message->name }}
                            @if (! $message->is_read)
                                <span class="rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-600">Baru</span>
                            @endif
                        </p>
                        <p class="truncate text-xs text-stone-500">{{ $message->subject ?: 'Tanpa subjek' }}</p>
                    </div>

                    <a href="{{ route('admin.messages.show', $message) }}"
                       class="rounded-lg border border-stone-300 px-3 py-1.5 text-xs font-bold text-stone-600 transition hover:bg-stone-50">
                        Baca
                    </a>
                </li>
            @endforeach
        </ul>
    @else
        <p class="px-5 py-8 text-center text-sm text-stone-500">Belum ada pesan masuk.</p>
    @endif
</div>
