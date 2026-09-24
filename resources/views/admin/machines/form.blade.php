@extends('layouts.admin')

@php $isEdit = $machine->exists; @endphp

@section('title', $isEdit ? 'Edit Model Mesin' : 'Tambah Model Mesin')
@section('page_title', $isEdit ? 'Edit Model Mesin' : 'Tambah Model Mesin')
@section('page_subtitle', $isEdit ? 'Perbarui data model mesin' : 'Lengkapi data model mesin baru')

@section('content')
    <div class="max-w-4xl">
        <form method="POST"
              action="{{ $isEdit ? route('admin.machines.update', $machine) : route('admin.machines.store') }}"
              enctype="multipart/form-data"
              class="space-y-6">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            {{-- Data utama --}}
            <div class="space-y-5 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-7 dark:border-slate-700 dark:bg-slate-900">
                <h2 class="text-base font-extrabold text-stone-900 dark:text-white">Data utama</h2>

                <x-admin.select name="category_id" label="Jenis mesin" :options="$categories->pluck('name', 'id')"
                                :selected="$machine->category_id" placeholder="— Pilih jenis mesin —" required />

                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.input name="name" label="Nama model mesin" :value="$machine->name" required
                                   maxlength="100"
                                   placeholder="Contoh: Mesin Pulper Kopi Basah"
                                   hint="Maksimal 100 karakter. Tidak boleh sama dengan model mesin lain pada jenis mesin yang sama." />

                    <x-admin.input name="model_code" label="Kode / tipe model" :value="$machine->model_code"
                                   maxlength="50"
                                   placeholder="Contoh: MDI-PL200"
                                   hint="Maksimal 50 karakter. Tidak boleh sama dengan model mesin lain pada jenis mesin yang sama." />
                </div>

                <x-admin.textarea name="function" label="Fungsi mesin" :value="$machine->function" :rows="3"
                                  maxlength="100"
                                  hint="Maksimal 100 karakter. Jelaskan kegunaan mesin ini dalam proses pengolahan." />

                <x-admin.textarea name="short_description" label="Deskripsi singkat" :value="$machine->short_description" :rows="2"
                                  maxlength="150"
                                  hint="Maksimal 150 karakter. Muncul pada kartu produk di halaman daftar produk." />
            </div>

            {{-- Spesifikasi --}}
            <div class="space-y-5 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-7 dark:border-slate-700 dark:bg-slate-900">
                <h2 class="text-base font-extrabold text-stone-900 dark:text-white">Spesifikasi &amp; deskripsi lengkap</h2>

                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <x-admin.input name="capacity" label="Kapasitas" :value="$machine->capacity" maxlength="20"
                                   placeholder="500 kg/jam" hint="Maksimal 20 karakter." />

                    <x-admin.input name="power" label="Daya" :value="$machine->power" maxlength="20"
                                   placeholder="5,5 kW / 3 phase" hint="Maksimal 20 karakter." />

                    <x-admin.input name="dimension" label="Dimensi" :value="$machine->dimension" maxlength="20"
                                   placeholder="180 x 90 x 140 cm" hint="Maksimal 20 karakter." />

                    <x-admin.input name="weight" label="Berat" :value="$machine->weight" maxlength="10"
                                   placeholder="320 kg" hint="Maksimal 10 karakter." />

                    <x-admin.input name="material" label="Material" :value="$machine->material" maxlength="100"
                                   placeholder="Stainless steel 304" hint="Maksimal 100 karakter." />
                </div>

                <x-admin.textarea name="specifications" label="Spesifikasi tambahan" :value="$machine->specifications" :rows="5"
                                  maxlength="100"
                                  hint="Maksimal 100 karakter. Satu baris satu spesifikasi, format: Label: Nilai." />

                <x-admin.textarea name="description" label="Deskripsi lengkap" :value="$machine->description" :rows="6"
                                  maxlength="500"
                                  hint="Maksimal 500 karakter. Satu baris kosong memisahkan paragraf pada halaman detail produk." />
            </div>

            {{-- Foto utama --}}
            <div class="space-y-5 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-7  dark:border-slate-700 dark:bg-slate-900">
                <h2 class="text-base font-extrabold text-stone-900 dark:text-white">Foto mesin</h2>

                <x-admin.file name="main_image" label="Foto utama mesin" :current="$machine->mainImageUrl()"
                              hint="Format JPG, PNG, atau WEBP. Maksimal 6 MB. Disarankan rasio 4:3." />

                <p class="text-xs text-stone-500 dark:text-gray-400">
                    Model mesin yang disimpan langsung tampil di website dan diurutkan otomatis paling belakang.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button type="submit"
                        class="rounded-xl bg-brand-700 px-6 py-2.5 text-sm font-bold text-white transition hover:bg-brand-800">
                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Model Mesin' }}
                </button>

                <a href="{{ route('admin.machines.index') }}"
                   class="rounded-xl border border-stone-300 px-5 py-2.5 text-sm font-bold text-stone-600 transition hover:bg-stone-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
                    Batal
                </a>

                @if ($isEdit)
                    <a href="{{ route('products.show', $machine) }}" target="_blank"
                       class="text-sm font-bold text-brand-700 hover:text-brand-900">
                        Lihat di website &rarr;
                    </a>
                @endif
            </div>
        </form>
    </div>
@endsection