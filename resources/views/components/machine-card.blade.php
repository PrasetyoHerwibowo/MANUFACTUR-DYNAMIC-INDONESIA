@props(['machine'])

<a href="{{ route('products.show', $machine) }}"
   class="group flex flex-col overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:border-coffee-300 hover:shadow-xl dark:border-gray-700 dark:bg-gray-800 dark:hover:border-coffee-700">
    <div class="relative aspect-[4/3] overflow-hidden bg-stone-100 dark:bg-gray-700">
        <img src="{{ $machine->photo }}" alt="{{ $machine->name }}" loading="lazy"
             class="h-full w-full object-cover transition duration-500 group-hover:scale-105">

        @if ($machine->category)
            <span class="absolute left-3 top-3 rounded-full bg-white/95 px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-coffee-700 shadow-sm dark:bg-gray-900/90 dark:text-coffee-400">
                {{ $machine->category->name }}
            </span>
        @endif

    </div>

    <div class="flex flex-1 flex-col p-5">
        @if ($machine->model_code)
            <p class="text-xs font-bold uppercase tracking-wider text-coffee-500 dark:text-coffee-400">{{ $machine->model_code }}</p>
        @endif

        <h3 class="mt-1 text-lg font-bold leading-snug text-stone-800 transition group-hover:text-coffee-600 dark:text-white">
            {{ $machine->name }}
        </h3>

        @php $preview = $machine->short_description ?: $machine->function; @endphp
        @if ($preview)
            <p class="mt-2 flex-1 text-sm leading-relaxed text-stone-500 dark:text-gray-400">
                {{ \Illuminate\Support\Str::limit(strip_tags($preview), 120) }}
            </p>
        @endif

        <span class="mt-4 inline-flex items-center gap-1.5 text-sm font-bold text-coffee-600 dark:text-coffee-400">
            Lihat detail
            <span aria-hidden="true">&rarr;</span>
        </span>
    </div>
</a>
