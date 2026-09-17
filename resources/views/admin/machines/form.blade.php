@extends('layouts.admin')

@php $isEdit = $machine->exists; @endphp

@section('title', $isEdit ? 'Edit Model Mesin' : 'Tambah Model Mesin')
@section('page_title', $isEdit ? 'Edit Model Mesin' : 'Tambah Model Mesin')
@section('page_subtitle', $isEdit ? 'Perbarui data mesin, fungsi, spesifikasi, dan foto' : 'Lengkapi data model mesin baru')

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
            <div class="space-y-5 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-7">
                <h2 class="text-base font-extrabold text-stone-900">Data utama</h2>

                <x-admin.select name="category_id" label="Jenis mesin" :options="$categories->pluck('name', 'id')"
                                :selected="$machine->category_id" placeholder="— Pilih jenis mesin —" required />

                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.input name="name" label="Nama model mesin" :value="$machine->name" required
                                   placeholder="Contoh: Mesin Pulper Kopi Basah" />

                    <x-admin.input name="model_code" label="Kode / tipe model" :value="$machine->model_code"
                                   placeholder="Contoh: MDI-PL200" hint="Opsional, ditampilkan di halaman produk." />
                </div>

                <x-admin.textarea name="function" label="Fungsi mesin" :value="$machine->function" :rows="3"
                                  hint="Jelaskan kegunaan mesin ini dalam proses pengolahan." />

                <x-admin.textarea name="short_description" label="Deskripsi singkat" :value="$machine->short_description" :rows="2"
                                  hint="Muncul pada kartu produk di halaman daftar produk." />
            </div>

            {{-- Spesifikasi --}}
            <div class="space-y-5 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-7">
                <h2 class="text-base font-extrabold text-stone-900">Spesifikasi &amp; deskripsi lengkap</h2>

                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <x-admin.input name="capacity" label="Kapasitas" :value="$machine->capacity" placeholder="500 kg/jam" />
                    <x-admin.input name="power" label="Daya" :value="$machine->power" placeholder="5,5 kW / 3 phase" />
                    <x-admin.input name="dimension" label="Dimensi" :value="$machine->dimension" placeholder="180 x 90 x 140 cm" />
                    <x-admin.input name="weight" label="Berat" :value="$machine->weight" placeholder="320 kg" />
                    <x-admin.input name="material" label="Material" :value="$machine->material" placeholder="Stainless steel 304" />
                </div>

                <x-admin.textarea name="specifications" label="Spesifikasi tambahan" :value="$machine->specifications" :rows="5"
                                  hint="Satu baris satu spesifikasi, format: Label: Nilai. Contoh: Rangka: Besi UNP 80." />

                <x-admin.textarea name="description" label="Deskripsi lengkap" :value="$machine->description" :rows="6"
                                  hint="Satu baris kosong memisahkan paragraf pada halaman detail produk." />
            </div>

            {{-- Foto --}}
            <div class="space-y-5 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-7">
                <h2 class="text-base font-extrabold text-stone-900">Foto mesin</h2>

                <x-admin.file name="main_image" label="Foto utama mesin" :current="$machine->mainImageUrl()"
                              hint="Format JPG, PNG, atau WEBP. Maksimal 6 MB. Disarankan rasio 4:3." />

                <x-admin.file name="images[]" label="Tambah foto galeri" multiple
                              hint="Boleh pilih beberapa foto sekaligus (maksimal 12 foto per penyimpanan, masing-masing 6 MB)." />

                @if ($isEdit)
                    <x-admin.checkbox name="is_featured" label="Tandai sebagai mesin unggulan" :checked="$machine->is_featured"
                                      hint="Mesin unggulan tampil pada halaman beranda." />
                @else
                    <x-admin.checkbox name="is_featured" label="Tandai sebagai mesin unggulan" :checked="false"
                                      hint="Mesin unggulan tampil pada halaman beranda." />
                @endif

                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.input name="sort_order" label="Urutan tampil" type="number" :value="$machine->sort_order"
                                   hint="Angka lebih kecil tampil lebih dahulu." />

                    <div class="flex items-end">
                        <x-admin.checkbox name="is_active" label="Tampilkan di website" :checked="$machine->is_active"
                                          hint="Jika dimatikan, mesin tidak muncul di halaman publik." class="w-full" />
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button type="submit"
                        class="rounded-xl bg-brand-700 px-6 py-2.5 text-sm font-bold text-white transition hover:bg-brand-800">
                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Model Mesin' }}
                </button>

                <a href="{{ route('admin.machines.index') }}"
                   class="rounded-xl border border-stone-300 px-5 py-2.5 text-sm font-bold text-stone-600 transition hover:bg-white">
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

        @if ($isEdit && $machine->images->isNotEmpty())
            <div class="mt-8 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-7">
                <h2 class="text-base font-extrabold text-stone-900">Galeri foto ({{ $machine->images->count() }})</h2>
                <p class="mt-1 text-sm text-stone-500">Ubah keterangan foto atau hapus foto yang tidak diperlukan.</p>

                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    @foreach ($machine->images as $image)
                        <div class="rounded-2xl border border-stone-200 p-4">
                            <img src="{{ $image->url() }}" alt="{{ $image->caption ?: $machine->name }}"
                                 class="aspect-[4/3] w-full rounded-xl object-cover">

                            <form method="POST" action="{{ route('admin.machines.images.update', [$machine, $image]) }}" class="mt-3 space-y-2">
                                @csrf
                                @method('PUT')

                                <label for="caption-{{ $image->id }}" class="block text-xs font-semibold text-stone-500">Keterangan foto</label>
                                <input type="text" name="caption" id="caption-{{ $image->id }}" value="{{ $image->caption }}"
                                       placeholder="Contoh: Tampak depan"
                                       class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-200">

                                <button type="submit"
                                        class="rounded-lg bg-stone-800 px-3.5 py-2 text-xs font-bold text-white transition hover:bg-stone-700">
                                    Simpan keterangan
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.machines.images.destroy', [$machine, $image]) }}" class="mt-2"
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
