@extends('layouts.admin')

@section('title', 'Klaim Garansi & Verifikasi')
@section('page_title', 'Klaim Garansi Mesin')
@section('page_subtitle', 'Verifikasi foto/video kerusakan fisik mesin, validasi masa garansi, dan penjadwalan kunjungan perbaikan')

@section('content')
    {{-- Ringkasan statistik klaim garansi --}}
    <div class="mb-6 grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.stat-card
            label="Total Klaim Garansi"
            :value="number_format($stats['total'], 0, ',', '.')"
            icon="fas fa-shield-alt"
            gradient="stat-card-gradient-1"
            :note="$stats['dijadwalkan'].' terjadwal'" />

        <x-admin.stat-card
            label="Menunggu Verifikasi Bukti"
            :value="number_format($stats['menunggu'], 0, ',', '.')"
            icon="fas fa-search-plus"
            gradient="stat-card-gradient-2"
            note="Perlu cek bukti" />

        <x-admin.stat-card
            label="Kunjungan Perbaikan"
            :value="number_format($stats['dijadwalkan'], 0, ',', '.')"
            icon="fas fa-calendar-alt"
            gradient="stat-card-gradient-4"
            note="Teknisi dijadwalkan" />

        <x-admin.stat-card
            label="Perbaikan Selesai"
            :value="number_format($stats['selesai'], 0, ',', '.')"
            icon="fas fa-check-circle"
            gradient="stat-card-gradient-5"
            :note="$stats['ditolak'].' klaim ditolak'" />
    </div>

    {{-- Filter & pencarian --}}
    <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
        <form method="GET" action="{{ route('admin.warranties.index') }}" class="flex flex-wrap items-end gap-2" data-auto-filter>
            <div>
                <label for="q" class="mb-1 block text-xs font-semibold text-gray-500 dark:text-gray-400">Cari</label>
                <input type="text" name="q" id="q" value="{{ request('q') }}"
                       placeholder="Kode klaim / no seri / model / customer"
                       class="w-72 rounded-xl border border-gray-300 bg-white px-3.5 py-2 text-sm outline-none focus:border-coffee-500 focus:ring-2 focus:ring-coffee-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
            </div>

            <div>
                <label for="status" class="mb-1 block text-xs font-semibold text-gray-500 dark:text-gray-400">Status Klaim</label>
                <select name="status" id="status"
                        class="w-56 rounded-xl border border-gray-300 bg-white px-3.5 py-2 text-sm outline-none focus:border-coffee-500 focus:ring-2 focus:ring-coffee-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
                    <option value="">Semua status</option>
                    @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="dari" class="mb-1 block text-xs font-semibold text-gray-500 dark:text-gray-400">Tanggal Pengajuan Dari</label>
                <input type="date" name="dari" id="dari" value="{{ request('dari') }}"
                       class="rounded-xl border border-gray-300 bg-white px-3.5 py-2 text-sm outline-none focus:border-coffee-500 focus:ring-2 focus:ring-coffee-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
            </div>

            <div>
                <label for="sampai" class="mb-1 block text-xs font-semibold text-gray-500 dark:text-gray-400">Sampai</label>
                <input type="date" name="sampai" id="sampai" value="{{ request('sampai') }}"
                       class="rounded-xl border border-gray-300 bg-white px-3.5 py-2 text-sm outline-none focus:border-coffee-500 focus:ring-2 focus:ring-coffee-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
            </div>

            @if (request()->filled('q') || request()->filled('status') || request()->filled('dari') || request()->filled('sampai'))
                <a href="{{ route('admin.warranties.index') }}" class="rounded-xl border border-gray-300 px-4 py-2 text-sm font-bold text-gray-600 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Tabel klaim garansi --}}
    <div class="glass-card overflow-hidden rounded-2xl shadow-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                <thead class="bg-gray-50 text-left text-xs font-bold uppercase tracking-wide text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                    <tr>
                        <th class="px-5 py-3.5">Kode Klaim</th>
                        <th class="px-5 py-3.5">Pelanggan</th>
                        <th class="px-5 py-3.5">Model & No Seri</th>
                        <th class="px-5 py-3.5">Masa Garansi</th>
                        <th class="px-5 py-3.5">Bukti Kerusakan</th>
                        <th class="px-5 py-3.5">Status Penanganan</th>
                        <th class="px-5 py-3.5">Jadwal Kunjungan</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white dark:divide-gray-700/60 dark:bg-gray-800">
                    @forelse ($claims as $claim)
                        <tr class="transition hover:bg-gray-50/80 dark:hover:bg-gray-700/40">
                            <td class="whitespace-nowrap px-5 py-4 font-mono font-bold text-coffee-600 dark:text-coffee-400">
                                {{ $claim->claim_code }}
                                @if ($claim->order)
                                    <span class="block text-[11px] font-normal text-gray-400">PO: {{ $claim->order->order_code }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $claim->customer?->name ?? ($claim->user?->name ?? 'Pelanggan') }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $claim->customer?->company ?? ($claim->user?->company ?? '-') }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-medium text-gray-800 dark:text-gray-200">{{ $claim->machine_model ?? 'Mesin Industri' }}</p>
                                <p class="font-mono text-xs text-coffee-600 dark:text-coffee-400">{{ $claim->serial_number }}</p>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4">
                                @if ($claim->warranty_status === 'dalam_garansi')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 border border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-800">
                                        <i class="fas fa-shield-alt text-[10px]"></i> Garansi Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-semibold text-red-700 border border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-800">
                                        <i class="fas fa-exclamation-triangle text-[10px]"></i> Lewat Garansi
                                    </span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-4">
                                @if ($claim->isVideo())
                                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300">
                                        <i class="fas fa-video text-indigo-600"></i> Video MP4
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-bold text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                        <i class="fas fa-image text-gray-500"></i> Foto JPG/PNG
                                    </span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-4">
                                @php
                                    $statusClasses = match ($claim->status) {
                                        \App\Models\WarrantyClaim::STATUS_DALAM_PENINJAUAN => 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/40 dark:text-amber-300',
                                        \App\Models\WarrantyClaim::STATUS_DISETUJUI, \App\Models\WarrantyClaim::STATUS_KUNJUNGAN_DIJADWALKAN => 'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-900/40 dark:text-blue-300',
                                        \App\Models\WarrantyClaim::STATUS_TEKNISI_OTW, \App\Models\WarrantyClaim::STATUS_DALAM_PERBAIKAN => 'bg-purple-100 text-purple-800 border-purple-300 dark:bg-purple-900/40 dark:text-purple-300',
                                        \App\Models\WarrantyClaim::STATUS_SELESAI => 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300',
                                        \App\Models\WarrantyClaim::STATUS_DITOLAK => 'bg-red-100 text-red-800 border-red-300 dark:bg-red-900/40 dark:text-red-300',
                                        default => 'bg-gray-100 text-gray-700 border-gray-300 dark:bg-gray-700 dark:text-gray-300',
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-bold {{ $statusClasses }}">
                                    {{ $claim->statusLabel() }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4">
                                @if ($claim->scheduled_visit_date)
                                    <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $claim->scheduled_visit_date->format('d M Y') }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $claim->scheduled_visit_time ?? '10:00 WIB' }}</p>
                                @else
                                    <span class="text-xs italic text-gray-400">Belum dijadwalkan</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-right">
                                <a href="{{ route('admin.warranties.show', $claim) }}"
                                   class="inline-flex items-center gap-1.5 rounded-xl bg-coffee-50 px-3.5 py-1.5 text-xs font-bold text-coffee-700 transition hover:bg-coffee-100 dark:bg-coffee-900/40 dark:text-coffee-300 dark:hover:bg-coffee-900/60">
                                    <i class="fas fa-search-plus"></i> Verifikasi Bukti
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-gray-400">
                                <i class="fas fa-shield-alt mb-3 text-3xl text-gray-300 dark:text-gray-600"></i>
                                <p class="text-sm font-semibold">Belum ada pengajuan klaim garansi.</p>
                                <p class="text-xs text-gray-400">Klaim garansi beserta bukti video/foto dari aplikasi customer akan muncul di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($claims->hasPages())
            <div class="border-t border-gray-200 p-4 dark:border-gray-700">
                {{ $claims->links() }}
            </div>
        @endif
    </div>
@endsection

