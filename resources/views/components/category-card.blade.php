@props(['category'])

<a href="{{ route('products.index', ['kategori' => $category->slug]) }}"
   class="group flex flex-col overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:border-zinc-900 hover:shadow-lg dark:border-slate-800 dark:bg-slate-900 dark:hover:border-white">
    <div class="relative flex aspect-[4/3] items-center justify-center overflow-hidden bg-gradient-to-br from-zinc-900 via-zinc-800 to-zinc-950">
        @if ($category->imageUrl())
            <img src="{{ $category->imageUrl() }}" alt="{{ $category->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
        @else
            <i aria-hidden="true" class="fas fa-cogs text-5xl text-white/20 transition duration-500 group-hover:scale-110"></i>
        @endif

        <span class="absolute right-3 top-3 rounded-full bg-white/20 backdrop-blur-sm px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-white">
            {{ $category->machines_count ?? $category->machines->count() }} model
        </span>
    </div>

    <div class="flex flex-1 flex-col p-5">
        <h3 class="text-lg font-bold leading-snug text-navy transition group-hover:text-zinc-600 dark:text-white dark:group-hover:text-zinc-300">
            {{ $category->name }}
        </h3>

        @if ($category->tagline || $category->description)
            <p class="mt-2 flex-1 text-sm leading-relaxed text-stone-500 dark:text-gray-400">
                {{ \Illuminate\Support\Str::limit(strip_tags($category->tagline ?: $category->description), 110) }}
            </p>
        @endif

        <span class="mt-4 inline-flex items-center gap-1.5 text-sm font-bold text-zinc-900 dark:text-white">
            Lihat model mesin
            <i class="fas fa-arrow-right text-xs transition-transform group-hover:translate-x-1"></i>
        </span>
    </div>
</a>
