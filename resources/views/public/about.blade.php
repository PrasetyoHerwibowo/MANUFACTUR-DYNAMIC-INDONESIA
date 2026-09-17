@extends('layouts.public')

@section('title', 'Tentang Kami — '.$company->name)

@section('content')
    <x-page-header
        title="Tentang Kami"
        :subtitle="$company->tagline"
        :crumbs="[['label' => 'Beranda', 'url' => route('home')], ['label' => 'Tentang Kami']]"
    />

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
        <div class="grid gap-12 lg:grid-cols-2 lg:items-start">
            <div>
                <span class="text-xs font-bold uppercase tracking-[0.2em] text-brand-500">Profil Perusahaan</span>
                <h2 class="mt-3 text-2xl font-extrabold tracking-tight text-stone-900 sm:text-3xl">
                    {{ $company->name }}
                </h2>

                <div class="mt-6 space-y-4 text-sm leading-relaxed text-stone-600">
                    @if ($company->about)
                        @foreach (preg_split('/\r\n|\r|\n/', $company->about) as $paragraph)
                            @if (trim($paragraph) !== '')
                                <p>{{ trim($paragraph) }}</p>
                            @endif
                        @endforeach
                    @else
                        <p>
                            {{ $company->name }} adalah perusahaan manufaktur yang berfokus pada perancangan dan
                            produksi mesin pengolahan kopi serta kakao. Produk kami digunakan oleh perkebunan,
                            koperasi, pabrik pengolahan, dan UMKM di berbagai daerah di Indonesia.
                        </p>
                        <p>
                            Setiap mesin dirancang dengan memperhatikan kapasitas produksi, kemudahan perawatan,
                            serta kebersihan hasil olahan sehingga siap dipakai untuk kebutuhan industri pangan.
                        </p>
                    @endif
                </div>

                <dl class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3">
                    @foreach (array_filter([
                        'Berdiri Sejak' => $company->founded_year,
                        'Jumlah Karyawan' => $company->employees,
                        'Wilayah Pasar' => $company->export_countries,
                        'Lokasi' => $company->city,
                    ]) as $label => $value)
                        <div class="rounded-xl border border-stone-200 bg-stone-50 p-4">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-stone-400">{{ $label }}</dt>
                            <dd class="mt-1 text-sm font-bold text-stone-800">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            <div class="overflow-hidden rounded-3xl border border-stone-200 shadow-xl">
                <img src="{{ $company->aboutUrl() }}" alt="Fasilitas produksi {{ $company->name }}" class="aspect-[4/3] w-full object-cover">
            </div>
        </div>
    </section>

    @if ($company->vision || ! empty($company->missionList()))
        <section class="bg-stone-50 py-14">
            <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
                @if ($company->vision)
                    <div class="rounded-2xl border border-stone-200 bg-white p-7 shadow-sm">
                        <h3 class="text-lg font-extrabold text-stone-900">Visi</h3>
                        <p class="mt-4 text-sm leading-relaxed text-stone-600">{{ $company->vision }}</p>
                    </div>
                @endif

                @if (! empty($company->missionList()))
                    <div class="rounded-2xl border border-stone-200 bg-white p-7 shadow-sm">
                        <h3 class="text-lg font-extrabold text-stone-900">Misi</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-relaxed text-stone-600">
                            @foreach ($company->missionList() as $mission)
                                <li class="flex gap-3">
                                    <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-accent-100 text-[11px] font-black text-accent-700">✓</span>
                                    <span>{{ $mission }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </section>
    @endif

    @if ($categories->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <h2 class="text-xl font-extrabold text-stone-900">Jenis mesin yang kami kerjakan</h2>

            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($categories as $category)
                    <a href="{{ route('products.index', ['kategori' => $category->slug]) }}"
                       class="rounded-xl border border-stone-200 bg-white p-5 transition hover:border-brand-300 hover:shadow-md">
                        <p class="text-sm font-bold text-stone-800">{{ $category->name }}</p>
                        <p class="mt-1 text-xs text-stone-500">{{ $category->machines_count }} model mesin</p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <div class="rounded-3xl bg-gradient-to-br from-brand-800 to-brand-600 px-6 py-12 text-center sm:px-12">
            <h2 class="text-2xl font-extrabold text-white">Ingin bekerja sama dengan kami?</h2>
            <p class="mx-auto mt-3 max-w-2xl text-sm text-brand-50">
                Kirimkan kebutuhan mesin Anda, tim kami akan membantu memilih spesifikasi yang paling sesuai.
            </p>
            <a href="{{ route('contact') }}" class="mt-7 inline-block rounded-xl bg-white px-6 py-3 text-sm font-bold text-brand-800 transition hover:bg-brand-50">
                Hubungi Kami Sekarang
            </a>
        </div>
    </section>
@endsection
