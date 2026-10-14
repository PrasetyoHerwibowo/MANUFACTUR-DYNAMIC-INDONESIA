@extends('layouts.public')

@section('title', 'Terjadi Gangguan Sistem — ' . ($company->name ?? 'Pabrik Mesin Pengolah Kopi'))

@section('content')
<section class="min-h-[70vh] flex items-center justify-center py-20 px-4 bg-stone-50 dark:bg-gray-900 transition-colors">
    <div class="max-w-md w-full text-center">
        <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 flex items-center justify-center text-3xl shadow-inner">
            <i class="ti ti-server-off"></i>
        </div>
        <span class="inline-block px-3 py-1 text-xs font-semibold uppercase tracking-wider text-rose-700 bg-rose-100 dark:bg-rose-900/40 dark:text-rose-300 rounded-full mb-3">
            Error 500
        </span>
        <h1 class="text-3xl font-extrabold text-stone-900 dark:text-white tracking-tight mb-3">
            Terjadi Kendala Sistem
        </h1>
        <p class="text-sm text-stone-600 dark:text-gray-400 mb-8 leading-relaxed">
            Sistem kami sedang mengalami kendala sementara saat menghubungkan ke layanan basis data atau penyimpanan. Tim teknis telah menerima log kendala ini dan sedang menanganinya.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <button onclick="window.location.reload()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm text-white bg-stone-800 hover:bg-stone-900 dark:bg-stone-700 dark:hover:bg-stone-600 shadow-md transition-all duration-200">
                <i class="ti ti-refresh"></i> Coba Muat Ulang
            </button>
            <a href="{{ route('home') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm text-stone-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-stone-200 dark:border-gray-700 hover:bg-stone-50 dark:hover:bg-gray-750 transition-all duration-200">
                <i class="ti ti-home"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</section>
@endsection
