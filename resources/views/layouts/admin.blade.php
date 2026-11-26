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
            ],
        ],
        [
            'group' => 'Transaksi',
            'items' => [
                ['route' => 'admin.customers.index', 'icon' => 'fas fa-users', 'label' => 'Pelanggan', 'match' => 'admin.customers.*'],
                ['route' => 'admin.orders.index', 'icon' => 'fas fa-file-invoice-dollar', 'label' => 'Pesanan & Pembayaran', 'match' => 'admin.orders.*', 'badge' => 'payments'],
            ],
        ],
        [
            'group' => 'Layanan Purna Jual',
            'items' => [
                ['route' => 'admin.services.index', 'icon' => 'fas fa-wrench', 'label' => 'Booking Servis', 'match' => 'admin.services.*', 'badge' => 'services'],
                ['route' => 'admin.warranties.index', 'icon' => 'fas fa-shield-alt', 'label' => 'Klaim Garansi', 'match' => 'admin.warranties.*', 'badge' => 'warranties'],
            ],
        ],
        [
            'group' => 'Website',
            'items' => [
                ['route' => 'admin.profile.edit', 'icon' => 'fas fa-building', 'label' => 'Profil Perusahaan', 'match' => 'admin.profile.*'],
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
    $pendingPayments = \Illuminate\Support\Facades\Cache::remember('admin_pending_payments_count', 60, fn () => \App\Models\Payment::query()->pending()->count());
    $pendingServices = \Illuminate\Support\Facades\Cache::remember('admin_pending_services_count', 60, fn () => \App\Models\ServiceBooking::whereIn('status', [\App\Models\ServiceBooking::STATUS_PENDING, \App\Models\ServiceBooking::STATUS_MENUNGGU])->count());
    $pendingWarranties = \Illuminate\Support\Facades\Cache::remember('admin_pending_warranties_count', 60, fn () => \App\Models\WarrantyClaim::where('status', \App\Models\WarrantyClaim::STATUS_DALAM_PENINJAUAN)->count());

    /** Jumlah notifikasi pada sebuah item menu. */
    $badgeCount = fn (array $item): int => match ($item['badge'] ?? null) {
        'payments' => $pendingPayments,
        'services' => $pendingServices,
        'warranties' => $pendingWarranties,
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
    {{-- Font Awesome: ikon panel admin mengikuti tampilan Eiko Coffee Roaster. --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
    @stack('styles')
</head>
<body class="bg-zinc-50 font-sans text-zinc-950">
<div class="flex min-h-screen">
    {{-- Latar gelap saat sidebar dibuka pada layar kecil --}}
    <div data-sidebar-backdrop class="fixed inset-0 z-40 bg-zinc-950/50 backdrop-blur-sm lg:hidden" aria-hidden="true"></div>
    <aside id="adminSidebar" data-sidebar class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col overflow-y-auto border-r border-zinc-200 bg-white">
        <div class="border-b border-zinc-200 p-5">
            <div class="flex w-full items-center justify-center py-2">
                @if ($company->logoUrl())
                    <div class="flex h-16 w-36 items-center justify-center overflow-hidden rounded-xl border border-zinc-200 bg-white p-1 shadow-sm">
                        <img src="{{ $company->logoUrl() }}" alt="{{ $company->name }}" class="h-full w-full object-contain">
                    </div>
                @else
                    <div class="flex h-16 w-36 items-center justify-center rounded-xl border border-zinc-800 bg-gradient-to-br from-zinc-900 to-zinc-950 p-2.5 shadow-md">
                        <img src="{{ asset('images/logo_eiko.png') }}" alt="{{ $company->name }}" class="h-10 w-auto max-w-full object-contain brightness-0 invert" style="filter: brightness(0) invert(1);">
                    </div>
                @endif
            </div>

            <p class="mt-2 truncate text-center text-[11px] font-semibold text-zinc-500">{{ Str::limit($company->name, 28) }}</p>

            <button type="button" data-sidebar-close
                    class="absolute right-3 top-3 rounded-lg p-1.5 text-zinc-400 transition hover:bg-zinc-100 hover:text-zinc-900 lg:hidden"
                    aria-label="Tutup menu navigasi">
                <i aria-hidden="true" class="fas fa-times text-base"></i>
            </button>
        </div>

        <nav class="flex-1 space-y-1 p-3">
            @foreach ($navigation as $group)
                <p class="px-3 pb-2 pt-3 text-xs font-semibold uppercase tracking-wider text-zinc-400">{{ $group['group'] }}</p>

                @foreach ($group['items'] as $item)
                    @php
                        $active = request()->routeIs($item['match']);
                        $notifikasi = $badgeCount($item);
                    @endphp
                    <a href="{{ route($item['route']) }}"
                       class="nav-item flex w-full items-center gap-3 rounded-lg border-l-4 px-3 py-2.5 text-sm font-medium transition-all {{ $active ? 'border-zinc-950 bg-zinc-100 font-semibold text-zinc-950' : 'border-transparent text-zinc-600 hover:bg-zinc-100 hover:text-zinc-950' }}">
                        <i aria-hidden="true" class="{{ $item['icon'] }} w-5 {{ $active ? 'text-zinc-950' : 'text-zinc-500' }}"></i>
                        <span>{{ $item['label'] }}</span>

                        @if ($notifikasi > 0)
                            <span class="ml-auto rounded-full bg-red-600 px-2 py-0.5 text-xs font-bold text-white">{{ $notifikasi }}</span>
                        @endif
                    </a>
                @endforeach
            @endforeach
        </nav>

        <div class="border-t border-zinc-200 p-3">
            <div class="mb-3 flex items-center gap-3 rounded-xl border border-zinc-200 bg-zinc-100 p-2.5">
                <img src="{{ $currentUser->avatarUrl() }}" alt="{{ $currentUser->name }}"
                     class="h-9 w-9 shrink-0 rounded-full object-cover ring-2 ring-white">
                <div class="min-w-0 flex-1">
                    <p class="truncate text-xs font-semibold text-zinc-950">{{ $currentUser->name }}</p>
                    <p class="truncate text-[10px] text-zinc-500">{{ $currentUser->email }}</p>
                </div>
                <span class="h-2 w-2 shrink-0 rounded-full bg-emerald-600" title="Sesi aktif"></span>
            </div>

            <a href="{{ route('admin.account.edit') }}"
               class="flex w-full items-center justify-center gap-2 rounded-lg bg-zinc-100 px-3 py-2 text-xs font-medium text-zinc-800 transition-all hover:bg-zinc-200">
                <i aria-hidden="true" class="fas fa-user-cog"></i>
                <span>Edit Profil</span>
            </a>

            <form method="POST" action="{{ route('admin.logout') }}" class="mt-2">
                @csrf
                <button type="submit"
                        class="flex w-full items-center justify-center gap-2 rounded-lg bg-red-50 px-3 py-2 text-xs font-medium text-red-600 transition-all hover:bg-red-100">
                    <i aria-hidden="true" class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </form>

            <a href="{{ route('home') }}" target="_blank" rel="noopener"
               class="mt-2 flex items-center justify-center gap-2 text-[10px] font-semibold text-zinc-500 transition hover:text-zinc-950">
                <i aria-hidden="true" class="fas fa-external-link-alt"></i> Lihat website
            </a>
        </div>
    </aside>
    <main class="flex-1 lg:ml-64">
        <header class="sticky top-0 z-30 border-b border-zinc-200 bg-white/90 px-4 py-4 backdrop-blur-lg sm:px-6">
            <div class="flex items-center justify-between gap-3">
                <div class="flex min-w-0 items-center gap-3">
                    <button type="button" data-sidebar-open
                            class="rounded-lg p-2 text-zinc-600 transition hover:bg-zinc-100 lg:hidden"
                            aria-label="Buka menu navigasi" aria-controls="adminSidebar" aria-expanded="false">
                        <i class="fas fa-bars text-lg" aria-hidden="true"></i>
                    </button>

                    <div class="min-w-0">
                        <h1 class="truncate text-xl font-bold tracking-tight text-zinc-950 sm:text-2xl">
                            @yield('page_title', 'Dashboard')
                        </h1>
                        <p class="truncate text-xs text-zinc-500 sm:text-sm">
                            @yield('page_subtitle', 'Kelola konten website perusahaan')
                        </p>
                    </div>
                </div>

                <div class="flex shrink-0 items-center gap-2 sm:gap-3">
                    <a href="{{ route('admin.account.edit') }}" title="{{ $currentUser->name }}">
                        <img src="{{ $currentUser->avatarUrl() }}" alt="{{ $currentUser->name }}"
                             class="h-9 w-9 rounded-full object-cover ring-2 ring-zinc-200">
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
        <footer class="border-t border-zinc-200 px-6 py-4 text-center text-xs text-zinc-500">
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
    })();
</script>

@stack('scripts')
</body>
</html>

