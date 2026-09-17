@props(['category'])

<a href="{{ route('products.index', ['kategori' => $category->slug]) }}"
   class="group relative flex flex-col overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:border-brand-300 hover:shadow-xl">
    <div class="relative aspect-[4/3] overflow-hidden bg-stone-100">
        <img src="{{ $category->photo }}" alt="{{ $category->name }}" loading="lazy"
             class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
        <span class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-brand-950/85 to-transparent p-4 pt-10">
            <span class="block text-base font-bold text-white">{{ $category->name }}</span>
            <span class="mt-0.5 block text-xs font-medium text-brand-100">
                {{ $category->machines_count ?? $category->machines->count() }} model mesin
            </span>
        </span>
    </div>

    @if ($category->tagline || $category->description)
        <p class="flex-1 p-4 text-sm leading-relaxed text-stone-500">
            {{ \Illuminate\Support\Str::limit(strip_tags($category->tagline ?: $category->description), 110) }}
        </p>
    @endif
</a>
