@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Ringkasan konten website company profile')

@section('content')
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ([
            ['label' => 'Jenis Mesin', 'value' => $stats['kategori'], 'icon' => '🗂️', 'route' => 'admin.categories.index'],
            ['label' => 'Model Mesin', 'value' => $stats['mesin'], 'icon' => '⚙️', 'route' => 'admin.machines.index'],
            ['label' => 'Katalog', 'value' => $stats['katalog'], 'icon' => '📕', 'route' => 'admin.catalogs.index'],
            ['label' => 'Foto Produk', 'value' => $stats['foto'], 'icon' => '🖼️', 'route' => 'admin.machines.index'],
            ['label' => 'Total Pesan', 'value' => $stats['pesan'], 'icon' => '✉️', 'route' => 'admin.messages.index'],
            ['label' => 'Pesan Belum Dibaca', 'value' => $stats['pesan_baru'], 'icon' => '🔔', 'route' => 'admin.messages.index'],
        ] as $card)
            <a href="{{ route($card['route']) }}"
               class="flex items-center gap-4 rounded-2xl border border-stone-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-brand-300 hover:shadow-md">
                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-xl">{{ $card['icon'] }}</span>
                <span>
                    <span class="block text-2xl font-extrabold text-stone-900">{{ $card['value'] }}</span>
                    <span class="block text-xs font-semibold uppercase tracking-wide text-stone-400">{{ $card['label'] }}</span>
                </span>
            </a>
        @endforeach
    </div>

    <div class="mt-6 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
        <h2 class="text-base font-extrabold text-stone-900">Aksi cepat</h2>

        <div class="mt-4 flex flex-wrap gap-3">
            <a href="{{ route('admin.machines.create') }}"
               class="rounded-xl bg-brand-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-brand-800">
                + Tambah Model Mesin
            </a>
            <a href="{{ route('admin.categories.create') }}"
               class="rounded-xl border border-stone-300 px-4 py-2.5 text-sm font-bold text-stone-700 transition hover:bg-stone-50">
                + Tambah Jenis Mesin
            </a>
            <a href="{{ route('admin.catalogs.create') }}"
               class="rounded-xl border border-stone-300 px-4 py-2.5 text-sm font-bold text-stone-700 transition hover:bg-stone-50">
                + Tambah Katalog
            </a>
            <a href="{{ route('admin.profile.edit') }}"
               class="rounded-xl border border-stone-300 px-4 py-2.5 text-sm font-bold text-stone-700 transition hover:bg-stone-50">
                Edit Profil Perusahaan
            </a>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        @include('admin.partials.recent-machines')
    </div>
@endsection
