@extends('layouts.public')

@section('title', 'MD-Cocoa Series — Lini Mesin Pengolahan Kakao | '.$company->name)
@section('meta_description', 'Katalog lini mesin pengolahan kakao industri MD-Cocoa Series produksi '.$company->name.'. Mesin fermentasi, pengeringan, roasting, winnowing, hingga grinding pasta dan bubuk cokelat.')

@section('content')
    @php
        $waNum = preg_replace('/[^0-9]/', '', (string) ($company->whatsapp ?? ''));
    @endphp

    {{-- ============ COCOA HERO (MONOCHROME INDUSTRIAL) ============ --}}
    <section class="cocoa-hero-section">
        <div class="cocoa-hero-bg"></div>
        <div class="cocoa-hero-overlay"></div>

        <div class="cocoa-hero-content">

            <h1 class="cocoa-hero-title">
                Lini Mesin <span>Pengolahan Kakao</span> Terpadu
            </h1>

            <p class="cocoa-hero-desc">
                Dirancang presisi dari tahap fermentasi pascapanen, pengeringan higienis, roasting terkontrol, hingga penggilingan pasta cokelat halus berstandar ekspor.
            </p>

            <div class="cocoa-hero-stats">
                <div class="c-stat-item">
                    <span class="c-stat-val">{{ $machines->total() }}</span>
                    <span class="c-stat-lbl">Model Mesin Kakao</span>
                </div>
                <div class="c-stat-div"></div>
                <div class="c-stat-item">
                    <span class="c-stat-val">100%</span>
                    <span class="c-stat-lbl">Fabrikasi Presisi</span>
                </div>
            </div>

            <div class="cocoa-hero-cta">
                <a href="#katalog-kakao" class="c-btn-primary">
                    <i class="fas fa-layer-group"></i> Lihat Model Mesin
                </a>
            </div>
        </div>

        <a href="#alur-proses" class="cocoa-scroll-indicator" aria-label="Gulir ke konten">
            <span>Explore</span>
            <i class="fas fa-chevron-down"></i>
        </a>
    </section>

    {{-- ============ ALUR PROSES KAKAO (MONOCHROME PIPELINE SHOWCASE) ============ --}}
    <section class="cocoa-pipeline-wrap" id="alur-proses">
        <div class="cocoa-container">
            <div class="pipeline-header">
                <h2>4 Tahap Pengolahan Kakao Standar Pabrik</h2>
                <p>Mesin-mesin MD-Cocoa Series mencakup keseluruhan siklus pengolahan biji kakao mentah menjadi produk bernilai tinggi.</p>
            </div>

            <div class="pipeline-grid">
                <div class="pipeline-card">
                    <div class="step-num">01</div>
                    <div class="step-icon"><i class="fas fa-box-archive"></i></div>
                    <h3>Pascapanen &amp; Fermentasi</h3>
                    <p>Box fermentasi bertingkat dengan sirkulasi udara terkendali untuk mengoptimalkan aroma &amp; menurunkan kepahitan biji.</p>
                </div>

                <div class="pipeline-card">
                    <div class="step-num">02</div>
                    <div class="step-icon"><i class="fas fa-sun"></i></div>
                    <h3>Pengeringan Higienis</h3>
                    <p>Mesin pengering sistem pemanas tidak langsung (indirect heat) menjaga kadar air 7–8% tanpa kontaminasi asap pembakaran.</p>
                </div>

                <div class="pipeline-card">
                    <div class="step-num">03</div>
                    <div class="step-icon"><i class="fas fa-fire-burner"></i></div>
                    <h3>Sangrai &amp; Deshelling</h3>
                    <p>Roasting drum terkontrol merata, dilanjutkan pemisahan kulit ari kakao (winnowing) menghasilkan nibs bersih siap olah.</p>
                </div>

                <div class="pipeline-card">
                    <div class="step-num">04</div>
                    <div class="step-icon"><i class="fas fa-mortar-pestle"></i></div>
                    <h3>Pasta &amp; Bubuk Kakao</h3>
                    <p>Penepungan dan ball mill grinding presisi tinggi untuk menghasilkan cocoa liquor, cocoa butter, serta bubuk cokelat murni.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ KATALOG MESIN KAKAO ============ --}}
    <section class="cocoa-catalog-wrap" id="katalog-kakao">
        <div class="cocoa-container">

            {{-- Filter & Search Toolbar --}}
            <div class="catalog-toolbar">
                <div class="category-pills">
                    <a href="{{ route('products.cocoa') }}#katalog-kakao"
                       class="cat-pill {{ empty($categorySlug) ? 'active' : '' }}">
                        <i class="fas fa-cubes"></i> Semua Kategori ({{ $categories->sum('machines_count') }})
                    </a>
                    @foreach ($categories as $cat)
                        <a href="{{ route('products.cocoa', array_filter(['kategori' => $cat->slug, 'q' => $keyword])) }}#katalog-kakao"
                           class="cat-pill {{ $categorySlug === $cat->slug ? 'active' : '' }}">
                            {{ $cat->name }} ({{ $cat->machines_count }})
                        </a>
                    @endforeach
                </div>

                <form method="GET" action="{{ route('products.cocoa') }}" class="cocoa-search-form">
                    @if ($categorySlug)
                        <input type="hidden" name="kategori" value="{{ $categorySlug }}">
                    @endif
                    <div class="search-box-inner">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" name="q" value="{{ $keyword }}"
                               placeholder="Cari model mesin kakao..." class="search-input">
                        @if ($keyword)
                            <a href="{{ route('products.cocoa', $categorySlug ? ['kategori' => $categorySlug] : []) }}#katalog-kakao"
                               class="clear-search" title="Hapus filter pencarian">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                    <button type="submit" class="search-btn">
                        Cari
                    </button>
                </form>
            </div>

            @if ($keyword || $activeCategory)
                <div class="filter-status-banner">
                    <div class="filter-status-text">
                        <i class="fas fa-info-circle text-zinc-500"></i>
                        <span>
                            Menampilkan hasil
                            @if ($keyword) untuk kata kunci <strong>"{{ $keyword }}"</strong> @endif
                            @if ($activeCategory) pada kategori <strong>{{ $activeCategory->name }}</strong> @endif
                            ({{ $machines->total() }} model ditemukan).
                        </span>
                    </div>
                    <a href="{{ route('products.cocoa') }}#katalog-kakao" class="reset-filter-link">
                        Reset Filter <i class="fas fa-rotate-left text-xs"></i>
                    </a>
                </div>
            @endif

            {{-- Grid Mesin Kakao --}}
            @if ($machines->isNotEmpty())
                <div class="cocoa-cards-grid">
                    @foreach ($machines as $machine)
                        <div class="cocoa-machine-card group">
                            <div class="card-media" onclick="openSpecModal('{{ $machine->slug }}')" style="cursor: pointer;">
                                <img src="{{ $machine->photo }}" alt="{{ $machine->name }}" loading="lazy"
                                     onerror="this.src='https://images.unsplash.com/photo-1516937941344-00b4ec27492b?w=800&auto=format&fit=crop'">
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

                                <h3 class="card-title">
                                    <button type="button" class="card-title-btn" onclick="openSpecModal('{{ $machine->slug }}')">
                                        {{ $machine->name }}
                                    </button>
                                </h3>

                                <p class="card-desc">
                                    {{ $machine->function ?: ($machine->short_description ?: \Illuminate\Support\Str::limit(strip_tags((string) $machine->description), 120)) }}
                                </p>

                                {{-- Ringkasan Spesifikasi & Estimasi Harga --}}
                                <div class="card-specs-summary">
                                    <div class="summary-specs-list">
                                        <div class="spec-row-item">
                                            <span class="s-label"><i class="fas fa-gauge-high"></i> Kapasitas</span>
                                            <span class="s-val">{{ $machine->capacity ?: 'Kustom Industri' }}</span>
                                        </div>
                                        <div class="spec-row-item">
                                            <span class="s-label"><i class="fas fa-bolt"></i> Daya Motor</span>
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

                {{-- Pagination Mesin Kakao (Selalu Muncul) --}}
                <div class="catalog-pagination-wrap">
                    {{ $machines->links('components.pagination') }}
                </div>
            @else
                <div class="cocoa-empty-state">
                    <div class="empty-icon-wrap">
                        <i class="fas fa-seedling text-zinc-500"></i>
                    </div>
                    <h3>Mesin Kakao Tidak Ditemukan</h3>
                    <p>Tidak ada model mesin kakao yang cocok dengan kata kunci atau filter saat ini.</p>
                    <a href="{{ route('products.cocoa') }}#katalog-kakao" class="c-btn-primary mt-4">
                        Lihat Seluruh Mesin Kakao
                    </a>
                </div>
            @endif

        </div>
    </section>

    {{-- ============ SWITCHER TO COFFEE SERIES ============ --}}
    <section class="series-switcher-banner">
        <div class="cocoa-container">
            <div class="switcher-card">
                <div class="switcher-text">
                    <span class="switcher-pill">Lini Mesin Lainnya</span>
                    <h3>Memerlukan Solusi Pengolahan Biji Kopi?</h3>
                    <p>Jelajahi lini <strong>MD-Coffee Series</strong> dari pascapanen pulper basah hingga mesin sangrai (roaster) otomatis presisi.</p>
                </div>
                <div class="switcher-cta">
                    <a href="{{ route('products.coffee') }}" class="switcher-btn">
                        <span>Buka MD-Coffee Series</span>
                        <i class="fas fa-mug-hot"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ POPUP MODAL SPESIFIKASI LENGKAP ============ --}}
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
                             onerror="this.src='https://images.unsplash.com/photo-1516937941344-00b4ec27492b?w=800&auto=format&fit=crop'">
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
                                <span class="ms-val">Stainless Steel SUS 304 Food Grade / Plat Baja Industri</span>
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
                                <span>Dukungan teknisi &amp; ketersediaan sparepart</span>
                            </div>
                        </div>
                        <div class="w-item">
                            <i class="fas fa-handshake"></i>
                            <div>
                                <strong>Estimasi Harga: Made-to-Order</strong>
                                <span>Penyesuaian kapasitas &amp; layout pabrik Anda</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-modal-cancel" onclick="closeSpecModal('{{ $machine->slug }}')">
                        Tutup
                    </button>
                    @if ($waNum)
                        <a href="https://wa.me/{{ $waNum }}?text={{ urlencode('Halo Manufactur Dynamic Indonesia, saya ingin konsultasi penawaran dan spesifikasi mesin kakao: '.$machine->name.($machine->model_code ? ' ('.$machine->model_code.')' : '')) }}"
                           target="_blank" rel="noopener" class="btn-modal-wa">
                            <i class="fab fa-whatsapp"></i>
                            <span>Hubungi Sales Engineering via WhatsApp</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endforeach

    {{-- ============ STYLES FOR COCOA SERIES (BLACK & WHITE / NO BLACK STROKES) ============ --}}
    <style>
        .cocoa-hero-section {
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

        .cocoa-hero-bg {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            background-image: url('/images/GambarBG_GIF.gif'), url('/images/GambarBG.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            filter: grayscale(100%) contrast(115%);
            opacity: 0.40;
            z-index: 0;
        }

        .cocoa-hero-overlay {
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at center, rgba(0, 0, 0, 0.40) 0%, rgba(9, 9, 11, 0.90) 100%),
                        linear-gradient(180deg, rgba(0, 0, 0, 0.65) 0%, rgba(9, 9, 11, 0.96) 100%);
            z-index: 1;
        }

        .cocoa-hero-content {
            position: relative;
            z-index: 2;
            max-width: 900px;
            margin: 0 auto;
        }

        .cocoa-scroll-indicator {
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

        .cocoa-scroll-indicator:hover {
            color: #ffffff;
            transform: translateX(-50%) translateY(3px);
        }

        .cocoa-scroll-indicator i {
            animation: bounce-subtle 1.8s infinite ease-in-out;
        }

        @keyframes bounce-subtle {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(4px); }
        }

        .cocoa-pill-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.16);
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
            backdrop-filter: blur(10px);
        }

        .cocoa-hero-title {
            font-size: clamp(34px, 5.2vw, 54px);
            font-weight: 800;
            line-height: 1.18;
            color: #ffffff;
            letter-spacing: -0.8px;
            margin-bottom: 18px;
        }

        .cocoa-hero-title span {
            color: #a1a1aa;
        }

        .cocoa-hero-desc {
            font-size: clamp(15px, 2vw, 17.5px);
            color: #d4d4d8;
            line-height: 1.7;
            max-width: 740px;
            margin: 0 auto 30px;
        }

        .cocoa-hero-stats {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 24px;
            padding: 14px 28px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            margin-bottom: 32px;
        }

        .c-stat-item {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .c-stat-val {
            font-size: 20px;
            font-weight: 800;
            color: #ffffff;
        }

        .c-stat-lbl {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #a1a1aa;
        }

        .c-stat-div {
            width: 1px;
            height: 28px;
            background: rgba(255, 255, 255, 0.14);
        }

        .cocoa-hero-cta {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .c-btn-primary {
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

        .c-btn-primary:hover {
            background: #e4e4e7;
            color: #000000;
            transform: translateY(-2px);
        }

        /* Container */
        .cocoa-container {
            max-width: 1240px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Pipeline Showcase */
        .cocoa-pipeline-wrap {
            padding: 70px 0;
            background: #f4f4f5;
            border-bottom: 1px solid #e4e4e7;
        }

        .dark .cocoa-pipeline-wrap {
            background: #121215;
            border-bottom-color: #27272a;
        }

        .pipeline-header {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 46px;
        }

        .pipeline-tag {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #71717a;
            margin-bottom: 8px;
            display: block;
        }

        .dark .pipeline-tag {
            color: #a1a1aa;
        }

        .pipeline-header h2 {
            font-size: clamp(24px, 3.4vw, 34px);
            font-weight: 800;
            color: #09090b;
            letter-spacing: -0.5px;
            margin-bottom: 12px;
        }

        .dark .pipeline-header h2 {
            color: #f4f4f5;
        }

        .pipeline-header p {
            font-size: 14.5px;
            color: #71717a;
            line-height: 1.65;
        }

        .dark .pipeline-header p {
            color: #a1a1aa;
        }

        .pipeline-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .pipeline-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 26px 22px;
            border: 1px solid #e4e4e7;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            position: relative;
            transition: all 0.28s ease;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .dark .pipeline-card {
            background: #18181b;
            border-color: #27272a;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.4);
        }

        .pipeline-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08);
            border-color: #d4d4d8;
        }

        .dark .pipeline-card:hover {
            border-color: #3f3f46;
        }

        .step-num {
            font-size: 12px;
            font-weight: 800;
            color: #71717a;
            letter-spacing: 1px;
            margin-bottom: 14px;
            text-align: center;
        }

        .dark .step-num {
            color: #a1a1aa;
        }

        .step-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: #f4f4f5;
            color: #09090b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            margin: 0 auto 16px;
        }

        .dark .step-icon {
            background: #27272a;
            color: #ffffff;
        }

        .pipeline-card h3 {
            font-size: 16.5px;
            font-weight: 700;
            color: #09090b;
            margin-bottom: 8px;
            line-height: 1.35;
        }

        .dark .pipeline-card h3 {
            color: #f4f4f5;
        }

        .pipeline-card p {
            font-size: 13px;
            color: #71717a;
            line-height: 1.6;
        }

        .dark .pipeline-card p {
            color: #a1a1aa;
        }

        /* Catalog Section */
        .cocoa-catalog-wrap {
            padding: 70px 0 90px;
            background: #ffffff;
        }

        .dark .cocoa-catalog-wrap {
            background: #09090b;
        }

        .catalog-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .category-pills {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .cat-pill {
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

        .dark .cat-pill {
            background: #18181b;
            color: #d4d4d8;
            border-color: #27272a;
        }

        .cat-pill:hover {
            background: #e4e4e7;
            color: #09090b;
        }

        .dark .cat-pill:hover {
            background: #27272a;
            color: #ffffff;
        }

        .cat-pill.active {
            background: #09090b;
            color: #ffffff;
            border-color: #27272a;
        }

        .dark .cat-pill.active {
            background: #ffffff;
            color: #09090b;
            border-color: #ffffff;
        }

        .cocoa-search-form {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
            box-sizing: border-box;
        }

        .search-box-inner {
            position: relative;
            display: flex;
            align-items: center;
            box-sizing: border-box;
            min-width: 0;
        }

        .search-icon {
            position: absolute;
            left: 12px;
            color: #71717a;
            font-size: 13px;
            pointer-events: none;
        }

        .search-input {
            padding: 9px 34px 9px 34px;
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

        .dark .search-input {
            background: #18181b;
            border-color: #27272a;
            color: #f4f4f5;
        }

        .search-input:focus {
            background: #ffffff;
            border-color: #71717a;
            width: 260px;
        }

        .dark .search-input:focus {
            background: #27272a;
            border-color: #a1a1aa;
        }

        .clear-search {
            position: absolute;
            right: 12px;
            color: #71717a;
            font-size: 12px;
        }

        .search-btn {
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

        .dark .search-btn {
            background: #ffffff;
            color: #09090b;
        }

        .search-btn:hover {
            background: #27272a;
        }

        .dark .search-btn:hover {
            background: #e4e4e7;
        }

        .filter-status-banner {
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

        .dark .filter-status-banner {
            background: #18181b;
            border-color: #27272a;
            color: #d4d4d8;
        }

        .filter-status-text {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .reset-filter-link {
            font-weight: 700;
            color: #09090b;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .dark .reset-filter-link {
            color: #ffffff;
        }

        .reset-filter-link:hover {
            text-decoration: underline;
        }

        /* Card Grid */
        .cocoa-cards-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 26px;
        }

        .cocoa-machine-card {
            background: #ffffff;
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid #e4e4e7;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .dark .cocoa-machine-card {
            background: #121215;
            border-color: #27272a;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        }

        .cocoa-machine-card:hover {
            transform: translateY(-5px);
            border-color: #d4d4d8;
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.10);
        }

        .dark .cocoa-machine-card:hover {
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

        .cocoa-machine-card:hover .card-media img {
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

        .card-title {
            font-size: 18px;
            font-weight: 800;
            line-height: 1.35;
            margin-bottom: 10px;
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
            text-decoration: none;
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
        }

        .dark .btn-card-wa {
            background: #18181b;
            color: #ffffff;
            border-color: #27272a;
        }

        .btn-card-wa:hover {
            background: #09090b;
            color: #ffffff;
        }

        .dark .btn-card-wa:hover {
            background: #ffffff;
            color: #09090b;
        }

        /* Empty State */
        .cocoa-empty-state {
            text-align: center;
            padding: 60px 20px;
            border-radius: 18px;
            background: #fafafa;
            border: 1px dashed #d4d4d8;
            margin: 20px 0;
        }

        .dark .cocoa-empty-state {
            background: #121215;
            border-color: #3f3f46;
        }

        .empty-icon-wrap {
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

        .dark .empty-icon-wrap {
            background: #27272a;
        }

        .cocoa-empty-state h3 {
            font-size: 19px;
            font-weight: 800;
            color: #09090b;
            margin-bottom: 6px;
        }

        .dark .cocoa-empty-state h3 {
            color: #f4f4f5;
        }

        .cocoa-empty-state p {
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

        /* Features Section */
        .cocoa-features-wrap {
            padding: 60px 0;
            background: #f4f4f5;
            border-top: 1px solid #e4e4e7;
        }

        .dark .cocoa-features-wrap {
            background: #121215;
            border-top-color: #27272a;
        }

        .features-inner-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .feature-box {
            padding: 26px;
            border-radius: 16px;
            background: #ffffff;
            border: 1px solid #e4e4e7;
        }

        .dark .feature-box {
            background: #18181b;
            border-color: #27272a;
        }

        .fb-icon {
            font-size: 26px;
            color: #09090b;
            margin-bottom: 14px;
        }

        .dark .fb-icon {
            color: #ffffff;
        }

        .feature-box h4 {
            font-size: 17px;
            font-weight: 800;
            color: #09090b;
            margin-bottom: 8px;
        }

        .dark .feature-box h4 {
            color: #f4f4f5;
        }

        .feature-box p {
            font-size: 13.5px;
            color: #71717a;
            line-height: 1.6;
        }

        .dark .feature-box p {
            color: #a1a1aa;
        }

        /* Switcher Banner */
        .series-switcher-banner {
            padding: 60px 0 80px;
            background: #ffffff;
        }

        .dark .series-switcher-banner {
            background: #09090b;
        }

        .switcher-card {
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

        .switcher-pill {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #a1a1aa;
            margin-bottom: 8px;
            display: block;
        }

        .switcher-text h3 {
            font-size: 24px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 6px;
        }

        .switcher-text p {
            font-size: 14.5px;
            color: #d4d4d8;
            max-width: 600px;
        }

        .switcher-btn {
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

        .switcher-btn:hover {
            background: #e4e4e7;
            color: #000000;
            transform: translateX(4px);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .pipeline-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .cocoa-cards-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .features-inner-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .pipeline-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }
            .pipeline-card {
                padding: 18px 14px;
                border-radius: 14px;
            }
            .pipeline-card h3 {
                font-size: 14px;
                margin-bottom: 6px;
            }
            .pipeline-card p {
                font-size: 11.5px;
                line-height: 1.45;
            }
            .step-icon {
                width: 38px;
                height: 38px;
                font-size: 16px;
                margin: 0 auto 12px;
                border-radius: 10px;
            }
            .step-num {
                font-size: 10.5px;
                margin-bottom: 10px;
            }
            .cocoa-container {
                padding: 0 14px;
            }
            .cocoa-cards-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }
            .cocoa-machine-card {
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
            .card-title {
                font-size: 13.5px;
                margin-bottom: 6px;
            }
            .card-title-btn {
                font-size: 13.5px;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
                line-height: 1.35;
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
            .card-actions {
                padding-top: 8px;
                gap: 6px;
            }
            .btn-card-detail {
                padding: 6px 8px;
                font-size: 11px;
            }
            .btn-card-wa {
                width: 32px;
                height: 32px;
                font-size: 14px;
            }
            .switcher-card {
                flex-direction: column;
                align-items: flex-start;
                padding: 30px 24px;
            }
            .catalog-toolbar {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
                margin-bottom: 22px;
                width: 100%;
                box-sizing: border-box;
            }
            .category-pills {
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
            .category-pills::-webkit-scrollbar {
                display: none;
            }
            .cat-pill {
                flex-shrink: 0;
                white-space: nowrap;
                padding: 7px 14px;
                font-size: 12px;
            }
            .cocoa-search-form {
                width: 100%;
                display: flex;
                align-items: center;
                gap: 8px;
                box-sizing: border-box;
            }
            .search-box-inner {
                flex: 1;
                min-width: 0;
                box-sizing: border-box;
            }
            .search-input {
                width: 100% !important;
                min-width: 0 !important;
                box-sizing: border-box !important;
                padding: 9px 34px 9px 34px;
                font-size: 12.5px;
            }
            .search-input:focus {
                width: 100% !important;
            }
            .search-btn {
                flex-shrink: 0;
                padding: 9px 18px;
                font-size: 12.5px;
                white-space: nowrap;
                box-sizing: border-box;
            }
            .cocoa-hero-stats {
                gap: 16px;
                padding: 12px 18px;
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
            .pipeline-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }
            .pipeline-card {
                padding: 14px 10px;
                border-radius: 12px;
            }
            .pipeline-card h3 {
                font-size: 12.5px;
                line-height: 1.3;
                margin-bottom: 4px;
            }
            .pipeline-card p {
                font-size: 10px;
                line-height: 1.35;
            }
            .step-icon {
                width: 32px;
                height: 32px;
                font-size: 14px;
                margin: 0 auto 8px;
                border-radius: 8px;
            }
            .step-num {
                font-size: 9px;
                margin-bottom: 6px;
            }
            .cocoa-container {
                padding: 0 10px;
            }
            .cocoa-cards-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }
            .cocoa-machine-card {
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
            .cat-pill {
                padding: 6px 12px;
                font-size: 11px;
            }
            .search-input {
                padding: 8px 30px 8px 30px !important;
                font-size: 12px;
            }
            .search-icon {
                left: 10px;
                font-size: 11px;
            }
            .clear-search {
                right: 10px;
                font-size: 11px;
            }
            .search-btn {
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
