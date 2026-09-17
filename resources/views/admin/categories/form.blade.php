@extends('layouts.admin')

@php $isEdit = $category->exists; @endphp

@section('title', $isEdit ? 'Edit Jenis Mesin' : 'Tambah Jenis Mesin')
@section('page_title', $isEdit ? 'Edit Jenis Mesin' : 'Tambah Jenis Mesin')
@section('page_subtitle', $isEdit ? 'Perbarui data jenis mesin dan fotonya' : 'Buat kategori/ jenis mesin baru')

@section('content')
    <div class="max-w-3xl">
        <form method="POST"
              action="{{ $isEdit ? route('admin.categories.update', $category) : route('admin.categories.store') }}"
              enctype="multipart/form-data"
              class="space-y-5 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-7">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <x-admin.input name="name" label="Nama jenis mesin" :value="$category->name" required
                           placeholder="Contoh: Mesin Pengolahan Kopi" />

            <x-admin.input name="tagline" label="Tagline singkat" :value="$category->tagline"
                           placeholder="Contoh: Dari cherry hingga green bean" />

            <x-admin.textarea name="description" label="Deskripsi" :value="$category->description" :rows="4"
                              hint="Penjelasan singkat mengenai jenis mesin ini." />

            <x-admin.file name="image" label="Foto jenis mesin" :current="$category->imageUrl()"
                          hint="Format JPG, PNG, atau WEBP. Maksimal 4 MB. Disarankan rasio 4:3." />

            <div class="grid gap-5 sm:grid-cols-2">
                <x-admin.input name="sort_order" label="Urutan tampil" type="number" :value="$category->sort_order"
                               hint="Angka lebih kecil tampil lebih dahulu." />

                <div class="flex items-end">
                    <x-admin.checkbox name="is_active" label="Tampilkan di website" :checked="$category->is_active"
                                      hint="Jika dimatikan, jenis mesin tidak muncul di halaman publik." class="w-full" />
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 border-t border-stone-200 pt-5">
                <button type="submit"
                        class="rounded-xl bg-brand-700 px-6 py-2.5 text-sm font-bold text-white transition hover:bg-brand-800">
                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Jenis Mesin' }}
                </button>

                <a href="{{ route('admin.categories.index') }}"
                   class="rounded-xl border border-stone-300 px-5 py-2.5 text-sm font-bold text-stone-600 transition hover:bg-stone-50">
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
