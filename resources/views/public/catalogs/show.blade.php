@extends('layouts.public')

@section('title', $catalog->title.' — '.$company->name)
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($catalog->description ?: $catalog->title), 150))

@section('content')
    <x-page-header
        :title="$catalog->title"
        :subtitle="$catalog->description"
        :crumbs="array_values(array_filter([
            ['label' => 'Beranda', 'url' => route('home')],
            ['label' => 'Katalog', 'url' => route('catalogs.index')],
            $catalog->year ? ['label' => 'Edisi '.$catalog->year] : null,
            ['label' => $catalog->title],
        ]))"
    />

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
        <div class="grid gap-10 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="overflow-hidden rounded-3xl border border-stone-200 dark:border-gray-700 bg-stone-100 dark:bg-gray-700 shadow-sm">
                    <img src="{{ $catalog->photo }}" alt="{{ $catalog->title }}" class="aspect-[4/3] w-full object-cover">
                </div>

                @if ($catalog->images->isNotEmpty())
                    <h2 class="mt-12 text-xl font-extrabold text-stone-900 dark:text-white">Isi katalog</h2>
                    <p class="mt-2 text-sm text-stone-500 dark:text-gray-400">
                        {{ $catalog->images->count() }} foto halaman katalog. Klik foto untuk melihat ukuran penuh.
                    </p>

                    <div class="mt-6 grid gap-5 sm:grid-cols-2">
                        @foreach ($catalog->images as $image)
                            <figure class="overflow-hidden rounded-2xl border border-stone-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
                                <a href="{{ $image->url() }}" target="_blank" rel="noopener" class="group block bg-stone-100 dark:bg-gray-700">
                                    <img src="{{ $image->url() }}" alt="{{ $image->caption ?: $catalog->title }}" loading="lazy"
                                         class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-105">
                                </a>

                                @if ($image->caption)
                                    <figcaption class="px-4 py-3 text-xs font-medium text-stone-500 dark:text-gray-400">{{ $image->caption }}</figcaption>
                                @endif
                            </figure>
                        @endforeach
                    </div>
                @endif
            </div>

            <aside class="space-y-5">
                <div class="rounded-2xl border border-stone-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow-sm">
                    <h2 class="text-base font-extrabold text-stone-900 dark:text-white">Informasi katalog</h2>

                    <dl class="mt-4 space-y-3 text-sm">
                        @if ($catalog->year)
                            <div class="flex justify-between gap-4 border-b border-dashed border-stone-200 dark:border-gray-700 pb-2">
                                <dt class="text-stone-500 dark:text-gray-400">Edisi</dt>
                                <dd class="font-semibold text-stone-800 dark:text-white">{{ $catalog->year }}</dd>
                            </div>
                        @endif

                        <div class="flex justify-between gap-4 border-b border-dashed border-stone-200 dark:border-gray-700 pb-2">
                            <dt class="text-stone-500 dark:text-gray-400">Jumlah halaman</dt>
                            <dd class="font-semibold text-stone-800 dark:text-white">{{ $catalog->images->count() }} foto</dd>
                        </div>

                        <div class="flex justify-between gap-4">
                            <dt class="text-stone-500 dark:text-gray-400">File PDF</dt>
                            <dd class="font-semibold text-stone-800 dark:text-white">{{ $catalog->pdf_file ? 'Tersedia' : 'Belum tersedia' }}</dd>
                        </div>
                    </dl>

                    @if ($catalog->pdf_file)
                        <a href="{{ \App\Support\ImageUploader::url($catalog->pdf_file) }}" target="_blank" rel="noopener"
                           class="mt-6 block rounded-xl bg-gradient-to-r from-red-600 to-red-700 px-5 py-3 text-center text-sm font-bold text-white transition hover:from-red-700 hover:to-red-800">
                            Unduh Katalog (PDF)
                        </a>
                    @endif

                    <a href="{{ route('contact') }}"
                       class="mt-3 block rounded-xl border border-stone-300 dark:border-gray-600 px-5 py-3 text-center text-sm font-bold text-stone-700 dark:text-gray-200 transition hover:bg-stone-50 dark:hover:bg-gray-700">
                        Tanya Sales
                    </a>
                </div>

                <div class="rounded-2xl border border-stone-200 dark:border-gray-700 bg-stone-50 dark:bg-gray-800/40 p-6">
                    <p class="text-xs font-bold uppercase tracking-wide text-stone-400 dark:text-gray-500">Katalog lainnya</p>
                    <a href="{{ route('catalogs.index') }}" class="mt-3 inline-block text-sm font-bold text-red-700 dark:text-red-400">
                        Lihat semua katalog &rarr;
                    </a>
                </div>
            </aside>
        </div>
    </section>
@endsection
