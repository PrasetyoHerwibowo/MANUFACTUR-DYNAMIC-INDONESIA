@extends('layouts.admin')

@php $isEdit = $customer->exists; @endphp

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

    <form method="POST"
          action="{{ $isEdit ? route('admin.customers.update', $customer) : route('admin.customers.store') }}">
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
                <x-admin.input name="name" label="Nama Pelanggan" :value="$customer->name" required
                               placeholder="Contoh: Budi Santoso" />

                <x-admin.input name="email" label="Email" type="email" :value="$customer->email" required
                               placeholder="nama@perusahaan.com"
                               hint="Dipakai untuk pencarian dan identitas pelanggan." />

                <x-admin.input name="phone" label="Nomor Telepon" :value="$customer->phone"
                               placeholder="Contoh: 0812-3456-7890" />

                <x-admin.input name="company" label="Perusahaan" :value="$customer->company"
                               placeholder="Contoh: CV Kopi Nusantara" hint="Opsional." />
            </div>
        </div>

        <div class="glass-card mt-5 rounded-2xl p-5 shadow-lg">
            <h2 class="text-base font-bold text-gray-800 dark:text-white">Alamat Pelanggan</h2>
            <p class="mb-4 text-xs text-gray-500 dark:text-gray-400">Dipakai sebagai alamat pengiriman mesin.</p>

            <div class="space-y-4">
                <x-admin.textarea name="address" label="Alamat Lengkap" :value="$customer->address" :rows="3"
                                  placeholder="Nama jalan, nomor, RT/RW, kelurahan, kecamatan" />

                <div class="grid gap-4 md:grid-cols-3">
                    <x-admin.input name="city" label="Kota / Kabupaten" :value="$customer->city" placeholder="Contoh: Bandung" />
                    <x-admin.input name="province" label="Provinsi" :value="$customer->province" placeholder="Contoh: Jawa Barat" />
                    <x-admin.input name="postal_code" label="Kode Pos" :value="$customer->postal_code" placeholder="40123" />
                </div>
            </div>
        </div>

        <div class="glass-card mt-5 rounded-2xl p-5 shadow-lg">
            <h2 class="text-base font-bold text-gray-800 dark:text-white">Catatan &amp; Status Akun</h2>
            <p class="mb-4 text-xs text-gray-500 dark:text-gray-400">
                Catatan internal hanya terlihat oleh admin.
            </p>

            <div class="space-y-4">
                <x-admin.textarea name="notes" label="Catatan Internal" :value="$customer->notes" :rows="3"
                                  placeholder="Contoh: pembayaran selalu melalui transfer bank perusahaan" />

                <x-admin.checkbox name="is_active" label="Akun pelanggan aktif" :checked="$customer->is_active"
                                  hint="Pelanggan nonaktif tidak dipakai pada pesanan baru." />
            </div>
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
