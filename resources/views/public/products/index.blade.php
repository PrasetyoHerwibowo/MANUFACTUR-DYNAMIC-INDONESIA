@extends('layouts.public')

@section('title', 'Produk & Mesin — '.$company->name)
@section('meta_description', 'Daftar jenis dan model mesin pengolahan kopi serta kakao yang diproduksi oleh '.$company->name.'.')

@section('content')
    <x-page-header
        title="Produk &amp; Mesin"
        subtitle="Telusuri jenis mesin, model, dan fungsi mesin yang kami produksi untuk kebutuhan pengolahan kopi dan kakao Anda."
        :crumbs="[['label' => 'Beranda', 'url' => route('home')], ['label' => 'Produk & Mesin']]"
    />

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
        {{-- Filter --}}
        <form method="GET" action="{{ route('products.index') }}"
              class="grid gap-4 rounded-2xl border border-stone-200 bg-stone-50 p-5 sm:grid-cols-2 lg:grid-cols-4">
            <div class="lg:col-span-2">
                <label for="q" class="mb-1.5 block text-sm font-semibold text-stone-700">Cari mesin</label>
                <input type="text" name="q" id="q" value="{{ request('q') }}"
                       placeholder="Nama mesin, kode model, atau fungsi..."
                       class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-800 shadow-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
            </div>

            <div>
                <label for="kategori" class="mb-1.5 block text-sm font-semibold text-stone-700">Jenis mesin</label>
                <select name="kategori" id="kategori"
                        class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-800 shadow-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
                    <option value="">Semua jenis mesin</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->slug }}" @selected(request('kategori') === $category->slug)>
                            {{ $category->name }} ({{ $category->machines_count }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit"
                        class="flex-1 rounded-xl bg-brand-700 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-brand-800">
                    Terapkan
                </button>
                @if (request()->filled('q') || request()->filled('kategori'))
                    <a href="{{ route('products.index') }}"
                       class="rounded-xl border border-stone-300 px-4 py-2.5 text-sm font-semibold text-stone-600 transition hover:bg-white">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <div class="mt-8 flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-stone-500">
                Menampilkan <span class="font-bold text-stone-700">{{ $machines->count() }}</span> dari
                <span class="font-bold text-stone-700">{{ $machines->total() }}</span> model mesin
                @if ($activeCategory)
                    pada kategori <span class="font-bold text-brand-700">{{ $activeCategory->name }}</span>
                @endif
            </p>

            @if ($activeCategory && $activeCategory->description)
                <p class="max-w-xl text-xs leading-relaxed text-stone-500">{{ $activeCategory->description }}</p>
            @endif
        </div>

        @if ($machines->isNotEmpty())
            <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($machines as $machine)
                    <x-machine-card :machine="$machine" />
                @endforeach
            </div>

            <div class="mt-10">
                {{ $machines->links() }}
            </div>
        @else
            <div class="mt-8 rounded-2xl border border-dashed border-stone-300 bg-stone-50 px-6 py-16 text-center">
                <p class="text-lg font-bold text-stone-700">Mesin tidak ditemukan</p>
                <p class="mt-2 text-sm text-stone-500">
                    Coba ubah kata kunci pencarian atau pilih jenis mesin lain.
                </p>
                <a href="{{ route('products.index') }}" class="mt-6 inline-block rounded-xl bg-brand-700 px-5 py-2.5 text-sm font-bold text-white">
                    Lihat semua mesin
                </a>
            </div>
        @endif
    </section>
@endsection
