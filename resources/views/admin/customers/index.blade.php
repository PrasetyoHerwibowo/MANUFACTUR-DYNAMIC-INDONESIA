@extends('layouts.admin')

@section('title', 'Pelanggan')
@section('page_title', 'Pelanggan')
@section('page_subtitle', 'Data pelanggan terdaftar beserta riwayat pesanannya')

@section('content')
    {{-- Ringkasan pelanggan --}}
    <div class="mb-6 grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.stat-card
            label="Total Pelanggan"
            :value="number_format($stats['total'], 0, ',', '.')"
            icon="fas fa-users"
            gradient="stat-card-gradient-1"
            :note="$stats['aktif'].' aktif'" />

        <x-admin.stat-card
            label="Pelanggan Baru"
            :value="number_format($stats['baru'], 0, ',', '.')"
            icon="fas fa-user-plus"
            gradient="stat-card-gradient-3"
            note="Bulan ini" />

        <x-admin.stat-card
            label="Pelanggan Berpesanan"
            :value="number_format($stats['berpesanan'], 0, ',', '.')"
            icon="fas fa-user-check"
            gradient="stat-card-gradient-2"
            note="Pernah membuat pesanan" />

        <x-admin.stat-card
            label="Nilai Pembayaran Lunas"
            :value="'Rp '.number_format($stats['nilai_lunas'], 0, ',', '.')"
            icon="fas fa-hand-holding-usd"
            gradient="stat-card-gradient-4"
            note="Seluruh pelanggan" />
    </div>

    {{-- Filter & tombol tambah pelanggan --}}
    <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
        <form method="GET" action="{{ route('admin.customers.index') }}" class="flex flex-wrap items-end gap-2">
            <div>
                <label for="q" class="mb-1 block text-xs font-semibold text-gray-500 dark:text-gray-400">Cari</label>
                <input type="text" name="q" id="q" value="{{ request('q') }}"
                       placeholder="Nama / email / telepon / kode pelanggan"
                       class="w-72 rounded-xl border border-gray-300 bg-white px-3.5 py-2 text-sm outline-none focus:border-coffee-500 focus:ring-2 focus:ring-coffee-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
            </div>

            <div>
                <label for="status" class="mb-1 block text-xs font-semibold text-gray-500 dark:text-gray-400">Status akun</label>
                <select name="status" id="status"
                        class="w-40 rounded-xl border border-gray-300 bg-white px-3.5 py-2 text-sm outline-none focus:border-coffee-500 focus:ring-2 focus:ring-coffee-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
                    <option value="">Semua status</option>
                    <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
                </select>
            </div>

            <button type="submit" class="rounded-xl bg-gray-800 px-4 py-2 text-sm font-bold text-white transition hover:bg-gray-700">
                Filter
            </button>

            @if (request()->filled('q') || request()->filled('status'))
                <a href="{{ route('admin.customers.index') }}"
                   class="rounded-xl border border-gray-300 px-4 py-2 text-sm font-bold text-gray-600 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">
                    Reset
                </a>
            @endif
        </form>

        <a href="{{ route('admin.customers.create') }}"
           class="rounded-xl bg-coffee-600 px-4 py-2.5 text-sm font-bold text-white shadow-md transition hover:bg-coffee-700">
            <i aria-hidden="true" class="fas fa-plus mr-1"></i> Tambah Pelanggan
        </a>
    </div>

    <div class="glass-card overflow-hidden rounded-2xl shadow-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                <thead class="bg-gray-50 text-left text-xs font-bold uppercase tracking-wide text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                    <tr>
                        <th class="px-5 py-3.5">Nama Pelanggan</th>
                        <th class="px-5 py-3.5">Kontak</th>
                        <th class="hidden px-5 py-3.5 lg:table-cell">Alamat</th>
                        <th class="px-5 py-3.5 text-center">Pesanan</th>
                        <th class="hidden px-5 py-3.5 xl:table-cell">Total Lunas</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                    @forelse ($customers as $customer)
                        <tr class="align-top transition hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-coffee-500 to-coffee-700 text-xs font-bold text-white">
                                        {{ $customer->initials() }}
                                    </div>

                                    <div class="min-w-0">
                                        <a href="{{ route('admin.customers.show', $customer) }}"
                                           class="font-semibold text-gray-800 transition hover:text-coffee-600 dark:text-gray-100 dark:hover:text-coffee-400">
                                            {{ $customer->name }}
                                        </a>
                                        <span class="block font-mono text-[10px] text-gray-400">{{ $customer->customer_code }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="px-5 py-3.5">
                                <p class="text-xs font-semibold text-gray-700 dark:text-gray-200">{{ $customer->email }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $customer->phone ?: '—' }}</p>
                            </td>

                            <td class="hidden px-5 py-3.5 text-xs text-gray-600 dark:text-gray-300 lg:table-cell">
                                {{ $customer->fullAddress() }}

                                @if ($customer->company)
                                    <span class="block text-[10px] text-gray-400">{{ $customer->company }}</span>
                                @endif
                            </td>

                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex min-w-8 items-center justify-center rounded-full bg-coffee-100 px-2.5 py-1 text-xs font-bold text-coffee-700 dark:bg-coffee-500/15 dark:text-coffee-300">
                                    {{ $customer->orders_count }}
                                </span>

                                @if ($customer->last_order_at)
                                    <span class="mt-1 block text-[10px] text-gray-400">Terakhir {{ $customer->last_order_at->format('d M Y') }}</span>
                                @endif
                            </td>

                            <td class="hidden px-5 py-3.5 font-semibold text-gray-800 dark:text-gray-100 xl:table-cell">
                                Rp {{ number_format((float) ($customer->total_lunas ?? 0), 0, ',', '.') }}
                            </td>

                            <td class="px-5 py-3.5">
                                @if ($customer->is_active)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-2.5 py-1 text-[11px] font-bold text-green-800 dark:bg-green-500/15 dark:text-green-300">
                                        <i aria-hidden="true" class="fas fa-check text-[10px]"></i> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-200 px-2.5 py-1 text-[11px] font-bold text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                        <i aria-hidden="true" class="fas fa-ban text-[10px]"></i> Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-3.5">
                                <div class="flex flex-wrap items-center justify-end gap-2">
                                    <a href="{{ route('admin.customers.show', $customer) }}"
                                       class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-bold text-gray-600 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">
                                        Detail
                                    </a>

                                    <a href="{{ route('admin.customers.edit', $customer) }}"
                                       class="rounded-lg border border-coffee-300 px-3 py-1.5 text-xs font-bold text-coffee-700 transition hover:bg-coffee-50 dark:border-coffee-500/40 dark:text-coffee-300 dark:hover:bg-coffee-500/10">
                                        Ubah
                                    </a>

                                    <form method="POST" action="{{ route('admin.customers.toggle', $customer) }}">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit"
                                                class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-bold text-gray-600 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">
                                            {{ $customer->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>

                                    @if ($customer->orders_count === 0)
                                        <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}"
                                              data-confirm="Hapus pelanggan {{ $customer->name }}?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-bold text-red-600 transition hover:bg-red-50 dark:border-red-500/40 dark:text-red-300 dark:hover:bg-red-500/10">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <i aria-hidden="true" class="fas fa-users mb-3 text-4xl text-gray-300 dark:text-gray-600"></i>
                                <p class="text-sm font-semibold text-gray-600 dark:text-gray-300">Belum ada pelanggan yang sesuai.</p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Tambahkan pelanggan terlebih dahulu sebelum membuat pesanan.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($customers->hasPages())
            <div class="border-t border-gray-200 bg-gray-50 px-5 py-4 dark:border-gray-700 dark:bg-gray-800">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

    <p class="mt-4 text-xs text-gray-500 dark:text-gray-400">
        <i aria-hidden="true" class="fas fa-info-circle mr-1 text-coffee-500"></i>
        Pelanggan yang sudah memiliki riwayat pesanan tidak dapat dihapus — nonaktifkan akunnya agar tidak dipakai pada
        pesanan baru.
    </p>
@endsection
