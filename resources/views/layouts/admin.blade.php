@php
    /** Menu panel admin: setiap item mengarah ke route yang benar-benar ada. */
    $navigation = [
        [
            'group' => 'Ringkasan',
            'items' => [
                ['route' => 'admin.dashboard', 'icon' => 'fas fa-home', 'label' => 'Dashboard', 'match' => 'admin.dashboard'],
            ],
        ],
        [
            'group' => 'Data Produk',
            'items' => [
                ['route' => 'admin.categories.index', 'icon' => 'fas fa-layer-group', 'label' => 'Jenis Mesin', 'match' => 'admin.categories.*'],
                ['route' => 'admin.machines.index', 'icon' => 'fas fa-cogs', 'label' => 'Model Mesin', 'match' => 'admin.machines.*'],
                ['route' => 'admin.catalogs.index', 'icon' => 'fas fa-book', 'label' => 'Katalog', 'match' => 'admin.catalogs.*'],
            ],
        ],
        [
            'group' => 'Transaksi',
            'items' => [
                ['route' => 'admin.orders.index', 'icon' => 'fas fa-file-invoice-dollar', 'label' => 'Pesanan & Pembayaran', 'match' => 'admin.orders.*', 'badge' => 'payments'],
                ['route' => 'admin.customers.index', 'icon' => 'fas fa-users', 'label' => 'Pelanggan', 'match' => 'admin.customers.*'],
            ],
        ],
        [
            'group' => 'Website',
            'items' => [
                ['route' => 'admin.profile.edit', 'icon' => 'fas fa-building', 'label' => 'Profil Perusahaan', 'match' => 'admin.profile.*'],
                ['route' => 'admin.messages.index', 'icon' => 'fas fa-envelope', 'label' => 'Pesan Masuk', 'match' => 'admin.messages.*', 'badge' => true],
            ],
        ],
        [
            'group' => 'Akun',
            'items' => [
                ['route' => 'admin.account.edit', 'icon' => 'fas fa-user-cog', 'label' => 'Pengaturan Akun', 'match' => 'admin.account.*'],
            ],
        ],
    ];

    $currentUser = auth()->user();
    $unreadMessages = \App\Models\ContactMessage::query()->unread()->count();
    $pendingPayments = \App\Models\Payment::query()->pending()->count();

    /** Jumlah notifikasi pada sebuah item menu (true = pesan masuk). */
    $badgeCount = fn (array $item): int => match ($item['badge'] ?? null) {
        'messages', true => $unreadMessages,
        'payments' => $pendingPayments,
        default => 0,
    };
@endphp
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Panel Admin') — {{ $company->name }}</title>
    <link rel="icon" href="{{ $company->logoUrl() ?? asset('uploads/placeholder.svg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.31.0/dist/tabler-icons.min.css" rel="stylesheet">
    {{-- Font Awesome: ikon panel admin mengikuti tampilan Eiko Coffee Roaster. --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Terapkan tema tersimpan sebelum halaman digambar agar tidak berkedip. --}}
    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
        }
    </script>

    @stack('head')
    @stack('styles')
</head>
<body class="bg-gray-50 font-sans transition-colors duration-300 dark:bg-gray-900">
<div class="flex min-h-screen">
    {{-- Latar gelap saat sidebar dibuka pada layar kecil --}}
    <div data-sidebar-backdrop class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm lg:hidden" aria-hidden="true"></div>
    <aside id="adminSidebar" data-sidebar class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col overflow-y-auto border-r border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
        <div class="border-b border-gray-200 p-5 dark:border-gray-700">
            <div class="flex w-full items-center justify-center py-2">
                @if ($company->logoUrl())
                    <div class="flex h-16 w-32 items-center justify-center overflow-hidden rounded-xl bg-white p-1 shadow-lg">
                        <img src="{{ $company->logoUrl() }}" alt="{{ $company->name }}" class="h-full w-full object-contain">
                    </div>
                @else
                    <div class="flex h-16 w-32 items-center justify-center gap-2 rounded-xl bg-gradient-to-br from-coffee-500 to-coffee-700 shadow-lg">
                        <i aria-hidden="true" class="fas fa-industry text-xl text-white"></i>
                        <span class="text-xs font-extrabold uppercase tracking-wide text-white">Panel Admin</span>
                    </div>
                @endif
            </div>

            <p class="mt-2 truncate text-center text-[11px] font-semibold text-gray-400">{{ Str::limit($company->name, 28) }}</p>

            <button type="button" data-sidebar-close
                    class="absolute right-3 top-3 rounded-lg p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-700 dark:hover:text-white lg:hidden"
                    aria-label="Tutup menu navigasi">
                <i aria-hidden="true" class="fas fa-times text-base"></i>
            </button>
        </div>

        <nav class="flex-1 space-y-1 p-3">
            @foreach ($navigation as $group)
                <p class="px-3 pb-2 pt-3 text-xs font-semibold uppercase tracking-wider text-gray-400">{{ $group['group'] }}</p>

                @foreach ($group['items'] as $item)
                    @php
                        $active = request()->routeIs($item['match']);
                        $notifikasi = $badgeCount($item);
                    @endphp
                    <a href="{{ route($item['route']) }}"
                       class="nav-item flex w-full items-center gap-3 rounded-lg border-l-4 px-3 py-2.5 text-sm font-medium transition-all {{ $active ? 'border-coffee-500 bg-coffee-50 text-coffee-700 dark:bg-coffee-900/20 dark:text-coffee-400' : 'border-transparent text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700/50' }}">
                        <i aria-hidden="true" class="{{ $item['icon'] }} w-5 {{ $active ? 'text-coffee-600' : 'text-coffee-500' }}"></i>
                        <span>{{ $item['label'] }}</span>

                        @if ($notifikasi > 0)
                            <span class="ml-auto rounded-full bg-red-500 px-2 py-0.5 text-xs font-bold text-white">{{ $notifikasi }}</span>
                        @endif
                    </a>
                @endforeach
            @endforeach
        </nav>

        <div class="border-t border-gray-200 p-3 dark:border-gray-700">
            <div class="mb-3 flex items-center gap-3 rounded-lg bg-gradient-to-r from-coffee-50 to-orange-50 p-2.5 dark:from-gray-700 dark:to-gray-600">
                <img src="{{ $currentUser->avatarUrl() }}" alt="{{ $currentUser->name }}"
                     class="h-9 w-9 shrink-0 rounded-full object-cover ring-2 ring-white/70 dark:ring-gray-700">
                <div class="min-w-0 flex-1">
                    <p class="truncate text-xs font-semibold text-gray-900 dark:text-white">{{ $currentUser->name }}</p>
                    <p class="truncate text-[10px] text-gray-500 dark:text-gray-400">{{ $currentUser->email }}</p>
                </div>
                <span class="h-2 w-2 shrink-0 rounded-full bg-green-500" title="Sesi aktif"></span>
            </div>

            <a href="{{ route('admin.account.edit') }}"
               class="flex w-full items-center justify-center gap-2 rounded-lg bg-coffee-50 px-3 py-2 text-xs font-medium text-coffee-700 transition-all hover:bg-coffee-100 dark:bg-coffee-900/20 dark:text-coffee-400 dark:hover:bg-coffee-900/30">
                <i aria-hidden="true" class="fas fa-user-cog"></i>
                <span>Edit Profil</span>
            </a>

            <form method="POST" action="{{ route('admin.logout') }}" class="mt-2">
                @csrf
                <button type="submit"
                        class="flex w-full items-center justify-center gap-2 rounded-lg bg-red-50 px-3 py-2 text-xs font-medium text-red-600 transition-all hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30">
                    <i aria-hidden="true" class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </form>

            <a href="{{ route('home') }}" target="_blank" rel="noopener"
               class="mt-2 flex items-center justify-center gap-2 text-[10px] font-semibold text-gray-400 transition hover:text-coffee-600 dark:hover:text-coffee-400">
                <i aria-hidden="true" class="fas fa-external-link-alt"></i> Lihat website
            </a>
        </div>
    </aside>
    <main class="flex-1 lg:ml-64">
        <header class="sticky top-0 z-30 border-b border-gray-200 bg-white/80 px-4 py-4 backdrop-blur-lg dark:border-gray-700 dark:bg-gray-800/80 sm:px-6">
            <div class="flex items-center justify-between gap-3">
                <div class="flex min-w-0 items-center gap-3">
                    <button type="button" data-sidebar-open
                            class="rounded-lg p-2 text-gray-500 transition hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 lg:hidden"
                            aria-label="Buka menu navigasi" aria-controls="adminSidebar" aria-expanded="false">
                        <i class="fas fa-bars text-lg" aria-hidden="true"></i>
                    </button>

                    <div class="min-w-0">
                        <h1 class="truncate bg-gradient-to-r from-coffee-600 to-orange-600 bg-clip-text text-xl font-bold text-transparent sm:text-2xl">
                            @yield('page_title', 'Dashboard')
                        </h1>
                        <p class="truncate text-xs text-gray-500 dark:text-gray-400 sm:text-sm">
                            @yield('page_subtitle', 'Kelola konten website perusahaan')
                        </p>
                    </div>
                </div>

                <div class="flex shrink-0 items-center gap-2 sm:gap-3">
                    <button type="button" data-theme-toggle onclick="toggleAdminTheme()"
                            class="relative h-6 w-12 rounded-full bg-gray-200 transition-colors duration-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-coffee-500 dark:bg-gray-700"
                            title="Ganti tema terang / gelap" aria-label="Ganti tema terang / gelap" aria-pressed="false">
                        <span data-theme-knob
                              class="absolute left-0.5 top-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-white shadow-md transition-transform duration-300 dark:translate-x-6 dark:bg-gray-200">
                            <i data-theme-icon class="fas fa-moon text-[10px] text-gray-600 dark:hidden" aria-hidden="true"></i>
                            <i class="fas fa-sun hidden text-[10px] text-yellow-500 dark:inline" aria-hidden="true"></i>
                        </span>
                    </button>

                    <a href="{{ route('admin.messages.index') }}" title="Pesan masuk"
                       class="relative p-2 text-gray-400 transition hover:text-coffee-600 dark:hover:text-coffee-400">
                        <i aria-hidden="true" class="fas fa-bell text-lg"></i>
                        @if ($unreadMessages > 0)
                            <span class="absolute right-0 top-0 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[9px] font-bold text-white">
                                {{ $unreadMessages }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('admin.account.edit') }}" title="{{ $currentUser->name }}">
                        <img src="{{ $currentUser->avatarUrl() }}" alt="{{ $currentUser->name }}"
                             class="h-9 w-9 rounded-full object-cover ring-2 ring-coffee-500/30">
                    </a>
                </div>
            </div>
        </header>

        <div class="animate-fade-in p-4 sm:p-6">
            @if (session('success'))
                <x-alert type="success" class="mb-5">{{ session('success') }}</x-alert>
            @endif

            @if (session('error'))
                <x-alert type="error" class="mb-5">{{ session('error') }}</x-alert>
            @endif

            @if (session('status'))
                <x-alert type="info" class="mb-5">{{ session('status') }}</x-alert>
            @endif

            @yield('content')
        </div>
        <footer class="border-t border-gray-200 px-6 py-4 text-center text-xs text-gray-400 dark:border-gray-700 dark:text-gray-500">
            &copy; {{ now()->year }} {{ $company->name }} &middot; Panel Admin
        </footer>
    </main>
</div>

<script>
    (function () {
        const sidebar = document.querySelector('[data-sidebar]');
        const backdrop = document.querySelector('[data-sidebar-backdrop]');
        const desktop = window.matchMedia('(min-width: 1024px)');
        let lastTrigger = null;

        function syncSidebar() {
            const isDesktop = desktop.matches;
            const isOpen = sidebar?.classList.contains('is-open') ?? false;
            // Sidebar yang tertutup di layar kecil tidak boleh dijangkau keyboard/screen reader.
            const hidden = !isDesktop && !isOpen;

            sidebar?.toggleAttribute('inert', hidden);
            sidebar?.setAttribute('aria-hidden', String(hidden));
            document.body.classList.toggle('overflow-hidden', isOpen && !isDesktop);

            document.querySelectorAll('[data-sidebar-open]').forEach((button) => {
                button.setAttribute('aria-expanded', String(isOpen && !isDesktop));
            });
        }

        function openSidebar() {
            lastTrigger = document.activeElement instanceof HTMLElement ? document.activeElement : null;
            sidebar?.classList.add('is-open');
            backdrop?.classList.add('is-open');
            syncSidebar();
            sidebar?.querySelector('[data-sidebar-close]')?.focus();
        }

        function closeSidebar() {
            sidebar?.classList.remove('is-open');
            backdrop?.classList.remove('is-open');
            syncSidebar();
            lastTrigger?.focus();
            lastTrigger = null;
        }

        document.querySelectorAll('[data-sidebar-open]').forEach((button) => button.addEventListener('click', openSidebar));
        document.querySelectorAll('[data-sidebar-close]').forEach((button) => button.addEventListener('click', closeSidebar));
        backdrop?.addEventListener('click', closeSidebar);

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && sidebar?.classList.contains('is-open')) {
                closeSidebar();
            }
        });

        desktop.addEventListener('change', syncSidebar);
        syncSidebar();

        const toggle = document.querySelector('[data-theme-toggle]');

        function paintTheme() {
            const isDark = document.documentElement.classList.contains('dark');

            if (toggle) {
                toggle.setAttribute('aria-pressed', isDark ? 'true' : 'false');
                toggle.setAttribute('title', isDark ? 'Aktifkan tema terang' : 'Aktifkan tema gelap');
            }

            if (typeof window.onAdminThemeChange === 'function') {
                window.onAdminThemeChange(isDark);
            }
        }

        // Dipakai tombol tema (onclick="toggleAdminTheme()") — penanda geser & ikon
        // bulan/matahari diatur lewat kelas dark: Tailwind, seperti panel Eiko.
        window.toggleAdminTheme = function () {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            paintTheme();

            return isDark;
        };

        paintTheme();
    })();
</script>

@stack('scripts')
</body>
</html>

