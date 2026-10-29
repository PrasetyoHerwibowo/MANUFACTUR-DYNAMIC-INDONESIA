@extends('layouts.public')

@section('title', $company->name.' — '.$company->tagline)

@section('content')
    @php
        $wa = preg_replace('/[^0-9]/', '', (string) $company->whatsapp);
        $exports = array_values(array_filter(array_map('trim', explode(',', (string) $company->export_countries)), fn ($v) => $v !== ''));
    @endphp

    {{-- ============ HOME HERO ============ --}}
    <section class="home-hero">
        <div class="hero-bg"></div>
        <div class="home-hero-content">
            <h1 class="text-3xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">WE BUILD<br>THE FUTURE OF MACHINERY</h1>
            <p class="hero-desc-desktop">
                {{ $company->about
                    ? strip_tags($company->about)
                    : 'PT Manufactur Dynamic Indonesia adalah perusahaan manufaktur yang bergerak di bidang perancangan, pembuatan, dan pemasangan mesin pengolahan kopi serta kakao. Kami melayani perkebunan, koperasi, pabrik pengolahan, dan UMKM dengan mesin yang disesuaikan terhadap kapasitas produksi serta standar mutu food grade.' }}
            </p>
            <p class="hero-desc-mobile">
                {{ $company->about
                    ? \Illuminate\Support\Str::limit(strip_tags($company->about), 125, '...')
                    : 'Manufaktur mesin pengolahan kakao dan kopi presisi dengan standar mutu food grade dan efisiensi energi tinggi.' }}
            </p>
            <div class="hero-actions">
                <a href="{{ route('products.index') }}" class="btn-learn-more">
                    <i class="fas fa-cogs"></i> Jelajahi Produk
                </a>
                <a href="{{ route('about') }}" class="btn-hero-outline">
                    Learn More <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            {{-- Micro Trust Badge Strip --}}
            <div class="hero-trust-bar">
                
            </div>
        </div>

    </section>

    {{-- ============ ZIGZAG: KAKAO ============ --}}
    <section class="zigzag-section" id="cocoa-series">
        <div class="zigzag-inner">
            <div class="zigzag-image">
                @if ($cocoaCategory?->imageUrl())
                    <div class="w-full h-full min-h-[260px] rounded-2xl overflow-hidden shadow-lg border border-stone-200 dark:border-zinc-800">
                        <img src="{{ $cocoaCategory->imageUrl() }}" alt="{{ $cocoaCategory->name }}" class="w-full h-full object-cover min-h-[260px]">
                    </div>
                @else
                    <div class="w-full h-full min-h-[260px] rounded-2xl bg-gradient-to-br from-stone-900 via-stone-800 to-zinc-950 flex flex-col items-center justify-center text-white/30 p-8 text-center border border-stone-800 shadow-inner">
                        <i class="fas fa-seedling text-5xl mb-3 opacity-25"></i>
                        <span class="text-xs uppercase font-bold tracking-widest text-stone-400">
                            {{ $cocoaCategory->name ?? 'Mesin Pengolahan Kakao' }}
                        </span>
                    </div>
                @endif
            </div>
            <div class="zigzag-content">
                <h2>{{ $cocoaCategory->name ?? 'Mesin Pengolahan Kakao' }}</h2>
                @if ($cocoaCategory?->tagline)
                    <p class="text-xs font-semibold uppercase tracking-wider text-stone-500 dark:text-stone-400 -mt-2 mb-2">
                        {{ $cocoaCategory->tagline }}
                    </p>
                @endif
                <p>
                    {{ $cocoaCategory?->description ?? 'Lini mesin pengolahan kakao terintegrasi dari tahap fermentasi, pengeringan, roasting presisi, hingga pemisahan kulit dan penggilingan halus. Dirancang untuk menghasilkan cocoa mass, butter, dan powder berkualitas ekspor.' }}
                </p>
                <div class="zigzag-features">
                    <span class="feat-pill"><i class="fas fa-bolt"></i> Motor dinamo dan sparepart electric 6 bulan</span>
                    <span class="feat-pill"><i class="fas fa-tools"></i> Servis gratis 1 tahun (Akomodasi pp, penginapan, konsumsi ditanggung pembeli)</span>
                    <span class="feat-pill"><i class="fas fa-stamp"></i> Logo custom perusahaan</span>
                </div>
                <div class="zigzag-actions">
                    <a href="{{ route('products.cocoa') }}" class="btn-zigzag">
                        Lihat Koleksi Kakao <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ ZIGZAG: KOPI ============ --}}
    <section class="zigzag-section alt" id="coffee-series">
        <div class="zigzag-inner reverse">
            <div class="zigzag-image">
                @if ($coffeeCategory?->imageUrl())
                    <div class="w-full h-full min-h-[260px] rounded-2xl overflow-hidden shadow-lg border border-stone-200 dark:border-zinc-800">
                        <img src="{{ $coffeeCategory->imageUrl() }}" alt="{{ $coffeeCategory->name }}" class="w-full h-full object-cover min-h-[260px]">
                    </div>
                @else
                    <div class="w-full h-full min-h-[260px] rounded-2xl bg-gradient-to-br from-stone-900 via-stone-800 to-zinc-950 flex flex-col items-center justify-center text-white/30 p-8 text-center border border-stone-800 shadow-inner">
                        <i class="fas fa-coffee text-5xl mb-3 opacity-25"></i>
                        <span class="text-xs uppercase font-bold tracking-widest text-stone-400">
                            {{ $coffeeCategory->name ?? 'Mesin Pengolahan Kopi' }}
                        </span>
                    </div>
                @endif
            </div>
            <div class="zigzag-content">
                <h2>{{ $coffeeCategory->name ?? 'Mesin Pengolahan Kopi' }}</h2>
                @if ($coffeeCategory?->tagline)
                    <p class="text-xs font-semibold uppercase tracking-wider text-stone-500 dark:text-stone-400 -mt-2 mb-2">
                        {{ $coffeeCategory->tagline }}
                    </p>
                @endif
                <p>
                    {{ $coffeeCategory?->description ?? 'Rangkaian mesin pengolahan kopi dari sortasi biji, pencucian, roasting otomatis kendali profil PLC, hingga penggilingan presisi untuk menjaga kestabilan aroma dan konsistensi cita rasa kopi specialty.' }}
                </p>
                <div class="zigzag-features">
                    <span class="feat-pill"><i class="fas fa-bolt"></i> Motor dinamo dan sparepart electric 6 bulan</span>
                    <span class="feat-pill"><i class="fas fa-tools"></i> Servis gratis 1 tahun (Akomodasi pp, penginapan, konsumsi ditanggung pembeli)</span>
                    <span class="feat-pill"><i class="fas fa-stamp"></i> Logo custom perusahaan</span>
                </div>
                <div class="zigzag-actions">
                    <a href="{{ route('products.coffee') }}" class="btn-zigzag">
                        Lihat Koleksi Kopi <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ SUSTAINABILITY HOME ============ --}}
    <section class="sustainability-home" id="sustainability">
        <div class="sus-bg"></div>
        <div class="sus-home-inner">
            <h2>Going Green To Be Hero</h2>
            <p>
                <strong>Manufactur Dynamic</strong> berkomitmen pada solusi manufaktur ramah lingkungan dengan target minimasi residu produksi, perancangan mesin beremisi rendah, dan pemanfaatan energi yang bertanggung jawab.
            </p>
            <div class="sus-highlights">
                <div class="sus-hi-card">
                    <i class="fas fa-bolt"></i>
                    <div>
                        <strong>Efisiensi Energi</strong>
                        <span>Konsumsi daya motor optimal</span>
                    </div>
                </div>
                <div class="sus-hi-card">
                    <i class="fas fa-recycle"></i>
                    <div>
                        <strong>Zero-Waste Target</strong>
                        <span>Minimasi residu &amp; limbah</span>
                    </div>
                </div>
                <div class="sus-hi-card">
                    <i class="fas fa-shield-alt"></i>
                    <div>
                        <strong>Eco Stainless</strong>
                        <span>100% dapat didaur ulang</span>
                    </div>
                </div>
            </div>
            <div>
                <a href="{{ route('about') }}#sustainability" class="btn-learn-more-orange">
                    Pelajari Komitmen Hijau
                </a>
            </div>
        </div>
    </section>

    {{-- ============ STATS ============ --}}
    <section class="stats-section">
        <div class="stats-inner">
            <div class="stat-card">
                <span class="stat-prefix">Katalog Produk</span>
                <div class="stat-number">
                    {{ !empty($stats['kategori']) && $stats['kategori'] > 0 ? $stats['kategori'].'+' : '150+' }}
                </div>
                <p class="stat-label">Model &amp; Kategori Mesin</p>
            </div>
            <div class="stat-card">
                <span class="stat-prefix">Distribusi Unit</span>
                <div class="stat-number">20rb+</div>
                <p class="stat-label">Unit Mesin Terdistribusi</p>
            </div>
            <div class="stat-card">
                <span class="stat-prefix">Jaringan Anggota &amp; Mitra Pendukung</span>
                <div class="stat-number">10+</div>
                <p class="stat-label">Mitra &amp; Jaringan Industri</p>
            </div>
        </div>
    </section>

    {{-- ============ AJAKAN KONSULTASI / KONTAK ============ --}}
    <section class="zigzag-section alt pt-0 pb-12">
        <div class="max-w-5xl mx-auto px-4">
            <div class="consult-box">
                <h2>Butuh Mesin Sesuai Kapasitas Produksi Anda?</h2>
                <p>
                    Tim engineering Manufactur Dynamic siap membantu merancang spesifikasi lini pengolahan yang paling presisi, hemat energi, dan efisien untuk kapasitas pabrik Anda.
                </p>
                <div class="consult-actions">
                    <a href="{{ route('contact') }}" class="btn-consult-primary">
                        <i class="fas fa-envelope"></i> Hubungi Konsultasi
                    </a>
                    @if ($wa)
                        <a href="https://wa.me/{{ $wa }}?text={{ urlencode('Halo, saya ingin konsultasi kebutuhan mesin pengolahan.') }}"
                           target="_blank" rel="noopener" class="btn-consult-secondary">
                            <i class="fab fa-whatsapp"></i> WhatsApp Engineering
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ============ EKSPOR / WILAYAH PASAR ============ --}}
    @if ($exports)
        <section class="border-t border-stone-200/80 bg-white py-8 dark:border-zinc-800 dark:bg-zinc-950">
            <div class="max-w-6xl mx-auto px-4 text-center">
                <div class="inline-flex items-center gap-2 mb-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <p class="text-[11px] font-bold uppercase tracking-widest text-zinc-500 dark:text-zinc-400">
                        Jangkauan Distribusi &amp; Ekspor Internasional
                    </p>
                </div>
                <div class="flex flex-wrap items-center justify-center gap-2">
                    @foreach ($exports as $country)
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-stone-200/80 bg-stone-50/80 px-3 py-1 text-[11px] font-medium text-zinc-700 shadow-xs dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-300">
                            <i class="fas fa-location-dot text-zinc-900 dark:text-white text-[10px]"></i> {{ $country }}
                        </span>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

@endsection
