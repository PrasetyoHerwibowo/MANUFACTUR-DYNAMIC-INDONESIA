@extends('layouts.public')

@section('title', 'Tentang Kami — '.$company->name)
@section('meta_description', 'Profil perusahaan, komitmen mutu, fasilitas workshop manufaktur, dan lini mesin pengolahan kopi serta kakao produksi '.$company->name.'.')

@section('content')

    {{-- ============ ABOUT HERO ============ --}}
    <section class="home-hero about-hero">
        <div class="hero-bg" style="background-image: url('{{ asset('images/GambarBG_GIF.gif') }}'), url('{{ asset('images/GambarBG.jpg') }}'); background-size: cover; background-position: center;"></div>
        <div class="home-hero-content about-hero-content">
            <h1 class="text-3xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">
                PT MANUFACTUR DYNAMIC INDONESIA
            </h1>

            <p class="mx-auto mt-2 max-w-3xl leading-relaxed text-zinc-200">
                Pusat Rekayasa &amp; Manufaktur Mesin Pengolahan Kopi serta Kakao Presisi Tinggi Berstandar Food Grade SUS 304/316 — Menghadirkan Solusi Fabrikasi Andal, Efisiensi Energi Tinggi, dan Kapasitas Terukur untuk Kebutuhan Industri Agro Modern.
            </p>

            <div class="hero-actions">
                <a href="#profil" class="btn-learn-more">
                    <i class="fas fa-industry"></i> Jelajahi Profil Pabrik
                </a>
                <a href="{{ route('contact') }}" class="btn-hero-outline">
                    Konsultasi Spesifikasi <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            {{-- Trust Bar --}}
            <div class="hero-trust-bar">
                <div class="trust-item"><i class="fas fa-check-circle"></i><span>ISO 9001:2015 Mutu</span></div>
                <div class="trust-item"><i class="fas fa-shield-halved"></i><span>Food Grade SUS 304/316</span></div>
                <div class="trust-item"><i class="fas fa-bolt"></i><span>Efisiensi Daya Motor</span></div>
                <div class="trust-item"><i class="fas fa-wrench"></i><span>Garansi &amp; Layanan Purna Jual</span></div>
            </div>
        </div>
    </section>

    {{-- ============ PROFIL & WORKSHOP (CARD-BASED INDUSTRIAL REVAMP) ============ --}}
    <section class="py-16 sm:py-24 bg-zinc-50/70 dark:bg-zinc-950 transition-colors" id="profil">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Section Title & Intro --}}
            <div class="max-w-3xl mb-12">
                <div class="inline-flex items-center gap-2 rounded-full border border-zinc-200 bg-white px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-zinc-700 shadow-xs dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-300 mb-3">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Profil &amp; Dedikasi Manufaktur
                </div>
                <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-zinc-950 dark:text-white">
                    Pusat Fabrikasi Mesin Pengolahan Presisi Tinggi
                </h2>
                <p class="mt-3 text-sm sm:text-base leading-relaxed text-zinc-600 dark:text-zinc-400">
                    Menghubungkan rekayasa mesin modern dengan kebutuhan nyata para pelaku industri pengolahan kopi dan kakao di seluruh Nusantara.
                </p>
            </div>

            {{-- Main Content Grid: Story + Visual Workshop --}}
            <div class="grid gap-6 lg:grid-cols-2 mb-12">

                {{-- Left: Company Profile Story Card --}}
                <div class="rounded-3xl border border-zinc-200/80 bg-gradient-to-br from-white via-white to-zinc-50/30 p-8 shadow-lg dark:border-zinc-800 dark:from-zinc-900 dark:via-zinc-900 dark:to-zinc-800/30 transition-all duration-300 hover:shadow-xl hover:border-zinc-300 dark:hover:border-zinc-700">
                    <div class="flex items-start gap-4 mb-6">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-zinc-900 to-zinc-700 text-white shadow-lg dark:from-white dark:to-zinc-200 dark:text-zinc-950">
                            <i class="fas fa-industry text-xl"></i>
                        </div>
                        <div>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400 mb-2">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Established Company
                            </span>
                            <h3 class="text-xl sm:text-2xl font-extrabold text-zinc-950 dark:text-white leading-tight">
                                {{ $company->name }}
                            </h3>
                        </div>
                    </div>

                    <div class="prose prose-sm prose-zinc dark:prose-invert max-w-none space-y-3.5 text-zinc-600 dark:text-zinc-300 leading-relaxed">
                        @if ($company->about)
                            @foreach (preg_split('/\r\n|\r|\n/', $company->about) as $paragraph)
                                @if (trim($paragraph) !== '')
                                    <p class="text-sm">{{ trim($paragraph) }}</p>
                                @endif
                            @endforeach
                        @else
                            <p class="text-sm">
                                <strong class="text-zinc-900 dark:text-white">{{ $company->name }}</strong> adalah perusahaan rekayasa manufaktur yang berfokus pada perancangan, fabrikasi presisi, dan perakitan lini mesin pengolahan kopi serta kakao. Produk kami dipercaya oleh perkebunan, koperasi, pabrik industri pengolahan, dan UMKM di berbagai wilayah nusantara serta pasar ekspor.
                            </p>
                            <p class="text-sm">
                                Setiap unit mesin dirancang dengan mengutamakan standar higienitas food grade (SUS 304/316), durabilitas mekanis operasional tinggi, efisiensi konsumsi daya listrik, dan kemudahan servis purna jual.
                            </p>
                        @endif
                    </div>

                    {{-- Core Strengths Pills --}}
                    <div class="mt-6 pt-6 border-t border-zinc-200/80 dark:border-zinc-700/80 flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-2 rounded-xl bg-white border border-zinc-200 px-3.5 py-2 text-xs font-bold text-zinc-800 shadow-sm dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-200">
                            <i class="fas fa-shield-halved text-emerald-600 dark:text-emerald-400"></i> SUS 304/316
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-xl bg-white border border-zinc-200 px-3.5 py-2 text-xs font-bold text-zinc-800 shadow-sm dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-200">
                            <i class="fas fa-crosshairs text-emerald-600 dark:text-emerald-400"></i> CNC Precision
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-xl bg-white border border-zinc-200 px-3.5 py-2 text-xs font-bold text-zinc-800 shadow-sm dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-200">
                            <i class="fas fa-tools text-emerald-600 dark:text-emerald-400"></i> On-Site Support
                        </span>
                    </div>
                </div>

                {{-- Right: Workshop Facility Visual Showcase --}}
                <div class="rounded-3xl border border-zinc-200/80 bg-white p-4 shadow-lg dark:border-zinc-800 dark:bg-zinc-900 transition-all duration-300 hover:shadow-xl">
                    <div class="relative overflow-hidden rounded-2xl aspect-[4/3] group">
                        <img src="{{ $company->about_image ? $company->aboutUrl() : asset('images/GambarBG.jpg') }}"
                             alt="Workshop Facility {{ $company->name }}"
                             class="h-full w-full object-cover transition duration-700 ease-out group-hover:scale-110"
                             onerror="this.src='{{ asset('images/GambarBG.jpg') }}'">

                        {{-- Gradient Overlays --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-zinc-950/95 via-zinc-950/30 to-transparent"></div>
                        <div class="absolute inset-0 bg-gradient-to-br from-zinc-950/20 via-transparent to-transparent"></div>

                        {{-- Top Status Indicators --}}
                        <div class="absolute top-4 left-4 right-4 flex items-center justify-between z-10">
                            <span class="inline-flex items-center gap-2 rounded-full bg-zinc-900/90 px-4 py-2 text-xs font-bold uppercase tracking-wide text-white backdrop-blur-xl border border-white/20 shadow-lg">
                                <span class="relative flex h-2.5 w-2.5">
                                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                                </span>
                                Operational
                            </span>
                            <span class="rounded-full bg-white/90 backdrop-blur-sm px-3 py-1.5 text-[11px] font-bold text-zinc-900 shadow-lg">
                                Full Capacity
                            </span>
                        </div>

                        {{-- Bottom Facility Info Card --}}
                        <div class="absolute bottom-4 left-4 right-4 z-10">
                            <div class="rounded-2xl border border-white/20 bg-zinc-900/90 backdrop-blur-xl p-5 shadow-2xl">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-white/20 to-white/10 text-white shadow-lg">
                                        <i class="fas fa-warehouse text-lg"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-extrabold uppercase tracking-wide text-white">Manufacturing Facility</p>
                                        <p class="text-xs text-zinc-300 flex items-center gap-1.5 mt-0.5">
                                            <i class="fas fa-map-marker-alt text-[10px] text-emerald-400"></i>
                                            {{ $company->city ?: 'Malang, Jawa Timur' }}
                                        </p>
                                    </div>
                                    <div class="hidden sm:flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-500/20 text-emerald-400">
                                        <i class="fas fa-check-circle text-base"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Facility Capabilities Grid --}}
                    <div class="grid grid-cols-2 gap-3 mt-4">
                        <div class="rounded-xl bg-gradient-to-br from-zinc-50 to-zinc-100/50 dark:from-zinc-800 dark:to-zinc-800/50 p-4 border border-zinc-200/50 dark:border-zinc-700/50 transition hover:border-zinc-300 dark:hover:border-zinc-600">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-zinc-900 dark:bg-white text-white dark:text-zinc-900">
                                    <i class="fas fa-cogs text-xs"></i>
                                </div>
                                <p class="text-xs font-extrabold text-zinc-900 dark:text-white">Assembly Line</p>
                            </div>
                            <p class="text-[10px] text-zinc-500 dark:text-zinc-400">Functional Testing & Calibration</p>
                        </div>
                        <div class="rounded-xl bg-gradient-to-br from-zinc-50 to-zinc-100/50 dark:from-zinc-800 dark:to-zinc-800/50 p-4 border border-zinc-200/50 dark:border-zinc-700/50 transition hover:border-zinc-300 dark:hover:border-zinc-600">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-zinc-900 dark:bg-white text-white dark:text-zinc-900">
                                    <i class="fas fa-microscope text-xs"></i>
                                </div>
                                <p class="text-xs font-extrabold text-zinc-900 dark:text-white">Quality Control</p>
                            </div>
                            <p class="text-[10px] text-zinc-500 dark:text-zinc-400">Precision Standards QC</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4 Stat Metric Cards Grid --}}
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 mb-10">
                <div class="rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-xs transition hover:border-zinc-900 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-white">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Didirikan</span>
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200">
                            <i class="fas fa-calendar-check text-xs"></i>
                        </div>
                    </div>
                    <p class="text-2xl sm:text-3xl font-extrabold text-zinc-950 dark:text-white">
                        {{ $company->founded_year ?: '2018' }}
                    </p>
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Tahun Pengalaman Industri</p>
                </div>

                <div class="rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-xs transition hover:border-zinc-900 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-white">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Tenaga Ahli</span>
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200">
                            <i class="fas fa-users-gear text-xs"></i>
                        </div>
                    </div>
                    <p class="text-2xl sm:text-3xl font-extrabold text-zinc-950 dark:text-white">
                        {{ $company->employees ?: '50+' }}
                    </p>
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Engineering &amp; Perakitan</p>
                </div>

                <div class="rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-xs transition hover:border-zinc-900 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-white">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Pasar Distribusi</span>
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200">
                            <i class="fas fa-globe-asia text-xs"></i>
                        </div>
                    </div>
                    <p class="text-2xl sm:text-3xl font-extrabold text-zinc-950 dark:text-white">
                        {{ !empty($company->export_countries) ? count(explode(',', $company->export_countries)).' Wilayah' : 'Nasional' }}
                    </p>
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Domestik &amp; Ekspor</p>
                </div>

                <div class="rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-xs transition hover:border-zinc-900 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-white">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Lokasi Workshop</span>
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200">
                            <i class="fas fa-map-pin text-xs"></i>
                        </div>
                    </div>
                    <p class="text-xl sm:text-2xl font-extrabold text-zinc-950 dark:text-white truncate" title="{{ $company->city ?: 'Malang, Jawa Timur' }}">
                        {{ $company->city ?: 'Malang, Jatim' }}
                    </p>
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Pabrik &amp; Layanan Servis</p>
                </div>
            </div>

            {{-- 3 Value Feature Cards --}}
            <div class="grid gap-6 md:grid-cols-3">
                <div class="rounded-2xl border border-zinc-200/80 bg-white p-6 shadow-xs transition hover:border-zinc-950 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-white">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 shadow-xs mb-4">
                        <i class="fas fa-microchip text-base"></i>
                    </div>
                    <h4 class="text-base font-extrabold text-zinc-950 dark:text-white">Desain Berbasis Kapasitas Kustom</h4>
                    <p class="mt-2 text-xs sm:text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
                        Dimensi, daya motor dinamo, dan volume hopper dapat disesuaikan langsung dengan skala perkebunan, koperasi, atau industri pabrik Anda.
                    </p>
                </div>

                <div class="rounded-2xl border border-zinc-200/80 bg-white p-6 shadow-xs transition hover:border-zinc-950 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-white">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 shadow-xs mb-4">
                        <i class="fas fa-award text-base"></i>
                    </div>
                    <h4 class="text-base font-extrabold text-zinc-950 dark:text-white">Standar Sanitasi Pangan SUS 304</h4>
                    <p class="mt-2 text-xs sm:text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
                        Bahan kontak produk anti-kontaminasi logam dan higienis, memastikan hasil olahan kopi dan bubuk kakao memenuhi standar ekspor internasional.
                    </p>
                </div>

                <div class="rounded-2xl border border-zinc-200/80 bg-white p-6 shadow-xs transition hover:border-zinc-950 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-white">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 shadow-xs mb-4">
                        <i class="fas fa-handshake-angle text-base"></i>
                    </div>
                    <h4 class="text-base font-extrabold text-zinc-950 dark:text-white">Dukungan Commissioning On-Site</h4>
                    <p class="mt-2 text-xs sm:text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
                        Teknisi berpengalaman kami mendampingi perakitan mesin langsung di lokasi pabrik Anda serta memberikan pelatihan operasional operator.
                    </p>
                </div>
            </div>

        </div>
    </section>

    {{-- ============ VISI & MISI ============ --}}
    @if ($company->vision || !empty($company->missionList()))
        <section class="py-16 sm:py-20 bg-zinc-50 dark:bg-zinc-900/40 border-y border-zinc-200/80 dark:border-zinc-800/80 transition-colors" id="vision">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-12">
                    <span class="text-xs font-bold uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">Kompas Organisasi</span>
                    <h2 class="mt-2 text-2xl font-extrabold tracking-tight text-zinc-950 dark:text-white sm:text-3xl">
                        Visi &amp; Misi Strategis
                    </h2>
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                        Prinsip dasar yang mengarahkan setiap rancang bangun mesin dan pelayanan kepada mitra industri kami.
                    </p>
                </div>

                <div class="grid gap-8 lg:grid-cols-2">
                    {{-- Kartu Visi --}}
                    @if ($company->vision)
                        <div class="relative flex flex-col justify-between rounded-3xl border border-zinc-200/80 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 transition hover:shadow-md">
                            <div>
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 shadow-sm">
                                        <i class="fas fa-compass text-lg"></i>
                                    </div>
                                    <div>
                                        <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Arah Masa Depan</span>
                                        <h3 class="text-xl font-extrabold text-zinc-950 dark:text-white">Visi Perusahaan</h3>
                                    </div>
                                </div>
                                <p class="text-sm sm:text-base leading-relaxed text-zinc-600 dark:text-zinc-300">
                                    {{ $company->vision }}
                                </p>
                            </div>
                            <div class="mt-8 pt-6 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400 font-medium">
                                <span>Target Keunggulan Nasional &amp; Global</span>
                                <i class="fas fa-arrow-trend-up text-zinc-900 dark:text-white"></i>
                            </div>
                        </div>
                    @endif

                    {{-- Kartu Misi --}}
                    @if (!empty($company->missionList()))
                        <div class="relative flex flex-col justify-between rounded-3xl border border-zinc-200/80 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 transition hover:shadow-md">
                            <div>
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 shadow-sm">
                                        <i class="fas fa-bullseye text-lg"></i>
                                    </div>
                                    <div>
                                        <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Langkah Operasional</span>
                                        <h3 class="text-xl font-extrabold text-zinc-950 dark:text-white">Misi Perusahaan</h3>
                                    </div>
                                </div>
                                <ul class="space-y-4">
                                    @foreach ($company->missionList() as $index => $mission)
                                        <li class="flex items-start gap-3.5 text-sm text-zinc-600 dark:text-zinc-300">
                                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-zinc-900 text-[11px] font-bold text-white dark:bg-zinc-100 dark:text-zinc-900 mt-0.5">
                                                {{ $index + 1 }}
                                            </span>
                                            <span class="leading-relaxed">{{ $mission }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="mt-8 pt-6 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400 font-medium">
                                <span>Komitmen Eksekusi Mutu Berkelanjutan</span>
                                <i class="fas fa-check-double text-zinc-900 dark:text-white"></i>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- ============ PILAR KEUNGGULAN REKAYASA ============ --}}
    <section class="py-16 sm:py-20 bg-white dark:bg-zinc-950 transition-colors" id="values">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-xs font-bold uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">Standar Engineering</span>
                <h2 class="mt-2 text-2xl font-extrabold tracking-tight text-zinc-950 dark:text-white sm:text-3xl">
                    4 Pilar Mutu Fabrikasi Kami
                </h2>
                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                    Standar ketat yang diterapkan pada setiap tahap pembuatan mesin demi performa mesin yang handal dan tahan lama.
                </p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                {{-- Pilar 1 --}}
                <div class="rounded-2xl border border-zinc-200/80 bg-zinc-50/50 p-6 transition duration-200 hover:-translate-y-1 hover:border-zinc-900 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900/50 dark:hover:border-white">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-zinc-900 text-white dark:bg-white dark:text-zinc-950 shadow-sm mb-5">
                        <i class="fas fa-shield-halved text-lg"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-zinc-950 dark:text-white">Food Grade SUS 304/316</h3>
                    <p class="mt-2 text-xs sm:text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
                        Kontak produk menggunakan stainless steel higienis anti-korosi standar industri pangan dan ekspor internasional.
                    </p>
                </div>

                {{-- Pilar 2 --}}
                <div class="rounded-2xl border border-zinc-200/80 bg-zinc-50/50 p-6 transition duration-200 hover:-translate-y-1 hover:border-zinc-900 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900/50 dark:hover:border-white">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-zinc-900 text-white dark:bg-white dark:text-zinc-950 shadow-sm mb-5">
                        <i class="fas fa-crosshairs text-lg"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-zinc-950 dark:text-white">Fabrikasi CNC &amp; Laser Presisi</h3>
                    <p class="mt-2 text-xs sm:text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
                        Toleransi mekanikal presisi meminimalkan getaran mesin dan memastikan keawetan komponen dalam penggunaan kontinu 24/7.
                    </p>
                </div>

                {{-- Pilar 3 --}}
                <div class="rounded-2xl border border-zinc-200/80 bg-zinc-50/50 p-6 transition duration-200 hover:-translate-y-1 hover:border-zinc-900 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900/50 dark:hover:border-white">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-zinc-900 text-white dark:bg-white dark:text-zinc-950 shadow-sm mb-5">
                        <i class="fas fa-bolt text-lg"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-zinc-950 dark:text-white">Efisiensi Konsumsi Daya</h3>
                    <p class="mt-2 text-xs sm:text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
                        Perancangan transmisi daya dan motor dinamo efisien guna menekan biaya operasional listrik pabrik pengolahan Anda.
                    </p>
                </div>

                {{-- Pilar 4 --}}
                <div class="rounded-2xl border border-zinc-200/80 bg-zinc-50/50 p-6 transition duration-200 hover:-translate-y-1 hover:border-zinc-900 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900/50 dark:hover:border-white">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-zinc-900 text-white dark:bg-white dark:text-zinc-950 shadow-sm mb-5">
                        <i class="fas fa-headset text-lg"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-zinc-950 dark:text-white">Garansi &amp; Layanan Purna Jual</h3>
                    <p class="mt-2 text-xs sm:text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
                        Dukungan teknisi langsung, garansi motor dinamo 6 bulan, servis 1 tahun, dan ketersediaan suku cadang terjamin.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ ALUR 7 TAHAP PRODUKSI ============ --}}
    <section class="py-16 sm:py-20 bg-zinc-50 dark:bg-zinc-900/40 border-y border-zinc-200/80 dark:border-zinc-800/80 transition-colors" id="journey">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-14">
                <span class="text-xs font-bold uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">Pipeline Manufaktur</span>
                <h2 class="mt-2 text-2xl font-extrabold tracking-tight text-zinc-950 dark:text-white sm:text-3xl">
                    7 Tahapan Terstruktur Produksi Mesin
                </h2>
                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                    Setiap pesanan mesin dikerjakan secara sistematis melalui kontrol kualitas ketat dari desain hingga serah terima unit di lokasi pabrik Anda.
                </p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7">
                @php
                    $pipelineSteps = [
                        ['num' => '01', 'title' => 'Verifikasi Desain', 'desc' => 'Penyusunan spesifikasi kapasitas & dimensi disesuaikan dengan kebutuhan pabrik.', 'icon' => 'fa-drafting-compass'],
                        ['num' => '02', 'title' => 'Persiapan Bahan', 'desc' => 'Inspeksi mutu plat stainless steel SUS 304/316 dan komponen transmisi utama.', 'icon' => 'fa-layer-group'],
                        ['num' => '03', 'title' => 'Fabrikasi Plat', 'desc' => 'Pemotongan laser CNC, bending presisi, dan pengelasan TIG berstandar sanitasi.', 'icon' => 'fa-industry'],
                        ['num' => '04', 'title' => 'Perakitan Mekanik', 'desc' => 'Instalasi motor dinamo, gearbox, poros bearing, dan sistem kelistrikan / panel PLC.', 'icon' => 'fa-cogs'],
                        ['num' => '05', 'title' => 'Quality Control', 'desc' => 'Uji kalibrasi sensor, kestabilan rpm, pengecekan kebocoran, dan simulasi beban kerja.', 'icon' => 'fa-check-circle'],
                        ['num' => '06', 'title' => 'Pengiriman Unit', 'desc' => 'Packing kayu proteksi tinggi dan pengiriman ke lokasi pabrik mitra di seluruh Indonesia.', 'icon' => 'fa-truck-fast'],
                        ['num' => '07', 'title' => 'Instalasi & Servis', 'desc' => 'Commissioning di tempat, pelatihan operator pabrik, dan aktivasi garansi resmi.', 'icon' => 'fa-wrench'],
                    ];
                @endphp

                @foreach ($pipelineSteps as $step)
                    <div class="relative flex flex-col justify-between rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-xs transition hover:border-zinc-900 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-white">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="font-mono text-xs font-black tracking-widest text-zinc-400 dark:text-zinc-500">{{ $step['num'] }}</span>
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200">
                                    <i class="fas {{ $step['icon'] }} text-xs"></i>
                                </div>
                            </div>
                            <h4 class="text-sm font-extrabold text-zinc-950 dark:text-white">{{ $step['title'] }}</h4>
                            <p class="mt-2 text-xs leading-relaxed text-zinc-500 dark:text-zinc-400">{{ $step['desc'] }}</p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800/80">
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                                <i class="fas fa-check text-[9px]"></i> Terverifikasi
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ KOMITMEN SUSTAINABILITY ============ --}}
    <section class="py-16 sm:py-20 bg-zinc-950 text-white relative overflow-hidden" id="sustainability">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-zinc-800/40 via-zinc-950/80 to-zinc-950"></div>
        <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto text-center">
                <span class="inline-flex items-center gap-2 rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3.5 py-1 text-xs font-bold text-emerald-400 mb-4">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                    Going Green To Be Hero
                </span>
                <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl text-white">
                    Komitmen Manufaktur Hijau &amp; Ramah Lingkungan
                </h2>
                <p class="mt-4 text-sm sm:text-base leading-relaxed text-zinc-400">
                    <strong>{{ $company->name }}</strong> berkomitmen pada praktik rekayasa manufaktur yang berwawasan lingkungan dengan target efisiensi energi terbarukan, minimasi limbah fabrikasi, dan pemilihan material ramah lingkungan.
                </p>
            </div>

            <div class="mt-12 grid gap-6 sm:grid-cols-3 max-w-5xl mx-auto">
                <div class="rounded-2xl border border-white/10 bg-white/5 p-6 backdrop-blur-sm transition hover:bg-white/10">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/20 text-emerald-400 mb-4">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-white">Efisiensi Konsumsi Listrik</h3>
                    <p class="mt-2 text-xs sm:text-sm leading-relaxed text-zinc-400">
                        Optimalisasi transmisi gearbox dan motor berdaya efisien menekan konsumsi kilowatt per ton hasil olahan.
                    </p>
                </div>

                <div class="rounded-2xl border border-white/10 bg-white/5 p-6 backdrop-blur-sm transition hover:bg-white/10">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/20 text-emerald-400 mb-4">
                        <i class="fas fa-recycle"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-white">Target Zero-Waste Fabrikasi</h3>
                    <p class="mt-2 text-xs sm:text-sm leading-relaxed text-zinc-400">
                        Manajemen pemotongan plat presisi berbasis CAD/CAM meminimalkan sisa scrap logam dan limbah proses.
                    </p>
                </div>

                <div class="rounded-2xl border border-white/10 bg-white/5 p-6 backdrop-blur-sm transition hover:bg-white/10">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/20 text-emerald-400 mb-4">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-white">Eco Stainless 100% Recyclable</h3>
                    <p class="mt-2 text-xs sm:text-sm leading-relaxed text-zinc-400">
                        Seluruh material stainless steel yang kami gunakan bebas bahan beracun dan 100% dapat didaur ulang.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ KATEGORI PRODUK YANG DIKERJAKAN ============ --}}
    @if ($categories->isNotEmpty())
        <section class="py-16 sm:py-20 bg-white dark:bg-zinc-950 transition-colors">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between mb-10 gap-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">Portofolio Mesin</span>
                        <h2 class="mt-2 text-2xl font-extrabold tracking-tight text-zinc-950 dark:text-white sm:text-3xl">
                            Jenis Lini Mesin yang Kami Produksi
                        </h2>
                    </div>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-zinc-900 dark:text-white hover:underline">
                        Lihat Seluruh Katalog <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($categories as $category)
                        @php
                            $isCocoa = str_contains(strtolower($category->name), 'kakao') || str_contains(strtolower($category->name), 'cokelat');
                            $isCoffee = str_contains(strtolower($category->name), 'kopi') || str_contains(strtolower($category->name), 'coffee');
                        @endphp
                        <a href="{{ route('products.index', ['kategori' => $category->slug]) }}"
                           class="group relative flex flex-col justify-between rounded-2xl border border-zinc-200/80 bg-zinc-50/50 p-5 transition duration-200 hover:-translate-y-1 hover:border-zinc-950 hover:bg-white hover:shadow-lg dark:border-zinc-800 dark:bg-zinc-900/60 dark:hover:border-white dark:hover:bg-zinc-900">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 text-sm shadow-xs transition group-hover:scale-105">
                                        @if ($isCocoa)
                                            <i class="fas fa-seedling"></i>
                                        @elseif ($isCoffee)
                                            <i class="fas fa-mug-hot"></i>
                                        @else
                                            <i class="fas fa-cogs"></i>
                                        @endif
                                    </div>
                                    <span class="rounded-full bg-zinc-200/70 px-2.5 py-0.5 text-[11px] font-bold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                                        {{ $category->machines_count }} unit
                                    </span>
                                </div>
                                <h4 class="text-sm sm:text-base font-extrabold text-zinc-950 dark:text-white group-hover:text-zinc-900 dark:group-hover:text-white">
                                    {{ $category->name }}
                                </h4>
                                @if ($category->description)
                                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400 line-clamp-2">
                                        {{ $category->description }}
                                    </p>
                                @endif
                            </div>
                            <div class="mt-4 flex items-center gap-1.5 text-xs font-semibold text-zinc-950 dark:text-white pt-2 border-t border-zinc-200/60 dark:border-zinc-800/60">
                                <span>Buka Katalog</span>
                                <i class="fas fa-arrow-right text-[10px] transition-transform group-hover:translate-x-1"></i>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ CALL TO ACTION ============ --}}
    @php
        $wa = isset($company) && $company->whatsapp ? preg_replace('/[^0-9]/', '', (string) $company->whatsapp) : null;
    @endphp

    <section class="py-16 sm:py-20 bg-zinc-50 dark:bg-zinc-900/40 border-t border-zinc-200/80 dark:border-zinc-800/80 transition-colors">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="consult-box">
                <h2>Ingin Bekerja Sama atau Konsultasi Spesifikasi?</h2>
                <p>
                    Tim engineering <strong>{{ $company->name }}</strong> siap membantu merancang spesifikasi lini pengolahan kopi &amp; kakao yang paling presisi, hemat energi, dan efisien untuk kapasitas pabrik Anda.
                </p>
                <div class="consult-actions">
                    <a href="{{ route('contact') }}" class="btn-consult-primary">
                        <i class="fas fa-paper-plane"></i> Hubungi Kami Sekarang
                    </a>
                    @if ($wa)
                        <a href="https://wa.me/{{ $wa }}?text={{ urlencode('Halo Manufactur Dynamic, saya ingin konsultasi kebutuhan mesin pengolahan kopi / kakao.') }}"
                           target="_blank" rel="noopener" class="btn-consult-secondary">
                            <i class="fab fa-whatsapp text-emerald-400"></i> WhatsApp Engineering
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

@endsection
