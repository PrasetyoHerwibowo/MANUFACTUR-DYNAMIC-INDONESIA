@extends('layouts.admin')

@section('title', 'Pesanan & Pembayaran')
@section('page_title', 'Pesanan & Pembayaran')
@section('page_subtitle', 'Kode pesanan, kode pembayaran, dan status pembayaran pelanggan')

@section('content')
    {{-- Ringkasan pesanan & pembayaran --}}
    <div class="mb-6 grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.stat-card
            label="Total Pesanan"
            :value="number_format($stats['total'], 0, ',', '.')"
            icon="fas fa-file-invoice-dollar"
            gradient="stat-card-gradient-1"
            :note="$stats['lunas'].' lunas'" />

        <x-admin.stat-card
            label="Menunggu Pembayaran"
            :value="number_format($stats['menunggu'], 0, ',', '.')"
            icon="fas fa-hourglass-half"
            gradient="stat-card-gradient-2"
            :note="'Rp '.number_format($stats['nilai_menunggu'], 0, ',', '.')" />

        <x-admin.stat-card
            label="Pembayaran Lunas"
            :value="number_format($stats['lunas'], 0, ',', '.')"
            icon="fas fa-check-circle"
            gradient="stat-card-gradient-4"
            :note="'Rp '.number_format($stats['nilai_lunas'], 0, ',', '.')" />

        <x-admin.stat-card
            label="Pembayaran Gagal"
            :value="number_format($stats['gagal'], 0, ',', '.')"
            icon="fas fa-times-circle"
            gradient="stat-card-gradient-5"
            note="Perlu bayar ulang" />
    </div>

    {{-- Filter & tombol tambah pesanan --}}
    <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap items-end gap-2">
            <div>
                <label for="q" class="mb-1 block text-xs font-semibold text-gray-500 dark:text-gray-400">Cari</label>
                <input type="text" name="q" id="q" value="{{ request('q') }}"
                       placeholder="Kode pesanan / pembayaran / pelanggan"
                       class="w-64 rounded-xl border border-gray-300 bg-white px-3.5 py-2 text-sm outline-none focus:border-coffee-500 focus:ring-2 focus:ring-coffee-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
            </div>

            <div>
                <label for="status" class="mb-1 block text-xs font-semibold text-gray-500 dark:text-gray-400">Status pembayaran</label>
                <select name="status" id="status"
                        class="w-48 rounded-xl border border-gray-300 bg-white px-3.5 py-2 text-sm outline-none focus:border-coffee-500 focus:ring-2 focus:ring-coffee-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
                    <option value="">Semua status</option>
                    @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="metode" class="mb-1 block text-xs font-semibold text-gray-500 dark:text-gray-400">Metode</label>
                <select name="metode" id="metode"
                        class="w-44 rounded-xl border border-gray-300 bg-white px-3.5 py-2 text-sm outline-none focus:border-coffee-500 focus:ring-2 focus:ring-coffee-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
                    <option value="">Semua metode</option>
                    @foreach ($methodLabels as $value => $label)
                        <option value="{{ $value }}" @selected(request('metode') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="dari" class="mb-1 block text-xs font-semibold text-gray-500 dark:text-gray-400">Tanggal dari</label>
                <input type="date" name="dari" id="dari" value="{{ request('dari') }}"
                       class="rounded-xl border border-gray-300 bg-white px-3.5 py-2 text-sm outline-none focus:border-coffee-500 focus:ring-2 focus:ring-coffee-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
            </div>

            <div>
                <label for="sampai" class="mb-1 block text-xs font-semibold text-gray-500 dark:text-gray-400">Sampai</label>
                <input type="date" name="sampai" id="sampai" value="{{ request('sampai') }}"
                       class="rounded-xl border border-gray-300 bg-white px-3.5 py-2 text-sm outline-none focus:border-coffee-500 focus:ring-2 focus:ring-coffee-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
            </div>

            <button type="submit" class="rounded-xl bg-gray-800 px-4 py-2 text-sm font-bold text-white transition hover:bg-gray-700">
                Filter
            </button>

            @if (request()->filled('q') || request()->filled('status') || request()->filled('metode') || request()->filled('dari') || request()->filled('sampai'))
                <a href="{{ route('admin.orders.index') }}" class="rounded-xl border border-gray-300 px-4 py-2 text-sm font-bold text-gray-600 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">
                    Reset
                </a>
            @endif
        </form>

        <a href="{{ route('admin.orders.create') }}"
           class="rounded-xl bg-coffee-600 px-4 py-2.5 text-sm font-bold text-white shadow-md transition hover:bg-coffee-700">
            <i aria-hidden="true" class="fas fa-plus mr-1"></i> Tambah Pesanan
        </a>
    </div>

    <div class="glass-card overflow-hidden rounded-2xl shadow-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                <thead class="bg-gray-50 text-left text-xs font-bold uppercase tracking-wide text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                    <tr>
                        <th class="px-5 py-3.5">Kode Pembayaran</th>
                        <th class="px-5 py-3.5">Kode Pesanan</th>
                        <th class="px-5 py-3.5">Nama Pelanggan</th>
                        <th class="px-5 py-3.5">Total Pembayaran</th>
                        <th class="hidden px-5 py-3.5 md:table-cell">Metode Pembayaran</th>
                        <th class="hidden px-5 py-3.5 lg:table-cell">Tanggal</th>
                        <th class="px-5 py-3.5">Status Pembayaran</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                    @forelse ($orders as $order)
                        @php
                            $payment = $order->latestPayment;
                            $status = $payment?->status ?? $order->status;
                        @endphp

                        <tr class="align-top transition hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-5 py-3.5">
                                @if ($payment)
                                    <span class="font-mono text-xs font-bold text-coffee-700 dark:text-coffee-300">{{ $payment->payment_code }}</span>

                                    @if ($payment->attempt > 1)
                                        <span class="mt-1 block text-[10px] font-semibold text-gray-400">Percobaan ke-{{ $payment->attempt }}</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">Belum ada</span>
                                @endif
                            </td>

                            <td class="px-5 py-3.5">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="font-mono text-xs font-bold text-gray-800 transition hover:text-coffee-600 dark:text-gray-100 dark:hover:text-coffee-400">
                                    {{ $order->order_code }}
                                </a>
                            </td>

                            <td class="px-5 py-3.5">
                                <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $order->customer?->name ?? 'Pelanggan dihapus' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $order->customer?->email }}</p>
                            </td>

                            <td class="px-5 py-3.5 font-semibold text-gray-800 dark:text-gray-100">
                                Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}
                            </td>

                            <td class="hidden px-5 py-3.5 text-gray-600 dark:text-gray-300 md:table-cell">
                                {{ $payment?->methodLabel() ?? '—' }}
                            </td>

                            <td class="hidden px-5 py-3.5 text-xs text-gray-500 dark:text-gray-400 lg:table-cell">
                                {{ $order->created_at->format('d M Y') }}
                                <span class="block text-[10px] text-gray-400">{{ $order->created_at->format('H:i') }} WIB</span>
                            </td>

                            <td class="px-5 py-3.5">
                                <x-admin.status-badge :status="$status" />

                                @if ($payment && $payment->status === \App\Models\Payment::STATUS_MENUNGGU && $payment->expires_at)
                                    <span class="mt-1 block text-[10px] text-gray-500 dark:text-gray-400">
                                        Batas {{ $payment->expires_at->format('d M Y H:i') }}
                                    </span>
                                @elseif ($payment && $payment->failure_reason)
                                    <span class="mt-1 block text-[10px] text-red-500">{{ $payment->failure_reason }}</span>
                                @endif
                            </td>

                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                       class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-bold text-gray-600 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">
                                        Detail
                                    </a>

                                    @if ($payment && $payment->status === \App\Models\Payment::STATUS_MENUNGGU)
                                        <form method="POST" action="{{ route('admin.payments.verify', $payment) }}"
                                              data-confirm="Verifikasi pembayaran {{ $payment->payment_code }} sebagai lunas?">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit"
                                                    class="rounded-lg border border-green-200 px-3 py-1.5 text-xs font-bold text-green-700 transition hover:bg-green-50 dark:border-green-500/40 dark:text-green-300 dark:hover:bg-green-500/10">
                                                Lunas
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.payments.fail', $payment) }}"
                                              data-confirm="Tandai pembayaran {{ $payment->payment_code }} gagal? Pelanggan harus mengulang pembayaran dari awal.">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit"
                                                    class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-bold text-red-600 transition hover:bg-red-50 dark:border-red-500/40 dark:text-red-300 dark:hover:bg-red-500/10">
                                                Gagal
                                            </button>
                                        </form>
                                    @elseif ($status === \App\Models\Order::STATUS_GAGAL)
                                        <a href="{{ route('admin.orders.show', $order) }}#pembayaran"
                                           class="rounded-lg border border-coffee-300 px-3 py-1.5 text-xs font-bold text-coffee-700 transition hover:bg-coffee-50 dark:border-coffee-500/40 dark:text-coffee-300 dark:hover:bg-coffee-500/10">
                                            Bayar Ulang
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center">
                                <i aria-hidden="true" class="fas fa-file-invoice-dollar mb-3 text-4xl text-gray-300 dark:text-gray-600"></i>
                                <p class="text-sm font-semibold text-gray-600 dark:text-gray-300">Belum ada pesanan & pembayaran yang sesuai.</p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Tambahkan pesanan baru untuk membuat kode pesanan dan kode pembayarannya.
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

    <p class="mt-4 text-xs text-gray-500 dark:text-gray-400">
        <i aria-hidden="true" class="fas fa-info-circle mr-1 text-coffee-500"></i>
        Pembayaran yang melewati batas pembayaran otomatis berstatus <strong>Gagal</strong> dan pelanggan wajib
        mengulang pembayaran dari awal dengan kode pembayaran baru.
    </p>
@endsection
