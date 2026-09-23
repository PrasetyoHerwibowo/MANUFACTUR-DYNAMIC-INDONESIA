@extends('layouts.public')

@section('title', 'Katalog Produk — '.$company->name)
@section('meta_description', 'Katalog produk mesin pengolahan kopi dan kakao dari '.$company->name.'.')

@section('content')
    <x-page-header
        title="Katalog Produk"
        subtitle="Kumpulan katalog resmi kami yang memuat foto, spesifikasi, dan penjelasan setiap model mesin pengolahan kopi dan kakao."
        :crumbs="[['label' => 'Beranda', 'url' => route('home')], ['label' => 'Katalog']]"
    />

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
        @if ($catalogs->isNotEmpty())
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($catalogs as $catalog)
                    <x-catalog-card :catalog="$catalog" />
                @endforeach
            </div>

            <div class="mt-10">
                {{ $catalogs->links() }}
            </div>
        @else
            <div class="rounded-2xl border border-dashed border-stone-300 dark:border-gray-600 bg-stone-50 dark:bg-gray-800/40 px-6 py-16 text-center">
                <p class="text-lg font-bold text-stone-700 dark:text-gray-200">Belum ada katalog</p>
                <p class="mt-2 text-sm text-stone-500 dark:text-gray-400">
                    Katalog produk sedang kami siapkan. Silakan hubungi kami untuk informasi produk terbaru.
                </p>
                <a href="{{ route('contact') }}" class="mt-6 inline-block rounded-xl bg-gradient-to-r from-red-600 to-red-700 px-5 py-2.5 text-sm font-bold text-white">
                    Hubungi Kami
                </a>
            </div>
        @endif
    </section>
@endsection
