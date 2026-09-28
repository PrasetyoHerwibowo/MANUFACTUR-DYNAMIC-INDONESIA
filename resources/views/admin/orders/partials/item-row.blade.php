@php
    /** Dipakai dua kali: baris item pertama dan contoh baris untuk JavaScript. */
    $index = $index ?? '__INDEX__';
@endphp

<div class="item-row grid gap-3 rounded-xl border border-gray-200 bg-gray-50/70 p-3 dark:border-gray-700 dark:bg-gray-900/40 md:grid-cols-12" data-item-row>
    <div class="md:col-span-6">
        <x-admin.select
            :name="'items['.$index.'][machine_id]'"
            label="Model mesin"
            :options="$machineOptions"
            placeholder="— Pilih model mesin —"
            required />
    </div>

    <div class="md:col-span-3">
        <x-admin.input
            :name="'items['.$index.'][unit_price]'"
            label="Harga satuan"
            inputmode="numeric"
            maxlength="9"
            pattern="[0-9]*"
            data-input-filter="digits"
            value="0"
            hint="Hanya angka, maksimal 9 digit (satuan juta)."
            required />
    </div>

    <div class="md:col-span-2">
        <x-admin.input
            :name="'items['.$index.'][quantity]'"
            label="Jumlah"
            inputmode="numeric"
            maxlength="2"
            pattern="[0-9]*"
            data-input-filter="digits"
            value="1"
            hint="Hanya angka, maksimal 2 digit."
            required />
    </div>

    <div class="flex items-end md:col-span-1">
        <button type="button" data-remove-row title="Hapus item"
                class="w-full rounded-xl border border-red-200 px-2 py-2.5 text-xs font-bold text-red-600 transition hover:bg-red-50 dark:border-red-500/40 dark:text-red-300 dark:hover:bg-red-500/10">
            <i aria-hidden="true" class="fas fa-trash"></i>
        </button>
    </div>
</div>
