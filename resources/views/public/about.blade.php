@extends('layouts.public')

@section('title', 'Tentang Kami — '.$company->name)
@section('meta_description', 'Mengenal '.$company->name.', manufaktur rekayasa mesin pengolahan kopi dan kakao berstandar industri presisi, food grade SUS 304/316, dan efisiensi energi.')

@push('styles')
    <style>
        .profile-card-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2.5rem;
            align-items: start;
            width: 100%;
        }

        @media (min-width: 1024px) {
            .profile-card-grid {
                grid-template-columns: 1.12fr 1fr;
                gap: 3rem;
            }
        }

        .profile-stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            width: 100%;
        }

        @media (max-width: 640px) {
            .profile-stats-grid {
                grid-template-columns: 1fr;
            }
        }

        .pillars-compact-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
            width: 100%;
        }

        @media (min-width: 768px) {
            .pillars-compact-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1.25rem;
            }
        }

        @media (min-width: 1024px) {
            .pillars-compact-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 1.5rem;
            }
        }

        .location-section-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
            width: 100%;
        }

        @media (min-width: 1024px) {
            .location-section-grid {
                grid-template-columns: 1fr 1.25fr;
                gap: 2.25rem;
                align-items: stretch;
            }
        }

        .map-responsive-container {
            width: 100%;
            height: 280px;
            position: relative;
            overflow: hidden;
            border-radius: 1.25rem;
        }

        @media (min-width: 640px) {
            .map-responsive-container {
                height: 340px;
            }
        }

        @media (min-width: 1024px) {
            .map-responsive-container {
                height: 100%;
                min-height: 480px;
            }
        }

        .map-responsive-container iframe {
            width: 100% !important;
            height: 100% !important;
            border: 0 !important;
            position: absolute;
            inset: 0;
        }

        .kontak-lokasi-section {
            padding-top: 50px !important;
            padding-bottom: 70px !important;
            width: 100%;
            position: relative;
        }

        @media (min-width: 640px) {
            .kontak-lokasi-section {
                padding-top: 65px !important;
                padding-bottom: 85px !important;
            }
        }

        @media (min-width: 1024px) {
            .kontak-lokasi-section {
                padding-top: 80px !important;
                padding-bottom: 100px !important;
            }
        }
    </style>
@endpush

@section('content')
    {{-- ============ 1. HERO BANNER ATAS ============ --}}
    <section class="home-hero">
        <div class="hero-bg"></div>
        <div class="home-hero-content">
            <h1 class="text-3xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">
                REKAYASA PRESISI<br>
                <span class="text-zinc-200">MESIN KOPI &amp; KAKAO</span>
            </h1>

            <p class="hero-desc-desktop">
                Mengenal dedikasi <strong>{{ $company->name }}</strong> dalam perancangan dan fabrikasi mesin pengolahan agro berstandar industri modern, higienis Food Grade SUS 304/316, serta efisiensi daya motor untuk mendukung produktivitas perkebunan dan pabrik di seluruh Indonesia.
            </p>

            <p class="hero-desc-mobile">
                Pusat rekayasa dan fabrikasi mesin pengolahan kopi serta kakao berstandar food grade dan efisiensi tinggi produksi {{ $company->name }}.
            </p>

            <div class="hero-actions">
                <a href="#profil-perusahaan" class="btn-learn-more">
                    <i class="fas fa-industry"></i> Profil Perusahaan
                </a>
                <a href="{{ route('contact') }}" class="btn-hero-outline">
                    Hubungi Kami <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            {{-- Micro Trust Badge Strip --}}
            <div class="hero-trust-bar">
            </div>
        </div>
    </section>

    {{-- ============ 2. PROFIL SHOWCASE (SATU CARD UTUH, LEBIH LUAS & PROPORSIONAL) ============ --}}
    <section class="py-16 sm:py-20 lg:py-24 bg-stone-50 dark:bg-zinc-950" id="profil-perusahaan">
        <div class="max-w-[1360px] mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Wrapper Satu Card Utuh --}}
            <div class="bg-white dark:bg-zinc-900 rounded-3xl p-8 sm:p-10 lg:p-14 shadow-xl shadow-stone-200/50 dark:shadow-none">
                {{-- Header Profil di Dalam Card --}}
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 mb-8">
                    <div>
                        <h2 class="text-2xl sm:text-3xl lg:text-4xl xl:text-[40px] font-extrabold text-stone-900 dark:text-white tracking-tight">
                            {{ $company->name }}
                        </h2>
                    </div>

                </div>

                {{-- Grid Konten: Kiri (Narasi) & Kanan (Gambar + 3 Info Metrik di Bawahnya) --}}
                <div class="profile-card-grid">
                    {{-- Sisi Kiri: Narasi Profil & Keunggulan --}}
                    <div class="flex flex-col justify-between h-full">
                        <div>
                            <h3 class="text-lg sm:text-xl font-bold text-stone-900 dark:text-white tracking-tight mb-3.5">
                                Dedikasi Kualitas Dalam Setiap Detil Komponen Mesin
                            </h3>

                            <div class="text-stone-600 dark:text-stone-300 text-sm sm:text-base leading-relaxed space-y-4 mb-8">
                                @if ($company->about)
                                    @foreach (preg_split('/\r\n|\r|\n/', $company->about) as $paragraph)
                                        @if (trim($paragraph) !== '')
                                            <p>{{ trim($paragraph) }}</p>
                                        @endif
                                    @endforeach
                                @else
                                    <p>
                                        <strong>{{ $company->name }}</strong> adalah perusahaan manufaktur spesialis perancangan dan fabrikasi mesin pengolahan komoditas kopi serta kakao berstandar industri presisi.
                                    </p>
                                    <p>
                                        Kami melayani perkebunan, koperasi, pabrik pengolahan, serta UMKM dengan mesin yang disesuaikan terhadap kapasitas produksi, efisiensi konsumsi daya, dan higienitas hasil olahan pangan.
                                    </p>
                                @endif
                            </div>

                            {{-- Fitur Mini Pilar --}}
                            <div class="space-y-3 mb-8">
                                <div class="flex items-center gap-2.5 text-xs sm:text-sm font-medium text-stone-800 dark:text-zinc-200">
                                    <span class="w-5 h-5 rounded-full bg-stone-900 dark:bg-white text-white dark:text-stone-900 flex items-center justify-center text-[10px] shrink-0">
                                        <i class="fas fa-check"></i>
                                    </span>
                                    <span>Material Kontak Stainless Steel SUS 304/316</span>
                                </div>
                                <div class="flex items-center gap-2.5 text-xs sm:text-sm font-medium text-stone-800 dark:text-zinc-200">
                                    <span class="w-5 h-5 rounded-full bg-stone-900 dark:bg-white text-white dark:text-stone-900 flex items-center justify-center text-[10px] shrink-0">
                                        <i class="fas fa-check"></i>
                                    </span>
                                    <span>Rancang Bangun Custom Sesuai Kapasitas &amp; Lahan</span>
                                </div>
                                <div class="flex items-center gap-2.5 text-xs sm:text-sm font-medium text-stone-800 dark:text-zinc-200">
                                    <span class="w-5 h-5 rounded-full bg-stone-900 dark:bg-white text-white dark:text-stone-900 flex items-center justify-center text-[10px] shrink-0">
                                        <i class="fas fa-check"></i>
                                    </span>
                                    <span>Dukungan Suku Cadang &amp; Teknisi Servis Berpengalaman</span>
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="pt-3 flex flex-wrap items-center gap-3.5">
                            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider bg-stone-900 text-white hover:bg-stone-800 dark:bg-white dark:text-stone-900 dark:hover:bg-stone-100 shadow-sm transition">
                                <i class="fas fa-cogs"></i> Jelajahi Lini Mesin
                            </a>
                            <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider text-stone-600 hover:text-stone-900 dark:text-stone-400 dark:hover:text-white transition">
                                <span>Konsultasi Spesifikasi</span> <i class="fas fa-arrow-right text-[11px]"></i>
                            </a>
                        </div>
                    </div>

                    {{-- Sisi Kanan: Gambar Fasilitas + 3 Info Metrik Tepat Di Bawahnya --}}
                    <div class="flex flex-col gap-4">
                        {{-- 1. Gambar Workshop --}}
                        <div class="w-full h-72 sm:h-80 lg:h-96 rounded-2xl overflow-hidden relative shadow-md group bg-stone-900">
                            @if ($company->about_image)
                                <img src="{{ $company->aboutUrl() }}"
                                     alt="Workshop {{ $company->name }}"
                                     class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
                                     onerror="this.src='{{ asset('images/GambarBG.jpg') }}'">
                            @else
                                <img src="{{ asset('images/GambarBG.jpg') }}"
                                     alt="Workshop Manufaktur"
                                     class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105">
                            @endif

                            {{-- Overlay Gradien Halus --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-transparent to-black/20 pointer-events-none"></div>


                            {{-- Floating Spec Tag Pojok Bawah --}}
                            <div class="absolute bottom-3 left-3 right-3 px-3.5 py-2.5 rounded-xl bg-white/95 dark:bg-zinc-900/95 backdrop-blur-md shadow-md flex items-center justify-between text-stone-900 dark:text-white">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-shield-halved text-xs text-stone-600 dark:text-zinc-400"></i>
                                    <span class="text-xs font-bold text-stone-800 dark:text-white">Food Grade SUS 304/316</span>
                                </div>
                                <span class="text-[10px] font-bold text-stone-500 dark:text-zinc-400 bg-stone-100 dark:bg-zinc-800 px-2.5 py-0.5 rounded-full">
                                    QC Teruji
                                </span>
                            </div>
                        </div>

                        {{-- 2. Tiga Info Metrik Tepat Di Bawah Gambar (Sesuai Field Admin) --}}
                        <div class="profile-stats-grid">
                            {{-- Info 1: Tahun Berdiri --}}
                            <div class="p-4 sm:p-5 rounded-2xl bg-stone-50 dark:bg-zinc-800/60 shadow-xs flex flex-col justify-between">
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 dark:text-zinc-500 block mb-0.5">Tahun Berdiri</span>
                                    <div class="text-xl sm:text-2xl font-extrabold text-stone-900 dark:text-white tracking-tight leading-snug mb-1">
                                        {{ $company->founded_year ?: '2010' }}
                                    </div>
                                </div>
                                <p class="text-xs text-stone-500 dark:text-zinc-400 leading-tight">Dedikasi riset &amp; rekayasa manufaktur presisi</p>
                            </div>

                            {{-- Info 2: Jumlah Karyawan --}}
                            <div class="p-4 sm:p-5 rounded-2xl bg-stone-50 dark:bg-zinc-800/60 shadow-xs flex flex-col justify-between">
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 dark:text-zinc-500 block mb-0.5">Jumlah Karyawan</span>
                                    <div class="text-xl sm:text-2xl font-extrabold text-stone-900 dark:text-white tracking-tight leading-snug mb-1">
                                        {{ $company->employees ?: '50+ karyawan' }}
                                    </div>
                                </div>
                                <p class="text-xs text-stone-500 dark:text-zinc-400 leading-tight">Tim rekayasa mekanik, elektrikal, &amp; perakitan</p>
                            </div>

                            {{-- Info 3: Wilayah Pemasaran --}}
                            <div class="p-4 sm:p-5 rounded-2xl bg-stone-50 dark:bg-zinc-800/60 shadow-xs flex flex-col justify-between">
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 dark:text-zinc-500 block mb-0.5">Wilayah Pemasaran</span>
                                    <div class="text-sm sm:text-base font-extrabold text-stone-900 dark:text-white tracking-tight leading-snug mb-1">
                                        {{ $company->export_countries ?: 'Nasional & Asia Tenggara' }}
                                    </div>
                                </div>
                                <p class="text-xs text-stone-500 dark:text-zinc-400 leading-tight">Melayani perkebunan &amp; industri skala nasional &amp; ekspor</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ 3. VISI & MISI PERUSAHAAN (EDITORIAL INSPIRATIF & MINIMALIS) ============ --}}
    @if ($company->vision || !empty($company->missionList()))
        <section class="py-20 lg:py-28 bg-stone-50 dark:bg-zinc-900/40">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                {{-- Visi: Statement Kutipan Besar Sesuai Data Admin --}}
                @if ($company->vision)
                    <div class="text-center max-w-4xl mx-auto mb-20">
                        <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-stone-100 dark:bg-zinc-800 text-stone-700 dark:text-zinc-300 text-xs font-bold uppercase tracking-wider mb-4">
                            <i class="fas fa-bullseye text-[11px]"></i>
                            <span>Visi Perusahaan</span>
                        </div>
                        <div class="relative mt-2">
                            <span class="text-6xl sm:text-8xl text-stone-200 dark:text-zinc-800 font-serif leading-none absolute -top-8 -left-4 select-none pointer-events-none">&ldquo;</span>
                            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-stone-900 dark:text-white leading-snug tracking-tight px-6 sm:px-12">
                                {{ $company->vision }}
                            </h2>
                            <span class="text-6xl sm:text-8xl text-stone-200 dark:text-zinc-800 font-serif leading-none absolute -bottom-12 -right-4 select-none pointer-events-none">&rdquo;</span>
                        </div>
                    </div>
                @endif

                {{-- Misi: Grid Kartu Sesuai Data Admin --}}
                @if (!empty($company->missionList()))
                    <div class="mt-14">
                        <div class="text-center mb-10">
                            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-stone-100 dark:bg-zinc-800 text-stone-700 dark:text-zinc-300 text-xs font-bold uppercase tracking-wider mb-2">
                                <i class="fas fa-list-check text-[11px]"></i>
                                <span>Misi Perusahaan</span>
                            </div>
                            <h3 class="text-2xl sm:text-3xl font-extrabold text-stone-900 dark:text-white tracking-tight mt-1">
                                Landasan Aksi &amp; Komitmen Layanan
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 max-w-5xl mx-auto">
                            @foreach ($company->missionList() as $index => $mission)
                                <div class="p-6 sm:p-7 rounded-2xl bg-white dark:bg-zinc-900 shadow-sm hover:shadow-md transition flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-stone-100 dark:bg-zinc-800 text-stone-900 dark:text-white flex items-center justify-center font-extrabold text-sm shrink-0 shadow-xs">
                                        0{{ $index + 1 }}
                                    </div>
                                    <p class="text-stone-700 dark:text-stone-300 text-sm sm:text-[15px] leading-relaxed">
                                        {{ $mission }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- ============ 4. KOMITMEN MUTU & KEUNGGULAN REKAYASA (4 PILAR COMPACT BENTO) ============ --}}
    <section class="py-12 sm:py-16 lg:py-20 bg-white dark:bg-zinc-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-8 sm:mb-12">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-stone-900 dark:text-white tracking-tight mt-1">
                    Keunggulan Rekayasa Presisi
                </h2>
                <p class="text-stone-500 dark:text-zinc-400 text-xs sm:text-sm mt-2">
                    Komponen dan lini mesin dirancang berstandar higienis pangan, akurasi tinggi, serta efisiensi energi.
                </p>
            </div>

            <div class="pillars-compact-grid">
                {{-- Pilar 1 --}}
                <div class="p-3.5 sm:p-5 lg:p-6 rounded-2xl bg-stone-50 dark:bg-zinc-900/60 transition hover:-translate-y-1 hover:shadow-md shadow-xs flex flex-col justify-between text-center">
                    <div>
                        <div class="w-8 h-8 sm:w-10 sm:h-10 mx-auto rounded-xl bg-stone-900 text-white dark:bg-white dark:text-stone-900 flex items-center justify-center text-xs sm:text-base mb-2.5 sm:mb-3.5 shadow-sm">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="text-xs sm:text-sm lg:text-base font-bold text-stone-900 dark:text-white mb-1 leading-snug">
                            Food Grade SUS 304/316
                        </h3>
                        <p class="text-stone-600 dark:text-stone-400 text-[11px] sm:text-xs leading-snug sm:leading-relaxed">
                            Material kontak stainless steel anti-karat standar higienis industri pangan.
                        </p>
                    </div>
                </div>

                {{-- Pilar 2 --}}
                <div class="p-3.5 sm:p-5 lg:p-6 rounded-2xl bg-stone-50 dark:bg-zinc-900/60 transition hover:-translate-y-1 hover:shadow-md shadow-xs flex flex-col justify-between text-center">
                    <div>
                        <div class="w-8 h-8 sm:w-10 sm:h-10 mx-auto rounded-xl bg-stone-900 text-white dark:bg-white dark:text-stone-900 flex items-center justify-center text-xs sm:text-base mb-2.5 sm:mb-3.5 shadow-sm">
                            <i class="fas fa-drafting-compass"></i>
                        </div>
                        <h3 class="text-xs sm:text-sm lg:text-base font-bold text-stone-900 dark:text-white mb-1 leading-snug">
                            Desain CAD/CAM Presisi
                        </h3>
                        <p class="text-stone-600 dark:text-stone-400 text-[11px] sm:text-xs leading-snug sm:leading-relaxed">
                            Mekanikal akurasi tinggi, minim getaran, suara halus, dan durabilitas panjang.
                        </p>
                    </div>
                </div>

                {{-- Pilar 3 --}}
                <div class="p-3.5 sm:p-5 lg:p-6 rounded-2xl bg-stone-50 dark:bg-zinc-900/60 transition hover:-translate-y-1 hover:shadow-md shadow-xs flex flex-col justify-between text-center">
                    <div>
                        <div class="w-8 h-8 sm:w-10 sm:h-10 mx-auto rounded-xl bg-stone-900 text-white dark:bg-white dark:text-stone-900 flex items-center justify-center text-xs sm:text-base mb-2.5 sm:mb-3.5 shadow-sm">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h3 class="text-xs sm:text-sm lg:text-base font-bold text-stone-900 dark:text-white mb-1 leading-snug">
                            Efisiensi Konsumsi Daya
                        </h3>
                        <p class="text-stone-600 dark:text-stone-400 text-[11px] sm:text-xs leading-snug sm:leading-relaxed">
                            Rasio transmisi dan motor dinamo optimal menekan biaya listrik operasional.
                        </p>
                    </div>
                </div>

                {{-- Pilar 4 --}}
                <div class="p-3.5 sm:p-5 lg:p-6 rounded-2xl bg-stone-50 dark:bg-zinc-900/60 transition hover:-translate-y-1 hover:shadow-md shadow-xs flex flex-col justify-between text-center">
                    <div>
                        <div class="w-8 h-8 sm:w-10 sm:h-10 mx-auto rounded-xl bg-stone-900 text-white dark:bg-white dark:text-stone-900 flex items-center justify-center text-xs sm:text-base mb-2.5 sm:mb-3.5 shadow-sm">
                            <i class="fas fa-wrench"></i>
                        </div>
                        <h3 class="text-xs sm:text-sm lg:text-base font-bold text-stone-900 dark:text-white mb-1 leading-snug">
                            Garansi &amp; Layanan Servis
                        </h3>
                        <p class="text-stone-600 dark:text-stone-400 text-[11px] sm:text-xs leading-snug sm:leading-relaxed">
                            Teknisi berpengalaman, ketersediaan suku cadang, dan uji fungsi lapangan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ 5. KONTAK & LOKASI WORKSHOP BESERTA PETA ============ --}}
    @php
        $contactWa = preg_replace('/[^0-9]/', '', (string) $company->whatsapp);
        $contactPhone = preg_replace('/[^0-9+]/', '', (string) $company->phone);
        $fullAddress = trim(($company->address ? $company->address . ', ' : '') . ($company->city ?? ''), ', ');
        $mapsSearchUrl = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($fullAddress ?: 'Malang, Jawa Timur');
    @endphp
    <section class="kontak-lokasi-section bg-stone-50 dark:bg-zinc-900/40" id="kontak-lokasi" style="padding-top: 50px; padding-bottom: 70px;">
        <div class="max-w-[1360px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-12 sm:mb-16">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-stone-900 dark:text-white tracking-tight">
                    Kunjungi Workshop &amp; Fabrikasi Kami
                </h2>
                <p class="text-stone-500 dark:text-zinc-400 text-xs sm:text-sm mt-2 max-w-xl mx-auto">
                    Kunjungi fasilitas {{ $company->name }} untuk konsultasi teknis spesifikasi, inspeksi unit mesin, atau uji fungsi operasional.
                </p>
            </div>

            <div class="location-section-grid">
                {{-- Sisi Kiri: Detail Kontak, Jam Kunjungan, & Navigasi --}}
                <div class="flex flex-col gap-4 sm:gap-5 justify-between">
                    {{-- 1. Card Alamat & Workshop --}}
                    <div class="p-5 sm:p-7 rounded-3xl bg-white dark:bg-zinc-900 shadow-sm hover:shadow-md transition">
                        <div class="flex items-start gap-4">
                            <div class="flex-1">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 dark:text-zinc-500 block mb-1">
                                    Alamat Kantor &amp; Workshop
                                </span>
                                <h3 class="text-base sm:text-lg font-bold text-stone-900 dark:text-white leading-snug">
                                    {{ $company->address ?: 'Jl. Industri Raya No. 88, Karangploso' }}
                                </h3>
                                <p class="text-xs sm:text-sm text-stone-500 dark:text-zinc-400 mt-1">
                                    {{ $company->city ?: 'Kabupaten Malang, Jawa Timur' }}
                                </p>

                                <div class="mt-4 pt-3 flex flex-wrap items-center gap-2">
                                    <a href="{{ $mapsSearchUrl }}" target="_blank" rel="noopener noreferrer"
                                       class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-bold bg-stone-900 text-white hover:bg-stone-800 dark:bg-white dark:text-stone-900 dark:hover:bg-stone-100 shadow-sm transition">
                                        <i class="fas fa-diamond-turn-right text-[11px]"></i>
                                        <span>Petunjuk Arah Rute</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Card Komunikasi (Telepon, WhatsApp, Email) --}}
                    <div class="p-5 sm:p-7 rounded-3xl bg-white dark:bg-zinc-900 shadow-sm hover:shadow-md transition">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 dark:text-zinc-500 block mb-3">
                            Saluran Komunikasi Langsung
                        </span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            {{-- WhatsApp --}}
                            @if ($contactWa)
                                <a href="https://wa.me/{{ $contactWa }}?text={{ urlencode('Halo Manufactur Dynamic Indonesia, saya ingin bertanya tentang mesin dan workshop.') }}"
                                   target="_blank" rel="noopener noreferrer"
                                   class="p-3.5 rounded-2xl bg-stone-50 dark:bg-zinc-800/60 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 transition group flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-sm shrink-0 shadow-xs">
                                        <i class="fab fa-whatsapp"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-[10px] font-bold text-stone-400 dark:text-zinc-500 uppercase tracking-wider block">WhatsApp</span>
                                        <span class="text-xs sm:text-sm font-bold text-stone-900 dark:text-white truncate block group-hover:text-emerald-600 transition">
                                            +{{ $contactWa }}
                                        </span>
                                    </div>
                                </a>
                            @endif

                            {{-- Telepon --}}
                            @if ($company->phone)
                                <a href="tel:{{ $contactPhone }}"
                                   class="p-3.5 rounded-2xl bg-stone-50 dark:bg-zinc-800/60 hover:bg-stone-100 dark:hover:bg-zinc-800 transition group flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-stone-900 dark:bg-white text-white dark:text-stone-900 flex items-center justify-center text-sm shrink-0 shadow-xs">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-[10px] font-bold text-stone-400 dark:text-zinc-500 uppercase tracking-wider block">Telepon Kantor</span>
                                        <span class="text-xs sm:text-sm font-bold text-stone-900 dark:text-white truncate block">
                                            {{ $company->phone }}
                                        </span>
                                    </div>
                                </a>
                            @endif

                            {{-- Email --}}
                            @if ($company->email)
                                <a href="mailto:{{ $company->email }}"
                                   class="p-3.5 rounded-2xl bg-stone-50 dark:bg-zinc-800/60 hover:bg-stone-100 dark:hover:bg-zinc-800 transition group flex items-center gap-3 sm:col-span-2">
                                    <div class="w-9 h-9 rounded-xl bg-stone-900 dark:bg-white text-white dark:text-stone-900 flex items-center justify-center text-sm shrink-0 shadow-xs">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-[10px] font-bold text-stone-400 dark:text-zinc-500 uppercase tracking-wider block">Email Resmi</span>
                                        <span class="text-xs sm:text-sm font-bold text-stone-900 dark:text-white truncate block">
                                            {{ $company->email }}
                                        </span>
                                    </div>
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- 3. Card Jam Operasional & Layanan --}}
                    <div class="p-5 sm:p-7 rounded-3xl bg-white dark:bg-zinc-900 shadow-sm hover:shadow-md transition">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-stone-100 dark:bg-zinc-800 text-stone-900 dark:text-white flex items-center justify-center text-sm shrink-0 shadow-xs">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 dark:text-zinc-500 block">Jam Operasional Workshop</span>
                                    <p class="text-xs sm:text-sm font-bold text-stone-900 dark:text-white mt-0.5">
                                        Senin &ndash; Sabtu : 08.00 &ndash; 17.00 WIB
                                    </p>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-[11px] font-bold shrink-0">
                                Buka
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Sisi Kanan: Peta Google Maps Interaktif (Lebar & Tinggi di Laptop/Web) --}}
                <div class="rounded-3xl bg-white dark:bg-zinc-900 p-3 sm:p-4 shadow-sm hover:shadow-md transition flex flex-col">
                    <div class="flex items-center justify-between px-3 py-2 mb-2">
                        <div class="inline-flex items-center gap-2 text-xs font-bold text-stone-800 dark:text-zinc-200">
                            <span>Peta Lokasi Interaktif</span>
                        </div>
                    </div>

                    <div class="map-responsive-container shadow-xs">
                        @if ($company->map_embed)
                            {!! $company->map_embed !!}
                        @else
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126438.33878193836!2d112.5617424!3d-7.9786395!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd6282206340f29%3A0x3027a76e352bea0!2sMalang%2C%20Kota%20Malang%2C%20Jawa%20Timur!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid"
                                    allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
