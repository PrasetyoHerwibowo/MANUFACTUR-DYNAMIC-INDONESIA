@extends('layouts.public')

@section('title', $company->name.' — '.$company->tagline)

@section('content')
    {{-- ============ HERO ============ --}}
    <section class="relative overflow-hidden bg-brand-950 text-white">
        <div class="absolute inset-0">
            <img src="{{ $company->heroUrl() }}" alt="" class="h-full w-full object-cover opacity-25">
            <div class="absolute inset-0 bg-gradient-to-br from-brand-950 via-brand-950/95 to-brand-800/70"></div>
        </div>

        <div class="relative mx-auto grid max-w-7xl gap-12 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:items-center lg:px-8 lg:py-24">
            <div>
                <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-brand-100">
                    Produsen Mesin Kopi &amp; Kakao
                </span>

                <h1 class="mt-6 text-3xl font-extrabold leading-tight tracking-tight sm:text-4xl lg:text-5xl">
                    {{ $company->name }}
                </h1>

                <p class="mt-5 max-w-xl text-base leading-relaxed text-brand-100 sm:text-lg">
                    {{ $company->tagline }}
                </p>

                <ul class="mt-7 space-y-3 text-sm text-brand-100">
                    @foreach ([
                        'Perancangan mesin sesuai kapasitas produksi Anda',
                        'Rangka kuat, material food grade, dan mudah dirawat',
                        'Dukungan instalasi, pelatihan operator, dan suku cadang',
                    ] as $point)
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-accent-500 text-[11px] font-black text-white">✓</span>
                            <span>{{ $point }}</span>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-9 flex flex-wrap gap-3">
                    <a href="{{ route('products.index') }}"
                       class="rounded-xl bg-brand-500 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-brand-950/30 transition hover:bg-brand-400">
                        Lihat Produk &amp; Mesin
                    </a>
                    <a href="{{ route('contact') }}"
                       class="rounded-xl border border-white/25 bg-white/5 px-6 py-3 text-sm font-bold text-white transition hover:bg-white/15">
                        Minta Penawaran
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 lg:gap-5">
                @foreach ([
                    ['label' => 'Jenis Mesin', 'value' => $stats['kategori']],
                    ['label' => 'Model Mesin', 'value' => $stats['mesin']],
                    ['label' => 'Katalog Produk', 'value' => $stats['katalog']],
                    ['label' => 'Foto Produk', 'value' => $stats['foto']],
                ] as $stat)
                    <div class="rounded-2xl border border-white/15 bg-white/5 p-5 backdrop-blur">
                        <p class="text-3xl font-extrabold text-white">{{ $stat['value'] }}+</p>
                        <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-brand-100">{{ $stat['label'] }}</p>
                    </div>
                @endforeach

                <div class="col-span-2 rounded-2xl border border-white/15 bg-white/5 p-5 backdrop-blur">
                    <p class="text-xs font-bold uppercase tracking-wide text-brand-100">Pengalaman Industri</p>
                    <p class="mt-1 text-sm leading-relaxed text-brand-100/90">
                        Dipercaya oleh perkebunan, koperasi, dan UMKM pengolahan kopi &amp; kakao di seluruh Indonesia
                        @if ($company->export_countries)
                            hingga pasar {{ $company->export_countries }}
                        @endif.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ TENTANG SINGKAT ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
        <div class="grid items-center gap-12 lg:grid-cols-2">
            <div class="relative">
                <div class="overflow-hidden rounded-3xl border border-stone-200 shadow-xl">
                    <img src="{{ $company->aboutUrl() }}" alt="Pabrik {{ $company->name }}" class="aspect-[4/3] w-full object-cover">
                </div>

                <div class="mt-5 grid grid-cols-3 gap-3">
                    @foreach ([
                        ['title' => 'Presisi', 'desc' => 'Produksi terukur'],
                        ['title' => 'Aman', 'desc' => 'Material food grade'],
                        ['title' => 'Bergaransi', 'desc' => 'Layanan purna jual'],
                    ] as $item)
                        <div class="rounded-xl border border-stone-200 bg-stone-50 p-3 text-center">
                            <p class="text-sm font-bold text-brand-700">{{ $item['title'] }}</p>
                            <p class="mt-0.5 text-[11px] text-stone-500">{{ $item['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div>
                <span class="text-xs font-bold uppercase tracking-[0.2em] text-brand-500">Tentang Kami</span>
                <h2 class="mt-3 text-2xl font-extrabold tracking-tight text-stone-900 sm:text-3xl">
                    Mitra terpercaya pengolahan hasil perkebunan
                </h2>

                <div class="mt-5 space-y-4 text-sm leading-relaxed text-stone-600">
                    <p>
                        {{ $company->about
                            ? \Illuminate\Support\Str::limit(strip_tags($company->about), 520)
                            : $company->name.' merupakan perusahaan manufaktur yang merancang dan memproduksi mesin pengolahan kopi dan kakao, mulai dari mesin pengupas, fermentasi, pengering, hingga pengemasan.' }}
                    </p>

                    @if ($company->vision)
                        <p><span class="font-bold text-stone-700">Visi:</span> {{ \Illuminate\Support\Str::limit(strip_tags($company->vision), 200) }}</p>
                    @endif
                </div>

                <dl class="mt-7 grid grid-cols-2 gap-4 text-sm">
                    @if ($company->founded_year)
                        <div class="rounded-xl border border-stone-200 p-4">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-stone-400">Berdiri Sejak</dt>
                            <dd class="mt-1 font-bold text-stone-800">{{ $company->founded_year }}</dd>
                        </div>
                    @endif
                    @if ($company->employees)
                        <div class="rounded-xl border border-stone-200 p-4">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-stone-400">Jumlah Karyawan</dt>
                            <dd class="mt-1 font-bold text-stone-800">{{ $company->employees }}</dd>
                        </div>
                    @endif
                </dl>

                <a href="{{ route('about') }}" class="mt-7 inline-flex items-center gap-2 rounded-xl bg-brand-700 px-5 py-3 text-sm font-bold text-white transition hover:bg-brand-800">
                    Selengkapnya tentang kami
                    <span aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </div>
    </section>

    {{-- ============ JENIS MESIN ============ --}}
    @if ($categories->isNotEmpty())
        <section class="bg-stone-50 py-16 lg:py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-[0.2em] text-brand-500">Jenis Mesin</span>
                        <h2 class="mt-3 text-2xl font-extrabold tracking-tight text-stone-900 sm:text-3xl">
                            Kategori mesin yang kami produksi
                        </h2>
                    </div>

                    <a href="{{ route('products.index') }}" class="text-sm font-bold text-brand-700 hover:text-brand-900">
                        Semua produk &rarr;
                    </a>
                </div>

                <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($categories as $category)
                        <x-category-card :category="$category" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ MODEL MESIN UNGGULAN ============ --}}
    @if ($featured->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-[0.2em] text-brand-500">Model Mesin</span>
                    <h2 class="mt-3 text-2xl font-extrabold tracking-tight text-stone-900 sm:text-3xl">
                        Mesin unggulan pilihan pelanggan
                    </h2>
                </div>

                <a href="{{ route('products.index') }}" class="text-sm font-bold text-brand-700 hover:text-brand-900">
                    Lihat semua model &rarr;
                </a>
            </div>

            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($featured as $machine)
                    <x-machine-card :machine="$machine" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============ KATALOG ============ --}}
    @if ($catalogs->isNotEmpty())
        <section class="bg-stone-50 py-16 lg:py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-[0.2em] text-brand-500">Katalog</span>
                        <h2 class="mt-3 text-2xl font-extrabold tracking-tight text-stone-900 sm:text-3xl">
                            Unduh dan lihat katalog produk kami
                        </h2>
                    </div>

                    <a href="{{ route('catalogs.index') }}" class="text-sm font-bold text-brand-700 hover:text-brand-900">
                        Semua katalog &rarr;
                    </a>
                </div>

                <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($catalogs as $catalog)
                        <x-catalog-card :catalog="$catalog" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ AJAKAN KONTAK ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
        <div class="overflow-hidden rounded-3xl bg-gradient-to-br from-brand-800 via-brand-700 to-brand-500 px-6 py-12 text-center shadow-xl sm:px-12">
            <h2 class="text-2xl font-extrabold tracking-tight text-white sm:text-3xl">
                Butuh mesin sesuai kapasitas produksi Anda?
            </h2>
            <p class="mx-auto mt-4 max-w-2xl text-sm leading-relaxed text-brand-50">
                Tim engineering kami siap membantu memilih model mesin, menghitung kapasitas, hingga merancang
                lini produksi kopi dan kakao yang paling efisien untuk usaha Anda.
            </p>

            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ route('contact') }}" class="rounded-xl bg-white px-6 py-3 text-sm font-bold text-brand-800 shadow-lg transition hover:bg-brand-50">
                    Konsultasi Gratis
                </a>

                @php $wa = preg_replace('/[^0-9]/', '', (string) $company->whatsapp); @endphp
                @if ($wa)
                    <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener"
                       class="rounded-xl border border-white/40 px-6 py-3 text-sm font-bold text-white transition hover:bg-white/10">
                        WhatsApp Kami
                    </a>
                @endif
            </div>
        </div>
    </section>
@endsection
