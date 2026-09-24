@extends('layouts.admin')

@php $isEdit = $category->exists; @endphp

@section('title', $isEdit ? 'Edit Jenis Mesin' : 'Tambah Jenis Mesin')
@section('page_title', $isEdit ? 'Edit Jenis Mesin' : 'Tambah Jenis Mesin')
@section('page_subtitle', $isEdit ? 'Perbarui data jenis mesin' : 'Buat kategori/ jenis mesin baru')

@section('content')
    <div class="max-w-3xl">
        <form method="POST"
              action="{{ $isEdit ? route('admin.categories.update', $category) : route('admin.categories.store') }}"
            class="space-y-5 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-7 dark:border-slate-700 dark:bg-slate-900"
              @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <x-admin.input
            name="name"
                label="Nama jenis mesin"
                :value="$category->name"
                required
                maxlength="100"
                pattern="[A-Za-z][A-Za-z &amp;-]*"
                title="Hanya huruf, spasi, tanda hubung (-), dan tanda &amp;."
                hint="Hanya huruf, spasi, tanda hubung (-), dan tanda &. Maksimal 100 karakter. Karakter lain otomatis diabaikan saat diketik."
                data-input-filter="letter"
                placeholder="Contoh: Mesin Pengolahan Kopi"
            />

            <x-admin.input
                name="tagline"
                label="Tagline singkat"
                :value="$category->tagline"
                maxlength="50"
                pattern="[A-Za-z0-9][A-Za-z0-9 ]*"
                title="Hanya huruf dan angka."
                hint="Hanya huruf dan angka. Maksimal 50 karakter. Karakter lain otomatis diabaikan saat diketik."
                data-input-filter="alnum"
                placeholder="Contoh: Dari cherry hingga green bean"
            />

            <x-admin.textarea
                name="description"
                label="Deskripsi"
                :value="$category->description"
                :rows="3"
                maxlength="150"
                hint="Huruf, angka, dan tanda baca diperbolehkan. Maksimal 150 karakter. Karakter khusus pembentuk tag HTML otomatis diabaikan saat diketik."
                data-input-filter="text"
            />

            <div class="flex flex-wrap items-center gap-3 border-t border-stone-200 pt-5 dark:border-slate-700">
                <button type="submit"
                        class="rounded-xl bg-brand-700 px-6 py-2.5 text-sm font-bold text-white transition hover:bg-brand-800">
                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Jenis Mesin' }}
                </button>

                <a href="{{ route('admin.categories.index') }}"
                class="rounded-xl border border-stone-300 px-5 py-2.5 text-sm font-bold text-stone-600 transition hover:bg-stone-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
                    Batal
                </a>

                @if ($isEdit)
                    <a href="{{ route('products.index', ['kategori' => $category->slug]) }}" target="_blank"
                       class="text-sm font-bold text-brand-700 hover:text-brand-900">
                        Lihat di website &rarr;
                    </a>
                @endif
            </div>
        </form>
    </div>
@endsection
