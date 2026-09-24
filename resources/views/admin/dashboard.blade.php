@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Ringkasan konten website ' . $company->name)

@php
    /** Hitung persentase aman (tanpa pembagian nol). */
    $persen = fn (int $bagian, int $total): int => $total > 0 ? (int) round($bagian / $total * 100) : 0;

    $mesinAktifBar = $persen((int) $stats['mesin_aktif'], (int) $stats['mesin']);
    $kategoriAktifBar = $persen((int) $stats['kategori_aktif'], (int) $stats['kategori']);
    $katalogBar = $persen((int) $stats['katalog_berfoto'], (int) $stats['katalog']);
    $pesanBaruBar = $persen((int) $stats['pesan_baru'], (int) $stats['pesan']);

    $pesanTerakhir = $recentMessages->first();

    /** Metrik performa konten pada bilah progres. */
    $metricBars = [
        ['label' => 'Model mesin aktif', 'value' => (int) $stats['mesin_aktif'], 'total' => (int) $stats['mesin'], 'color' => 'from-blue-500 to-blue-600'],
        ['label' => 'Jenis mesin aktif', 'value' => (int) $stats['kategori_aktif'], 'total' => (int) $stats['kategori'], 'color' => 'from-green-500 to-green-600'],
        ['label' => 'Model mesin berfoto', 'value' => (int) $stats['mesin_berfoto'], 'total' => (int) $stats['mesin'], 'color' => 'from-coffee-500 to-coffee-600'],
        ['label' => 'Katalog berfoto', 'value' => (int) $stats['katalog_berfoto'], 'total' => (int) $stats['katalog'], 'color' => 'from-orange-500 to-orange-600'],
    ];

    /** Warna seragam untuk titik daftar & grafik donat. */
    $categoryColors = ['#3b82f6', '#22c55e', '#a855f7', '#f97316', '#ec4899', '#cd6624'];
    $categoryStyles = [
        ['dot' => 'bg-blue-500', 'tint' => 'bg-blue-50 dark:bg-blue-900/20'],
        ['dot' => 'bg-green-500', 'tint' => 'bg-green-50 dark:bg-green-900/20'],
        ['dot' => 'bg-purple-500', 'tint' => 'bg-purple-50 dark:bg-purple-900/20'],
        ['dot' => 'bg-orange-500', 'tint' => 'bg-orange-50 dark:bg-orange-900/20'],
        ['dot' => 'bg-pink-500', 'tint' => 'bg-pink-50 dark:bg-pink-900/20'],
        ['dot' => 'bg-coffee-500', 'tint' => 'bg-coffee-50 dark:bg-coffee-900/20'],
    ];
@endphp

@push('head')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@endpush

@section('content')
    <div class="page active animate-fade-in" id="page-dashboard">
        {{-- Kartu statistik bergradasi --}}
        <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
            <div class="stat-card-gradient-1 transform rounded-2xl p-6 text-white shadow-xl transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl">
                <div class="mb-4 flex items-center justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur">
                        <i aria-hidden="true" class="fas fa-cogs text-2xl"></i>
                    </div>
                    <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-semibold backdrop-blur">{{ $mesinAktifBar }}% aktif</span>
                </div>
                <h3 class="mb-1 text-3xl font-bold">{{ number_format($stats['mesin'], 0, ',', '.') }}</h3>
                <p class="text-sm text-white/80">Model Mesin</p>
                <div class="mt-4 h-1 overflow-hidden rounded-full bg-white/20">
                    <div class="metric-bar h-full rounded-full bg-white/70" style="width: {{ $mesinAktifBar }}%"></div>
                </div>
            </div>

            <div class="stat-card-gradient-2 transform rounded-2xl p-6 text-white shadow-xl transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl">
                <div class="mb-4 flex items-center justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur">
                        <i aria-hidden="true" class="fas fa-layer-group text-2xl"></i>
                    </div>
                    <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-semibold backdrop-blur">{{ $stats['kategori_aktif'] }} aktif</span>
                </div>
                <h3 class="mb-1 text-3xl font-bold">{{ number_format($stats['kategori'], 0, ',', '.') }}</h3>
                <p class="text-sm text-white/80">Jenis Mesin</p>
                <div class="mt-4 h-1 overflow-hidden rounded-full bg-white/20">
                    <div class="metric-bar h-full rounded-full bg-white/70" style="width: {{ $kategoriAktifBar }}%"></div>
                </div>
            </div>

            <div class="stat-card-gradient-3 transform rounded-2xl p-6 text-white shadow-xl transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl">
                <div class="mb-4 flex items-center justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur">
                        <i aria-hidden="true" class="fas fa-book text-2xl"></i>
                    </div>
                    <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-semibold backdrop-blur">{{ $stats['katalog_berfoto'] }} berfoto</span>
                </div>
                <h3 class="mb-1 text-3xl font-bold">{{ number_format($stats['katalog'], 0, ',', '.') }}</h3>
                <p class="text-sm text-white/80">Katalog Produk</p>
                <div class="mt-4 h-1 overflow-hidden rounded-full bg-white/20">
                    <div class="metric-bar h-full rounded-full bg-white/70" style="width: {{ $katalogBar }}%"></div>
                </div>
            </div>

            <div class="stat-card-gradient-4 transform rounded-2xl p-6 text-white shadow-xl transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl">
                <div class="mb-4 flex items-center justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur">
                        <i aria-hidden="true" class="fas fa-envelope text-2xl"></i>
                    </div>
                    <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-semibold backdrop-blur">{{ $stats['pesan_baru'] }} baru</span>
                </div>
                <h3 class="mb-1 text-3xl font-bold">{{ number_format($stats['pesan'], 0, ',', '.') }}</h3>
                <p class="text-sm text-white/80">Pesan Masuk</p>
                <div class="mt-4 h-1 overflow-hidden rounded-full bg-white/20">
                    <div class="metric-bar h-full rounded-full bg-white/70" style="width: {{ $pesanBaruBar }}%"></div>
                </div>
            </div>
        </div>
        {{-- Kartu kaca ringkasan konten --}}
        <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-3">
            <div class="glass-card rounded-2xl p-6 transition-all duration-300 hover:shadow-xl">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/30">
                        <i aria-hidden="true" class="fas fa-image text-xl text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Foto Model Mesin</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['foto'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="glass-card rounded-2xl p-6 transition-all duration-300 hover:shadow-xl">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-purple-100 dark:bg-purple-900/30">
                        <i aria-hidden="true" class="fas fa-file-image text-xl text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Foto Katalog</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['foto_katalog'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="glass-card rounded-2xl p-6 transition-all duration-300 hover:shadow-xl">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-red-100 dark:bg-red-900/30">
                        <i aria-hidden="true" class="fas fa-envelope-open-text text-xl text-red-600 dark:text-red-400"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Pesan Belum Dibaca</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['pesan_baru'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>
        {{-- Grafik --}}
        <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="glass-card rounded-2xl p-6 lg:col-span-2">
                <div class="mb-6 flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Tren Pesan Masuk</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Enam bulan terakhir</p>
                    </div>
                    <span class="shrink-0 rounded-full bg-coffee-50 px-3 py-1 text-xs font-semibold text-coffee-700 dark:bg-coffee-900/30 dark:text-coffee-300">
                        {{ number_format(array_sum($monthCounts), 0, ',', '.') }} pesan
                    </span>
                </div>

                <div class="chart-container h-80">
                    <canvas id="adminMessageChart"></canvas>
                </div>
            </div>

            <div class="glass-card rounded-2xl p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Model per Jenis Mesin</h3>
                <p class="mb-6 text-sm text-gray-500 dark:text-gray-400">Enam jenis teratas</p>

                @if ($categoryChart->sum('machines_count') > 0)
                    <div class="chart-container h-48">
                        <canvas id="adminCategoryChart"></canvas>
                    </div>

                    <div class="mt-6 space-y-2">
                        @foreach ($categoryChart as $index => $kategori)
                            @php $style = $categoryStyles[$index % count($categoryStyles)]; @endphp
                            <div class="flex items-center justify-between rounded-lg p-3 {{ $style['tint'] }}">
                                <div class="flex min-w-0 items-center gap-2">
                                    <span class="h-3 w-3 shrink-0 rounded-full {{ $style['dot'] }}"></span>
                                    <span class="truncate text-sm text-gray-600 dark:text-gray-300">{{ $kategori->name }}</span>
                                </div>
                                <span class="ml-2 shrink-0 text-sm font-bold text-gray-900 dark:text-white">{{ $kategori->machines_count }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex h-48 flex-col items-center justify-center gap-2 rounded-xl bg-gray-50 text-center dark:bg-gray-700/30">
                        <i aria-hidden="true" class="fas fa-chart-pie text-3xl text-gray-300 dark:text-gray-600"></i>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada model mesin yang tercatat.</p>
                        <a href="{{ route('admin.machines.create') }}"
                           class="text-xs font-semibold text-coffee-600 hover:text-coffee-700 dark:text-coffee-400">
                            Tambah model mesin &rarr;
                        </a>
                    </div>
                @endif
            </div>
        </div>
        {{-- Metrik performa konten --}}
        <div class="glass-card mb-8 rounded-2xl p-6">
            <div class="mb-6 flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Metrik Performa Konten</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Kelengkapan data yang tampil di website</p>
                </div>
                <a href="{{ route('admin.machines.index') }}"
                   class="shrink-0 text-sm font-semibold text-coffee-600 hover:text-coffee-700 dark:text-coffee-400">Kelola &rarr;</a>
            </div>

            <div class="space-y-5">
                @foreach ($metricBars as $bar)
                    @php $nilaiBar = $persen($bar['value'], $bar['total']); @endphp
                    <div>
                        <div class="mb-2 flex items-center justify-between text-sm">
                            <span class="font-medium text-gray-600 dark:text-gray-400">{{ $bar['label'] }}</span>
                            <span class="font-bold text-gray-900 dark:text-white">
                                {{ $nilaiBar }}%
                                <span class="ml-1 font-normal text-gray-400">({{ number_format($bar['value'], 0, ',', '.') }}/{{ number_format($bar['total'], 0, ',', '.') }})</span>
                            </span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                            <div class="metric-bar h-full rounded-full bg-gradient-to-r {{ $bar['color'] }}" style="width: {{ $nilaiBar }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        {{-- Model mesin terbaru --}}
        @include('admin.partials.recent-machines')

        {{-- Pesan terbaru & aksi cepat --}}
        <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="glass-card rounded-2xl p-6 lg:col-span-2">
                <div class="mb-6 flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Pesan Masuk Terbaru</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            @if ($pesanTerakhir)
                                Terakhir dari {{ $pesanTerakhir->name }} &middot; {{ $pesanTerakhir->created_at->diffForHumans() }}
                            @else
                                Belum ada pesan dari pengunjung
                            @endif
                        </p>
                    </div>
                    <a href="{{ route('admin.messages.index') }}"
                       class="shrink-0 text-sm font-semibold text-coffee-600 hover:text-coffee-700 dark:text-coffee-400">Lihat semua &rarr;</a>
                </div>

                @include('admin.partials.recent-messages')
            </div>

            <div class="glass-card rounded-2xl p-6">
                <h3 class="mb-6 text-lg font-bold text-gray-900 dark:text-white">Aksi Cepat</h3>

                <div class="space-y-3">
                    <a href="{{ route('admin.machines.create') }}"
                       class="flex items-center gap-3 rounded-lg border border-gray-200 p-3 text-sm font-medium text-gray-600 transition-all hover:border-coffee-500 hover:bg-coffee-50 hover:text-coffee-700 dark:border-gray-700 dark:text-gray-400 dark:hover:border-coffee-500 dark:hover:bg-coffee-900/20 dark:hover:text-coffee-400">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-coffee-50 text-coffee-600 dark:bg-coffee-900/30 dark:text-coffee-400">
                            <i aria-hidden="true" class="fas fa-plus"></i>
                        </span>
                        Tambah Model Mesin
                    </a>

                    <a href="{{ route('admin.categories.create') }}"
                       class="flex items-center gap-3 rounded-lg border border-gray-200 p-3 text-sm font-medium text-gray-600 transition-all hover:border-green-500 hover:bg-green-50 hover:text-green-700 dark:border-gray-700 dark:text-gray-400 dark:hover:border-green-500 dark:hover:bg-green-900/20 dark:hover:text-green-400">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-400">
                            <i aria-hidden="true" class="fas fa-layer-group"></i>
                        </span>
                        Tambah Jenis Mesin
                    </a>

                    <a href="{{ route('admin.catalogs.create') }}"
                       class="flex items-center gap-3 rounded-lg border border-gray-200 p-3 text-sm font-medium text-gray-600 transition-all hover:border-blue-500 hover:bg-blue-50 hover:text-blue-700 dark:border-gray-700 dark:text-gray-400 dark:hover:border-blue-500 dark:hover:bg-blue-900/20 dark:hover:text-blue-400">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                            <i aria-hidden="true" class="fas fa-upload"></i>
                        </span>
                        Unggah Katalog
                    </a>

                    <a href="{{ route('admin.profile.edit') }}"
                       class="flex items-center gap-3 rounded-lg border border-gray-200 p-3 text-sm font-medium text-gray-600 transition-all hover:border-purple-500 hover:bg-purple-50 hover:text-purple-700 dark:border-gray-700 dark:text-gray-400 dark:hover:border-purple-500 dark:hover:bg-purple-900/20 dark:hover:text-purple-400">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-purple-50 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">
                            <i aria-hidden="true" class="fas fa-building"></i>
                        </span>
                        Perbarui Profil Perusahaan
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Chart === 'undefined') {
                return;
            }

            const tickColor = () => (document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280');
            const gridColor = () => (document.documentElement.classList.contains('dark') ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.06)');

            const monthLabels = @json($monthLabels);
            const monthCounts = @json($monthCounts);
            const categoryLabels = @json($categoryChart->pluck('name'));
            const categoryCounts = @json($categoryChart->pluck('machines_count'));
            const categoryColors = @json($categoryColors);

            let messageChart = null;
            let categoryChart = null;

            const messageCanvas = document.getElementById('adminMessageChart');

            if (messageCanvas) {
                const ctx = messageCanvas.getContext('2d');
                const gradient = ctx.createLinearGradient(0, 0, 0, 320);
                gradient.addColorStop(0, 'rgba(205, 102, 36, 0.35)');
                gradient.addColorStop(1, 'rgba(205, 102, 36, 0.02)');

                messageChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: monthLabels,
                        datasets: [{
                            label: 'Pesan masuk',
                            data: monthCounts,
                            borderColor: '#cd6624',
                            backgroundColor: gradient,
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 6,
                            pointHoverRadius: 8,
                            pointBackgroundColor: '#cd6624',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(17, 24, 39, 0.92)',
                                padding: 12,
                                cornerRadius: 10,
                                displayColors: false,
                            },
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0, color: tickColor() },
                                grid: { color: gridColor() },
                            },
                            x: {
                                ticks: { color: tickColor() },
                                grid: { display: false },
                            },
                        },
                    },
                });
            }

            const categoryCanvas = document.getElementById('adminCategoryChart');

            if (categoryCanvas && categoryCounts.length > 0) {
                categoryChart = new Chart(categoryCanvas.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: categoryLabels,
                        datasets: [{
                            data: categoryCounts,
                            backgroundColor: categoryColors.slice(0, categoryCounts.length),
                            borderWidth: 0,
                            hoverOffset: 8,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '75%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(17, 24, 39, 0.92)',
                                padding: 12,
                                cornerRadius: 10,
                            },
                        },
                    },
                });
            }

            // Warna garis bantu disesuaikan saat tema terang/gelap ditukar.
            window.onAdminThemeChange = function () {
                if (!messageChart) {
                    return;
                }

                messageChart.options.scales.y.grid.color = gridColor();
                messageChart.options.scales.y.ticks.color = tickColor();
                messageChart.options.scales.x.ticks.color = tickColor();
                messageChart.update();
            };
        });
    </script>
@endpush