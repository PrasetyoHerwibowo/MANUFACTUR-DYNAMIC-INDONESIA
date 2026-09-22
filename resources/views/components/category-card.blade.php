@props(['category'])

<a href="{{ route('products.index', ['kategori' => $category->slug]) }}"
   class="group flex flex-col overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:border-coffee-300 hover:shadow-xl dark:border-gray-700 dark:bg-gray-800 dark:hover:border-coffee-700">
    {{-- Jenis mesin tidak lagi memakai foto, jadi bagian atas kartu memakai
         panel gradasi berikon agar tetap sejajar dengan kartu model mesin. --}}
    <div class="relative flex aspect-[4/3] items-center justify-center overflow-hidden bg-gradient-to-br from-brand-800 via-brand-900 to-brand-950 dark:from-gray-800 dark:via-gray-900 dark:to-coffee-900">
        <i aria-hidden="true" class="fas fa-cogs text-5xl text-white/20 transition duration-500 group-hover:scale-110"></i>

        <span class="absolute right-3 top-3 rounded-full bg-white/10 px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-brand-100 dark:text-gray-300">
            {{ $category->machines_count ?? $category->machines->count() }} model
        </span>
    </div>

    <div class="flex flex-1 flex-col p-5">
        <h3 class="text-lg font-bold leading-snug text-stone-800 transition group-hover:text-coffee-600 dark:text-white">
            {{ $category->name }}
        </h3>

        @if ($category->tagline || $category->description)
            <p class="mt-2 flex-1 text-sm leading-relaxed text-stone-500 dark:text-gray-400">
                {{ \Illuminate\Support\Str::limit(strip_tags($category->tagline ?: $category->description), 110) }}
            </p>
        @endif

        <span class="mt-4 inline-flex items-center gap-1.5 text-sm font-bold text-coffee-600 dark:text-coffee-400">
            Lihat model mesin
            <span aria-hidden="true">&rarr;</span>
        </span>
    </div>
</a>
