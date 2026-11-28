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

    /** Metrik performa konten dengan palet industrial monochrome (acuan MANUFAKTUR-MOBILE). */
    $metricBars = [
        ['label' => 'Model mesin aktif', 'value' => (int) $stats['mesin_aktif'], 'total' => (int) $stats['mesin'], 'color' => 'from-zinc-950 to-zinc-700'],
        ['label' => 'Jenis mesin aktif', 'value' => (int) $stats['kategori_aktif'], 'total' => (int) $stats['kategori'], 'color' => 'from-zinc-800 to-zinc-600'],
        ['label' => 'Model mesin berfoto', 'value' => (int) $stats['mesin_berfoto'], 'total' => (int) $stats['mesin'], 'color' => 'from-zinc-700 to-zinc-500'],
    ];

    /** Warna industrial monochrome untuk donat chart dan dot list. */
    $categoryColors = ['#09090b', '#27272a', '#3f3f46', '#52525b', '#71717a', '#a1a1aa'];
    $categoryStyles = [
        ['dot' => 'bg-zinc-950', 'tint' => 'bg-zinc-100 text-zinc-900'],
        ['dot' => 'bg-zinc-800', 'tint' => 'bg-zinc-100/80 text-zinc-800'],
        ['dot' => 'bg-zinc-700', 'tint' => 'bg-zinc-100/60 text-zinc-800'],
        ['dot' => 'bg-zinc-600', 'tint' => 'bg-zinc-50 text-zinc-700'],
        ['dot' => 'bg-zinc-500', 'tint' => 'bg-zinc-50 text-zinc-700'],
        ['dot' => 'bg-zinc-400', 'tint' => 'bg-zinc-50 text-zinc-600'],
    ];
@endphp

@section('content')
    <div class="page active animate-fade-in" id="page-dashboard">
        {{-- Banner Notifikasi Layanan Purna Jual (Booking Servis & Klaim Garansi Masuk) --}}
        @if (($pendingServices ?? 0) > 0 || ($pendingWarranties ?? 0) > 0)
            <div class="mb-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                @if (($pendingServices ?? 0) > 0)
                    <div class="flex items-center justify-between gap-3 rounded-2xl border border-amber-200 bg-amber-50/80 p-4 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-600 text-white shadow-sm">
                                <i class="fas fa-wrench text-sm"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-amber-950">{{ $pendingServices }} Booking Servis Menunggu Verifikasi</h3>
                                <p class="text-xs text-amber-800">Customer mengajukan jadwal kunjungan maintenance & teknisi.</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.services.index', ['status' => 'menunggu_verifikasi']) }}"
                           class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-zinc-950 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm transition hover:bg-zinc-800">
                            Verifikasi <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                @endif

                @if (($pendingWarranties ?? 0) > 0)
                    <div class="flex items-center justify-between gap-3 rounded-2xl border border-red-200 bg-red-50/80 p-4 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-600 text-white shadow-sm">
                                <i class="fas fa-shield-alt text-sm"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-red-950">{{ $pendingWarranties }} Klaim Garansi Menunggu Pemeriksaan</h3>
                                <p class="text-xs text-red-800">Periksa foto/video kerusakan fisik mesin yang diunggah.</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.warranties.index', ['status' => 'dalam_peninjauan']) }}"
                           class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-zinc-950 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm transition hover:bg-zinc-800">
                            Periksa Bukti <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                @endif
            </div>
        @endif

        {{-- Card Tunggal Dashboard Terpadu (Monokrom Industrial selaras MANUFAKTUR-MOBILE) --}}
        <div class="glass-card overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm">

            {{-- Header Card Terpadu --}}
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-zinc-200 px-5 py-3.5">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-zinc-950 text-white shadow-sm">
                        <i aria-hidden="true" class="fas fa-chart-line text-xs"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-zinc-950">Ikhtisar Katalog & Operasional Mesin</h2>
                        <p class="text-[11px] text-zinc-500">Monitoring data mesin dan kesiapan konten secara terintegrasi</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-zinc-100 px-2.5 py-0.5 text-[11px] font-semibold text-zinc-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-zinc-950 animate-pulse"></span>
                        Data Terkini
                    </span>
                    <a href="{{ route('admin.machines.create') }}"
                       class="inline-flex items-center gap-1.5 rounded-full bg-zinc-950 px-3.5 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-zinc-800">
                        <i aria-hidden="true" class="fas fa-plus text-[10px]"></i>
                        <span>Tambah Mesin</span>
                    </a>
                </div>
            </div>

            {{-- Strip 4 Metrik Statistik Utama (Grid Padat & Rapat) --}}
            <div class="grid grid-cols-2 divide-x divide-y divide-zinc-200/80 border-b border-zinc-200 bg-zinc-50/50 sm:grid-cols-4 sm:divide-y-0">

                {{-- KPI 1: Model Mesin --}}
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-zinc-500">Model Mesin</span>
                        <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-zinc-100 text-zinc-900">
                            <i aria-hidden="true" class="fas fa-cogs text-[11px]"></i>
                        </div>
                    </div>
                    <div class="mt-1.5 flex items-baseline gap-2">
                        <span class="text-xl font-extrabold text-zinc-950">{{ number_format($stats['mesin'], 0, ',', '.') }}</span>
                        <span class="text-[11px] font-semibold text-zinc-600">({{ $mesinAktifBar }}% aktif)</span>
                    </div>
                    <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-zinc-100">
                        <div class="metric-bar h-full rounded-full bg-gradient-to-r from-zinc-950 to-zinc-700" style="width: {{ $mesinAktifBar }}%"></div>
                    </div>
                    <p class="mt-1 text-[11px] text-zinc-500">{{ $stats['mesin_aktif'] }} unit aktif tayang</p>
                </div>

                {{-- KPI 2: Jenis Mesin --}}
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-zinc-500">Jenis Mesin</span>
                        <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-zinc-100 text-zinc-900">
                            <i aria-hidden="true" class="fas fa-layer-group text-[11px]"></i>
                        </div>
                    </div>
                    <div class="mt-1.5 flex items-baseline gap-2">
                        <span class="text-xl font-extrabold text-zinc-950">{{ number_format($stats['kategori'], 0, ',', '.') }}</span>
                        <span class="text-[11px] font-semibold text-zinc-600">({{ $kategoriAktifBar }}% aktif)</span>
                    </div>
                    <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-zinc-100">
                        <div class="metric-bar h-full rounded-full bg-gradient-to-r from-zinc-800 to-zinc-600" style="width: {{ $kategoriAktifBar }}%"></div>
                    </div>
                    <p class="mt-1 text-[11px] text-zinc-500">{{ $stats['kategori_aktif'] }} kategori aktif</p>
                </div>

                {{-- KPI 3: Galeri Foto Mesin --}}
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-zinc-500">Galeri Foto</span>
                        <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-zinc-100 text-zinc-900">
                            <i aria-hidden="true" class="fas fa-images text-[11px]"></i>
                        </div>
                    </div>
                    <div class="mt-1.5 flex items-baseline gap-2">
                        <span class="text-xl font-extrabold text-zinc-950">{{ number_format($stats['foto'], 0, ',', '.') }}</span>
                        <span class="text-[11px] font-semibold text-zinc-500">Aset</span>
                    </div>
                    <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-zinc-100">
                        <div class="metric-bar h-full rounded-full bg-gradient-to-r from-zinc-700 to-zinc-500" style="width: 100%"></div>
                    </div>
                    <p class="mt-1 text-[11px] text-zinc-500">Total visualisasi produk</p>
                </div>

                {{-- KPI 4: Mesin Berfoto --}}
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-zinc-500">Mesin Berfoto</span>
                        <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-zinc-100 text-zinc-900">
                            <i aria-hidden="true" class="fas fa-check-double text-[11px]"></i>
                        </div>
                    </div>
                    <div class="mt-1.5 flex items-baseline gap-2">
                        <span class="text-xl font-extrabold text-zinc-950">{{ number_format($stats['mesin_berfoto'], 0, ',', '.') }}</span>
                        <span class="text-[11px] font-semibold text-zinc-500">({{ $mesinBerfotoBar }}%)</span>
                    </div>
                    <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-zinc-100">
                        <div class="metric-bar h-full rounded-full bg-gradient-to-r from-zinc-950 to-zinc-700" style="width: {{ $mesinBerfotoBar }}%"></div>
                    </div>
                    <p class="mt-1 text-[11px] text-zinc-500">{{ $stats['mesin_berfoto'] }} dari {{ $stats['mesin'] }} model lengkap</p>
                </div>
            </div>

            {{-- Bagian Bawah: Donut Chart Kategori (Kiri) + Kelengkapan & Aksi Cepat (Kanan) --}}
            <div class="grid grid-cols-1 divide-y divide-zinc-200 lg:grid-cols-12 lg:divide-x lg:divide-y-0">

                {{-- Sisi Kiri: Distribusi Jenis Mesin (7 kolom) --}}
                <div class="p-5 lg:col-span-7">
                    <div class="mb-3.5 flex items-center justify-between">
                        <div>
                            <h3 class="text-xs font-bold text-zinc-950">Distribusi Unit per Jenis Mesin</h3>
                            <p class="text-[11px] text-zinc-500">Proporsi unit mesin terdaftar per lini produksi</p>
                        </div>
                        <a href="{{ route('admin.categories.index') }}"
                           class="text-[11px] font-semibold text-zinc-700 hover:text-zinc-950">
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
                        <div class="flex h-36 flex-col items-center justify-center gap-1.5 rounded-xl bg-zinc-50 text-center">
                            <i aria-hidden="true" class="fas fa-chart-pie text-xl text-zinc-300"></i>
                            <p class="text-xs text-zinc-500">Belum ada model mesin yang tercatat.</p>
                            <a href="{{ route('admin.machines.create') }}" class="text-xs font-semibold text-zinc-900 hover:underline">
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
                            <h3 class="text-xs font-bold text-zinc-950">Kelengkapan Konten</h3>
                            <a href="{{ route('admin.machines.index') }}" class="text-[11px] font-semibold text-zinc-700 hover:text-zinc-950">
                                Kelola &rarr;
                            </a>
                        </div>

                        <div class="space-y-2.5">
                            @foreach ($metricBars as $bar)
                                @php $nilaiBar = $persen($bar['value'], $bar['total']); @endphp
                                <div>
                                    <div class="mb-1 flex items-center justify-between text-xs">
                                        <span class="text-[11px] font-medium text-zinc-600">{{ $bar['label'] }}</span>
                                        <span class="text-xs font-bold text-zinc-950">
                                            {{ $nilaiBar }}%
                                            <span class="font-normal text-zinc-400">({{ $bar['value'] }}/{{ $bar['total'] }})</span>
                                        </span>
                                    </div>
                                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-zinc-100">
                                        <div class="metric-bar h-full rounded-full bg-gradient-to-r {{ $bar['color'] }}" style="width: {{ $nilaiBar }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Aksi Cepat Rapat --}}
                    <div class="border-t border-zinc-100 pt-3">
                        <span class="mb-2 block text-[10px] font-bold uppercase tracking-wider text-zinc-400">Aksi Cepat</span>
                        <div class="grid grid-cols-3 gap-2">
                            <a href="{{ route('admin.machines.create') }}"
                               class="flex flex-col items-center justify-center rounded-xl border border-zinc-200 bg-zinc-50 p-2 text-center text-zinc-800 transition hover:border-zinc-950 hover:bg-zinc-100 hover:text-zinc-950">
                                <i aria-hidden="true" class="fas fa-plus mb-1 text-xs text-zinc-900"></i>
                                <span class="truncate text-[10px] font-semibold">+ Model</span>
                            </a>

                            <a href="{{ route('admin.categories.create') }}"
                               class="flex flex-col items-center justify-center rounded-xl border border-zinc-200 bg-zinc-50 p-2 text-center text-zinc-800 transition hover:border-zinc-950 hover:bg-zinc-100 hover:text-zinc-950">
                                <i aria-hidden="true" class="fas fa-layer-group mb-1 text-xs text-zinc-900"></i>
                                <span class="truncate text-[10px] font-semibold">+ Jenis</span>
                            </a>

                            <a href="{{ route('admin.profile.edit') }}"
                               class="flex flex-col items-center justify-center rounded-xl border border-zinc-200 bg-zinc-50 p-2 text-center text-zinc-800 transition hover:border-zinc-950 hover:bg-zinc-100 hover:text-zinc-950">
                                <i aria-hidden="true" class="fas fa-building mb-1 text-xs text-zinc-900"></i>
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
                                    backgroundColor: 'rgba(9, 9, 11, 0.95)',
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
