@props(['catalog'])

<a href="{{ route('catalogs.show', $catalog) }}"
   class="group flex flex-col overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:border-brand-300 hover:shadow-xl">
    <div class="relative aspect-[4/3] overflow-hidden bg-stone-100">
        <img src="{{ $catalog->photo }}" alt="{{ $catalog->title }}" loading="lazy"
             class="h-full w-full object-cover transition duration-500 group-hover:scale-105">

        @if ($catalog->year)
            <span class="absolute left-3 top-3 rounded-full bg-white/95 px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-brand-700 shadow-sm">
                {{ $catalog->year }}
            </span>
        @endif

        @if ($catalog->pdf_file)
            <span class="absolute right-3 top-3 rounded-full bg-brand-700 px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-white shadow-sm">
                PDF
            </span>
        @endif
    </div>

    <div class="flex flex-1 flex-col p-5">
        <h3 class="text-lg font-bold leading-snug text-stone-800 transition group-hover:text-brand-700">
            {{ $catalog->title }}
        </h3>

        @if ($catalog->description)
            <p class="mt-2 flex-1 text-sm leading-relaxed text-stone-500">
                {{ \Illuminate\Support\Str::limit(strip_tags($catalog->description), 120) }}
            </p>
        @endif

        <span class="mt-4 inline-flex items-center gap-1.5 text-sm font-bold text-brand-600">
            Buka katalog
            <span aria-hidden="true">&rarr;</span>
        </span>
    </div>
</a>
