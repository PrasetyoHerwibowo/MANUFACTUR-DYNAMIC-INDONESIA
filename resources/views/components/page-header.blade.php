@props(['title', 'subtitle' => null, 'crumbs' => []])

<section class="public-page-hero">
    <div class="hero-bg"></div>

    <div class="relative z-10 mx-auto max-w-7xl px-4 w-full">
        @if (!empty($crumbs))
            <nav class="mb-4 flex flex-wrap items-center justify-center gap-2 text-xs font-semibold text-zinc-300 uppercase tracking-wider">
                @foreach ($crumbs as $crumb)
                    @if (!empty($crumb['url']))
                        <a href="{{ $crumb['url'] }}" class="transition hover:text-white">{{ $crumb['label'] }}</a>
                        <span class="text-white/40">/</span>
                    @else
                        <span class="text-white font-bold">{{ $crumb['label'] }}</span>
                    @endif
                @endforeach
            </nav>
        @endif

        <h1>{!! $title !!}</h1>

        @if ($subtitle)
            <p>{{ $subtitle }}</p>
        @endif
    </div>
</section>
