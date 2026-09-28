@extends('layouts.admin')

@section('title', 'Pelanggan ' . $customer->name)
@section('page_title', 'Detail Pelanggan')
@section('page_subtitle', $customer->customer_code . ' · ' . $customer->email)

@section('content')
    <a href="{{ route('admin.customers.index') }}"
       class="mb-4 inline-flex items-center gap-1.5 text-xs font-bold text-coffee-600 transition hover:text-coffee-700 dark:text-coffee-400">
        <i aria-hidden="true" class="fas fa-arrow-left"></i> Kembali ke Pelanggan
    </a>

    {{-- Identitas pelanggan --}}
    <div class="mb-5 flex flex-wrap items-start justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-coffee-500 to-coffee-700 text-lg font-bold text-white shadow-lg">
                {{ $customer->initials() }}
            </div>

            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $customer->name }}</h2>

                <div class="mt-1.5 flex flex-wrap items-center gap-2">
                    <span class="font-mono text-xs text-gray-500 dark:text-gray-400">{{ $customer->customer_code }}</span>

                    @if ($customer->last_order_at)
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            Pesanan terakhir {{ $customer->last_order_at->format('d M Y') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.customers.edit', $customer) }}"
               class="rounded-xl border border-coffee-300 px-4 py-2.5 text-sm font-bold text-coffee-700 transition hover:bg-coffee-50 dark:border-coffee-500/40 dark:text-coffee-300 dark:hover:bg-coffee-500/10">
                <i aria-hidden="true" class="fas fa-pen mr-1"></i> Ubah Data
            </a>

            @if ($stats['pesanan'] === 0)
                <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}"
                      data-confirm="Hapus pelanggan {{ $customer->name }}?">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="rounded-xl border border-red-200 px-4 py-2.5 text-sm font-bold text-red-600 transition hover:bg-red-50 dark:border-red-500/40 dark:text-red-300 dark:hover:bg-red-500/10">
                        <i aria-hidden="true" class="fas fa-trash mr-1"></i> Hapus
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Ringkasan pesanan pelanggan --}}
    <div class="mb-6 grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.stat-card
            label="Total Pesanan"
            :value="number_format($stats['pesanan'], 0, ',', '.')"
            icon="fas fa-file-invoice-dollar"
            gradient="stat-card-gradient-1"
            :note="$stats['lunas'].' lunas'" />

        <x-admin.stat-card
            label="Menunggu Pembayaran"
            :value="number_format($stats['menunggu'], 0, ',', '.')"
            icon="fas fa-hourglass-half"
            gradient="stat-card-gradient-2"
            note="Belum lunas" />

        <x-admin.stat-card
            label="Pembayaran Gagal"
            :value="number_format($stats['gagal'], 0, ',', '.')"
            icon="fas fa-times-circle"
            gradient="stat-card-gradient-5"
            note="Perlu bayar ulang" />

        <x-admin.stat-card
            label="Nilai Pesanan Lunas"
            :value="'Rp '.number_format($stats['nilai_lunas'], 0, ',', '.')"
            icon="fas fa-hand-holding-usd"
            gradient="stat-card-gradient-4"
            note="Total pelanggan ini" />
    </div>

    {{-- Profil pelanggan --}}
    <div class="glass-card mb-5 rounded-2xl p-5 shadow-lg">
        <h3 class="mb-4 text-sm font-bold text-gray-800 dark:text-white">
            <i aria-hidden="true" class="fas fa-address-card mr-1.5 text-coffee-500"></i> Profil Pelanggan
        </h3>

        <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-400">Email</dt>
                <dd class="mt-1 text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $customer->email }}</dd>
            </div>

            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-400">Telepon</dt>
                <dd class="mt-1 text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $customer->phone ?: '—' }}</dd>
            </div>

            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-400">Perusahaan</dt>
                <dd class="mt-1 text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $customer->company ?: '—' }}</dd>
            </div>

            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-400">Terdaftar sejak</dt>
                <dd class="mt-1 text-sm font-semibold text-gray-800 dark:text-gray-100">
                    {{ $customer->created_at?->format('d M Y') }}
                </dd>
            </div>

            <div class="sm:col-span-2">
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-400">Alamat</dt>
                <dd class="mt-1 text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $customer->fullAddress() }}</dd>
            </div>

            <div class="sm:col-span-2">
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-400">Catatan Internal</dt>
                <dd class="mt-1 whitespace-pre-line text-sm text-gray-600 dark:text-gray-300">{{ $customer->notes ?: '—' }}</dd>
            </div>
        </dl>
    </div>

    {{-- Riwayat pesanan pelanggan --}}
    <div class="glass-card overflow-hidden rounded-2xl shadow-lg">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 px-5 py-4 dark:border-gray-700">
            <div>
                <h3 class="text-sm font-bold text-gray-800 dark:text-white">
                    <i aria-hidden="true" class="fas fa-history mr-1.5 text-coffee-500"></i> Riwayat Pesanan
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Total {{ number_format($stats['pesanan'], 0, ',', '.') }} pesanan atas nama pelanggan ini.
                </p>
            </div>

            <a href="{{ route('admin.orders.create') }}"
               class="rounded-xl bg-coffee-600 px-4 py-2.5 text-sm font-bold text-white shadow-md transition hover:bg-coffee-700">
                <i aria-hidden="true" class="fas fa-plus mr-1"></i> Pesanan Baru
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                <thead class="bg-gray-50 text-left text-xs font-bold uppercase tracking-wide text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                    <tr>
                        <th class="px-5 py-3.5">Kode Pesanan</th>
                        <th class="px-5 py-3.5">Tanggal</th>
                        <th class="px-5 py-3.5">Total Pembayaran</th>
                        <th class="px-5 py-3.5">Kode Pembayaran</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                    @forelse ($orders as $order)
                        @php
                            $payment = $order->payments->first();
                        @endphp

                        <tr class="align-top transition hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-5 py-3.5">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="font-mono text-xs font-bold text-gray-800 transition hover:text-coffee-600 dark:text-gray-100 dark:hover:text-coffee-400">
                                    {{ $order->order_code }}
                                </a>
                            </td>

                            <td class="px-5 py-3.5 text-xs text-gray-500 dark:text-gray-400">
                                {{ $order->created_at->format('d M Y') }}
                                <span class="block text-[10px] text-gray-400">{{ $order->created_at->format('H:i') }} WIB</span>
                            </td>

                            <td class="px-5 py-3.5 font-semibold text-gray-800 dark:text-gray-100">
                                Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}
                            </td>

                            <td class="px-5 py-3.5">
                                @if ($payment)
                                    <span class="font-mono text-xs text-coffee-700 dark:text-coffee-300">{{ $payment->payment_code }}</span>
                                    <span class="block text-[10px] font-semibold text-gray-400">{{ $payment->methodLabel() }}</span>
                                @else
                                    <span class="text-xs text-gray-400">Belum ada</span>
                                @endif
                            </td>

                            <td class="px-5 py-3.5">
                                <x-admin.status-badge :status="$order->status" />
                            </td>

                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="inline-block rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-bold text-gray-600 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <i aria-hidden="true" class="fas fa-file-invoice-dollar mb-3 text-4xl text-gray-300 dark:text-gray-600"></i>
                                <p class="text-sm font-semibold text-gray-600 dark:text-gray-300">Pelanggan ini belum memiliki pesanan.</p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Buat pesanan baru agar kode pesanan dan kode pembayarannya dibuat otomatis.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($orders->hasPages())
            <div class="border-t border-gray-200 bg-gray-50 px-5 py-4 dark:border-gray-700 dark:bg-gray-800">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
@endsection
