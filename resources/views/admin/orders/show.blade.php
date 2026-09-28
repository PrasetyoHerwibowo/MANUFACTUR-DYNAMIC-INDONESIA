@extends('layouts.admin')

@section('title', 'Detail Pesanan ' . $order->order_code)
@section('page_title', 'Detail Pesanan')
@section('page_subtitle', 'Kode pesanan ' . $order->order_code . ' · ' . ($order->customer?->name ?? 'Pelanggan dihapus'))

@php
    $pendingPayment = $order->payments->firstWhere('status', \App\Models\Payment::STATUS_MENUNGGU);
    $activePayment = $pendingPayment ?? $order->payments->first();
@endphp

@section('content')
    <a href="{{ route('admin.orders.index') }}"
       class="mb-4 inline-flex items-center gap-1.5 text-xs font-bold text-coffee-600 transition hover:text-coffee-700 dark:text-coffee-400">
        <i aria-hidden="true" class="fas fa-arrow-left"></i> Kembali ke Pesanan & Pembayaran
    </a>

    <div class="mb-5 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Kode Pesanan</p>
            <h2 class="font-mono text-2xl font-bold text-gray-800 dark:text-white">{{ $order->order_code }}</h2>

            <div class="mt-2 flex flex-wrap items-center gap-2">
                <x-admin.status-badge :status="$order->status" />
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    Dibuat {{ $order->created_at->format('d M Y H:i') }} WIB
                </span>

                @if ($order->paid_at)
                    <span class="text-xs text-green-600 dark:text-green-400">
                        Dibayar {{ $order->paid_at->format('d M Y H:i') }} WIB
                    </span>
                @endif
            </div>
        </div>

        <div class="text-right">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Total Pembayaran</p>
            <p class="text-2xl font-bold text-coffee-700 dark:text-coffee-300">
                Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}
            </p>
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        {{-- Kolom kiri: pelanggan & item pesanan --}}
        <div class="space-y-5 lg:col-span-2">
            <div class="glass-card rounded-2xl p-5 shadow-lg">
                <h3 class="mb-3 text-sm font-bold text-gray-800 dark:text-white">
                    <i aria-hidden="true" class="fas fa-user mr-1.5 text-coffee-500"></i> Nama Pelanggan
                </h3>

                @if ($order->customer)
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-coffee-500 to-coffee-700 text-sm font-bold text-white">
                            {{ $order->customer->initials() }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="font-bold text-gray-800 dark:text-white">{{ $order->customer->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $order->customer->customer_code }}</p>

                            <dl class="mt-3 grid gap-2 text-xs sm:grid-cols-2">
                                <div>
                                    <dt class="text-gray-400">Email</dt>
                                    <dd class="font-semibold text-gray-700 dark:text-gray-200">{{ $order->customer->email }}</dd>
                                </div>
                                <div>
                                    <dt class="text-gray-400">Telepon</dt>
                                    <dd class="font-semibold text-gray-700 dark:text-gray-200">{{ $order->customer->phone ?: '—' }}</dd>
                                </div>
                                <div class="sm:col-span-2">
                                    <dt class="text-gray-400">Alamat</dt>
                                    <dd class="font-semibold text-gray-700 dark:text-gray-200">{{ $order->customer->fullAddress() }}</dd>
                                </div>
                            </dl>

                            <a href="{{ route('admin.customers.show', $order->customer) }}"
                               class="mt-3 inline-block text-xs font-bold text-coffee-600 hover:text-coffee-700 dark:text-coffee-400">
                                Lihat profil pelanggan →
                            </a>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pelanggan sudah tidak tersedia.</p>
                @endif
            </div>

            <div class="glass-card overflow-hidden rounded-2xl shadow-lg">
                <h3 class="border-b border-gray-200 px-5 py-4 text-sm font-bold text-gray-800 dark:border-gray-700 dark:text-white">
                    <i aria-hidden="true" class="fas fa-cogs mr-1.5 text-coffee-500"></i> Item Pesanan
                </h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                        <thead class="bg-gray-50 text-left text-xs font-bold uppercase tracking-wide text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                            <tr>
                                <th class="px-5 py-3">Model / Item</th>
                                <th class="px-5 py-3">Harga Satuan</th>
                                <th class="px-5 py-3 text-center">Jumlah</th>
                                <th class="px-5 py-3 text-right">Subtotal</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                            @foreach ($order->items as $item)
                                <tr>
                                    <td class="px-5 py-3">
                                        <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $item->machineName() }}</p>

                                        @if ($item->machine)
                                            <a href="{{ route('products.show', $item->machine) }}" target="_blank"
                                               class="text-xs text-coffee-600 hover:underline dark:text-coffee-400">
                                                {{ $item->machineCode() ?: $item->machine->name }}
                                            </a>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-300">
                                        Rp {{ number_format((float) $item->unit_price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-5 py-3 text-center text-gray-600 dark:text-gray-300">{{ $item->quantity }}</td>
                                    <td class="px-5 py-3 text-right font-bold text-gray-800 dark:text-gray-100">
                                        Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                        <tfoot class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <td colspan="3" class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                    Total Pembayaran
                                </td>
                                <td class="px-5 py-3 text-right text-base font-bold text-coffee-700 dark:text-coffee-300">
                                    Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            @if ($order->notes)
                <div class="glass-card rounded-2xl p-5 shadow-lg">
                    <h3 class="mb-2 text-sm font-bold text-gray-800 dark:text-white">
                        <i aria-hidden="true" class="fas fa-sticky-note mr-1.5 text-coffee-500"></i> Catatan Pesanan
                    </h3>
                    <p class="whitespace-pre-line text-sm text-gray-600 dark:text-gray-300">{{ $order->notes }}</p>
                </div>
            @endif

            <div class="glass-card overflow-hidden rounded-2xl shadow-lg">
                <h3 class="border-b border-gray-200 px-5 py-4 text-sm font-bold text-gray-800 dark:border-gray-700 dark:text-white">
                    <i aria-hidden="true" class="fas fa-history mr-1.5 text-coffee-500"></i>
                    Riwayat Pembayaran ({{ $order->payments->count() }} kode pembayaran)
                </h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                        <thead class="bg-gray-50 text-left text-xs font-bold uppercase tracking-wide text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                            <tr>
                                <th class="px-5 py-3">Kode Pembayaran</th>
                                <th class="px-5 py-3">Metode</th>
                                <th class="px-5 py-3">Jumlah</th>
                                <th class="px-5 py-3">Batas Pembayaran</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3">Bukti</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                            @forelse ($order->payments as $payment)
                                <tr>
                                    <td class="px-5 py-3">
                                        <span class="font-mono text-xs font-bold text-coffee-700 dark:text-coffee-300">{{ $payment->payment_code }}</span>
                                        <span class="block text-[10px] font-semibold text-gray-400">Percobaan ke-{{ $payment->attempt }}</span>
                                    </td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $payment->methodLabel() }}</td>
                                    <td class="px-5 py-3 font-semibold text-gray-800 dark:text-gray-100">
                                        Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-5 py-3 text-xs text-gray-500 dark:text-gray-400">
                                        {{ $payment->expires_at?->format('d M Y H:i') ?? 'Tanpa batas' }}
                                    </td>
                                    <td class="px-5 py-3">
                                        <x-admin.status-badge :status="$payment->status" />

                                        @if ($payment->paid_at)
                                            <span class="mt-1 block text-[10px] text-green-600 dark:text-green-400">
                                                Lunas {{ $payment->paid_at->format('d M Y H:i') }}
                                            </span>
                                        @elseif ($payment->failure_reason)
                                            <span class="mt-1 block text-[10px] text-red-500">{{ $payment->failure_reason }}</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3">
                                        @if ($payment->proof)
                                            <a href="{{ \App\Support\ImageUploader::url($payment->proof) }}" target="_blank"
                                               class="text-xs font-bold text-coffee-600 hover:underline dark:text-coffee-400">Lihat</a>
                                        @else
                                            <span class="text-xs text-gray-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-8 text-center text-xs text-gray-500 dark:text-gray-400">
                                        Belum ada pembayaran untuk pesanan ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Kolom kanan: pembayaran & tindakan --}}
        <div class="space-y-5">
            <div id="pembayaran" class="glass-card rounded-2xl p-5 shadow-lg">
                <h3 class="mb-3 text-sm font-bold text-gray-800 dark:text-white">
                    <i aria-hidden="true" class="fas fa-credit-card mr-1.5 text-coffee-500"></i> Ringkasan Pembayaran
                </h3>

                @if ($activePayment)
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Kode Pembayaran</p>
                    <p class="font-mono text-lg font-bold text-coffee-700 dark:text-coffee-300">{{ $activePayment->payment_code }}</p>

                    <dl class="mt-4 space-y-2 text-sm">
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-gray-500 dark:text-gray-400">Metode</dt>
                            <dd class="font-semibold text-gray-800 dark:text-gray-100">{{ $activePayment->methodLabel() }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-gray-500 dark:text-gray-400">Jumlah</dt>
                            <dd class="font-semibold text-gray-800 dark:text-gray-100">
                                Rp {{ number_format((float) $activePayment->amount, 0, ',', '.') }}
                            </dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-gray-500 dark:text-gray-400">Tanggal</dt>
                            <dd class="font-semibold text-gray-800 dark:text-gray-100">
                                {{ $activePayment->created_at->format('d M Y H:i') }}
                            </dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-gray-500 dark:text-gray-400">Batas pembayaran</dt>
                            <dd class="font-semibold text-gray-800 dark:text-gray-100">
                                {{ $activePayment->expires_at?->format('d M Y H:i') ?? 'Tanpa batas' }}
                            </dd>
                        </div>
                    </dl>

                    <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-gray-200 pt-4 dark:border-gray-700">
                        <x-admin.status-badge :status="$activePayment->status" />

                        @if ($activePayment->status === \App\Models\Payment::STATUS_MENUNGGU)
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                Sisa waktu {{ $activePayment->remainingTime() }}
                            </span>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Pesanan ini belum memiliki kode pembayaran. Buat pembayaran baru di bawah.
                    </p>
                @endif
            </div>

            @if ($pendingPayment)
                <div class="glass-card rounded-2xl p-5 shadow-lg">
                    <h3 class="text-sm font-bold text-gray-800 dark:text-white">
                        <i aria-hidden="true" class="fas fa-check-double mr-1.5 text-coffee-500"></i> Verifikasi Pembayaran
                    </h3>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Tandai lunas setelah dana diterima, atau tandai gagal bila pelanggan tidak menyelesaikan
                        pembayaran.
                    </p>

                    <form method="POST" action="{{ route('admin.payments.verify', $pendingPayment) }}" class="mt-4 space-y-3">
                        @csrf
                        @method('PUT')

                        <x-admin.file name="proof" label="Bukti Pembayaran (opsional)" hint="Unggah bukti transfer / kuitansi bila ada." />

                        <x-admin.input name="notes" label="Catatan Verifikasi" placeholder="Contoh: transfer BCA 22 Sep 2026" />

                        <button type="submit"
                                class="w-full rounded-xl bg-green-600 px-4 py-2.5 text-sm font-bold text-white shadow-md transition hover:bg-green-700">
                            <i aria-hidden="true" class="fas fa-check mr-1"></i> Tandai Lunas
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.payments.fail', $pendingPayment) }}" class="mt-4 space-y-3 border-t border-gray-200 pt-4 dark:border-gray-700"
                          data-confirm="Tandai pembayaran {{ $pendingPayment->payment_code }} gagal? Pelanggan harus mengulang pembayaran dari awal.">
                        @csrf
                        @method('PUT')

                        <x-admin.input name="failure_reason" label="Alasan Gagal" value="Ditandai gagal oleh admin" />

                        <button type="submit"
                                class="w-full rounded-xl border border-red-200 px-4 py-2.5 text-sm font-bold text-red-600 transition hover:bg-red-50 dark:border-red-500/40 dark:text-red-300 dark:hover:bg-red-500/10">
                            <i aria-hidden="true" class="fas fa-times mr-1"></i> Tandai Gagal
                        </button>
                    </form>
                </div>

            @elseif ($order->status === \App\Models\Order::STATUS_LUNAS)
                <div class="glass-card rounded-2xl p-5 shadow-lg">
                    <h3 class="text-sm font-bold text-gray-800 dark:text-white">
                        <i aria-hidden="true" class="fas fa-check-circle mr-1.5 text-green-500"></i> Pembayaran Selesai
                    </h3>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Pesanan ini sudah lunas{{ $order->paid_at ? ' pada '.$order->paid_at->format('d M Y H:i').' WIB' : '' }}.
                        Kode pembayaran baru hanya dibuat bila pelanggan harus mengulang pembayaran pada pesanan yang
                        belum lunas.
                    </p>
                </div>
            @else
                <div class="glass-card rounded-2xl p-5 shadow-lg">
                    <h3 class="text-sm font-bold text-gray-800 dark:text-white">
                        <i aria-hidden="true" class="fas fa-redo mr-1.5 text-coffee-500"></i> Ulangi Pembayaran dari Awal
                    </h3>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Pesanan ini tidak memiliki pembayaran yang menunggu. Buat kode pembayaran baru agar pelanggan
                        mengulang pembayaran dari awal dengan batas {{ \App\Models\Order::PAYMENT_WINDOW_HOURS }} jam
                        sejak kode dibuat.
                    </p>

                    <form method="POST" action="{{ route('admin.orders.payments.renew', $order) }}" class="mt-4 space-y-4">
                        @csrf

                        <x-admin.select
                            name="method"
                            label="Metode Pembayaran"
                            :options="$methodLabels"
                            :selected="$activePayment?->method"
                            placeholder="— Pilih metode —"
                            required />

                        <x-admin.input name="notes" label="Catatan Pembayaran" placeholder="Contoh: pelanggan minta kode transfer ulang" />

                        <button type="submit"
                                class="w-full rounded-xl bg-coffee-600 px-4 py-2.5 text-sm font-bold text-white shadow-md transition hover:bg-coffee-700">
                            <i aria-hidden="true" class="fas fa-plus mr-1"></i> Buat Kode Pembayaran Baru
                        </button>
                    </form>
                </div>
            @endif

            <div class="glass-card rounded-2xl p-5 shadow-lg">
                <h3 class="text-sm font-bold text-gray-800 dark:text-white">
                    <i aria-hidden="true" class="fas fa-trash mr-1.5 text-red-500"></i> Hapus Pesanan
                </h3>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Menghapus pesanan juga menghapus seluruh item dan riwayat pembayarannya.
                </p>

                <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" class="mt-4"
                      data-confirm="Hapus pesanan {{ $order->order_code }} beserta seluruh pembayarannya?">
                    @csrf
                    @method('DELETE')

                    <button type="submit"
                            class="w-full rounded-xl border border-red-200 px-4 py-2.5 text-sm font-bold text-red-600 transition hover:bg-red-50 dark:border-red-500/40 dark:text-red-300 dark:hover:bg-red-500/10">
                        Hapus Pesanan
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
