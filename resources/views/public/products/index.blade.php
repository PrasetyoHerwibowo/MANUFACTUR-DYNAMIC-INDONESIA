@extends('layouts.public')

@section('title', 'Our Products — '.$company->name)
@section('meta_description', 'Katalog lini mesin pengolahan kakao (MD-Cocoa Series) dan kopi (MD-Coffee Series) produksi '.$company->name.'.')

@section('content')

    {{-- ============ PRODUCTS HERO ============ --}}
    <section class="products-hero">
        <div class="hero-bg"></div>
        <h1>Our <span>Products</span></h1>
        <p>Solusi manufaktur mesin pengolahan presisi tinggi untuk industri kakao dan kopi Indonesia.</p>
    </section>

    {{-- ============ SERIES FILTER & SEARCH BAR ============ --}}
    <div class="series-nav-container">
        <div class="series-nav-bar">
            <div class="series-tabs">
                <a href="{{ route('products.index') }}"
                   class="series-tab-btn active">
                    <i class="fas fa-layer-group"></i> Semua Lini ({{ $cocoaMachines->count() + $coffeeMachines->count() }})
                </a>
                <a href="{{ route('products.cocoa') }}"
                   class="series-tab-btn cocoa">
                    <i class="fas fa-seedling"></i> MD-Cocoa Series ({{ $cocoaMachines->count() }})
                </a>
                <a href="{{ route('products.coffee') }}"
                   class="series-tab-btn coffee">
                    <i class="fas fa-mug-hot"></i> MD-Coffee Series ({{ $coffeeMachines->count() }})
                </a>
            </div>

            <form method="GET" action="{{ route('products.index') }}" class="series-search-box">
                @if ($selectedSeries)
                    <input type="hidden" name="series" value="{{ $selectedSeries }}">
                @endif
                <div class="search-input-wrapper">
                    <i class="fas fa-search search-lens"></i>
                    <input type="text" name="q" value="{{ $keyword }}"
                           placeholder="Cari model mesin atau fungsi..."
                           class="search-input-field">
                    @if ($keyword)
                        <a href="{{ route('products.index', $selectedSeries ? ['series' => $selectedSeries] : []) }}" class="search-clear-btn" title="Hapus filter">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
                <button type="submit" class="search-submit-btn">
                    <i class="fas fa-filter"></i> Cari
                </button>
            </form>
        </div>

        @if ($keyword)
            <div class="search-feedback">
                Hasil pencarian untuk kata kunci: <strong>"{{ $keyword }}"</strong>
                @if ($selectedSeries)
                    pada seri <strong>{{ $selectedSeries === 'kakao' ? 'MD-Cocoa Series' : 'MD-Coffee Series' }}</strong>
                @endif
                — Ditemukan {{ $cocoaMachines->count() + $coffeeMachines->count() }} model mesin.
                <a href="{{ route('products.index') }}" class="search-reset-link">Reset Pencarian</a>
            </div>
        @endif
    </div>

    {{-- Empty State jika pencarian tidak menemukan hasil --}}
    @if ($cocoaMachines->isEmpty() && $coffeeMachines->isEmpty())
        <div class="series-empty-card">
            <div class="empty-icon"><i class="fas fa-search"></i></div>
            <h3>Model Mesin Tidak Ditemukan</h3>
            <p>Tidak ada mesin yang sesuai dengan kata kunci pencarian "{{ $keyword }}". Coba gunakan kata kunci lain.</p>
            <a href="{{ route('products.index') }}" class="btn-learn-more mt-4">
                Lihat Seluruh Mesin
            </a>
        </div>
    @endif

    {{-- ============ LINI PRODUK 1: MD-COCOA SERIES ============ --}}
    @if (($selectedSeries === '' || $selectedSeries === 'kakao') && $cocoaMachines->isNotEmpty())
        <section class="product-section" id="cocoa-series">
            <div class="product-inner">
                <div class="product-top">
                    <div class="product-brand">
                        <div class="product-brand-logo"><i class="fas fa-seedling"></i></div>
                        <div class="tagline">Cocoa Processing Line</div>
                        <h3>MD-Cocoa Series</h3>
                        <p>
                            Mesin pengolahan kakao terintegrasi dari tahap fermentasi, pengeringan, roasting, hingga grinding.
                            Dirancang untuk menghasilkan cocoa mass, cocoa butter, dan cocoa powder berkualitas ekspor dengan efisiensi energi tinggi.
                        </p>

                        @if ($cocoaCategories->isNotEmpty())
                            <div class="series-cat-badges">
                                <span class="cat-label">Jenis Mesin:</span>
                                @foreach ($cocoaCategories as $cat)
                                    <span class="cat-chip">{{ $cat->name }} ({{ $cat->machines_count }})</span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="product-machine">
                        <img src="https://images.unsplash.com/photo-1516937941344-00b4ec27492b?w=800&auto=format&fit=crop"
                             alt="Mesin Kakao"
                             onerror="this.style.display='none';this.parentElement.style.background='var(--grad-primary)';this.parentElement.innerHTML='<div style=\'display:flex;align-items:center;justify-content:center;height:100%;color:white;font-size:64px;\'><i class=\'fas fa-seedling\'></i></div>';">
                    </div>
                </div>

                {{-- Accordion Model Mesin Kakao (Dynamic Database) --}}
                <div class="product-accordion">
                    @foreach ($cocoaMachines as $index => $machine)
                        <div class="accordion-item {{ $index === 0 ? 'open' : '' }}" id="machine-{{ $machine->slug }}">
                            <div class="accordion-header" onclick="toggleAccordion(this)">
                                <div class="acc-left">
                                    <div class="acc-icon-num">{{ sprintf('%02d', $index + 1) }}</div>
                                    <div class="acc-title-group">
                                        <div class="acc-main-title">{{ $machine->name }}</div>
                                        <div class="acc-badges">
                                            @if ($machine->model_code)
                                                <span class="acc-badge-code">{{ $machine->model_code }}</span>
                                            @endif
                                            @if ($machine->category)
                                                <span class="acc-badge-cat">{{ $machine->category->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="acc-chevron"><i class="fas fa-chevron-down"></i></div>
                            </div>
                            <div class="accordion-body">
                                <div class="accordion-content">
                                    <div class="accordion-info-col">
                                        <p class="acc-function-desc">
                                            {{ $machine->function ?: ($machine->short_description ?: \Illuminate\Support\Str::limit(strip_tags((string) $machine->description), 200)) }}
                                        </p>

                                        @if ($machine->capacity || $machine->power || $machine->dimension || $machine->weight)
                                            <div class="acc-specs-card">
                                                @if ($machine->capacity)
                                                    <div class="spec-cell">
                                                        <span class="spec-label"><i class="fas fa-tachometer-alt"></i> Kapasitas</span>
                                                        <span class="spec-val">{{ $machine->capacity }}</span>
                                                    </div>
                                                @endif
                                                @if ($machine->power)
                                                    <div class="spec-cell">
                                                        <span class="spec-label"><i class="fas fa-bolt"></i> Daya Listrik</span>
                                                        <span class="spec-val">{{ $machine->power }}</span>
                                                    </div>
                                                @endif
                                                @if ($machine->dimension)
                                                    <div class="spec-cell">
                                                        <span class="spec-label"><i class="fas fa-ruler-combined"></i> Dimensi</span>
                                                        <span class="spec-val">{{ $machine->dimension }}</span>
                                                    </div>
                                                @endif
                                                @if ($machine->weight)
                                                    <div class="spec-cell">
                                                        <span class="spec-label"><i class="fas fa-weight-hanging"></i> Berat</span>
                                                        <span class="spec-val">{{ $machine->weight }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        <div class="acc-cta-row">
                                            <a href="{{ route('products.show', $machine) }}" class="btn-acc-detail">
                                                Lihat Spesifikasi Lengkap <i class="fas fa-arrow-right"></i>
                                            </a>
                                            <a href="{{ route('contact') }}?mesin={{ urlencode($machine->name) }}" class="btn-acc-contact">
                                                <i class="fas fa-paper-plane"></i> Konsultasi Unit
                                            </a>
                                        </div>
                                    </div>

                                    <div class="accordion-img-col">
                                        @if ($machine->mainImageUrl())
                                            <img src="{{ $machine->mainImageUrl() }}" alt="{{ $machine->name }}" class="acc-machine-img">
                                        @else
                                            <div class="acc-machine-placeholder cocoa">
                                                <i class="fas fa-seedling"></i>
                                                <span>{{ $machine->model_code ?: 'MD-Cocoa' }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ LINI PRODUK 2: MD-COFFEE SERIES ============ --}}
    @if (($selectedSeries === '' || $selectedSeries === 'kopi') && $coffeeMachines->isNotEmpty())
        <section class="product-section alt" id="coffee-series">
            <div class="product-inner">
                <div class="product-top">
                    <div class="product-brand">
                        <div class="product-brand-logo">
                            <i class="fas fa-mug-hot"></i>
                        </div>
                        <div class="tagline">Coffee Processing Line</div>
                        <h3>MD-Coffee Series</h3>
                        <p>
                            Mesin pengolahan kopi komprehensif dari green bean cleaning, roasting, grinding, hingga packaging.
                            Teknologi roasting profile yang dapat diprogram untuk menghasilkan berbagai karakter rasa kopi specialty.
                        </p>

                        @if ($coffeeCategories->isNotEmpty())
                            <div class="series-cat-badges">
                                <span class="cat-label">Jenis Mesin:</span>
                                @foreach ($coffeeCategories as $cat)
                                    <span class="cat-chip coffee">{{ $cat->name }} ({{ $cat->machines_count }})</span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="product-machine">
                        <img src="https://images.unsplash.com/photo-1442512595331-e89e73853f31?w=800&auto=format&fit=crop"
                             alt="Mesin Kopi"
                             onerror="this.style.display='none';this.parentElement.style.background='var(--grad-primary)';this.parentElement.innerHTML='<div style=\'display:flex;align-items:center;justify-content:center;height:100%;color:white;font-size:64px;\'><i class=\'fas fa-mug-hot\'></i></div>';">
                    </div>
                </div>

                {{-- Accordion Model Mesin Kopi (Dynamic Database) --}}
                <div class="product-accordion">
                    @foreach ($coffeeMachines as $index => $machine)
                        <div class="accordion-item {{ $index === 0 ? 'open' : '' }}" id="machine-{{ $machine->slug }}">
                            <div class="accordion-header" onclick="toggleAccordion(this)">
                                <div class="acc-left">
                                    <div class="acc-icon-num coffee">{{ sprintf('%02d', $index + 1) }}</div>
                                    <div class="acc-title-group">
                                        <div class="acc-main-title">{{ $machine->name }}</div>
                                        <div class="acc-badges">
                                            @if ($machine->model_code)
                                                <span class="acc-badge-code coffee">{{ $machine->model_code }}</span>
                                            @endif
                                            @if ($machine->category)
                                                <span class="acc-badge-cat">{{ $machine->category->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="acc-chevron coffee"><i class="fas fa-chevron-down"></i></div>
                            </div>
                            <div class="accordion-body">
                                <div class="accordion-content">
                                    <div class="accordion-info-col">
                                        <p class="acc-function-desc">
                                            {{ $machine->function ?: ($machine->short_description ?: \Illuminate\Support\Str::limit(strip_tags((string) $machine->description), 200)) }}
                                        </p>

                                        @if ($machine->capacity || $machine->power || $machine->dimension || $machine->weight)
                                            <div class="acc-specs-card">
                                                @if ($machine->capacity)
                                                    <div class="spec-cell">
                                                        <span class="spec-label"><i class="fas fa-tachometer-alt"></i> Kapasitas</span>
                                                        <span class="spec-val">{{ $machine->capacity }}</span>
                                                    </div>
                                                @endif
                                                @if ($machine->power)
                                                    <div class="spec-cell">
                                                        <span class="spec-label"><i class="fas fa-bolt"></i> Daya Listrik</span>
                                                        <span class="spec-val">{{ $machine->power }}</span>
                                                    </div>
                                                @endif
                                                @if ($machine->dimension)
                                                    <div class="spec-cell">
                                                        <span class="spec-label"><i class="fas fa-ruler-combined"></i> Dimensi</span>
                                                        <span class="spec-val">{{ $machine->dimension }}</span>
                                                    </div>
                                                @endif
                                                @if ($machine->weight)
                                                    <div class="spec-cell">
                                                        <span class="spec-label"><i class="fas fa-weight-hanging"></i> Berat</span>
                                                        <span class="spec-val">{{ $machine->weight }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        <div class="acc-cta-row">
                                            <a href="{{ route('products.show', $machine) }}" class="btn-acc-detail coffee">
                                                Lihat Spesifikasi Lengkap <i class="fas fa-arrow-right"></i>
                                            </a>
                                            <a href="{{ route('contact') }}?mesin={{ urlencode($machine->name) }}" class="btn-acc-contact">
                                                <i class="fas fa-paper-plane"></i> Konsultasi Unit
                                            </a>
                                        </div>
                                    </div>

                                    <div class="accordion-img-col">
                                        @if ($machine->mainImageUrl())
                                            <img src="{{ $machine->mainImageUrl() }}" alt="{{ $machine->name }}" class="acc-machine-img">
                                        @else
                                            <div class="acc-machine-placeholder coffee">
                                                <i class="fas fa-mug-hot"></i>
                                                <span>{{ $machine->model_code ?: 'MD-Coffee' }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

@endsection
