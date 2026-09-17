@extends('layouts.admin')

@php $isEdit = $catalog->exists; @endphp

@section('title', $isEdit ? 'Edit Katalog' : 'Tambah Katalog')
@section('page_title', $isEdit ? 'Edit Katalog' : 'Tambah Katalog')
@section('page_subtitle', $isEdit ? 'Perbarui data katalog dan foto halaman katalog' : 'Buat katalog produk baru')

@section('content')
    <div class="max-w-4xl">
        <form method="POST"
              action="{{ $isEdit ? route('admin.catalogs.update', $catalog) : route('admin.catalogs.store') }}"
              enctype="multipart/form-data"
              class="space-y-6">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="space-y-5 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-7">
                <h2 class="text-base font-extrabold text-stone-900">Data katalog</h2>

                <div class="grid gap-5 sm:grid-cols-3">
                    <div class="sm:col-span-2">
                        <x-admin.input name="title" label="Judul katalog" :value="$catalog->title" required
                                       placeholder="Contoh: Katalog Mesin Kopi 2026" />
                    </div>

                    <x-admin.input name="year" label="Edisi / tahun" :value="$catalog->year" placeholder="2026" />
                </div>

                <x-admin.textarea name="description" label="Deskripsi katalog" :value="$catalog->description" :rows="4"
                                  hint="Penjelasan singkat isi katalog yang tampil pada halaman publik." />
            </div>

            <div class="space-y-5 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-7">
                <h2 class="text-base font-extrabold text-stone-900">Foto katalog</h2>

                <x-admin.file name="cover_image" label="Foto sampul katalog" :current="$catalog->coverUrl()"
                              hint="Format JPG, PNG, atau WEBP. Maksimal 6 MB. Disarankan rasio 4:3." />

                <x-admin.file name="images[]" label="Tambah foto halaman katalog" multiple
                              hint="Boleh pilih beberapa foto sekaligus (maksimal 30 foto per penyimpanan, masing-masing 6 MB)." />

                <div class="rounded-xl border border-dashed border-stone-300 bg-stone-50/70 p-4">
                    <label for="pdf_file" class="mb-1.5 block text-sm font-semibold text-stone-700">File PDF katalog (opsional)</label>
                    <input type="file" name="pdf_file" id="pdf_file" accept="application/pdf"
                           class="block w-full cursor-pointer rounded-lg border border-stone-300 bg-white text-sm text-stone-600 file:mr-3 file:cursor-pointer file:rounded-l-lg file:border-0 file:bg-stone-800 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-white hover:file:bg-stone-700">
                    <p class="mt-2 text-xs text-stone-500">Maksimal 20 MB.</p>

                    @error('pdf_file')
                        <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror

                    @if ($isEdit && $catalog->pdf_file)
                        <a href="{{ \App\Support\ImageUploader::url($catalog->pdf_file) }}" target="_blank"
                           class="mt-2 inline-block text-xs font-bold text-brand-700">Lihat PDF saat ini</a>
                    @endif
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.input name="sort_order" label="Urutan tampil" type="number" :value="$catalog->sort_order"
                                   hint="Angka lebih kecil tampil lebih dahulu." />

                    <div class="flex items-end">
                        <x-admin.checkbox name="is_active" label="Tampilkan di website" :checked="$catalog->is_active"
                                          hint="Jika dimatikan, katalog tidak muncul di halaman publik." class="w-full" />
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button type="submit"
                        class="rounded-xl bg-brand-700 px-6 py-2.5 text-sm font-bold text-white transition hover:bg-brand-800">
                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Katalog' }}
                </button>

                <a href="{{ route('admin.catalogs.index') }}"
                   class="rounded-xl border border-stone-300 px-5 py-2.5 text-sm font-bold text-stone-600 transition hover:bg-white">
                    Batal
                </a>

                @if ($isEdit)
                    <a href="{{ route('catalogs.show', $catalog) }}" target="_blank"
                       class="text-sm font-bold text-brand-700 hover:text-brand-900">
                        Lihat di website &rarr;
                    </a>
                @endif
            </div>
        </form>

        @if ($isEdit && $catalog->images->isNotEmpty())
            <div class="mt-8 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-7">
                <h2 class="text-base font-extrabold text-stone-900">Foto halaman katalog ({{ $catalog->images->count() }})</h2>
                <p class="mt-1 text-sm text-stone-500">Ubah keterangan foto atau hapus foto yang tidak diperlukan.</p>

                <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($catalog->images as $image)
                        <div class="rounded-2xl border border-stone-200 p-4">
                            <img src="{{ $image->url() }}" alt="{{ $image->caption ?: $catalog->title }}"
                                 class="aspect-[4/3] w-full rounded-xl object-cover">

                            <form method="POST" action="{{ route('admin.catalogs.images.update', [$catalog, $image]) }}" class="mt-3 space-y-2">
                                @csrf
                                @method('PUT')

                                <label for="caption-{{ $image->id }}" class="block text-xs font-semibold text-stone-500">Keterangan foto</label>
                                <input type="text" name="caption" id="caption-{{ $image->id }}" value="{{ $image->caption }}"
                                       placeholder="Contoh: Halaman 3 — Mesin Pulper"
                                       class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-200">

                                <button type="submit"
                                        class="rounded-lg bg-stone-800 px-3.5 py-2 text-xs font-bold text-white transition hover:bg-stone-700">
                                    Simpan keterangan
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.catalogs.images.destroy', [$catalog, $image]) }}" class="mt-2"
                                  data-confirm="Hapus foto ini secara permanen?">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="rounded-lg border border-red-200 px-3.5 py-2 text-xs font-bold text-red-600 transition hover:bg-red-50">
                                    Hapus foto
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
