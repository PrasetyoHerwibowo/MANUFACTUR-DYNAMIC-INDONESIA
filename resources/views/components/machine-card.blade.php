@props(['machine'])

<a href="{{ route('products.show', $machine) }}"
   class="group flex flex-col overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:border-brand-300 hover:shadow-xl">
    <div class="relative aspect-[4/3] overflow-hidden bg-stone-100">
        <img src="{{ $machine->photo }}" alt="{{ $machine->name }}" loading="lazy"
             class="h-full w-full object-cover transition duration-500 group-hover:scale-105">

        @if ($machine->category)
            <span class="absolute left-3 top-3 rounded-full bg-white/95 px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-brand-700 shadow-sm">
                {{ $machine->category->name }}
            </span>
        @endif

        @if ($machine->is_featured)
            <span class="absolute right-3 top-3 rounded-full bg-accent-500 px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-white shadow-sm">
                Unggulan
            </span>
        @endif
    </div>

    <div class="flex flex-1 flex-col p-5">
        @if ($machine->model_code)
            <p class="text-xs font-bold uppercase tracking-wider text-brand-500">{{ $machine->model_code }}</p>
        @endif

        <h3 class="mt-1 text-lg font-bold leading-snug text-stone-800 transition group-hover:text-brand-700">
            {{ $machine->name }}
        </h3>

        @php $preview = $machine->short_description ?: $machine->function; @endphp
        @if ($preview)
            <p class="mt-2 flex-1 text-sm leading-relaxed text-stone-500">
                {{ \Illuminate\Support\Str::limit(strip_tags($preview), 120) }}
            </p>
        @endif

        <span class="mt-4 inline-flex items-center gap-1.5 text-sm font-bold text-brand-600">
            Lihat detail
            <span aria-hidden="true">&rarr;</span>
        </span>
    </div>
</a>
