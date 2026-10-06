@extends('layouts.admin')

@php
    $isEdit = $customer->exists;

    /**
     * Batas panjang kolom diambil dari CustomerRequest::LENGTHS, bukan ditulis
     * ulang di sini, sehingga atribut maxlength selalu sama dengan aturan
     * validasi sisi server.
     */
    $max = \App\Http\Requests\Admin\CustomerRequest::LENGTHS;

    /**
     * Provinsi & kota/kabupaten dipilih dari daftar resmi agar admin tidak
     * perlu mengetik. Kota/kabupaten mengikuti provinsi yang dipilih.
     */
    $selectedProvince = old('province', $customer->province);
    $selectedCity = old('city', $customer->city);

    $provinceOptions = \App\Support\Regions::provinces();
    $cityOptions = filled($selectedProvince) ? \App\Support\Regions::citiesOf($selectedProvince) : [];

    // Alamat lama yang belum ada di daftar wilayah tetap ditampilkan agar tidak hilang.
    if (filled($customer->province) && ! in_array($customer->province, $provinceOptions, true)) {
        $provinceOptions[] = $customer->province;
    }

    if (filled($selectedCity) && ! in_array($selectedCity, $cityOptions, true)) {
        $cityOptions[] = $selectedCity;
    }

    $provinceSelect = array_combine($provinceOptions, $provinceOptions);
    $citySelect = array_combine($cityOptions, $cityOptions);
@endphp

@section('title', $isEdit ? 'Ubah Pelanggan' : 'Tambah Pelanggan')
@section('page_title', $isEdit ? 'Ubah Pelanggan' : 'Tambah Pelanggan')
@section('page_subtitle', $isEdit
    ? 'Perbarui data pelanggan ' . $customer->customer_code
    : 'Pelanggan baru otomatis mendapat kode pelanggan')

@section('content')
    @if ($errors->any())
        <x-alert type="error" class="mb-5">
            <div>
                <p class="font-bold">Periksa kembali data pelanggan:</p>
                <ul class="mt-1 list-inside list-disc text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </x-alert>
    @endif

    {{--
        Nilai setiap kolom diambil dari old() lebih dulu (dilakukan oleh
        komponen x-admin.*), sehingga isian admin tidak hilang saat validasi
        gagal. Atribut maxlength di bawah adalah versi sisi klien dari
        CustomerRequest::LENGTHS, sedangkan pesan rinci datang dari server.
    --}}

    <form method="POST"
          action="{{ route('admin.customers.update', $customer) }}">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="glass-card rounded-2xl p-5 shadow-lg">
            <h2 class="text-base font-bold text-gray-800 dark:text-white">Data Pelanggan</h2>
            <p class="mb-4 text-xs text-gray-500 dark:text-gray-400">
                @if ($isEdit)
                    Kode pelanggan <span class="font-mono font-bold">{{ $customer->customer_code }}</span> tidak berubah.
                @else
                    Kode pelanggan dibuat otomatis oleh sistem.
                @endif
            </p>

            <div class="grid gap-4 md:grid-cols-2">
                <x-admin.input
                    name="name"
                    label="Nama Pelanggan"
                    :value="$customer->name"
                    :maxlength="$max['name']"
                    required
                    autocomplete="name"
                    pattern="[A-Za-z][A-Za-z ]*"
                    title="Hanya huruf dan spasi."
                    data-input-filter="name"
                    placeholder="Contoh: Budi Santoso"
                    hint="Hanya huruf dan spasi, maksimal {{ $max['name'] }} karakter. Biodata yang sama dan email yang sama tidak boleh diinputkan dua kali." />

                <x-admin.input
                    name="email"
                    label="Email"
                    type="email"
                    :value="$customer->email"
                    :maxlength="$max['email']"
                    required
                    autocomplete="email"
                    placeholder="nama@perusahaan.com"
                    hint="Wajib diisi dan harus unik. Dipakai untuk pencarian dan identitas pelanggan." />

                <x-admin.input
                    name="phone"
                    label="Nomor Telepon"
                    :value="$customer->phone"
                    :maxlength="$max['phone']"
                    inputmode="tel"
                    autocomplete="tel"
                    placeholder="Contoh: 0812-3456-7890"
                    hint="Hanya angka, tanda +, spasi, dan tanda hubung." />

                <x-admin.input
                    name="company"
                    label="Perusahaan"
                    :value="$customer->company"
                    :maxlength="$max['company']"
                    autocomplete="organization"
                    data-input-filter="punctuation"
                    placeholder="Contoh: CV Kopi Nusantara"
                    hint="Opsional. Hanya huruf, angka, dan tanda baca, maksimal {{ $max['company'] }} karakter." />
            </div>
        </div>


        <div class="glass-card mt-5 rounded-2xl p-5 shadow-lg">
            <h2 class="text-base font-bold text-gray-800 dark:text-white">Alamat Pelanggan</h2>
            <p class="mb-4 text-xs text-gray-500 dark:text-gray-400">Dipakai sebagai alamat pengiriman mesin.</p>

            <div class="space-y-4">
                <x-admin.textarea
                    name="address"
                    label="Alamat Lengkap"
                    :value="$customer->address"
                    :rows="3"
                    :maxlength="$max['address']"
                    data-input-filter="punctuation"
                    placeholder="Nama jalan, nomor, RT/RW, kelurahan, kecamatan"
                    hint="Hanya huruf, angka, dan tanda baca, maksimal {{ $max['address'] }} karakter." />

                <div class="grid gap-4 md:grid-cols-3">
                    <x-admin.select
                        name="province"
                        label="Provinsi"
                        :options="$provinceSelect"
                        :selected="$customer->province"
                        placeholder="— Pilih provinsi —"
                        hint="Pilih provinsi terlebih dahulu, kota/kabupaten menyesuaikan." />

                    <x-admin.select
                        name="city"
                        label="Kota / Kabupaten"
                        :options="$citySelect"
                        :selected="$customer->city"
                        placeholder="— Pilih kota/kabupaten —" />

                    <x-admin.input
                        name="postal_code"
                        label="Kode Pos"
                        :value="$customer->postal_code"
                        :maxlength="$max['postal_code']"
                        inputmode="numeric"
                        autocomplete="postal-code"
                        pattern="[0-9]*"
                        data-input-filter="digits"
                        placeholder="40123"
                        hint="Hanya angka, maksimal {{ $max['postal_code'] }} digit." />
                </div>
            </div>
        </div>


        <div class="glass-card mt-5 rounded-2xl p-5 shadow-lg">
            <h2 class="text-base font-bold text-gray-800 dark:text-white">Catatan Internal</h2>
            <p class="mb-4 text-xs text-gray-500 dark:text-gray-400">
                Catatan internal hanya terlihat oleh admin.
            </p>

            <x-admin.textarea
                name="notes"
                label="Catatan Internal"
                :value="$customer->notes"
                :rows="3"
                :maxlength="$max['notes']"
                placeholder="Contoh: pembayaran selalu melalui transfer bank perusahaan" />
        </div>

        <div class="mt-5 flex flex-wrap items-center justify-end gap-3">
            <a href="{{ $isEdit ? route('admin.customers.show', $customer) : route('admin.customers.index') }}"
               class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-bold text-gray-600 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">
                Batal
            </a>

            <button type="submit"
                    class="rounded-xl bg-coffee-600 px-5 py-2.5 text-sm font-bold text-white shadow-md transition hover:bg-coffee-700">
                <i aria-hidden="true" class="fas fa-save mr-1"></i> {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Pelanggan' }}
            </button>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        (function () {
            const province = document.getElementById('field-province');
            const city = document.getElementById('field-city');

            if (! province || ! city) {
                return;
            }

            // Daftar kota/kabupaten per provinsi: dropdown kota mengikuti provinsi.
            const citiesByProvince = @json(\App\Support\Regions::all());

            province.addEventListener('change', () => {
                const options = citiesByProvince[province.value] ?? [];

                city.innerHTML = '';

                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = '— Pilih kota/kabupaten —';
                city.append(placeholder);

                options.forEach((name) => {
                    const option = document.createElement('option');
                    option.value = name;
                    option.textContent = name;
                    city.append(option);
                });
            });
        })();
    </script>
@endpush
