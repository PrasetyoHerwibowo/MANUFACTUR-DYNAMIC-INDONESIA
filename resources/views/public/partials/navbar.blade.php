@php
    $menu = [
        ['label' => 'Beranda', 'route' => 'home'],
        ['label' => 'Tentang Kami', 'route' => 'about'],
        ['label' => 'Produk & Mesin', 'route' => 'products.index'],
        ['label' => 'Katalog', 'route' => 'catalogs.index'],
        ['label' => 'Kontak', 'route' => 'contact'],
    ];
@endphp

<header class="sticky top-0 z-40 border-b border-stone-200/80 bg-white/95 backdrop-blur transition-colors duration-300 dark:border-gray-800 dark:bg-gray-900/95">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3.5 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/logo_eiko.png') }}" alt="Logo {{ $company->name }}" class="h-auto w-40 object-contain transition-all duration-300 dark:brightness-0 dark:invert">
        </a>

        <nav class="hidden items-center gap-1 lg:flex">
            @foreach ($menu as $item)
                @php $active = request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*'); @endphp
                <a href="{{ route($item['route']) }}"
                   class="rounded-lg px-3.5 py-2 text-sm font-semibold transition {{ $active ? 'bg-red-100 text-red-600 dark:bg-red-500/10 dark:text-red-300' : 'text-stone-600 hover:bg-red-100 hover:text-red-600 dark:text-gray-300 dark:hover:bg-red-500/10 dark:hover:text-red-400' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach

            @auth
                <a href="{{ route('admin.dashboard') }}" class="ml-2 inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-red-600 to-red-700 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-red-600/20 transition hover:from-red-700 hover:to-red-800 hover:shadow-red-700">
                    <i class="ti ti-layout-dashboard text-base"></i> Panel Admin
                </a>
            @else
                <a href="{{ route('admin.login') }}" class="ml-2 inline-flex items-center gap-2 rounded-lg border border-red-200 px-4 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-50 dark:border-red-700/50 dark:text-red-400 dark:hover:bg-red-500/10">
                    <i class="ti ti-lock text-base"></i> Login Admin
                </a>
            @endauth
        </nav>

        <div class="flex items-center gap-2">
            {{-- Tombol ganti tema terang / gelap. --}}
            <button type="button" data-theme-toggle
                    class="relative h-6 w-12 shrink-0 rounded-full bg-stone-200 transition-colors duration-300 dark:bg-gray-700"
                    title="Ganti tema terang / gelap" aria-label="Ganti tema terang / gelap">
                <span data-theme-knob
                      class="absolute left-0.5 top-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-white shadow-md transition-transform duration-300">
                    <i data-theme-icon class="ti ti-moon text-[11px] text-gray-600"></i>
                </span>
            </button>

            <button type="button" data-menu-toggle="#menu-mobile"
                    class="inline-flex items-center justify-center rounded-lg border border-stone-300 p-2 text-stone-600 transition hover:bg-stone-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 lg:hidden"
                    aria-label="Buka menu">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                </svg>
            </button>
        </div>
    </div>

    <div id="menu-mobile" class="hidden border-t border-stone-200 bg-white px-4 py-3 transition-colors duration-300 dark:border-gray-800 dark:bg-gray-900 lg:hidden">
        <nav class="flex flex-col gap-1">
            @foreach ($menu as $item)
                <a href="{{ route($item['route']) }}"
                   class="rounded-lg px-3 py-2.5 text-sm font-semibold {{ request()->routeIs($item['route'].'*') ? 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400' : 'text-stone-600 dark:text-gray-300' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach

            @auth
                <a href="{{ route('admin.dashboard') }}" class="mt-1 rounded-lg bg-gradient-to-r from-red-600 to-red-700 px-3 py-2.5 text-sm font-semibold text-white transition hover:from-red-700 hover:to-red-800">Panel Admin</a>
            @else
                <a href="{{ route('admin.login') }}" class="mt-1 rounded-lg border border-red-200 px-3 py-2.5 text-sm font-semibold text-red-700 dark:border-red-700/50 dark:text-red-400">Login Admin</a>
            @endauth
        </nav>
    </div>
</header>
