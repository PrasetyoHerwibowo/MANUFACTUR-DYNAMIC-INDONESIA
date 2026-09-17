@php
    $navigation = [
        [
            'group' => 'Ringkasan',
            'items' => [
                ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => '📊'],
            ],
        ],
        [
            'group' => 'Data Produk',
            'items' => [
                ['label' => 'Jenis Mesin', 'route' => 'admin.categories.index', 'icon' => '🗂️', 'match' => 'admin.categories.*'],
                ['label' => 'Model Mesin', 'route' => 'admin.machines.index', 'icon' => '⚙️', 'match' => 'admin.machines.*'],
                ['label' => 'Katalog', 'route' => 'admin.catalogs.index', 'icon' => '📕', 'match' => 'admin.catalogs.*'],
            ],
        ],
        [
            'group' => 'Website',
            'items' => [
                ['label' => 'Profil Perusahaan', 'route' => 'admin.profile.edit', 'icon' => '🏢', 'match' => 'admin.profile.*'],
                ['label' => 'Pesan Masuk', 'route' => 'admin.messages.index', 'icon' => '✉️', 'match' => 'admin.messages.*'],
            ],
        ],
        [
            'group' => 'Akun',
            'items' => [
                ['label' => 'Pengaturan Akun', 'route' => 'admin.account.edit', 'icon' => '👤', 'match' => 'admin.account.*'],
            ],
        ],
    ];

    $unreadMessages = \App\Models\ContactMessage::query()->unread()->count();
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Panel Admin') — {{ $company->name }}</title>

    <link rel="icon" href="{{ $company->logoUrl() ?? asset('uploads/placeholder.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-100 font-sans text-stone-700 antialiased">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside class="hidden w-72 shrink-0 flex-col bg-brand-950 text-brand-100 lg:flex">
            <div class="flex items-center gap-3 border-b border-white/10 px-5 py-5">
                @if ($company->logoUrl())
                    <img src="{{ $company->logoUrl() }}" alt="Logo" class="h-10 w-10 rounded-xl bg-white/10 object-contain p-1">
                @else
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-600 text-sm font-extrabold text-white">MD</span>
                @endif

                <span class="leading-tight">
                    <span class="block text-xs font-extrabold text-white">Panel Admin</span>
                    <span class="block text-[11px] text-brand-300">{{ \Illuminate\Support\Str::limit($company->name, 24) }}</span>
                </span>
            </div>

            <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-5">
                @foreach ($navigation as $group)
                    <div>
                        <p class="px-3 pb-2 text-[10px] font-bold uppercase tracking-widest text-brand-400">{{ $group['group'] }}</p>

                        <ul class="space-y-1">
                            @foreach ($group['items'] as $item)
                                @php $active = request()->routeIs($item['match'] ?? $item['route']); @endphp
                                <li>
                                    <a href="{{ route($item['route']) }}"
                                       class="flex items-center justify-between gap-2 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ $active ? 'bg-brand-600 text-white shadow-lg shadow-brand-950/40' : 'text-brand-100 hover:bg-white/10' }}">
                                        <span class="flex items-center gap-2.5">
                                            <span aria-hidden="true">{{ $item['icon'] }}</span>
                                            {{ $item['label'] }}
                                        </span>

                                        @if ($item['route'] === 'admin.messages.index' && $unreadMessages > 0)
                                            <span class="rounded-full bg-red-500 px-2 py-0.5 text-[10px] font-bold text-white">{{ $unreadMessages }}</span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </nav>

            <div class="border-t border-white/10 px-4 py-4">
                <a href="{{ route('home') }}" target="_blank"
                   class="block rounded-xl bg-white/10 px-4 py-2.5 text-center text-xs font-bold text-white transition hover:bg-white/20">
                    Lihat Website
                </a>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            {{-- Topbar --}}
            <header class="sticky top-0 z-30 border-b border-stone-200 bg-white/95 backdrop-blur">
                <div class="flex items-center justify-between gap-4 px-4 py-3.5 sm:px-6">
                    <div class="flex items-center gap-3">
                        <button type="button" data-menu-toggle="#sidebar-mobile"
                                class="rounded-lg border border-stone-300 p-2 text-stone-600 lg:hidden" aria-label="Buka menu">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                            </svg>
                        </button>

                        <div>
                            <h1 class="text-base font-extrabold text-stone-900 sm:text-lg">@yield('page_title', 'Dashboard')</h1>
                            <p class="hidden text-xs text-stone-500 sm:block">@yield('page_subtitle', 'Kelola konten website company profile')</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('home') }}" target="_blank"
                           class="hidden rounded-xl border border-stone-300 px-3.5 py-2 text-xs font-bold text-stone-600 transition hover:bg-stone-50 sm:block">
                            Website
                        </a>

                        <div class="flex items-center gap-2.5">
                            <img src="{{ auth()->user()->avatarUrl() }}" alt="{{ auth()->user()->name }}" class="h-9 w-9 rounded-full object-cover">
                            <span class="hidden leading-tight sm:block">
                                <span class="block text-xs font-bold text-stone-800">{{ auth()->user()->name }}</span>
                                <span class="block text-[11px] text-stone-500">Administrator</span>
                            </span>
                        </div>

                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit"
                                    class="rounded-xl bg-stone-900 px-3.5 py-2 text-xs font-bold text-white transition hover:bg-stone-700">
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>

                <div id="sidebar-mobile" class="hidden border-t border-stone-200 bg-white px-4 py-3 lg:hidden">
                    <div class="flex flex-col gap-1">
                        @foreach ($navigation as $group)
                            @foreach ($group['items'] as $item)
                                <a href="{{ route($item['route']) }}"
                                   class="rounded-lg px-3 py-2.5 text-sm font-semibold {{ request()->routeIs($item['match'] ?? $item['route']) ? 'bg-brand-50 text-brand-700' : 'text-stone-600' }}">
                                    {{ $item['icon'] }} {{ $item['label'] }}
                                </a>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </header>

            <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                @if (session('success'))
                    <x-alert type="success" class="mb-5">{{ session('success') }}</x-alert>
                @endif

                @if (session('error'))
                    <x-alert type="error" class="mb-5">{{ session('error') }}</x-alert>
                @endif

                @yield('content')
            </main>

            <footer class="border-t border-stone-200 px-4 py-4 text-center text-xs text-stone-400 sm:px-6">
                &copy; {{ now()->year }} {{ $company->name }} — Panel Admin
            </footer>
        </div>
    </div>
</body>
</html>
