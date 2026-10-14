@extends('layouts.public')

@section('title', 'Halaman Tidak Ditemukan — ' . ($company->name ?? 'Pabrik Mesin Pengolah Kopi'))

@section('content')
<section class="min-h-[70vh] flex items-center justify-center py-20 px-4 bg-stone-50 dark:bg-gray-900 transition-colors">
    <div class="max-w-md w-full text-center">
        <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center text-3xl shadow-inner">
            <i class="ti ti-error-404"></i>
        </div>
        <span class="inline-block px-3 py-1 text-xs font-semibold uppercase tracking-wider text-amber-700 bg-amber-100 dark:bg-amber-900/40 dark:text-amber-300 rounded-full mb-3">
            Error 404
        </span>
        <h1 class="text-3xl font-extrabold text-stone-900 dark:text-white tracking-tight mb-3">
            Halaman Tidak Ditemukan
        </h1>
        <p class="text-sm text-stone-600 dark:text-gray-400 mb-8 leading-relaxed">
            Halaman atau unit mesin yang Anda cari mungkin telah dipindahkan, dinonaktifkan, atau alamat URL yang dimasukkan kurang tepat.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ route('home') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm text-white bg-amber-600 hover:bg-amber-700 shadow-md shadow-amber-600/20 transition-all duration-200">
                <i class="ti ti-home"></i> Kembali ke Beranda
            </a>
            <a href="{{ route('products.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm text-stone-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-stone-200 dark:border-gray-700 hover:bg-stone-50 dark:hover:bg-gray-750 transition-all duration-200">
                <i class="ti ti-box"></i> Lihat Katalog Mesin
            </a>
        </div>
    </div>
</section>
@endsection
