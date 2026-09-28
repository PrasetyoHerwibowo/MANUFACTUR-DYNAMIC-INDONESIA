@extends('layouts.public')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        .font-condensed {
            font-family: 'Oswald', ui-sans-serif, system-ui, sans-serif;
            letter-spacing: -0.01em;
        }

        .hero-gradient-overlay {
            background: linear-gradient(90deg, rgba(8, 14, 23, 0.95) 0%, rgba(13, 22, 36, 0.82) 48%, rgba(11, 20, 34, 0.45) 100%);
        }

        .hero-bottom-fade {
            background: linear-gradient(180deg, rgba(11, 17, 26, 0) 0%, rgba(11, 17, 26, 0.85) 85%, #0B111A 100%);
        }

        html:not(.dark) .hero-gradient-overlay {
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.92) 0%, rgba(249, 250, 251, 0.80) 48%, rgba(249, 250, 251, 0.40) 100%);
        }

html:not(.dark) .hero-bottom-fade {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0) 0%, rgba(249, 250, 251, 0.85) 85%, #F9FAFB 100%);
        }
    </style>
@endpush

@section('title', $company->name.' — '.$company->tagline)

@section('content')
    @php
        $wa = preg_replace('/[^0-9]/', '', (string) $company->whatsapp);
        $statsDisplay = [
            ['label' => 'Jenis Mesin', 'value' => $stats['kategori']],
            ['label' => 'Model Mesin', 'value' => $stats['mesin']],
            ['label' => 'Katalog Produk', 'value' => $stats['katalog']],
            ['label' => 'Foto Produk', 'value' => $stats['foto']],
        ];
        $exports = array_values(array_filter(array_map('trim', explode(',', (string) $company->export_countries)), fn ($v) => $v !== ''));
    @endphp

    {{-- ============ HERO ============ --}}
    <section id="hero"
        class="relative flex min-h-[720px] items-center overflow-hidden bg-gray-100 pt-20 pb-16 transition-colors duration-300 dark:bg-[#0B111A] lg:min-h-[820px]">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/bg.png') }}" alt="{{ $company->name }}"
                class="h-full w-full object-cover object-right brightness-90 contrast-110 dark:brightness-65 md:object-center">
            <div class="absolute inset-0 hero-gradient-overlay"></div>
            <div class="absolute inset-0 hero-bottom-fade"></div>
        </div>

        <div class="relative z-10 mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl lg:max-w-3xl">
                <div
                    class="mb-6 inline-flex items-center gap-2 rounded-md bg-red-600 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-white shadow-md shadow-red-600/30">
                    <span class="h-2 w-2 animate-pulse rounded-full bg-white"></span>
                    <span>Easy | Economic | Excellent</span>
                </div>

                <h1
                    class="font-condensed mb-5 text-5xl font-bold uppercase leading-[0.95] tracking-tight text-slate-900 text-shadow-lg dark:text-white sm:text-6xl md:text-7xl lg:text-[5rem]">
                    Mesin <span class="text-red-600">Pengolahan</span> Kopi &amp; Kakao untuk <span
                        class="text-red-600">Indonesia</span>
                </h1>

                <div class="mb-4 flex items-center gap-3">
                    <span class="inline-block h-6 w-1.5 rounded-full bg-red-600"></span>
                    <p class="text-lg font-semibold tracking-wide text-slate-700 dark:text-slate-200 md:text-xl">
                        {{ $company->tagline }}
                    </p>
                </div>

                <p class="mb-8 max-w-xl text-sm font-normal leading-relaxed text-slate-600 dark:text-slate-300 md:text-base">
                    {{ $company->about ? \Illuminate\Support\Str::limit(strip_tags($company->about), 230) : 'Produsen mesin pengolahan kopi dan kakao berkualitas, dipercaya oleh perkebunan, koperasi, pabrik pengolahan, dan UMKM di seluruh Indonesia.' }}
                </p>

                <div class="flex flex-wrap items-center gap-4">
                    <a href="{{ route('catalogs.index') }}"
                        class="inline-flex items-center gap-2.5 rounded-lg bg-red-600 px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-red-600/40 transition-all hover:-translate-y-0.5 hover:bg-red-700 hover:shadow-red-600/60">
                        <i class="ti ti-book text-base"></i>
                        <span>Lihat Katalog</span>
                    </a>
                    @if ($wa)
                        <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener"
                            class="inline-flex items-center gap-2.5 rounded-lg border border-gray-400 bg-white/60 px-6 py-3.5 text-sm font-medium text-slate-900 backdrop-blur-sm transition-all hover:-translate-y-0.5 hover:border-slate-400 hover:bg-gray-100 dark:border-slate-500/80 dark:bg-slate-900/60 dark:text-white dark:hover:border-slate-300 dark:hover:bg-slate-800/90">
                            <i class="ti ti-phone text-sm text-red-500"></i>
                            <span>Hubungi Kami</span>
                        </a>
                    @endif
                </div>

                <div
                    class="mt-12 grid max-w-xl grid-cols-4 gap-4 border-t border-gray-300/60 pt-4 dark:border-slate-700/60">
                    @foreach ($statsDisplay as $stat)
                        <div>
                            <p class="font-condensed text-2xl font-bold text-slate-900 dark:text-white">{{ $stat['value'] }}+</p>
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $stat['label'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ============ TENTANG SINGKAT ============ --}}
    <section class="bg-white py-20 transition-colors duration-300 dark:bg-[#0B111A]">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid items-center gap-12 lg:grid-cols-2">
                <div class="relative">
                    <div class="overflow-hidden rounded-2xl border border-gray-200 shadow-xl dark:border-slate-700">
                        <img src="{{ asset('images/bg.png') }}" alt="Pabrik {{ $company->name }}"
                            class="aspect-[4/3] w-full object-cover transition-transform duration-500 hover:scale-105">
                    </div>

                    <div class="mt-5 grid grid-cols-3 gap-3">
                        @foreach ([
                            ['title' => 'Presisi', 'desc' => 'Produksi terukur'],
                            ['title' => 'Aman', 'desc' => 'Material food grade'],
                            ['title' => 'Bergaransi', 'desc' => 'Layanan purna jual'],
                        ] as $item)
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-3 text-center dark:border-slate-800 dark:bg-slate-900/60">
                                <p class="text-sm font-bold text-red-600 dark:text-red-400">{{ $item['title'] }}</p>
                                <p class="mt-0.5 text-[11px] text-slate-500 dark:text-gray-400">{{ $item['desc'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-red-600 dark:text-red-500">Tentang Kami</span>
                    <h2 class="font-condensed mt-3 text-3xl font-bold uppercase tracking-tight text-slate-900 dark:text-white sm:text-4xl">
                        Mitra terpercaya pengolahan <span class="text-red-600">hasil perkebunan</span>
                    </h2>

                    <div class="mt-5 space-y-4 text-sm leading-relaxed text-slate-600 dark:text-gray-300">
                        <p>
                            {{ $company->about
                                ? \Illuminate\Support\Str::limit(strip_tags($company->about), 520)
                                : $company->name.' merupakan perusahaan manufaktur yang merancang dan memproduksi mesin pengolahan kopi dan kakao, mulai dari mesin pengupas, fermentasi, pengering, hingga pengemasan.' }}
                        </p>

                        @if ($company->vision)
                            <p><span class="font-bold text-slate-700 dark:text-gray-200">Visi:</span> {{ \Illuminate\Support\Str::limit(strip_tags($company->vision), 200) }}</p>
                        @endif
                    </div>

                    <dl class="mt-7 grid grid-cols-2 gap-4 text-sm">
                        @if ($company->founded_year)
                            <div class="rounded-xl border border-gray-200 p-4 dark:border-slate-800">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400 dark:text-gray-500">Berdiri Sejak</dt>
                                <dd class="mt-1 font-condensed text-lg font-bold text-slate-900 dark:text-white">{{ $company->founded_year }}</dd>
                            </div>
                        @endif
                        @if ($company->employees)
                            <div class="rounded-xl border border-gray-200 p-4 dark:border-slate-800">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400 dark:text-gray-500">Jumlah Karyawan</dt>
                                <dd class="mt-1 font-condensed text-lg font-bold text-slate-900 dark:text-white">{{ $company->employees }}</dd>
                            </div>
                        @endif
                    </dl>

                    <a href="{{ route('about') }}"
                        class="mt-7 inline-flex items-center gap-2.5 rounded-lg bg-red-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-red-600/40 transition-all hover:-translate-y-0.5 hover:bg-red-700">
                        Selengkapnya tentang kami
                        <i class="ti ti-arrow-right text-sm"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ MODEL MESIN UNGGULAN ============ --}}
    @if ($featured->isNotEmpty())
        <section id="products"
            class="relative border-y border-gray-200 bg-gray-100 py-20 transition-colors duration-300 dark:border-slate-800 dark:bg-[#131B26]">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-10 flex flex-wrap items-end justify-between gap-4 pb-4">
                    <div>
                        <div
                            class="mb-2 inline-flex items-center gap-2 rounded bg-gray-200 px-3 py-1 text-xs font-bold uppercase tracking-wider text-red-600 dark:bg-slate-800 dark:text-red-400">
                            <i class="ti ti-award"></i> Model Mesin
                        </div>
                        <h2
                            class="font-condensed text-3xl font-bold uppercase tracking-tight text-slate-900 dark:text-white md:text-4xl lg:text-5xl">
                            Mesin <span class="text-red-500">Unggulan</span> Kami
                        </h2>
                        <p class="mt-1 max-w-2xl text-sm text-slate-600 dark:text-slate-400 md:text-base">
                            Mesin-mesin pilihan pelanggan untuk pengolahan kopi &amp; kakao, dari skala UMKM hingga industri.
                        </p>
                    </div>

                    <a href="{{ route('products.index') }}"
                        class="inline-flex items-center gap-2 text-sm font-bold text-red-600 transition hover:text-red-700 dark:text-red-400">
                        Lihat semua model <i class="ti ti-arrow-right text-sm"></i>
                    </a>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($featured as $machine)
                        <x-machine-card :machine="$machine" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

            {{-- ============ KATALOG ============ --}}
    @if ($catalogs->isNotEmpty())
    <section class="bg-white py-20 transition-colors duration-300 dark:bg-[#0B111A]">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-10 pb-4 text-center">
                <div
                class="mb-2 inline-flex items-center gap-2 rounded bg-gray-200 px-3 py-1 text-xs font-bold uppercase tracking-wider text-red-600 dark:bg-slate-800 dark:text-red-400">
                <i class="ti ti-file-text"></i> Katalog &amp; Price List
            </div>
            <h2
            class="font-condensed text-3xl font-bold uppercase tracking-tight text-slate-900 dark:text-white md:text-4xl lg:text-5xl">
            Katalog <span class="text-red-500">Resmi</span>
        </h2>
        <p class="mt-3 max-w-2xl mx-auto text-sm text-slate-600 dark:text-slate-400">
            Lihat dan unduh katalog produk lengkap beserta informasi detail unit kami.
        </p>
    </div>
    
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($catalogs as $catalog)
        <x-catalog-card :catalog="$catalog" />
                    @endforeach
                </div>
            </div>
        </section>
        @endif

    {{-- ============ MENGAPA MEMILIH KAMI ============ --}}
    <section id="about" class="relative overflow-hidden bg-white py-20 transition-colors duration-300 dark:bg-[#0B111A]">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto mb-16 max-w-3xl text-center">
                <span class="text-xs font-bold uppercase tracking-widest text-red-600 dark:text-red-500">Keunggulan Utama</span>
                <h2
                    class="font-condensed mt-2 text-4xl font-bold uppercase tracking-tight text-slate-900 dark:text-white sm:text-5xl">
                    Mengapa Memilih <span class="text-red-600">{{ $company->name ? str_replace('PT ', '', $company->name) : 'Kami' }}?</span>
                </h2>
                <p class="mt-3 text-sm text-slate-600 dark:text-slate-400 sm:text-base">
                    Prinsip dasar rancang bangun mesin kami: kemudahan operasional, efisiensi energi tinggi, dan mutu
                    hasil olahan yang sempurna.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                <div
                    class="relative rounded-2xl border border-gray-200 bg-gray-50 p-8 transition-all duration-300 hover:border-red-600/60 dark:border-slate-800 dark:bg-slate-900/90">
                    <div
                        class="mb-6 flex h-14 w-14 items-center justify-center rounded-xl border border-red-600/30 bg-red-600/10 text-2xl text-red-600 dark:text-red-500">
                        <i class="ti ti-adjustments"></i>
                    </div>
                    <span class="font-mono text-xs font-bold uppercase tracking-widest text-red-600 dark:text-red-500">Pilar 01</span>
                    <h3 class="font-condensed mb-3 mt-1 text-2xl font-bold uppercase text-slate-900 dark:text-white">
                        Easy Operation &amp; Care</h3>
                    <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                        Rancangan modular memudahkan pembersihan komponen utama dan sirkulasi udara tanpa teknisi bongkar
                        khusus. Antarmuka kontrol intuitif bagi pemula maupun operator berpengalaman.
                    </p>
                    <div
                        class="mt-6 flex items-center gap-2 border-t border-gray-200 pt-4 text-xs text-slate-600 dark:border-slate-800 dark:text-slate-300">
                        <i class="ti ti-circle-check text-red-500"></i>
                        <span>Perawatan ringkas &amp; suku cadang tersedia</span>
                    </div>
                </div>

                <div
                    class="relative rounded-2xl border border-gray-200 bg-gray-50 p-8 transition-all duration-300 hover:border-red-600/60 dark:border-slate-800 dark:bg-slate-900/90">
                    <div
                        class="mb-6 flex h-14 w-14 items-center justify-center rounded-xl border border-red-600/30 bg-red-600/10 text-2xl text-red-600 dark:text-red-500">
                        <i class="ti ti-leaf"></i>
                    </div>
                    <span class="font-mono text-xs font-bold uppercase tracking-widest text-red-600 dark:text-red-500">Pilar 02</span>
                    <h3 class="font-condensed mb-3 mt-1 text-2xl font-bold uppercase text-slate-900 dark:text-white">
                        Economic &amp; Energy Efficient</h3>
                    <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                        Sistem pembakaran presisi dan insulasi termal tinggi menekan konsumsi energi secara signifikan.
                        Mesin dirancang tahan lama dengan material food grade.
                    </p>
                    <div
                        class="mt-6 flex items-center gap-2 border-t border-gray-200 pt-4 text-xs text-slate-600 dark:border-slate-800 dark:text-slate-300">
                        <i class="ti ti-circle-check text-red-500"></i>
                        <span>Hemat energi &amp; material food grade</span>
                    </div>
                </div>

                <div
                    class="relative rounded-2xl border border-gray-200 bg-gray-50 p-8 transition-all duration-300 hover:border-red-600/60 dark:border-slate-800 dark:bg-slate-900/90">
                    <div
                        class="mb-6 flex h-14 w-14 items-center justify-center rounded-xl border border-red-600/30 bg-red-600/10 text-2xl text-red-600 dark:text-red-500">
                        <i class="ti ti-chart-line"></i>
                    </div>
                    <span class="font-mono text-xs font-bold uppercase tracking-widest text-red-600 dark:text-red-500">Pilar 03</span>
                    <h3 class="font-condensed mb-3 mt-1 text-2xl font-bold uppercase text-slate-900 dark:text-white">
                        Excellent Quality</h3>
                    <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                        Setiap mesin melewati uji fungsi dan kontrol kualitas menyeluruh sebelum dikirim, sehingga mutu
                        hasil olahan konsisten dari batch ke batch.
                    </p>
                    <div
                        class="mt-6 flex items-center gap-2 border-t border-gray-200 pt-4 text-xs text-slate-600 dark:border-slate-800 dark:text-slate-300">
                        <i class="ti ti-circle-check text-red-500"></i>
                        <span>Uji fungsi &amp; kontrol kualitas menyeluruh</span>
                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- ============ LAYANAN & DUKUNGAN ============ --}}
    <section id="services"
        class="border-y border-gray-200 bg-gray-100 py-20 transition-colors duration-300 dark:border-slate-800 dark:bg-[#131B26]">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 items-center gap-12 lg:grid-cols-12">
                <div class="lg:col-span-5">
                    <span class="text-xs font-bold uppercase tracking-widest text-red-600 dark:text-red-500">Layanan Purna Jual</span>
                    <h2
                        class="font-condensed mb-6 mt-2 text-4xl font-bold uppercase tracking-tight text-slate-900 dark:text-white sm:text-5xl">
                        Dukungan Teknis &amp; <br /><span class="text-red-600">Garansi Pabrikan</span>
                    </h2>
                    <p class="mb-6 text-sm leading-relaxed text-slate-600 dark:text-slate-300 sm:text-base">
                        Kami memahami mesin pengolahan adalah jantung usaha Anda. Karena itu kami menjamin dukungan suku
                        cadang dan tim teknisi berpengalaman yang siap membantu di seluruh Indonesia.
                    </p>

                    <div class="space-y-4 text-sm text-slate-700 dark:text-slate-200">
                        <div class="flex items-start gap-3">
                            <div
                                class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-red-600/20 text-red-600 dark:text-red-500">
                                <i class="ti ti-screwdriver text-xs"></i>
                            </div>
                            <div>
                                <strong class="block font-medium text-slate-900 dark:text-white">Instalasi &amp; Pelatihan Onsite</strong>
                                <span class="text-xs text-slate-500 dark:text-slate-400">Pemasangan di lokasi lengkap dengan pelatihan pengoperasian.</span>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div
                                class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-red-600/20 text-red-600 dark:text-red-500">
                                <i class="ti ti-box text-xs"></i>
                            </div>
                            <div>
                                <strong class="block font-medium text-slate-900 dark:text-white">Ketersediaan Suku Cadang Lokal</strong>
                                <span class="text-xs text-slate-500 dark:text-slate-400">Tidak perlu menunggu impor berbulan-bulan; suku cadang selalu sedia.</span>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div
                                class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-red-600/20 text-red-600 dark:text-red-500">
                                <i class="ti ti-shield-check text-xs"></i>
                            </div>
                            <div>
                                <strong class="block font-medium text-slate-900 dark:text-white">Garansi Servis &amp; Konsultasi</strong>
                                <span class="text-xs text-slate-500 dark:text-slate-400">Garansi suku cadang serta asistensi teknis via WhatsApp.</span>
                            </div>
                        </div>
                    </div>

                    @if ($wa)
                        <div class="mt-8">
                            <a href="https://wa.me/{{ $wa }}?text={{ urlencode('Halo, saya ingin konsultasi kebutuhan mesin pengolahan kopi dan kakao.') }}"
                                target="_blank" rel="noopener"
                                class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-red-600/30 transition-all hover:bg-red-700">
                                <i class="ti ti-brand-whatsapp text-lg"></i>
                                <span>Konsultasi Kebutuhan Mesin</span>
                            </a>
                        </div>
                    @endif
                </div>

                <div class="lg:col-span-7">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="h-64 overflow-hidden rounded-xl border border-gray-300 shadow-xl dark:border-slate-700">
                            <img src="{{ $company->aboutUrl() }}" alt="Workshop {{ $company->name }}"
                                class="h-full w-full object-cover transition-transform duration-500 hover:scale-105">
                        </div>
                        <div class="h-64 overflow-hidden rounded-xl border border-gray-300 shadow-xl dark:border-slate-700">
                            <img src="{{ $company->heroUrl() }}" alt="Produksi Mesin {{ $company->name }}"
                                class="h-full w-full object-cover transition-transform duration-500 hover:scale-105">
                        </div>
                        <div class="relative col-span-2 h-52 overflow-hidden rounded-xl border border-gray-300 shadow-xl dark:border-slate-700">
                            <img src="{{ $company->aboutUrl() }}" alt="Workshop dan Engineering {{ $company->name }}"
                                class="h-full w-full object-cover brightness-75 dark:brightness-75">
                            <div
                                class="absolute inset-0 flex items-end bg-gradient-to-t from-gray-900/90 via-gray-900/40 to-transparent p-6">
                                <div>
                                    <p class="font-condensed text-xl font-bold uppercase tracking-wide text-white">
                                        Workshop &amp; Engineering Lab</p>
                                    <p class="text-xs text-gray-200 dark:text-slate-300">Setiap mesin melewati uji fungsi
                                        dan kontrol kualitas sebelum proses pengiriman.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    

        
        {{-- ============ AJAKAN KONTAK ============ --}}
        <section class="bg-gray-50 py-20 transition-colors duration-300 dark:bg-[#0B111A]">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#0B111A] via-[#131B26] to-red-900 px-6 py-14 text-center shadow-xl sm:px-12">
                    <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-red-600/20 blur-3xl"></div>
                    <div class="absolute -bottom-16 -left-16 h-64 w-64 rounded-full bg-red-600/10 blur-3xl"></div>
                    
                    <h2 class="font-condensed text-3xl font-bold uppercase tracking-tight text-white sm:text-4xl">
                        Butuh mesin sesuai kapasitas produksi Anda?
                    </h2>
                    <p class="mx-auto mt-4 max-w-2xl text-sm leading-relaxed text-gray-200">
                        Tim engineering kami siap membantu memilih model mesin, menghitung kapasitas, hingga merancang lini
                        produksi kopi dan kakao yang paling efisien untuk usaha Anda.
                    </p>
                    
                    <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <a href="{{ route('contact') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-red-600/40 transition-all hover:bg-red-700">
                    <i class="ti ti-mail"></i>
                    Konsultasi Gratis
                </a>
                @if ($wa)
                <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener"
                class="inline-flex items-center gap-2 rounded-lg border border-white/40 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                <i class="ti ti-brand-whatsapp"></i>
                WhatsApp Kami
            </a>
            @endif
        </div>
    </div>
</div>
{{-- ============ JANGKAUAN / EKSPOR ============ --}}
@if ($exports)
    <section class="border-b border-gray-200 bg-gray-50 py-16 transition-colors duration-300 dark:border-slate-800/80 dark:bg-[#0B111A]">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8 text-center">
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-500 dark:text-slate-400">
                    @if ($company->founded_year)
                        Terpercaya Sejak {{ $company->founded_year }} di Seluruh Indonesia
                    @endif
                </p>
            </div>
            <div
                class="flex flex-wrap items-center justify-center gap-3 text-xs font-medium text-slate-700 dark:text-slate-300 sm:text-sm">
                <span
                    class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 transition-colors hover:border-red-500/50 dark:border-slate-800 dark:bg-slate-900">
                    <i class="ti ti-map-pin text-red-500"></i>
                    {{ implode(' • ', $exports) }}
                </span>
            </div>
        </div>
    </section>
@endif
</section>

@endsection