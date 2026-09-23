@props(['title', 'subtitle' => null, 'crumbs' => []])

<section class="relative overflow-hidden bg-gradient-to-br from-[#0B111A] via-[#131B26] to-red-800 text-white transition-colors duration-300 dark:from-gray-900 dark:via-gray-900 dark:to-red-900">
    <div class="absolute -right-20 -top-24 h-72 w-72 rounded-full bg-red-500/25 blur-3xl"></div>

    <div class="relative mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
        @if (! empty($crumbs))
            <nav class="mb-4 flex flex-wrap items-center gap-2 text-xs font-semibold text-gray-200 dark:text-gray-300">
                @foreach ($crumbs as $crumb)
                    @if (! empty($crumb['url']))
                        <a href="{{ $crumb['url'] }}" class="transition hover:text-red-300 dark:hover:text-red-400">{{ $crumb['label'] }}</a>
                        <span class="text-gray-200/50 dark:text-gray-600">/</span>
                    @else
                        <span class="text-white dark:text-red-400">{{ $crumb['label'] }}</span>
                    @endif
                @endforeach
            </nav>
        @endif

        <h1 class="text-2xl font-extrabold tracking-tight sm:text-3xl lg:text-4xl">{{ $title }}</h1>

        @if ($subtitle)
            <p class="mt-4 max-w-3xl text-sm leading-relaxed text-gray-200 dark:text-gray-300 sm:text-base">{{ $subtitle }}</p>
        @endif
    </div>
</section>
