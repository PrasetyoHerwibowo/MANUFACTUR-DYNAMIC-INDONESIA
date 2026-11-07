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
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap items-end gap-2"
              data-auto-filter>
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

            @if (request()->filled('q') || request()->filled('status') || request()->filled('metode') || request()->filled('dari') || request()->filled('sampai'))
                <a href="{{ route('admin.orders.index') }}" class="rounded-xl border border-gray-300 px-4 py-2 text-sm font-bold text-gray-600 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">
                    Reset
                </a>
            @endif
        </form>


    </div>

    <div class="glass-card overflow-hidden rounded-2xl shadow-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                <thead class="bg-gray-50 text-left text-xs font-bold uppercase tracking-wide text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                    <tr>
                        <th class="px-5 py-3.5">Kode Bayar</th>
                        <th class="px-5 py-3.5">Kode PO</th>
                        <th class="px-5 py-3.5">Nama Pelanggan</th>
                        <th class="px-5 py-3.5">Model Mesin</th>
                        <th class="px-5 py-3.5 text-center">Jumlah</th>
                        <th class="px-5 py-3.5">Total Nilai</th>
                        <th class="px-5 py-3.5">Tahap Produksi</th>
                        <th class="px-5 py-3.5">Bukti Transfer</th>
                        <th class="px-5 py-3.5">Status Bayar</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                    @forelse ($orders as $order)
                        @php
                            $payment = $order->latestPayment;
                            $status = $payment?->status ?? $order->status;
                            $proof = $order->dp_proof ?: ($payment?->proof ?: null);

                            $stageColors = [
                                \App\Models\Order::STAGE_VERIFIKASI_DP => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
                                \App\Models\Order::STAGE_PERSIAPAN_BAHAN => 'bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-300',
                                \App\Models\Order::STAGE_PROSES_PRODUKSI => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300',
                                \App\Models\Order::STAGE_QUALITY_CONTROL => 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300',
                                \App\Models\Order::STAGE_MENUNGGU_PELUNASAN => 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300',
                                \App\Models\Order::STAGE_PENGIRIMAN_INSTALASI => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
                                \App\Models\Order::STAGE_AFTER_SALES => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300',
                            ];
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
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $order->customer?->company ?: $order->customer?->email }}</p>
                            </td>

                            <td class="px-5 py-3.5">
                                @forelse ($order->items as $item)
                                    <p class="text-xs font-semibold text-gray-800 dark:text-gray-100">
                                        {{ $item->machineName() }}
                                        @if ($item->machineCode())
                                            <span class="font-mono text-[10px] font-normal text-gray-400">{{ $item->machineCode() }}</span>
                                        @endif
                                    </p>
                                @empty
                                    <span class="text-gray-400">—</span>
                                @endforelse
                            </td>

                            <td class="px-5 py-3.5 text-center text-gray-700 dark:text-gray-200">
                                @forelse ($order->items as $item)
                                    <span class="block text-xs font-bold">{{ $item->quantity }}</span>
                                @empty
                                    <span class="text-gray-400">—</span>
                                @endforelse
                            </td>

                            <td class="px-5 py-3.5 font-semibold text-gray-800 dark:text-gray-100">
                                Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}
                            </td>

                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10.5px] font-bold {{ $stageColors[$order->production_status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $order->productionStageLabel() }}
                                </span>
                            </td>

                            <td class="px-5 py-3.5">
                                @if ($proof)
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                       class="inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-2 py-0.5 text-[11px] font-bold text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:text-emerald-300">
                                        <i class="fas fa-check-circle text-emerald-600"></i> Ada Bukti
                                    </a>
                                    @if ($order->production_status === \App\Models\Order::STAGE_VERIFIKASI_DP)
                                        <span class="mt-0.5 block text-[10px] font-bold text-amber-600 dark:text-amber-400">
                                            Perlu Verifikasi
                                        </span>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
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

                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="inline-flex items-center gap-1 rounded-lg bg-coffee-50 px-2.5 py-1 text-xs font-bold text-coffee-700 transition hover:bg-coffee-100 dark:bg-coffee-950/60 dark:text-coffee-300">
                                    Detail <i class="fas fa-chevron-right text-[10px]"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-5 py-12 text-center">
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
        Filter langsung diterapkan saat dropdown/ tanggal diubah. Klik kode pesanan pada tabel untuk melihat detail.
        Pembayaran yang melewati batas {{ \App\Models\Order::PAYMENT_WINDOW_HOURS }} jam otomatis berstatus
        <strong>Gagal</strong> dan pelanggan wajib mengulang pembayaran dari awal dengan kode pembayaran baru.
    </p>
@endsection

@push('scripts')
    <script>
        (function () {
            const form = document.querySelector('[data-auto-filter]');

            if (! form) {
                return;
            }

            // Dropdown & tanggal langsung dikirim begitu nilainya berubah.
            form.querySelectorAll('select, input[type="date"]').forEach((field) => {
                field.addEventListener('change', () => form.submit());
            });

            // Kolom pencarian dikirim otomatis setelah admin berhenti mengetik.
            let timer = 0;
            const search = form.querySelector('input[name="q"]');

            search?.addEventListener('input', () => {
                window.clearTimeout(timer);
                timer = window.setTimeout(() => form.submit(), 600);
            });
        })();
    </script>
@endpush
