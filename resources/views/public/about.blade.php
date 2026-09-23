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
                <span class="text-xs font-bold uppercase tracking-[0.2em] text-red-500 dark:text-red-400">Profil Perusahaan</span>
                <h2 class="mt-3 text-2xl font-extrabold tracking-tight text-stone-900 dark:text-white sm:text-3xl">
                    {{ $company->name }}
                </h2>

                <div class="mt-6 space-y-4 text-sm leading-relaxed text-stone-600 dark:text-gray-300">
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
                        <div class="rounded-xl border border-stone-200 dark:border-gray-700 bg-stone-50 dark:bg-gray-800/40 p-4">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-stone-400 dark:text-gray-500">{{ $label }}</dt>
                            <dd class="mt-1 text-sm font-bold text-stone-800 dark:text-white">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            <div class="overflow-hidden rounded-3xl border border-stone-200 dark:border-gray-700 shadow-xl">
                <img src="{{ $company->aboutUrl() }}" alt="Fasilitas produksi {{ $company->name }}" class="aspect-[4/3] w-full object-cover">
            </div>
        </div>
    </section>

    @if ($company->vision || ! empty($company->missionList()))
        <section class="bg-stone-50 dark:bg-gray-800/40 py-14">
            <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
                @if ($company->vision)
                    <div class="rounded-2xl border border-stone-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-7 shadow-sm">
                        <h3 class="text-lg font-extrabold text-stone-900 dark:text-white">Visi</h3>
                        <p class="mt-4 text-sm leading-relaxed text-stone-600 dark:text-gray-300">{{ $company->vision }}</p>
                    </div>
                @endif

                @if (! empty($company->missionList()))
                    <div class="rounded-2xl border border-stone-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-7 shadow-sm">
                        <h3 class="text-lg font-extrabold text-stone-900 dark:text-white">Misi</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-relaxed text-stone-600 dark:text-gray-300">
                            @foreach ($company->missionList() as $mission)
                                <li class="flex gap-3">
                                    <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-red-100 text-[11px] font-black text-red-700 dark:bg-red-700/15 dark:text-red-300">✓</span>
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
            <h2 class="text-xl font-extrabold text-stone-900 dark:text-white">Jenis mesin yang kami kerjakan</h2>

            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($categories as $category)
                    <a href="{{ route('products.index', ['kategori' => $category->slug]) }}"
                       class="rounded-xl border border-stone-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 transition hover:border-red-300 dark:hover:border-red-700 hover:shadow-md">
                        <p class="text-sm font-bold text-stone-800 dark:text-white">{{ $category->name }}</p>
                        <p class="mt-1 text-xs text-stone-500 dark:text-gray-400">{{ $category->machines_count }} model mesin</p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <div class="rounded-3xl bg-gradient-to-br from-red-800 to-red-600 px-6 py-12 text-center sm:px-12">
            <h2 class="text-2xl font-extrabold text-white">Ingin bekerja sama dengan kami?</h2>
            <p class="mx-auto mt-3 max-w-2xl text-sm text-red-50">
                Kirimkan kebutuhan mesin Anda, tim kami akan membantu memilih spesifikasi yang paling sesuai.
            </p>
            <a href="{{ route('contact') }}" class="mt-7 inline-block rounded-xl bg-white dark:bg-gray-800 px-6 py-3 text-sm font-bold text-red-800 dark:text-red-300 transition hover:bg-red-50 dark:hover:bg-red-500/10">
                Hubungi Kami Sekarang
            </a>
        </div>
    </section>
@endsection
