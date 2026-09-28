@extends('layouts.admin')

@section('title', 'Tambah Pesanan')
@section('page_title', 'Tambah Pesanan')
@section('page_subtitle', 'Pesanan baru otomatis mendapat kode pesanan dan kode pembayaran')

@php
    $customerOptions = $customers->mapWithKeys(
        fn ($customer) => [$customer->id => $customer->customer_code.' — '.$customer->name.' ('.$customer->email.')']
    )->all();

    $machineOptions = $machines->mapWithKeys(
        fn ($machine) => [$machine->id => $machine->name.($machine->model_code ? ' · '.$machine->model_code : '')]
    )->all();
@endphp

@section('content')
    @if ($errors->any())
        <x-alert type="error" class="mb-5">
            <div>
                <p class="font-bold">Periksa kembali data pesanan:</p>
                <ul class="mt-1 list-inside list-disc text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </x-alert>
    @endif

    @if ($customers->isEmpty())
        <x-alert type="warning" class="mb-5">
            Belum ada pelanggan terdaftar. Tambahkan pelanggan terlebih dahulu melalui menu
            <a href="{{ route('admin.customers.create') }}" class="font-bold underline">Pelanggan → Tambah Pelanggan</a>.
        </x-alert>
    @endif

    <form method="POST" action="{{ route('admin.orders.store') }}">
        @csrf

        <div class="glass-card rounded-2xl p-5 shadow-lg">
            <h2 class="text-base font-bold text-gray-800 dark:text-white">Data Pesanan</h2>
            <p class="mb-4 text-xs text-gray-500 dark:text-gray-400">
                Kode pesanan dan kode pembayaran dibuat otomatis oleh sistem.
            </p>

            <div class="grid gap-4 md:grid-cols-2">
                <x-admin.select
                    name="customer_id"
                    label="Nama Pelanggan"
                    :options="$customerOptions"
                    placeholder="— Pilih pelanggan —"
                    required
                    hint="Pelanggan baru dapat ditambahkan melalui menu Pelanggan." />

                <x-admin.input
                    name="notes"
                    label="Catatan Pesanan"
                    maxlength="250"
                    data-input-filter="punctuation"
                    placeholder="Contoh: pengiriman ke gudang Surabaya"
                    hint="Opsional, tampil pada detail pesanan. Maksimal 250 karakter dan hanya boleh huruf, angka, serta tanda baca." />
            </div>
        </div>

        <div class="glass-card mt-5 rounded-2xl p-5 shadow-lg">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-gray-800 dark:text-white">Item Pesanan</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Tentukan model mesin, harga satuan, dan jumlahnya.</p>
                </div>

                <button type="button" id="addItemRow"
                        class="rounded-xl border border-coffee-300 px-4 py-2 text-sm font-bold text-coffee-700 transition hover:bg-coffee-50 dark:border-coffee-500/40 dark:text-coffee-300 dark:hover:bg-coffee-500/10">
                    <i aria-hidden="true" class="fas fa-plus mr-1"></i> Tambah Item
                </button>
            </div>

            <div id="itemRows" class="mt-4 space-y-3">
                @include('admin.orders.partials.item-row', ['index' => 0, 'machineOptions' => $machineOptions])
            </div>

            <template id="itemRowTemplate">
                @include('admin.orders.partials.item-row', ['index' => '__INDEX__', 'machineOptions' => $machineOptions])
            </template>

            <div class="mt-4 flex items-center justify-end gap-3 border-t border-gray-200 pt-4 dark:border-gray-700">
                <span class="text-sm text-gray-500 dark:text-gray-400">Total pembayaran</span>
                <span id="itemsTotal" class="text-xl font-bold text-coffee-700 dark:text-coffee-300">Rp 0</span>
            </div>
        </div>

        <div class="glass-card mt-5 rounded-2xl p-5 shadow-lg">
            <h2 class="text-base font-bold text-gray-800 dark:text-white">Pembayaran</h2>
            <p class="mb-4 text-xs text-gray-500 dark:text-gray-400">
                Kode pembayaran dibuat otomatis dengan batas {{ $windowHours }} jam sejak dibuat. Bila pelanggan
                melewati batas tersebut, pembayaran berstatus <strong>Gagal</strong> dan harus diulang dari awal.
            </p>

            <x-admin.select
                name="method"
                label="Metode Pembayaran"
                :options="$methodLabels"
                placeholder="— Pilih metode —"
                required
                hint="Cara pelanggan membayar pesanan ini." />
        </div>

        <div class="mt-5 flex flex-wrap items-center justify-end gap-3">
            <a href="{{ route('admin.orders.index') }}"
               class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-bold text-gray-600 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">
                Batal
            </a>

            <button type="submit"
                    class="rounded-xl bg-coffee-600 px-5 py-2.5 text-sm font-bold text-white shadow-md transition hover:bg-coffee-700">
                <i aria-hidden="true" class="fas fa-save mr-1"></i> Simpan Pesanan
            </button>
        </div>

@push('scripts')
    <script>
        (function () {
            const rows = document.getElementById('itemRows');
            const template = document.getElementById('itemRowTemplate');
            const total = document.getElementById('itemsTotal');

            if (! rows || ! template) {
                return;
            }

            let nextIndex = rows.querySelectorAll('[data-item-row]').length;

            function rupiah(value) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(value));
            }

            function recalcTotal() {
                let sum = 0;

                rows.querySelectorAll('[data-item-row]').forEach((row) => {
                    const price = parseFloat(row.querySelector('input[name$="[unit_price]"]')?.value) || 0;
                    const quantity = parseInt(row.querySelector('input[name$="[quantity]"]')?.value, 10) || 0;

                    sum += price * quantity;
                });

                if (total) {
                    total.textContent = rupiah(sum);
                }
            }

            document.getElementById('addItemRow')?.addEventListener('click', () => {
                rows.insertAdjacentHTML('beforeend', template.innerHTML.replaceAll('__INDEX__', String(nextIndex)));
                nextIndex++;

                // Baris baru juga mengikuti aturan karakter (harga & jumlah: angka).
                const row = rows.lastElementChild;

                if (row && typeof window.initInputFilters === 'function') {
                    window.initInputFilters(row);
                }

                recalcTotal();
            });

            rows.addEventListener('change', recalcTotal);
            rows.addEventListener('input', recalcTotal);

            rows.addEventListener('click', (event) => {
                const button = event.target.closest('[data-remove-row]');

                if (! button) {
                    return;
                }

                // Sisakan minimal satu baris item.
                if (rows.querySelectorAll('[data-item-row]').length > 1) {
                    button.closest('[data-item-row]')?.remove();
                    recalcTotal();
                }
            });

            recalcTotal();
        })();
    </script>
@endpush
    </form>
@endsection
