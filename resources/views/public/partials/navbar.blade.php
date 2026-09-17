@php
    $menu = [
        ['label' => 'Beranda', 'route' => 'home'],
        ['label' => 'Tentang Kami', 'route' => 'about'],
        ['label' => 'Produk & Mesin', 'route' => 'products.index'],
        ['label' => 'Katalog', 'route' => 'catalogs.index'],
        ['label' => 'Kontak', 'route' => 'contact'],
    ];
@endphp

<header class="sticky top-0 z-40 border-b border-stone-200/80 bg-white/95 backdrop-blur">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3.5 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            @if ($company->logoUrl())
                <img src="{{ $company->logoUrl() }}" alt="Logo {{ $company->name }}" class="h-11 w-11 rounded-xl object-contain">
            @else
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-700 text-lg font-extrabold text-white">MD</span>
            @endif

            <span class="leading-tight">
                <span class="block text-sm font-extrabold tracking-tight text-brand-900 sm:text-base">{{ $company->name }}</span>
                <span class="hidden text-[11px] font-medium text-stone-500 sm:block">Mesin Pengolahan Kopi &amp; Kakao</span>
            </span>
        </a>

        <nav class="hidden items-center gap-1 lg:flex">
            @foreach ($menu as $item)
                @php $active = request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*'); @endphp
                <a href="{{ route($item['route']) }}"
                   class="rounded-lg px-3.5 py-2 text-sm font-semibold transition {{ $active ? 'bg-brand-50 text-brand-700' : 'text-stone-600 hover:bg-stone-100 hover:text-brand-700' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach

            @auth
                <a href="{{ route('admin.dashboard') }}" class="ml-2 rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-800">
                    Panel Admin
                </a>
            @else
                <a href="{{ route('admin.login') }}" class="ml-2 rounded-lg border border-brand-200 px-4 py-2 text-sm font-semibold text-brand-700 transition hover:bg-brand-50">
                    Login Admin
                </a>
            @endauth
        </nav>

        <button type="button" data-menu-toggle="#menu-mobile"
                class="inline-flex items-center justify-center rounded-lg border border-stone-300 p-2 text-stone-600 lg:hidden"
                aria-label="Buka menu">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
            </svg>
        </button>
    </div>

    <div id="menu-mobile" class="hidden border-t border-stone-200 bg-white px-4 py-3 lg:hidden">
        <nav class="flex flex-col gap-1">
            @foreach ($menu as $item)
                <a href="{{ route($item['route']) }}"
                   class="rounded-lg px-3 py-2.5 text-sm font-semibold {{ request()->routeIs($item['route'].'*') ? 'bg-brand-50 text-brand-700' : 'text-stone-600' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach

            @auth
                <a href="{{ route('admin.dashboard') }}" class="mt-1 rounded-lg bg-brand-700 px-3 py-2.5 text-sm font-semibold text-white">Panel Admin</a>
            @else
                <a href="{{ route('admin.login') }}" class="mt-1 rounded-lg border border-brand-200 px-3 py-2.5 text-sm font-semibold text-brand-700">Login Admin</a>
            @endauth
        </nav>
    </div>
</header>
