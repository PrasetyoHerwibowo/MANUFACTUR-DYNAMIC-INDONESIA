@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Ringkasan operasional dan katalog mesin ' . $company->name)

@php
    /** Hitung persentase aman (tanpa pembagian nol). */
    $persen = fn (int $bagian, int $total): int => $total > 0 ? (int) round($bagian / $total * 100) : 0;

    $mesinAktifBar = $persen((int) $stats['mesin_aktif'], (int) $stats['mesin']);
    $kategoriAktifBar = $persen((int) $stats['kategori_aktif'], (int) $stats['kategori']);
    $mesinBerfotoBar = $persen((int) $stats['mesin_berfoto'], (int) $stats['mesin']);

    /** Metrik performa konten dengan palet coffee & brand (@Desaind.md). */
    $metricBars = [
        ['label' => 'Model mesin aktif', 'value' => (int) $stats['mesin_aktif'], 'total' => (int) $stats['mesin'], 'color' => 'from-coffee-500 to-coffee-700'],
        ['label' => 'Jenis mesin aktif', 'value' => (int) $stats['kategori_aktif'], 'total' => (int) $stats['kategori'], 'color' => 'from-brand-600 to-brand-800'],
        ['label' => 'Model mesin berfoto', 'value' => (int) $stats['mesin_berfoto'], 'total' => (int) $stats['mesin'], 'color' => 'from-coffee-600 to-brand-900'],
    ];

    /** Warna seragam coffee & brand (@Desaind.md) untuk donat chart dan dot list. */
    $categoryColors = ['#ab4d1f', '#cd6624', '#db7f2e', '#cf985e', '#8b502c', '#5d3826'];
    $categoryStyles = [
        ['dot' => 'bg-coffee-700', 'tint' => 'bg-coffee-50/80 text-coffee-800 dark:bg-coffee-900/30 dark:text-coffee-300'],
        ['dot' => 'bg-coffee-600', 'tint' => 'bg-amber-50/80 text-amber-900 dark:bg-amber-950/30 dark:text-amber-300'],
        ['dot' => 'bg-coffee-500', 'tint' => 'bg-orange-50/80 text-orange-900 dark:bg-orange-950/30 dark:text-orange-300'],
        ['dot' => 'bg-brand-400',  'tint' => 'bg-stone-50 text-stone-800 dark:bg-stone-800/60 dark:text-stone-300'],
        ['dot' => 'bg-brand-700',  'tint' => 'bg-coffee-100/60 text-coffee-900 dark:bg-coffee-900/40 dark:text-coffee-200'],
        ['dot' => 'bg-brand-900',  'tint' => 'bg-stone-100 text-stone-900 dark:bg-stone-800 dark:text-stone-200'],
    ];
@endphp

@section('content')
    <div class="page active animate-fade-in" id="page-dashboard">
        {{-- Card Tunggal Dashboard Terpadu (Kompak, Rapat & Selaras Desaind.md) --}}
        <div class="glass-card overflow-hidden rounded-2xl border border-gray-200/90 bg-white shadow-sm dark:border-gray-700/80 dark:bg-gray-800">

            {{-- Header Card Terpadu --}}
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200/80 px-5 py-3.5 dark:border-gray-700/80">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-coffee-500 to-coffee-700 text-white shadow-sm">
                        <i aria-hidden="true" class="fas fa-chart-line text-xs"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-gray-900 dark:text-white">Ikhtisar Katalog & Operasional Mesin</h2>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400">Monitoring data mesin dan kesiapan konten secara terintegrasi</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-coffee-50 px-2.5 py-0.5 text-[11px] font-semibold text-coffee-700 dark:bg-coffee-900/30 dark:text-coffee-300">
                        <span class="h-1.5 w-1.5 rounded-full bg-coffee-500 animate-pulse"></span>
                        Data Terkini
                    </span>
                    <a href="{{ route('admin.machines.create') }}"
                       class="inline-flex items-center gap-1.5 rounded-xl bg-brand-700 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-brand-800">
                        <i aria-hidden="true" class="fas fa-plus text-[10px]"></i>
                        <span>Tambah Mesin</span>
                    </a>
                </div>
            </div>

            {{-- Strip 4 Metrik Statistik Utama (Grid Padat & Rapat) --}}
            <div class="grid grid-cols-2 divide-x divide-y divide-gray-200/70 border-b border-gray-200/80 bg-gradient-to-b from-coffee-50/30 to-transparent sm:grid-cols-4 sm:divide-y-0 dark:divide-gray-700/70 dark:border-gray-700/80 dark:from-coffee-950/10">

                {{-- KPI 1: Model Mesin --}}
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Model Mesin</span>
                        <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-coffee-100 text-coffee-700 dark:bg-coffee-900/40 dark:text-coffee-300">
                            <i aria-hidden="true" class="fas fa-cogs text-[11px]"></i>
                        </div>
                    </div>
                    <div class="mt-1.5 flex items-baseline gap-2">
                        <span class="text-xl font-extrabold text-gray-900 dark:text-white">{{ number_format($stats['mesin'], 0, ',', '.') }}</span>
                        <span class="text-[11px] font-semibold text-coffee-700 dark:text-coffee-400">({{ $mesinAktifBar }}% aktif)</span>
                    </div>
                    <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700">
                        <div class="metric-bar h-full rounded-full bg-gradient-to-r from-coffee-500 to-coffee-700" style="width: {{ $mesinAktifBar }}%"></div>
                    </div>
                    <p class="mt-1 text-[11px] text-gray-400 dark:text-gray-500">{{ $stats['mesin_aktif'] }} unit aktif tayang</p>
                </div>

                {{-- KPI 2: Jenis Mesin --}}
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Jenis Mesin</span>
                        <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300">
                            <i aria-hidden="true" class="fas fa-layer-group text-[11px]"></i>
                        </div>
                    </div>
                    <div class="mt-1.5 flex items-baseline gap-2">
                        <span class="text-xl font-extrabold text-gray-900 dark:text-white">{{ number_format($stats['kategori'], 0, ',', '.') }}</span>
                        <span class="text-[11px] font-semibold text-amber-700 dark:text-amber-400">({{ $kategoriAktifBar }}% aktif)</span>
                    </div>
                    <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700">
                        <div class="metric-bar h-full rounded-full bg-gradient-to-r from-brand-600 to-brand-800" style="width: {{ $kategoriAktifBar }}%"></div>
                    </div>
                    <p class="mt-1 text-[11px] text-gray-400 dark:text-gray-500">{{ $stats['kategori_aktif'] }} kategori aktif</p>
                </div>

                {{-- KPI 3: Galeri Foto Mesin --}}
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Galeri Foto</span>
                        <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-100 text-orange-800 dark:bg-orange-950/40 dark:text-orange-300">
                            <i aria-hidden="true" class="fas fa-images text-[11px]"></i>
                        </div>
                    </div>
                    <div class="mt-1.5 flex items-baseline gap-2">
                        <span class="text-xl font-extrabold text-gray-900 dark:text-white">{{ number_format($stats['foto'], 0, ',', '.') }}</span>
                        <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400">Aset</span>
                    </div>
                    <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700">
                        <div class="metric-bar h-full rounded-full bg-gradient-to-r from-coffee-600 to-orange-600" style="width: 100%"></div>
                    </div>
                    <p class="mt-1 text-[11px] text-gray-400 dark:text-gray-500">Total visualisasi produk</p>
                </div>

                {{-- KPI 4: Mesin Berfoto --}}
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Mesin Berfoto</span>
                        <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300">
                            <i aria-hidden="true" class="fas fa-check-double text-[11px]"></i>
                        </div>
                    </div>
                    <div class="mt-1.5 flex items-baseline gap-2">
                        <span class="text-xl font-extrabold text-gray-900 dark:text-white">{{ number_format($stats['mesin_berfoto'], 0, ',', '.') }}</span>
                        <span class="text-[11px] font-semibold text-emerald-700 dark:text-emerald-400">({{ $mesinBerfotoBar }}%)</span>
                    </div>
                    <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700">
                        <div class="metric-bar h-full rounded-full bg-gradient-to-r from-coffee-600 to-brand-900" style="width: {{ $mesinBerfotoBar }}%"></div>
                    </div>
                    <p class="mt-1 text-[11px] text-gray-400 dark:text-gray-500">{{ $stats['mesin_berfoto'] }} dari {{ $stats['mesin'] }} model lengkap</p>
                </div>
            </div>

            {{-- Bagian Bawah: Donut Chart Kategori (Kiri) + Kelengkapan & Aksi Cepat (Kanan) --}}
            <div class="grid grid-cols-1 divide-y divide-gray-200/80 lg:grid-cols-12 lg:divide-x lg:divide-y-0 dark:divide-gray-700/80">

                {{-- Sisi Kiri: Distribusi Jenis Mesin (7 kolom) --}}
                <div class="p-5 lg:col-span-7">
                    <div class="mb-3.5 flex items-center justify-between">
                        <div>
                            <h3 class="text-xs font-bold text-gray-900 dark:text-white">Distribusi Unit per Jenis Mesin</h3>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400">Proporsi unit mesin terdaftar per lini produksi</p>
                        </div>
                        <a href="{{ route('admin.categories.index') }}"
                           class="text-[11px] font-semibold text-coffee-600 hover:text-coffee-700 dark:text-coffee-400">
                            Semua Jenis &rarr;
                        </a>
                    </div>

                    @if ($categoryChart->sum('machines_count') > 0)
                        <div class="grid grid-cols-1 items-center gap-3 sm:grid-cols-12">
                            <div class="chart-container relative h-40 sm:col-span-5">
                                <canvas id="adminCategoryChart"></canvas>
                            </div>

                            <div class="space-y-1.5 sm:col-span-7">
                                @foreach ($categoryChart as $index => $kategori)
                                    @php $style = $categoryStyles[$index % count($categoryStyles)]; @endphp
                                    <div class="flex items-center justify-between rounded-lg px-2.5 py-1.5 {{ $style['tint'] }} transition hover:opacity-90">
                                        <div class="flex min-w-0 items-center gap-2">
                                            <span class="h-2 w-2 shrink-0 rounded-full {{ $style['dot'] }}"></span>
                                            <span class="truncate text-xs font-medium">{{ $kategori->name }}</span>
                                        </div>
                                        <span class="ml-2 shrink-0 text-xs font-bold">{{ $kategori->machines_count }} unit</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="flex h-36 flex-col items-center justify-center gap-1.5 rounded-xl bg-gray-50 text-center dark:bg-gray-700/30">
                            <i aria-hidden="true" class="fas fa-chart-pie text-xl text-gray-300 dark:text-gray-600"></i>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Belum ada model mesin yang tercatat.</p>
                            <a href="{{ route('admin.machines.create') }}" class="text-xs font-semibold text-coffee-600 hover:text-coffee-700 dark:text-coffee-400">
                                Tambah model mesin &rarr;
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Sisi Kanan: Kelengkapan Konten & Aksi Cepat (5 kolom) --}}
                <div class="flex flex-col justify-between space-y-4 p-5 lg:col-span-5">

                    {{-- Metrik Performa Konten Ringkas --}}
                    <div>
                        <div class="mb-2.5 flex items-center justify-between">
                            <h3 class="text-xs font-bold text-gray-900 dark:text-white">Kelengkapan Konten</h3>
                            <a href="{{ route('admin.machines.index') }}" class="text-[11px] font-semibold text-coffee-600 hover:text-coffee-700 dark:text-coffee-400">
                                Kelola &rarr;
                            </a>
                        </div>

                        <div class="space-y-2.5">
                            @foreach ($metricBars as $bar)
                                @php $nilaiBar = $persen($bar['value'], $bar['total']); @endphp
                                <div>
                                    <div class="mb-1 flex items-center justify-between text-xs">
                                        <span class="text-[11px] font-medium text-gray-600 dark:text-gray-400">{{ $bar['label'] }}</span>
                                        <span class="text-xs font-bold text-gray-900 dark:text-white">
                                            {{ $nilaiBar }}%
                                            <span class="font-normal text-gray-400">({{ $bar['value'] }}/{{ $bar['total'] }})</span>
                                        </span>
                                    </div>
                                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700">
                                        <div class="metric-bar h-full rounded-full bg-gradient-to-r {{ $bar['color'] }}" style="width: {{ $nilaiBar }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Aksi Cepat Rapat --}}
                    <div class="border-t border-gray-100 pt-3 dark:border-gray-700/60">
                        <span class="mb-2 block text-[10px] font-bold uppercase tracking-wider text-gray-400">Aksi Cepat</span>
                        <div class="grid grid-cols-3 gap-2">
                            <a href="{{ route('admin.machines.create') }}"
                               class="flex flex-col items-center justify-center rounded-xl border border-gray-200/80 bg-gray-50/70 p-2 text-center transition hover:border-coffee-500 hover:bg-coffee-50 hover:text-coffee-800 dark:border-gray-700 dark:bg-gray-800/60 dark:text-gray-300 dark:hover:border-coffee-500 dark:hover:bg-coffee-900/20 dark:hover:text-coffee-300">
                                <i aria-hidden="true" class="fas fa-plus mb-1 text-xs text-coffee-600 dark:text-coffee-400"></i>
                                <span class="truncate text-[10px] font-semibold">+ Model</span>
                            </a>

                            <a href="{{ route('admin.categories.create') }}"
                               class="flex flex-col items-center justify-center rounded-xl border border-gray-200/80 bg-gray-50/70 p-2 text-center transition hover:border-amber-500 hover:bg-amber-50 hover:text-amber-800 dark:border-gray-700 dark:bg-gray-800/60 dark:text-gray-300 dark:hover:border-amber-500 dark:hover:bg-amber-950/20 dark:hover:text-amber-300">
                                <i aria-hidden="true" class="fas fa-layer-group mb-1 text-xs text-amber-700 dark:text-amber-400"></i>
                                <span class="truncate text-[10px] font-semibold">+ Jenis</span>
                            </a>

                            <a href="{{ route('admin.profile.edit') }}"
                               class="flex flex-col items-center justify-center rounded-xl border border-gray-200/80 bg-gray-50/70 p-2 text-center transition hover:border-brand-600 hover:bg-stone-50 hover:text-brand-900 dark:border-gray-700 dark:bg-gray-800/60 dark:text-gray-300 dark:hover:border-brand-500 dark:hover:bg-stone-900/30 dark:hover:text-brand-300">
                                <i aria-hidden="true" class="fas fa-building mb-1 text-xs text-brand-700 dark:text-brand-400"></i>
                                <span class="truncate text-[10px] font-semibold">Profil</span>
                            </a>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function initCategoryChart() {
                if (typeof Chart === 'undefined') {
                    setTimeout(initCategoryChart, 50);
                    return;
                }

                const categoryLabels = @json($categoryChart->pluck('name'));
                const categoryCounts = @json($categoryChart->pluck('machines_count'));
                const categoryColors = @json($categoryColors);

                const categoryCanvas = document.getElementById('adminCategoryChart');

                if (categoryCanvas && categoryCounts.length > 0) {
                    new Chart(categoryCanvas.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: categoryLabels,
                            datasets: [{
                                data: categoryCounts,
                                backgroundColor: categoryColors.slice(0, categoryCounts.length),
                                borderWidth: 0,
                                hoverOffset: 6,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '72%',
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: 'rgba(50, 28, 19, 0.95)',
                                    padding: 10,
                                    cornerRadius: 8,
                                    titleFont: { size: 11, family: 'Plus Jakarta Sans' },
                                    bodyFont: { size: 11, family: 'Plus Jakarta Sans' },
                                },
                            },
                        },
                    });
                }
            }

            initCategoryChart();
        });
    </script>
@endpush
