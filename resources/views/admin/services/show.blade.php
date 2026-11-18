@extends('layouts.admin')

@section('title', 'Detail Booking Servis ' . $booking->booking_code)
@section('page_title', 'Detail Booking Servis')
@section('page_subtitle', 'Tiket ' . $booking->booking_code . ' · Unit: ' . $booking->machine_model . ' (' . $booking->serial_number . ')')

@section('content')
    <a href="{{ route('admin.services.index') }}"
       class="mb-4 inline-flex items-center gap-1.5 text-xs font-bold text-coffee-600 transition hover:text-coffee-700 dark:text-coffee-400">
        <i aria-hidden="true" class="fas fa-arrow-left"></i> Kembali ke Daftar Booking Servis
    </a>

    {{-- Header Info Tiket --}}
    <div class="mb-5 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Kode Tiket Servis</p>
            <h2 class="font-mono text-2xl font-bold text-gray-800 dark:text-white">{{ $booking->booking_code }}</h2>

            <div class="mt-2 flex flex-wrap items-center gap-2">
                @php
                    $statusClasses = match (true) {
                        $booking->isPending() => 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/40 dark:text-amber-300',
                        $booking->isConfirmed() => 'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-900/40 dark:text-blue-300',
                        in_array($booking->status, [\App\Models\ServiceBooking::STATUS_TEKNISI_OTW, \App\Models\ServiceBooking::STATUS_DALAM_PENGERJAAN], true) => 'bg-purple-100 text-purple-800 border-purple-300 dark:bg-purple-900/40 dark:text-purple-300',
                        $booking->status === \App\Models\ServiceBooking::STATUS_SELESAI => 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300',
                        default => 'bg-gray-100 text-gray-700 border-gray-300 dark:bg-gray-700 dark:text-gray-300',
                    };
                @endphp
                <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-bold {{ $statusClasses }}">
                    <i class="fas fa-circle text-[8px]"></i>
                    {{ $booking->statusLabel() }}
                </span>

                <span class="inline-flex items-center gap-1.5 rounded-full border border-gray-200 bg-gray-50 px-3 py-1 text-xs font-semibold text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    <i class="fas fa-tools text-coffee-500"></i>
                    {{ $booking->serviceTypeLabel() }}
                </span>
            </div>
        </div>

        <div class="text-right">
            <p class="text-xs text-gray-400">Dibuat Pada</p>
            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $booking->created_at->format('d M Y, H:i') }} WIB</p>
        </div>
    </div>

    {{-- Grid Konten Utama --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Kolom Kiri: Rincian Unit & Kendala Pelanggan --}}
        <div class="space-y-6 lg:col-span-2">
            {{-- Card Unit Mesin --}}
            <div class="glass-card rounded-2xl p-6 shadow-sm">
                <h3 class="mb-4 flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-gray-700 dark:text-gray-200">
                    <i class="fas fa-cogs text-coffee-500"></i>
                    Identitas Mesin & Riwayat Pesanan
                </h3>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                        <p class="text-xs text-gray-400">Model / Tipe Mesin</p>
                        <p class="mt-1 text-base font-bold text-gray-800 dark:text-gray-100">{{ $booking->machine_model }}</p>
                    </div>

                    <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                        <p class="text-xs text-gray-400">Nomor Seri Mesin (Serial Number)</p>
                        <p class="mt-1 font-mono text-base font-bold text-coffee-600 dark:text-coffee-400">{{ $booking->serial_number }}</p>
                    </div>

                    @if ($booking->order)
                        <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4 dark:border-gray-700 dark:bg-gray-800/50 sm:col-span-2">
                            <p class="text-xs text-gray-400">Terkait Purchase Order (PO)</p>
                            <div class="mt-1 flex items-center justify-between">
                                <span class="font-mono text-sm font-bold text-gray-800 dark:text-gray-200">{{ $booking->order->order_code }}</span>
                                <a href="{{ route('admin.orders.show', $booking->order) }}" class="text-xs font-bold text-coffee-600 hover:underline dark:text-coffee-400">
                                    Lihat Detail PO <i class="fas fa-external-link-alt ml-1"></i>
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Card Pelanggan & Lokasi Servis --}}
            <div class="glass-card rounded-2xl p-6 shadow-sm">
                <h3 class="mb-4 flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-gray-700 dark:text-gray-200">
                    <i class="fas fa-map-marker-alt text-coffee-500"></i>
                    Informasi Pelanggan & Lokasi Kunjungan
                </h3>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-xs text-gray-400">Nama Pelanggan / Kontak</p>
                        <p class="mt-1 font-semibold text-gray-800 dark:text-gray-100">{{ $booking->customer?->name ?? ($booking->user?->name ?? 'Pelanggan') }}</p>
                        <p class="text-xs text-gray-500">{{ $booking->customer?->phone ?? ($booking->user?->phone ?? '-') }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400">Perusahaan / Kafe</p>
                        <p class="mt-1 font-semibold text-gray-800 dark:text-gray-100">{{ $booking->company_name ?? ($booking->customer?->company ?? 'PT Kopi Nusantara') }}</p>
                        <p class="text-xs text-gray-500">{{ $booking->customer?->email ?? ($booking->user?->email ?? '-') }}</p>
                    </div>

                    <div class="sm:col-span-2">
                        <p class="text-xs text-gray-400">Alamat Lengkap Servis On-Site</p>
                        <div class="mt-1 rounded-xl border border-gray-200 bg-gray-50 p-3.5 text-sm text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                            {{ $booking->address }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card Kendala / Keluhan Pelanggan --}}
            <div class="glass-card rounded-2xl p-6 shadow-sm">
                <h3 class="mb-4 flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-gray-700 dark:text-gray-200">
                    <i class="fas fa-clipboard-list text-coffee-500"></i>
                    Deskripsi Kendala & Catatan Servis
                </h3>

                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    {{ $booking->issue_description ?: 'Tidak ada deskripsi tambahan.' }}
                </div>

                @if ($booking->admin_notes)
                    <div class="mt-4 rounded-xl border border-blue-200 bg-blue-50/60 p-4 text-sm text-blue-900 dark:border-blue-800 dark:bg-blue-950/40 dark:text-blue-300">
                        <p class="text-xs font-bold uppercase tracking-wide text-blue-600 dark:text-blue-400 mb-1">
                            <i class="fas fa-info-circle mr-1"></i> Catatan Internal Admin:
                        </p>
                        {{ $booking->admin_notes }}
                    </div>
                @endif
            </div>

            {{-- Card Foto Bukti Kerusakan / Error Mesin --}}
            <div class="glass-card rounded-2xl p-6 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-gray-700 dark:text-gray-200">
                        <i class="fas fa-camera text-rose-500"></i>
                        Foto Bukti Kerusakan / Error Mesin
                    </h3>
                    @if ($booking->photo_path)
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">
                            <i class="fas fa-check-circle"></i> Terlampir
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                            Tidak Dilampirkan (Opsional)
                        </span>
                    @endif
                </div>

                @if ($booking->photo_path)
                    @php
                        $photoUrl = $booking->mediaUrl() ?? $booking->photo_path;
                    @endphp
                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800/60 p-4">
                        <div class="group relative flex items-center justify-center overflow-hidden rounded-lg bg-black/5 dark:bg-black/30">
                            <img src="{{ $photoUrl }}"
                                 alt="Foto Kerusakan Mesin {{ $booking->booking_code }}"
                                 class="max-h-96 w-auto rounded-lg object-contain transition duration-200 group-hover:scale-[1.02]"
                                 loading="lazy" />
                        </div>

                        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 pt-3 dark:border-gray-700/60">
                            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                <i class="fas fa-cloud text-emerald-500"></i>
                                <span>Tersimpan di Cloud Supabase Storage</span>
                            </div>

                            <div class="flex items-center gap-2">
                                <a href="{{ $photoUrl }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="inline-flex items-center gap-1.5 rounded-lg bg-coffee-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm transition hover:bg-coffee-700">
                                    <i class="fas fa-external-link-alt"></i> Buka Gambar Penuh
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="rounded-xl border border-dashed border-gray-200 p-6 text-center dark:border-gray-700">
                        <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-400 dark:bg-gray-800">
                            <i class="fas fa-image text-sm"></i>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Customer tidak mengunggah foto kerusakan saat pengajuan booking ini.</p>
                        <p class="text-[11px] text-gray-400 dark:text-gray-500">Teknisi dapat melakukan inspeksi fisik langsung saat kunjungan ke lokasi.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Kolom Kanan: Panel Verifikasi & Penugasan Teknisi --}}
        <div class="space-y-6">
            <div class="glass-card rounded-2xl p-6 shadow-sm">
                <h3 class="mb-4 flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-gray-700 dark:text-gray-200">
                    <i class="fas fa-calendar-check text-coffee-500"></i>
                    Verifikasi & Penjadwalan Teknisi
                </h3>

                <form method="POST" action="{{ route('admin.services.assign', $booking) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="technician_name" class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-300">
                            Nama Teknisi yang Datang <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="technician_name" id="technician_name" required
                               value="{{ $booking->technician_name ?? '' }}"
                               placeholder="Contoh: Bpk. Budi Santoso"
                               class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2 text-sm outline-none focus:border-coffee-500 focus:ring-2 focus:ring-coffee-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
                    </div>

                    <div>
                        <label for="technician_phone" class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-300">
                            Nomor Kontak / WhatsApp Teknisi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="technician_phone" id="technician_phone" required
                               value="{{ $booking->technician_phone ?? '' }}"
                               placeholder="Contoh: 0812-3456-7890"
                               class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2 text-sm outline-none focus:border-coffee-500 focus:ring-2 focus:ring-coffee-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
                        <p class="mt-1 text-[11px] text-gray-400">Nomor ini tampil di aplikasi customer dan langsung terhubung dengan tombol "Hubungi" (WhatsApp & Telepon).</p>
                    </div>

                    <button type="submit"
                            class="w-full rounded-xl bg-coffee-600 py-3 text-sm font-bold text-white shadow-md transition hover:bg-coffee-700 focus:ring-4 focus:ring-coffee-300 dark:bg-coffee-700 dark:hover:bg-coffee-600">
                        <i class="fas {{ $booking->isPending() ? 'fa-check-circle' : 'fa-save' }} mr-1.5"></i>
                        {{ $booking->isPending() ? 'Konfirmasi & Tugaskan Teknisi' : 'Simpan Penugasan Teknisi' }}
                    </button>
                </form>

                @if ($booking->status !== \App\Models\ServiceBooking::STATUS_SELESAI)
                    <form method="POST" action="{{ route('admin.services.complete', $booking) }}" class="mt-3"
                          onsubmit="return confirm('Verifikasi bahwa perbaikan/servis unit {{ $booking->serial_number }} telah selesai?')">
                        @csrf
                        @method('PUT')
                        <button type="submit"
                                class="w-full rounded-xl bg-emerald-600 py-2.5 text-xs font-bold text-white shadow transition hover:bg-emerald-700 focus:ring-4 focus:ring-emerald-300">
                            <i class="fas fa-check-double mr-1.5"></i> Verifikasi Servis Selesai
                        </button>
                    </form>
                @endif

                <div class="mt-6 border-t border-gray-100 pt-4 dark:border-gray-700">
                    <form method="POST" action="{{ route('admin.services.destroy', $booking) }}"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus tiket booking servis ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full rounded-xl border border-red-200 px-4 py-2 text-xs font-bold text-red-600 transition hover:bg-red-50 dark:border-red-900/40 dark:text-red-400 dark:hover:bg-red-950/30">
                            <i class="fas fa-trash-alt mr-1"></i> Hapus Tiket Servis
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

