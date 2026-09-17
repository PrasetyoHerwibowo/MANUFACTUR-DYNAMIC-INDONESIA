@props(['title', 'subtitle' => null, 'crumbs' => []])

<section class="relative overflow-hidden bg-gradient-to-br from-brand-950 via-brand-900 to-brand-700 text-white">
    <div class="absolute -right-20 -top-24 h-72 w-72 rounded-full bg-brand-500/20 blur-3xl"></div>

    <div class="relative mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
        @if (! empty($crumbs))
            <nav class="mb-4 flex flex-wrap items-center gap-2 text-xs font-semibold text-brand-100">
                @foreach ($crumbs as $crumb)
                    @if (! empty($crumb['url']))
                        <a href="{{ $crumb['url'] }}" class="transition hover:text-white">{{ $crumb['label'] }}</a>
                        <span class="text-brand-100/50">/</span>
                    @else
                        <span class="text-white">{{ $crumb['label'] }}</span>
                    @endif
                @endforeach
            </nav>
        @endif

        <h1 class="text-2xl font-extrabold tracking-tight sm:text-3xl lg:text-4xl">{{ $title }}</h1>

        @if ($subtitle)
            <p class="mt-4 max-w-3xl text-sm leading-relaxed text-brand-100 sm:text-base">{{ $subtitle }}</p>
        @endif
    </div>
</section>
