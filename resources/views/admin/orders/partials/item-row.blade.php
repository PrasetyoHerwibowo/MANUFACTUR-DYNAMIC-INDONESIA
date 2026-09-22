@php
    /** Dipakai dua kali: baris item pertama dan contoh baris untuk JavaScript. */
    $index = $index ?? '__INDEX__';
@endphp

<div class="item-row grid gap-3 rounded-xl border border-gray-200 bg-gray-50/70 p-3 dark:border-gray-700 dark:bg-gray-900/40 md:grid-cols-12" data-item-row>
    <div class="md:col-span-5">
        <x-admin.select
            :name="'items['.$index.'][machine_id]'"
            label="Model mesin"
            :options="$machineOptions"
            placeholder="— Mesin lain / manual —" />
    </div>

    <div class="md:col-span-3">
        <x-admin.input :name="'items['.$index.'][name]'" label="Nama item" required />
    </div>

    <div class="md:col-span-2">
        <x-admin.input :name="'items['.$index.'][unit_price]'" label="Harga satuan" type="number" min="0" step="1000" value="0" required />
    </div>

    <div class="md:col-span-1">
        <x-admin.input :name="'items['.$index.'][quantity]'" label="Jumlah" type="number" min="1" value="1" required />
    </div>

    <div class="flex items-end md:col-span-1">
        <button type="button" data-remove-row title="Hapus item"
                class="w-full rounded-xl border border-red-200 px-2 py-2.5 text-xs font-bold text-red-600 transition hover:bg-red-50 dark:border-red-500/40 dark:text-red-300 dark:hover:bg-red-500/10">
            <i aria-hidden="true" class="fas fa-trash"></i>
        </button>
    </div>
</div>
