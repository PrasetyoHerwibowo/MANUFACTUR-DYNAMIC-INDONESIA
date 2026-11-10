@extends('layouts.public')

@section('title', 'MD-Coffee Series — Lini Mesin Pengolahan Kopi & Roastery | '.$company->name)
@section('meta_description', 'Katalog lini mesin pengolahan kopi presisi MD-Coffee Series produksi '.$company->name.'. Mesin pulper basah, washer, huller kering, roaster kopi profil otomatis, dan grinder specialty.')

@section('content')
    @php
        $waNum = preg_replace('/[^0-9]/', '', (string) ($company->whatsapp ?? ''));
    @endphp

    {{-- ============ COFFEE HERO (MONOCHROME INDUSTRIAL ROASTERY) ============ --}}
    <section class="coffee-hero-section">
        <div class="coffee-hero-bg"></div>
        <div class="coffee-grid-pattern"></div>
        <div class="coffee-hero-overlay"></div>

        <div class="coffee-hero-content">

            <h1 class="coffee-hero-title">
                Teknologi Presisi <span>Pengolahan Kopi</span>
            </h1>

            <p class="coffee-hero-desc">
                Solusi rekayasa mesin hulu hingga hilir: dari pemrosesan ceri basah (wet mill), pengupasan kulit tanduk (dry mill), hingga roasting profile kendali presisi untuk roastery specialty.
            </p>

            {{-- Technical Spec Highlights --}}
            <div class="coffee-spec-strip">
                <div class="strip-item">
                    <i class="fas fa-gauge-high"></i>
                    <div>
                        <strong>Inverter Controlled</strong>
                        <span>Airflow &amp; Drum Speed</span>
                    </div>
                </div>
                <div class="strip-div"></div>
                <div class="strip-item">
                    <i class="fas fa-award"></i>
                    <div>
                        <strong>Food Grade 304</strong>
                        <span>Standard Industri Pangan</span>
                    </div>
                </div>
            </div>

            <div class="coffee-hero-actions">
                <a href="#katalog-kopi" class="coffee-btn-explore">
                    <i class="fas fa-sliders"></i> Jelajahi Model Mesin Kopi
                </a>
            </div>
        </div>

        <a href="#alur-kopi" class="coffee-scroll-indicator" aria-label="Gulir ke konten">
            <span>Explore</span>
            <i class="fas fa-chevron-down"></i>
        </a>
    </section>

    {{-- ============ DUAL TRACK ECOSYSTEM: HULU VS HILIR ============ --}}
    <section class="coffee-tracks-section" id="alur-kopi">
        <div class="coffee-container">
            <div class="tracks-header">
                <h2>2 Jalur Terintegrasi Industri Kopi</h2>
                <p>Kami merancang mesin pengolahan kopi untuk dua fase utama: persiapan pascapanen perkebunan dan produksi sangrai komersial.</p>
            </div>

            <div class="tracks-grid">
                {{-- Track 1: Hulu --}}
                <div class="track-card">
                    <div class="track-badge">JALUR 1 • PASCAPANEN (WET &amp; DRY MILL)</div>
                    <div class="track-icon"><i class="fas fa-seedling"></i></div>
                    <h3>Pemrosesan Green Bean Hulu</h3>
                    <p>Menjaga keseragaman biji, minimasi defect, dan menjaga potensi cita rasa asli sejak tahap buah segar.</p>
                    <ul class="track-list">
                        <li><i class="fas fa-check"></i> Mesin Pulper Kopi Basah (Kapasitas 800 kg/jam)</li>
                        <li><i class="fas fa-check"></i> Mesin Pencuci &amp; Separator Biji Mengapung</li>
                        <li><i class="fas fa-check"></i> Mesin Huller Kering Pengupas Kulit Tanduk</li>
                    </ul>
                </div>

                {{-- Track 2: Hilir --}}
                <div class="track-card highlight">
                    <div class="track-badge">JALUR 2 • ROASTERY &amp; PACKAGING</div>
                    <div class="track-icon"><i class="fas fa-fire-flame-curved"></i></div>
                    <h3>Sangrai &amp; Penepungan Specialty</h3>
                    <p>Menghadirkan profil sangrai presisi, pengembangan aroma optimal, serta kehalusan bubuk siap seduh.</p>
                    <ul class="track-list">
                        <li><i class="fas fa-check"></i> Mesin Sangrai Kopi (Roaster drum 50 kg/batch)</li>
                        <li><i class="fas fa-check"></i> Mesin Penepung / Grinder Disk Mill Presisi</li>
                        <li><i class="fas fa-check"></i> Mesin Pengemas Vakum Kedap Udara</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ KATALOG MESIN KOPI ============ --}}
    <section class="coffee-catalog-section" id="katalog-kopi">
        <div class="coffee-container">

            {{-- Filter & Search Bar --}}
            <div class="coffee-filter-bar">
                <div class="coffee-cat-pills">
                    <a href="{{ route('products.coffee') }}#katalog-kopi"
                       class="coffee-pill {{ empty($categorySlug) ? 'active' : '' }}">
                        <i class="fas fa-layer-group"></i> Semua Kategori ({{ $categories->sum('machines_count') }})
                    </a>
                    @foreach ($categories as $cat)
                        <a href="{{ route('products.coffee', array_filter(['kategori' => $cat->slug, 'q' => $keyword])) }}#katalog-kopi"
                           class="coffee-pill {{ $categorySlug === $cat->slug ? 'active' : '' }}">
                            {{ $cat->name }} ({{ $cat->machines_count }})
                        </a>
                    @endforeach
                </div>

                <form method="GET" action="{{ route('products.coffee') }}" class="coffee-search-form">
                    @if ($categorySlug)
                        <input type="hidden" name="kategori" value="{{ $categorySlug }}">
                    @endif
                    <div class="search-wrap">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" name="q" value="{{ $keyword }}"
                               placeholder="Cari model mesin kopi..." class="search-input-field">
                        @if ($keyword)
                            <a href="{{ route('products.coffee', $categorySlug ? ['kategori' => $categorySlug] : []) }}#katalog-kopi"
                               class="search-clear-link" title="Reset pencarian">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                    <button type="submit" class="search-action-btn">
                        Cari
                    </button>
                </form>
            </div>

            @if ($keyword || $activeCategory)
                <div class="coffee-filter-banner">
                    <div class="banner-text">
                        <i class="fas fa-filter text-zinc-500"></i>
                        <span>
                            Filter aktif:
                            @if ($keyword) kata kunci <strong>"{{ $keyword }}"</strong> @endif
                            @if ($activeCategory) kategori <strong>{{ $activeCategory->name }}</strong> @endif
                            — {{ $machines->total() }} model mesin kopi siap dipesan.
                        </span>
                    </div>
                    <a href="{{ route('products.coffee') }}#katalog-kopi" class="banner-reset">
                        Reset <i class="fas fa-rotate-left"></i>
                    </a>
                </div>
            @endif

            {{-- Grid Mesin Kopi --}}
            @if ($machines->isNotEmpty())
                <div class="coffee-cards-grid">
                    @foreach ($machines as $machine)
                        <div class="coffee-machine-card group">
                            {{-- Visual Image Clean (Tanpa Overlay Badge) --}}
                            <div class="card-media" onclick="openSpecModal('{{ $machine->slug }}')" style="cursor: pointer;">
                                <img src="{{ $machine->photo }}" alt="{{ $machine->name }}" loading="lazy"
                                     onerror="this.src='https://images.unsplash.com/photo-1442512595331-e89e73853f31?w=800&auto=format&fit=crop'">
                            </div>

                            <div class="card-details">
                                {{-- Badge Model & Kategori di Bawah Gambar --}}
                                <div class="card-meta-badges">
                                    @if ($machine->model_code)
                                        <span class="badge-code">{{ $machine->model_code }}</span>
                                    @endif
                                    @if ($machine->category)
                                        <span class="badge-category">{{ $machine->category->name }}</span>
                                    @endif
                                </div>

                                <div class="card-header-row">
                                    <h3 class="card-title">
                                        <button type="button" class="card-title-btn" onclick="openSpecModal('{{ $machine->slug }}')">
                                            {{ $machine->name }}
                                        </button>
                                    </h3>
                                </div>

                                <p class="card-desc">
                                    {{ $machine->function ?: ($machine->short_description ?: \Illuminate\Support\Str::limit(strip_tags((string) $machine->description), 120)) }}
                                </p>

                                {{-- Ringkasan Spesifikasi & Estimasi Harga --}}
                                <div class="card-specs-summary">
                                    <div class="summary-specs-list">
                                        <div class="spec-row-item">
                                            <span class="s-label"><i class="fas fa-gauge"></i> Kapasitas</span>
                                            <span class="s-val">{{ $machine->capacity ?: 'Kustom Industri' }}</span>
                                        </div>
                                        <div class="spec-row-item">
                                            <span class="s-label"><i class="fas fa-bolt"></i> Daya Penggerak</span>
                                            <span class="s-val">{{ $machine->power ?: 'Sesuai Kapasitas' }}</span>
                                        </div>
                                    </div>

                                    <div class="summary-price-box">
                                        <div class="price-info-col">
                                            <span class="p-title">Estimasi Harga</span>
                                            <span class="p-sub">Made-to-Order</span>
                                        </div>
                                        <button type="button" class="price-tag-badge" onclick="openSpecModal('{{ $machine->slug }}')">
                                            Lihat Detail
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination Mesin Kopi (Selalu Muncul) --}}
                <div class="catalog-pagination-wrap">
                    {{ $machines->links('components.pagination') }}
                </div>
            @else
                <div class="coffee-empty-state">
                    <div class="empty-icon-circle">
                        <i class="fas fa-mug-hot text-zinc-500"></i>
                    </div>
                    <h3>Model Mesin Kopi Tidak Ditemukan</h3>
                    <p>Tidak ada mesin yang sesuai dengan pencarian Anda. Coba kata kunci lain atau reset filter.</p>
                    <a href="{{ route('products.coffee') }}#katalog-kopi" class="coffee-btn-explore mt-4">
                        Lihat Seluruh Mesin Kopi
                    </a>
                </div>
            @endif

        </div>
    </section>

    {{-- ============ ENGINEERING HIGHLIGHTS (PILLARS) ============ --}}


    {{-- ============ SWITCHER TO COCOA SERIES ============ --}}
    <section class="coffee-switcher-section">
        <div class="coffee-container">
            <div class="coffee-switch-card">
                <div class="switch-info">
                    <span class="switch-tag">Lini Mesin Lainnya</span>
                    <h3>Memerlukan Mesin Pengolahan Kakao &amp; Cokelat?</h3>
                    <p>Jelajahi lini <strong>MD-Cocoa Series</strong> dari box fermentasi aerasi, pengering higienis, hingga ball mill penghalus pasta cokelat murni.</p>
                </div>
                <div class="switch-action">
                    <a href="{{ route('products.cocoa') }}" class="btn-to-cocoa">
                        <span>Buka MD-Cocoa Series</span>
                        <i class="fas fa-seedling"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ POPUP MODAL SPESIFIKASI LENGKAP (MESIN KOPI) ============ --}}
    @foreach ($machines as $machine)
        <div class="spec-modal-backdrop" id="modal-{{ $machine->slug }}" style="display: none;" onclick="handleBackdropClick(event, '{{ $machine->slug }}')">
            <div class="spec-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="modal-title-{{ $machine->slug }}">
                <div class="modal-header">
                    <div class="modal-header-meta">
                        <div class="modal-badges">
                            @if ($machine->model_code)
                                <span class="badge-code">{{ $machine->model_code }}</span>
                            @endif
                            @if ($machine->category)
                                <span class="badge-category">{{ $machine->category->name }}</span>
                            @endif
                        </div>
                        <h2 class="modal-title" id="modal-title-{{ $machine->slug }}">{{ $machine->name }}</h2>
                    </div>
                    <button type="button" class="modal-close-btn" onclick="closeSpecModal('{{ $machine->slug }}')" aria-label="Tutup popup">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    {{-- Media Foto Mesin --}}
                    <div class="modal-media-wrap">
                        <img src="{{ $machine->photo }}" alt="{{ $machine->name }}" loading="lazy"
                             onerror="this.src='https://images.unsplash.com/photo-1442512595331-e89e73853f31?w=800&auto=format&fit=crop'">
                    </div>

                    {{-- Deskripsi Fungsi --}}
                    <div class="modal-section">
                        <h4 class="modal-sec-title"><i class="fas fa-circle-info"></i> Deskripsi &amp; Fungsi Kerja</h4>
                        <div class="modal-desc-box">
                            <p class="modal-desc-text">
                                {{ $machine->function ?: ($machine->short_description ?: strip_tags((string) $machine->description)) }}
                            </p>
                            @if ($machine->description && $machine->description !== $machine->function)
                                <div class="modal-desc-full">
                                    {!! nl2br(e($machine->description)) !!}
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Tabel Spesifikasi Teknis --}}
                    <div class="modal-section">
                        <h4 class="modal-sec-title"><i class="fas fa-sliders"></i> Parameter Spesifikasi Mesin</h4>
                        <div class="modal-specs-grid">
                            <div class="modal-spec-item">
                                <span class="ms-label"><i class="fas fa-gauge-high"></i> Kapasitas Produksi</span>
                                <span class="ms-val">{{ $machine->capacity ?: 'Kustomisasi Pabrik' }}</span>
                            </div>
                            <div class="modal-spec-item">
                                <span class="ms-label"><i class="fas fa-bolt"></i> Daya Penggerak / Listrik</span>
                                <span class="ms-val">{{ $machine->power ?: 'Sesuai Spesifikasi' }}</span>
                            </div>
                            <div class="modal-spec-item">
                                <span class="ms-label"><i class="fas fa-ruler-combined"></i> Dimensi Mesin</span>
                                <span class="ms-val">{{ $machine->dimension ?: 'Disesuaikan dengan Lokasi Pabrik' }}</span>
                            </div>
                            <div class="modal-spec-item">
                                <span class="ms-label"><i class="fas fa-weight-hanging"></i> Bobot Mesin</span>
                                <span class="ms-val">{{ $machine->weight ?: 'Disesuaikan Material' }}</span>
                            </div>
                            <div class="modal-spec-item full-width">
                                <span class="ms-label"><i class="fas fa-shield-halved"></i> Material Kontak Produk</span>
                                <span class="ms-val">Stainless Steel SUS 304 Food Grade / Drum Cast Iron Double-Wall</span>
                            </div>
                            @if ($machine->specifications)
                                <div class="modal-spec-item full-width">
                                    <span class="ms-label"><i class="fas fa-list-check"></i> Catatan Spesifikasi Tambahan</span>
                                    <span class="ms-val">{!! nl2br(e($machine->specifications)) !!}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Estimasi & Garansi --}}
                    <div class="modal-warranty-box">
                        <div class="w-item">
                            <i class="fas fa-certificate"></i>
                            <div>
                                <strong>Garansi Servis 1 Tahun</strong>
                                <span>Garansi dinamo &amp; elektrik 6 bulan, sparepart terjamin</span>
                            </div>
                        </div>
                        <div class="w-item">
                            <i class="fas fa-handshake"></i>
                            <div>
                                <strong>Estimasi Harga: Made-to-Order</strong>
                                <span>Penyesuaian kapasitas produksi &amp; kebutuhan roastery</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-modal-cancel" onclick="closeSpecModal('{{ $machine->slug }}')">
                        Tutup
                    </button>
                    @if ($waNum)
                        <a href="https://wa.me/{{ $waNum }}?text={{ urlencode('Halo Manufactur Dynamic Indonesia, saya ingin konsultasi penawaran dan spesifikasi mesin kopi: '.$machine->name.($machine->model_code ? ' ('.$machine->model_code.')' : '')) }}"
                           target="_blank" rel="noopener" class="btn-modal-wa">
                            <i class="fab fa-whatsapp"></i>
                            <span>Hubungi Sales Engineering via WhatsApp</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endforeach

    {{-- ============ STYLES FOR COFFEE SERIES (BLACK & WHITE / NO BLACK STROKES) ============ --}}
    <style>
        .coffee-hero-section {
            position: relative;
            min-height: 100vh;
            min-height: 100dvh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 130px 24px 80px;
            background: #000000;
            overflow: hidden;
        }

        .coffee-hero-bg {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            background-image: url('/images/GambarBG_GIF.gif'), url('/images/GambarBG.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            filter: grayscale(100%) contrast(115%);
            opacity: 0.38;
            z-index: 0;
        }

        .coffee-grid-pattern {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px);
            background-size: 28px 28px;
            z-index: 1;
            opacity: 0.6;
        }

        .coffee-hero-overlay {
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at center, rgba(0, 0, 0, 0.40) 0%, rgba(9, 9, 11, 0.92) 100%),
                        linear-gradient(180deg, rgba(0, 0, 0, 0.65) 0%, rgba(9, 9, 11, 0.98) 100%);
            z-index: 1;
        }

        .coffee-hero-content {
            position: relative;
            z-index: 2;
            max-width: 920px;
            margin: 0 auto;
        }

        .coffee-scroll-indicator {
            position: absolute;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            text-decoration: none;
            color: rgba(255, 255, 255, 0.7);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: all 0.25s ease;
        }

        .coffee-scroll-indicator:hover {
            color: #ffffff;
            transform: translateX(-50%) translateY(3px);
        }

        .coffee-scroll-indicator i {
            animation: bounce-subtle 1.8s infinite ease-in-out;
        }

        @keyframes bounce-subtle {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(4px); }
        }

        .coffee-tech-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 7px 18px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.16);
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.4px;
            margin-bottom: 22px;
            backdrop-filter: blur(10px);
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #ffffff;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.8);
            animation: pulse-ring 2s infinite ease-in-out;
        }

        @keyframes pulse-ring {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.85); }
        }

        .coffee-hero-title {
            font-size: clamp(34px, 5.2vw, 56px);
            font-weight: 800;
            line-height: 1.16;
            color: #ffffff;
            letter-spacing: -1px;
            margin-bottom: 18px;
        }

        .coffee-hero-title span {
            color: #a1a1aa;
        }

        .coffee-hero-desc {
            font-size: clamp(15px, 2vw, 17px);
            color: #d4d4d8;
            line-height: 1.7;
            max-width: 760px;
            margin: 0 auto 32px;
        }

        .coffee-spec-strip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 24px;
            padding: 14px 28px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 16px;
            backdrop-filter: blur(12px);
            margin-bottom: 34px;
            max-width: 100%;
            box-sizing: border-box;
        }

        .strip-item {
            display: flex;
            align-items: center;
            gap: 12px;
            text-align: left;
        }

        .strip-item i {
            font-size: 20px;
            color: #a1a1aa;
        }

        .strip-item div {
            display: flex;
            flex-direction: column;
        }

        .strip-item strong {
            font-size: 13px;
            font-weight: 700;
            color: #ffffff;
        }

        .strip-item span {
            font-size: 11px;
            color: #a1a1aa;
        }

        .strip-div {
            width: 1px;
            height: 32px;
            background: rgba(255, 255, 255, 0.12);
        }

        .coffee-hero-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .coffee-btn-explore {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 28px;
            background: #ffffff;
            color: #09090b;
            font-size: 14px;
            font-weight: 700;
            border-radius: 999px;
            text-decoration: none;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.35);
            transition: all 0.25s ease;
        }

        .coffee-btn-explore:hover {
            background: #e4e4e7;
            color: #000000;
            transform: translateY(-2px);
        }

        /* Container */
        .coffee-container {
            max-width: 1240px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Dual Track Section */
        .coffee-tracks-section {
            padding: 70px 0;
            background: #f4f4f5;
            border-bottom: 1px solid #e4e4e7;
        }

        .dark .coffee-tracks-section {
            background: #121215;
            border-bottom-color: #27272a;
        }

        .tracks-header {
            text-align: center;
            max-width: 720px;
            margin: 0 auto 46px;
        }

        .tag-label {
            font-size: 11.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #71717a;
            margin-bottom: 8px;
        }

        .dark .tag-label {
            color: #a1a1aa;
        }

        .tracks-header h2 {
            font-size: clamp(24px, 3.4vw, 34px);
            font-weight: 800;
            color: #09090b;
            letter-spacing: -0.5px;
            margin-bottom: 12px;
        }

        .dark .tracks-header h2 {
            color: #f4f4f5;
        }

        .tracks-header p {
            font-size: 14.5px;
            color: #71717a;
            line-height: 1.65;
        }

        .dark .tracks-header p {
            color: #a1a1aa;
        }

        .tracks-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 28px;
        }

        .track-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 36px 32px;
            border: 1px solid #e4e4e7;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            position: relative;
            transition: all 0.3s ease;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .dark .track-card {
            background: #18181b;
            border-color: #27272a;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
        }

        .track-card:hover {
            transform: translateY(-4px);
            border-color: #d4d4d8;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
        }

        .dark .track-card:hover {
            border-color: #3f3f46;
        }

        .track-badge {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1px;
            color: #71717a;
            margin-bottom: 16px;
            display: block;
            text-align: center;
        }

        .dark .track-badge {
            color: #a1a1aa;
        }

        .track-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: #f4f4f5;
            color: #09090b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin: 0 auto 18px;
        }

        .dark .track-icon {
            background: #27272a;
            color: #ffffff;
        }

        .track-card h3 {
            font-size: 20px;
            font-weight: 800;
            color: #09090b;
            margin-bottom: 10px;
            text-align: center;
        }

        .dark .track-card h3 {
            color: #f4f4f5;
        }

        .track-card p {
            font-size: 14px;
            color: #71717a;
            line-height: 1.6;
            margin-bottom: 20px;
            text-align: center;
            max-width: 540px;
        }

        .dark .track-card p {
            color: #a1a1aa;
        }

        .track-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            width: 100%;
        }

        .track-list li {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            gap: 10px;
            font-size: 13.5px;
            font-weight: 600;
            color: #27272a;
        }

        .dark .track-list li {
            color: #d4d4d8;
        }

        .track-list li i {
            color: #09090b;
            font-size: 13px;
        }

        .dark .track-list li i {
            color: #ffffff;
        }

        /* Catalog Section */
        .coffee-catalog-section {
            padding: 70px 0 90px;
            background: #ffffff;
        }

        .dark .coffee-catalog-section {
            background: #09090b;
        }

        .coffee-filter-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .coffee-cat-pills {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .coffee-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 999px;
            background: #f4f4f5;
            color: #52525b;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid #e4e4e7;
            transition: all 0.2s ease;
        }

        .dark .coffee-pill {
            background: #18181b;
            color: #d4d4d8;
            border-color: #27272a;
        }

        .coffee-pill:hover {
            background: #e4e4e7;
            color: #09090b;
        }

        .dark .coffee-pill:hover {
            background: #27272a;
            color: #ffffff;
        }

        .coffee-pill.active {
            background: #09090b;
            color: #ffffff;
            border-color: #27272a;
        }

        .dark .coffee-pill.active {
            background: #ffffff;
            color: #09090b;
            border-color: #ffffff;
        }

        .coffee-search-form {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
            box-sizing: border-box;
        }

        .search-wrap {
            position: relative;
            display: flex;
            align-items: center;
            box-sizing: border-box;
            min-width: 0;
        }

        .search-wrap .search-icon {
            position: absolute;
            left: 12px;
            color: #71717a;
            font-size: 13px;
            pointer-events: none;
        }

        .search-input-field {
            padding: 9px 34px;
            border-radius: 999px;
            background: #f4f4f5;
            border: 1px solid #e4e4e7;
            font-size: 13px;
            color: #09090b;
            width: 220px;
            outline: none;
            box-sizing: border-box;
            transition: all 0.2s ease;
        }

        .dark .search-input-field {
            background: #18181b;
            border-color: #27272a;
            color: #f4f4f5;
        }

        .search-input-field:focus {
            background: #ffffff;
            border-color: #71717a;
            width: 260px;
        }

        .dark .search-input-field:focus {
            background: #27272a;
            border-color: #a1a1aa;
        }

        .search-clear-link {
            position: absolute;
            right: 12px;
            color: #71717a;
            font-size: 12px;
        }

        .search-action-btn {
            padding: 9px 18px;
            border-radius: 999px;
            background: #09090b;
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .dark .search-action-btn {
            background: #ffffff;
            color: #09090b;
        }

        .search-action-btn:hover {
            background: #27272a;
        }

        .dark .search-action-btn:hover {
            background: #e4e4e7;
        }

        .coffee-filter-banner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 18px;
            border-radius: 12px;
            background: #f4f4f5;
            border: 1px solid #e4e4e7;
            color: #27272a;
            font-size: 13px;
            margin-bottom: 26px;
        }

        .dark .coffee-filter-banner {
            background: #18181b;
            border-color: #27272a;
            color: #d4d4d8;
        }

        .banner-text {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .banner-reset {
            font-weight: 700;
            color: #09090b;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .dark .banner-reset {
            color: #ffffff;
        }

        /* Card Grid: 3 Columns like Cocoa Series */
        .coffee-cards-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 26px;
        }

        .coffee-machine-card {
            background: #ffffff;
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid #e4e4e7;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .dark .coffee-machine-card {
            background: #121215;
            border-color: #27272a;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        }

        .coffee-machine-card:hover {
            transform: translateY(-5px);
            border-color: #d4d4d8;
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.10);
        }

        .dark .coffee-machine-card:hover {
            border-color: #3f3f46;
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.7);
        }

        .card-media {
            position: relative;
            aspect-ratio: 16 / 10;
            background: #f4f4f5;
            overflow: hidden;
        }

        .dark .card-media {
            background: #18181b;
        }

        .card-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .coffee-machine-card:hover .card-media img {
            transform: scale(1.05);
        }

        .card-details {
            padding: 22px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .card-meta-badges {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .badge-code {
            padding: 4px 10px;
            border-radius: 6px;
            background: #09090b;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
        }

        .badge-category {
            padding: 4px 10px;
            border-radius: 6px;
            background: #f4f4f5;
            color: #09090b;
            font-size: 11px;
            font-weight: 700;
            border: 1px solid #e4e4e7;
        }

        .dark .badge-category {
            background: #27272a;
            color: #f4f4f5;
            border-color: #3f3f46;
        }

        .card-header-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 10px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 800;
            line-height: 1.35;
            margin: 0;
            flex: 1;
        }

        .card-title-btn {
            background: none;
            border: none;
            padding: 0;
            margin: 0;
            text-align: left;
            font-size: 18px;
            font-weight: 800;
            line-height: 1.35;
            color: #09090b;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .dark .card-title-btn {
            color: #f4f4f5;
        }

        .card-title-btn:hover {
            color: #71717a;
        }

        .dark .card-title-btn:hover {
            color: #a1a1aa;
        }

        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 700;
            color: #27272a;
            background: #f4f4f5;
            border: 1px solid #e4e4e7;
            padding: 3px 10px;
            border-radius: 999px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .dark .status-indicator {
            color: #f4f4f5;
            background: #18181b;
            border-color: #27272a;
        }

        .indicator-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #71717a;
        }

        .dark .indicator-dot {
            background: #a1a1aa;
        }

        .card-desc {
            font-size: 13.5px;
            color: #71717a;
            line-height: 1.6;
            margin-bottom: 16px;
            flex-grow: 1;
        }

        .dark .card-desc {
            color: #a1a1aa;
        }

        /* Card Specs Summary & Price */
        .card-specs-summary {
            margin-bottom: 16px;
            padding: 12px;
            border-radius: 12px;
            background: #fafafa;
            border: 1px solid #e4e4e7;
        }

        .dark .card-specs-summary {
            background: #18181b;
            border-color: #27272a;
        }

        .summary-specs-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 10px;
        }

        .spec-row-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            gap: 8px;
        }

        .spec-row-item .s-label {
            color: #71717a;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .dark .spec-row-item .s-label {
            color: #a1a1aa;
        }

        .spec-row-item .s-val {
            font-weight: 600;
            color: #09090b;
            text-align: right;
        }

        .dark .spec-row-item .s-val {
            color: #f4f4f5;
        }

        .summary-price-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 8px;
            border-top: 1px dashed #e4e4e7;
            gap: 8px;
        }

        .dark .summary-price-box {
            border-top-color: #27272a;
        }

        .price-info-col {
            display: flex;
            flex-direction: column;
        }

        .price-info-col .p-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #71717a;
        }

        .dark .price-info-col .p-title {
            color: #a1a1aa;
        }

        .price-info-col .p-sub {
            font-size: 12px;
            font-weight: 700;
            color: #09090b;
        }

        .dark .price-info-col .p-sub {
            color: #ffffff;
        }

        .price-tag-badge {
            background: #f4f4f5;
            color: #09090b;
            border: 1px solid #e4e4e7;
            border-radius: 6px;
            padding: 4px 8px;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .dark .price-tag-badge {
            background: #27272a;
            color: #f4f4f5;
            border-color: #3f3f46;
        }

        .price-tag-badge:hover {
            background: #09090b;
            color: #ffffff;
        }

        .dark .price-tag-badge:hover {
            background: #ffffff;
            color: #09090b;
        }

        .card-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: auto;
            padding-top: 14px;
            border-top: 1px solid #f4f4f5;
        }

        .dark .card-actions {
            border-top-color: #27272a;
        }

        .btn-card-detail {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px 16px;
            border-radius: 10px;
            background: #09090b;
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .dark .btn-card-detail {
            background: #ffffff;
            color: #09090b;
        }

        .btn-card-detail:hover {
            background: #27272a;
            color: #ffffff;
        }

        .dark .btn-card-detail:hover {
            background: #e4e4e7;
            color: #09090b;
        }

        .btn-card-wa {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #f4f4f5;
            color: #09090b;
            border: 1px solid #e4e4e7;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            text-decoration: none;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .dark .btn-card-wa {
            background: #18181b;
            color: #ffffff;
            border-color: #27272a;
        }

        .btn-card-wa:hover {
            background: #09090b;
            color: #ffffff;
            border-color: #27272a;
        }

        .dark .btn-card-wa:hover {
            background: #ffffff;
            color: #09090b;
            border-color: #e4e4e7;
        }

        /* Empty State */
        .coffee-empty-state {
            text-align: center;
            padding: 60px 20px;
            border-radius: 18px;
            background: #fafafa;
            border: 1px dashed #d4d4d8;
            margin: 20px 0;
        }

        .dark .coffee-empty-state {
            background: #121215;
            border-color: #3f3f46;
        }

        .empty-icon-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #f4f4f5;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 16px;
        }

        .dark .empty-icon-circle {
            background: #27272a;
        }

        .coffee-empty-state h3 {
            font-size: 19px;
            font-weight: 800;
            color: #09090b;
            margin-bottom: 6px;
        }

        .dark .coffee-empty-state h3 {
            color: #f4f4f5;
        }

        .coffee-empty-state p {
            font-size: 14px;
            color: #71717a;
        }

        /* Catalog Pagination (Pure Black & White / No Black Strokes) */
        .catalog-pagination-wrap {
            margin-top: 48px;
            display: flex;
            justify-content: center;
        }

        .product-pagination {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 14px;
            background: #fafafa;
            border: 1px solid #e4e4e7;
        }

        .dark .product-pagination {
            background: #121215;
            border-color: #27272a;
        }

        .page-numbers {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 38px;
            height: 38px;
            padding: 0 10px;
            border-radius: 9px;
            font-size: 13.5px;
            font-weight: 700;
            color: #27272a;
            background: #ffffff;
            border: 1px solid #e4e4e7;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .dark .page-link {
            background: #18181b;
            color: #d4d4d8;
            border-color: #27272a;
        }

        .page-link:hover {
            background: #f4f4f5;
            color: #09090b;
            border-color: #d4d4d8;
        }

        .dark .page-link:hover {
            background: #27272a;
            color: #ffffff;
            border-color: #3f3f46;
        }

        .page-link.active {
            background: #09090b;
            color: #ffffff;
            border-color: #27272a;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .dark .page-link.active {
            background: #ffffff;
            color: #09090b;
            border-color: #e4e4e7;
            box-shadow: 0 2px 10px rgba(255, 255, 255, 0.15);
        }

        .page-link.disabled {
            opacity: 0.35;
            cursor: not-allowed;
            pointer-events: none;
            background: #f4f4f5;
            border-color: #e4e4e7;
        }

        .dark .page-link.disabled {
            background: #18181b;
            border-color: #27272a;
        }

        .page-link.dots {
            border: none;
            background: transparent;
            cursor: default;
            min-width: 24px;
            color: #71717a;
        }

        .dark .page-link.dots {
            color: #a1a1aa;
        }

        /* Engineering Pillars */
        .coffee-pillars-section {
            padding: 60px 0;
            background: #f4f4f5;
            border-top: 1px solid #e4e4e7;
        }

        .dark .coffee-pillars-section {
            background: #121215;
            border-top-color: #27272a;
        }

        .pillars-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .pillar-card {
            padding: 28px;
            border-radius: 16px;
            background: #ffffff;
            border: 1px solid #e4e4e7;
        }

        .dark .pillar-card {
            background: #18181b;
            border-color: #27272a;
        }

        .pillar-icon {
            font-size: 24px;
            color: #09090b;
            margin-bottom: 14px;
        }

        .dark .pillar-icon {
            color: #ffffff;
        }

        .pillar-card h4 {
            font-size: 17px;
            font-weight: 800;
            color: #09090b;
            margin-bottom: 8px;
        }

        .dark .pillar-card h4 {
            color: #f4f4f5;
        }

        .pillar-card p {
            font-size: 13.5px;
            color: #71717a;
            line-height: 1.6;
        }

        .dark .pillar-card p {
            color: #a1a1aa;
        }

        /* Switcher Section */
        .coffee-switcher-section {
            padding: 60px 0 80px;
            background: #ffffff;
        }

        .dark .coffee-switcher-section {
            background: #09090b;
        }

        .coffee-switch-card {
            border-radius: 20px;
            background: linear-gradient(135deg, #09090b 0%, #18181b 100%);
            border: 1px solid #27272a;
            padding: 40px 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 30px;
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.2);
        }

        .switch-tag {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #a1a1aa;
            margin-bottom: 8px;
            display: block;
        }

        .switch-info h3 {
            font-size: 24px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 6px;
        }

        .switch-info p {
            font-size: 14.5px;
            color: #d4d4d8;
            max-width: 600px;
        }

        .btn-to-cocoa {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 13px 26px;
            border-radius: 999px;
            background: #ffffff;
            color: #09090b;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            white-space: nowrap;
            transition: all 0.25s ease;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
        }

        .btn-to-cocoa:hover {
            background: #e4e4e7;
            color: #000000;
            transform: translateX(4px);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .tracks-grid {
                grid-template-columns: 1fr;
            }
            .pillars-grid {
                grid-template-columns: 1fr;
            }
            .coffee-cards-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .coffee-container {
                padding: 0 14px;
            }
            .tracks-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }
            .track-card {
                padding: 24px 20px;
                border-radius: 16px;
            }
            .track-badge {
                font-size: 10px;
                letter-spacing: 0.8px;
                margin-bottom: 12px;
            }
            .track-icon {
                width: 44px;
                height: 44px;
                font-size: 19px;
                margin: 0 auto 14px;
                border-radius: 12px;
            }
            .track-card h3 {
                font-size: 17px;
                margin-bottom: 8px;
                text-align: center;
            }
            .track-card p {
                font-size: 13px;
                line-height: 1.55;
                margin-bottom: 16px;
                text-align: center;
            }
            .track-list {
                gap: 8px;
                align-items: center;
            }
            .track-list li {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                text-align: center;
                font-size: 12.5px;
                gap: 8px;
                line-height: 1.4;
            }
            .track-list li i {
                font-size: 11px;
                flex-shrink: 0;
            }
            .coffee-cards-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }
            .coffee-machine-card {
                border-radius: 14px;
            }
            .card-details {
                padding: 12px 10px;
            }
            .card-meta-badges {
                margin-bottom: 6px;
                gap: 4px;
            }
            .badge-code,
            .badge-category {
                font-size: 9px;
                padding: 2px 6px;
                border-radius: 4px;
            }
            .card-header-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
                margin-bottom: 6px;
            }
            .card-title {
                font-size: 13.5px;
                margin: 0;
            }
            .card-title-btn {
                font-size: 13.5px;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
                line-height: 1.35;
            }
            .status-indicator {
                font-size: 8.5px;
                padding: 2px 6px;
            }
            .indicator-dot {
                width: 4px;
                height: 4px;
            }
            .card-desc {
                font-size: 11px;
                line-height: 1.4;
                margin-bottom: 8px;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            .card-specs-summary {
                margin-bottom: 8px;
                padding: 8px;
                border-radius: 8px;
            }
            .summary-specs-list {
                gap: 4px;
                margin-bottom: 6px;
            }
            .spec-row-item {
                font-size: 10px;
                gap: 4px;
            }
            .spec-row-item .s-label {
                font-size: 9.5px;
                gap: 3px;
            }
            .spec-row-item .s-val {
                font-size: 9.5px;
            }
            .summary-price-box {
                padding-top: 6px;
                gap: 4px;
            }
            .price-info-col .p-title {
                font-size: 8px;
            }
            .price-info-col .p-sub {
                font-size: 10px;
            }
            .price-tag-badge {
                padding: 3px 6px;
                font-size: 9px;
                border-radius: 4px;
            }
            .coffee-switch-card {
                flex-direction: column;
                align-items: flex-start;
                padding: 30px 24px;
            }
            .coffee-filter-bar {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
                margin-bottom: 22px;
                width: 100%;
                box-sizing: border-box;
            }
            .coffee-cat-pills {
                display: flex;
                align-items: center;
                gap: 8px;
                flex-wrap: nowrap;
                overflow-x: auto;
                width: 100%;
                max-width: 100%;
                padding: 2px 2px 6px 2px;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
                -ms-overflow-style: none;
                box-sizing: border-box;
            }
            .coffee-cat-pills::-webkit-scrollbar {
                display: none;
            }
            .coffee-pill {
                flex-shrink: 0;
                white-space: nowrap;
                padding: 7px 14px;
                font-size: 12px;
            }
            .coffee-search-form {
                width: 100%;
                display: flex;
                align-items: center;
                gap: 8px;
                box-sizing: border-box;
            }
            .search-wrap {
                flex: 1;
                min-width: 0;
                box-sizing: border-box;
            }
            .search-input-field {
                width: 100% !important;
                min-width: 0 !important;
                box-sizing: border-box !important;
                padding: 9px 34px 9px 34px;
                font-size: 12.5px;
            }
            .search-input-field:focus {
                width: 100% !important;
            }
            .search-action-btn {
                flex-shrink: 0;
                padding: 9px 18px;
                font-size: 12.5px;
                white-space: nowrap;
                box-sizing: border-box;
            }
            .technical-table {
                grid-template-columns: 1fr;
            }
            .coffee-spec-strip {
                display: inline-flex;
                flex-wrap: nowrap;
                gap: 12px;
                padding: 8px 16px;
                border-radius: 12px;
                margin-bottom: 24px;
                max-width: 100%;
            }
            .coffee-spec-strip .strip-div {
                height: 22px;
                opacity: 0.7;
            }
            .coffee-spec-strip .strip-item {
                gap: 8px;
            }
            .coffee-spec-strip .strip-item i {
                font-size: 14px;
            }
            .coffee-spec-strip .strip-item strong {
                font-size: 11px;
                white-space: nowrap;
                line-height: 1.25;
            }
            .coffee-spec-strip .strip-item span {
                font-size: 9.5px;
                white-space: nowrap;
                line-height: 1.2;
            }
            .product-pagination {
                padding: 6px 10px;
                gap: 5px;
            }
            .page-link {
                min-width: 32px;
                height: 32px;
                font-size: 12px;
                padding: 0 6px;
            }
        }

        @media (max-width: 480px) {
            .coffee-container {
                padding: 0 10px;
            }
            .tracks-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }
            .track-card {
                padding: 20px 16px;
                border-radius: 14px;
            }
            .track-badge {
                font-size: 9.5px;
                letter-spacing: 0.5px;
                margin-bottom: 10px;
            }
            .track-icon {
                width: 40px;
                height: 40px;
                font-size: 17px;
                margin: 0 auto 12px;
                border-radius: 10px;
            }
            .track-card h3 {
                font-size: 15.5px;
                margin-bottom: 6px;
                text-align: center;
            }
            .track-card p {
                font-size: 12px;
                line-height: 1.5;
                margin-bottom: 14px;
                text-align: center;
            }
            .track-list {
                gap: 7px;
                align-items: center;
            }
            .track-list li {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                text-align: center;
                font-size: 11.5px;
                gap: 6px;
            }
            .track-list li i {
                font-size: 10px;
                flex-shrink: 0;
            }
            .coffee-cards-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }
            .coffee-machine-card {
                border-radius: 12px;
            }
            .card-details {
                padding: 10px 8px;
            }
            .badge-code,
            .badge-category {
                font-size: 8px;
                padding: 2px 5px;
            }
            .card-title,
            .card-title-btn {
                font-size: 12px;
            }
            .status-indicator {
                font-size: 7.5px;
                padding: 1px 5px;
            }
            .card-desc {
                font-size: 10px;
                line-height: 1.35;
                margin-bottom: 6px;
            }
            .card-specs-summary {
                padding: 6px;
                border-radius: 6px;
                margin-bottom: 6px;
            }
            .spec-row-item {
                font-size: 9px;
            }
            .spec-row-item .s-label {
                font-size: 8px;
            }
            .spec-row-item .s-val {
                font-size: 8.5px;
            }
            .price-info-col .p-title {
                font-size: 7px;
            }
            .price-info-col .p-sub {
                font-size: 9px;
            }
            .price-tag-badge {
                padding: 2px 5px;
                font-size: 8px;
            }
            .coffee-pill {
                padding: 6px 12px;
                font-size: 11px;
            }
            .search-input-field {
                padding: 8px 30px 8px 30px !important;
                font-size: 12px;
            }
            .search-wrap .search-icon {
                left: 10px;
                font-size: 11px;
            }
            .search-clear-link {
                right: 10px;
                font-size: 11px;
            }
            .search-action-btn {
                padding: 8px 14px;
                font-size: 12px;
            }
            .product-pagination {
                padding: 4px 8px;
                gap: 4px;
            }
            .page-link {
                min-width: 28px;
                height: 28px;
                font-size: 11px;
                padding: 0 4px;
            }
            .coffee-spec-strip {
                gap: 8px;
                padding: 6px 10px;
                border-radius: 10px;
                margin-bottom: 20px;
            }
            .coffee-spec-strip .strip-div {
                height: 18px;
            }
            .coffee-spec-strip .strip-item {
                gap: 6px;
            }
            .coffee-spec-strip .strip-item i {
                font-size: 12px;
            }
            .coffee-spec-strip .strip-item strong {
                font-size: 10px;
                line-height: 1.2;
            }
            .coffee-spec-strip .strip-item span {
                font-size: 8.5px;
                line-height: 1.2;
            }
        }

        /* ============ SPEC MODAL STYLES (NO BLACK STROKES) ============ */
        .spec-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(6px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-y: auto;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.25s ease, visibility 0.25s ease;
        }

        .spec-modal-backdrop.is-active {
            opacity: 1;
            visibility: visible;
        }

        .spec-modal-dialog {
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 20px;
            width: 100%;
            max-width: 680px;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            transform: scale(0.96) translateY(10px);
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .dark .spec-modal-dialog {
            background: #121215;
            border-color: #27272a;
        }

        .spec-modal-backdrop.is-active .spec-modal-dialog {
            transform: scale(1) translateY(0);
        }

        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e4e4e7;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            background: #fafafa;
        }

        .dark .modal-header {
            background: #18181b;
            border-bottom-color: #27272a;
        }

        .modal-header-meta {
            flex: 1;
        }

        .modal-badges {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            margin-bottom: 8px;
        }

        .modal-title {
            font-size: 20px;
            font-weight: 800;
            color: #09090b;
            line-height: 1.3;
            margin: 0;
        }

        .dark .modal-title {
            color: #ffffff;
        }

        .modal-close-btn {
            background: #f4f4f5;
            border: 1px solid #e4e4e7;
            color: #71717a;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .dark .modal-close-btn {
            background: #27272a;
            border-color: #3f3f46;
            color: #d4d4d8;
        }

        .modal-close-btn:hover {
            background: #09090b;
            color: #ffffff;
            border-color: #27272a;
        }

        .dark .modal-close-btn:hover {
            background: #ffffff;
            color: #09090b;
            border-color: #e4e4e7;
        }

        .modal-body {
            padding: 24px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .modal-media-wrap {
            width: 100%;
            height: 260px;
            border-radius: 14px;
            overflow: hidden;
            background: #f4f4f5;
            border: 1px solid #e4e4e7;
        }

        .dark .modal-media-wrap {
            background: #18181b;
            border-color: #27272a;
        }

        .modal-media-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .modal-section {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .modal-sec-title {
            font-size: 14px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #09090b;
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
        }

        .dark .modal-sec-title {
            color: #f4f4f5;
        }

        .modal-sec-title i {
            color: #71717a;
            font-size: 13px;
        }

        .dark .modal-sec-title i {
            color: #a1a1aa;
        }

        .modal-desc-box {
            background: #fafafa;
            border: 1px solid #e4e4e7;
            border-radius: 12px;
            padding: 16px;
        }

        .dark .modal-desc-box {
            background: #18181b;
            border-color: #27272a;
        }

        .modal-desc-text {
            font-size: 13.5px;
            color: #27272a;
            line-height: 1.6;
            margin: 0;
        }

        .dark .modal-desc-text {
            color: #d4d4d8;
        }

        .modal-desc-full {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #e4e4e7;
            font-size: 13px;
            color: #71717a;
            line-height: 1.6;
        }

        .dark .modal-desc-full {
            border-top-color: #27272a;
            color: #a1a1aa;
        }

        .modal-specs-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        @media (max-width: 640px) {
            .modal-specs-grid {
                grid-template-columns: 1fr;
            }
        }

        .modal-spec-item {
            padding: 12px 14px;
            border-radius: 10px;
            background: #fafafa;
            border: 1px solid #e4e4e7;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .dark .modal-spec-item {
            background: #18181b;
            border-color: #27272a;
        }

        .modal-spec-item.full-width {
            grid-column: 1 / -1;
        }

        .ms-label {
            font-size: 11px;
            font-weight: 700;
            color: #71717a;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .dark .ms-label {
            color: #a1a1aa;
        }

        .ms-val {
            font-size: 13px;
            font-weight: 700;
            color: #09090b;
        }

        .dark .ms-val {
            color: #ffffff;
        }

        .modal-warranty-box {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            padding: 14px 16px;
            border-radius: 12px;
            background: #f4f4f5;
            border: 1px solid #e4e4e7;
        }

        .dark .modal-warranty-box {
            background: #18181b;
            border-color: #27272a;
        }

        @media (max-width: 640px) {
            .modal-warranty-box {
                grid-template-columns: 1fr;
            }
        }

        .w-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .w-item i {
            font-size: 20px;
            color: #09090b;
        }

        .dark .w-item i {
            color: #ffffff;
        }

        .w-item strong {
            display: block;
            font-size: 12.5px;
            font-weight: 800;
            color: #09090b;
        }

        .dark .w-item strong {
            color: #ffffff;
        }

        .w-item span {
            display: block;
            font-size: 11px;
            color: #71717a;
        }

        .dark .w-item span {
            color: #a1a1aa;
        }

        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid #e4e4e7;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            background: #fafafa;
        }

        .dark .modal-footer {
            background: #18181b;
            border-top-color: #27272a;
        }

        .btn-modal-cancel {
            padding: 10px 18px;
            border-radius: 10px;
            background: #ffffff;
            color: #71717a;
            border: 1px solid #e4e4e7;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .dark .btn-modal-cancel {
            background: #27272a;
            color: #d4d4d8;
            border-color: #3f3f46;
        }

        .btn-modal-cancel:hover {
            background: #f4f4f5;
            color: #09090b;
        }

        .dark .btn-modal-cancel:hover {
            background: #3f3f46;
            color: #ffffff;
        }

        .btn-modal-wa {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 10px;
            background: #09090b;
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .dark .btn-modal-wa {
            background: #ffffff;
            color: #09090b;
        }

        .btn-modal-wa:hover {
            background: #27272a;
            color: #ffffff;
        }

        .dark .btn-modal-wa:hover {
            background: #e4e4e7;
            color: #09090b;
        }
    </style>

    {{-- ============ MODAL SCRIPTS ============ --}}
    <script>
        function openSpecModal(slug) {
            const modal = document.getElementById('modal-' + slug);
            if (!modal) return;
            modal.style.display = 'flex';
            void modal.offsetWidth;
            modal.classList.add('is-active');
            document.body.style.overflow = 'hidden';
        }

        function closeSpecModal(slug) {
            const modal = document.getElementById('modal-' + slug);
            if (!modal) return;
            modal.classList.remove('is-active');
            setTimeout(() => {
                modal.style.display = 'none';
                if (!document.querySelector('.spec-modal-backdrop.is-active')) {
                    document.body.style.overflow = '';
                }
            }, 250);
        }

        function handleBackdropClick(event, slug) {
            if (event.target === event.currentTarget) {
                closeSpecModal(slug);
            }
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                const activeModal = document.querySelector('.spec-modal-backdrop.is-active');
                if (activeModal) {
                    const id = activeModal.id.replace('modal-', '');
                    closeSpecModal(id);
                }
            }
        });
    </script>

@endsection
