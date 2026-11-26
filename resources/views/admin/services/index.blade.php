@extends('layouts.admin')

@section('title', 'Booking Servis Mesin')
@section('page_title', 'Booking Servis Mesin')
@section('page_subtitle', 'Verifikasi pengajuan servis customer dan penugasan teknisi pabrik')

@section('content')
    {{-- Ringkasan statistik booking servis --}}
    <div class="mb-6 grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.stat-card
            label="Total Booking Servis"
            :value="number_format($stats['total'], 0, ',', '.')"
            icon="fas fa-wrench"
            gradient="stat-card-gradient-1"
            :note="$stats['selesai'].' servis selesai'" />

        <x-admin.stat-card
            label="Pending"
            :value="number_format($stats['menunggu'], 0, ',', '.')"
            icon="fas fa-hourglass-half"
            gradient="stat-card-gradient-2"
            note="Perlu konfirmasi" />

        <x-admin.stat-card
            label="Confirmed"
            :value="number_format($stats['berjalan'], 0, ',', '.')"
            icon="fas fa-calendar-check"
            gradient="stat-card-gradient-4"
            note="Teknisi ditugaskan" />

        <x-admin.stat-card
            label="Servis Selesai"
            :value="number_format($stats['selesai'], 0, ',', '.')"
            icon="fas fa-check-double"
            gradient="stat-card-gradient-5"
            note="Maintenance tuntas" />
    </div>

    {{-- Filter & pencarian --}}
    <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
        <form method="GET" action="{{ route('admin.services.index') }}" class="flex flex-wrap items-end gap-2" data-auto-filter>
            <div>
                <label for="q" class="mb-1 block text-xs font-semibold text-gray-500 dark:text-gray-400">Cari</label>
                <input type="text" name="q" id="q" value="{{ request('q') }}"
                       placeholder="Kode tiket / no seri / model / customer"
                       class="w-72 rounded-xl border border-gray-300 bg-white px-3.5 py-2 text-sm outline-none focus:border-coffee-500 focus:ring-2 focus:ring-coffee-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
            </div>

            <div>
                <label for="status" class="mb-1 block text-xs font-semibold text-gray-500 dark:text-gray-400">Status Servis</label>
                <select name="status" id="status"
                        class="w-56 rounded-xl border border-gray-300 bg-white px-3.5 py-2 text-sm outline-none focus:border-coffee-500 focus:ring-2 focus:ring-coffee-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
                    <option value="">Semua status</option>
                    @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="dari" class="mb-1 block text-xs font-semibold text-gray-500 dark:text-gray-400">Tanggal Servis Dari</label>
                <input type="date" name="dari" id="dari" value="{{ request('dari') }}"
                       class="rounded-xl border border-gray-300 bg-white px-3.5 py-2 text-sm outline-none focus:border-coffee-500 focus:ring-2 focus:ring-coffee-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
            </div>

            <div>
                <label for="sampai" class="mb-1 block text-xs font-semibold text-gray-500 dark:text-gray-400">Sampai</label>
                <input type="date" name="sampai" id="sampai" value="{{ request('sampai') }}"
                       class="rounded-xl border border-gray-300 bg-white px-3.5 py-2 text-sm outline-none focus:border-coffee-500 focus:ring-2 focus:ring-coffee-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
            </div>

            @if (request()->filled('q') || request()->filled('status') || request()->filled('dari') || request()->filled('sampai'))
                <a href="{{ route('admin.services.index') }}" class="rounded-xl border border-gray-300 px-4 py-2 text-sm font-bold text-gray-600 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Tabel Daftar Booking Servis Customer --}}
    <div class="glass-card overflow-hidden rounded-2xl shadow-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                <thead class="bg-gray-50 text-left text-xs font-bold uppercase tracking-wide text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                    <tr>
                        <th class="px-5 py-3.5">Kode Tiket</th>
                        <th class="px-5 py-3.5">Pelanggan & Perusahaan</th>
                        <th class="px-5 py-3.5">Model Mesin & No Seri</th>
                        <th class="px-5 py-3.5">Jenis Servis</th>
                        <th class="px-5 py-3.5">Tanggal Servis</th>
                        <th class="px-5 py-3.5">Teknisi Bertugas</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white dark:divide-gray-700/60 dark:bg-gray-800">
                    @forelse ($bookings as $booking)
                        <tr class="transition hover:bg-gray-50/80 dark:hover:bg-gray-700/40">
                            <td class="whitespace-nowrap px-5 py-4 font-mono font-bold text-coffee-600 dark:text-coffee-400">
                                {{ $booking->booking_code }}
                                @if ($booking->order)
                                    <span class="block text-[11px] font-normal text-gray-400">PO: {{ $booking->order->order_code }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $booking->customer?->name ?? ($booking->user?->name ?? 'Pelanggan') }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $booking->company_name ?? ($booking->customer?->company ?? 'PT Kopi Nusantara') }}</p>
                                <p class="text-[11px] text-gray-400">{{ $booking->customer?->phone ?? ($booking->user?->phone ?? '-') }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-medium text-gray-800 dark:text-gray-200">{{ $booking->machine_model }}</p>
                                <p class="font-mono text-xs text-coffee-600 dark:text-coffee-400">{{ $booking->serial_number }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                    <i class="fas fa-tools text-coffee-500"></i>
                                    {{ $booking->serviceTypeLabel() }}
                                </span>
                                @if ($booking->photo_path)
                                    <a href="{{ route('admin.services.show', $booking) }}"
                                       class="mt-1 flex w-fit items-center gap-1 rounded bg-rose-50 px-2 py-0.5 text-[10.5px] font-bold text-rose-700 hover:bg-rose-100 dark:bg-rose-950/40 dark:text-rose-300 dark:hover:bg-rose-900/60"
                                       title="Lihat foto kerusakan mesin dari customer">
                                        <i class="fas fa-camera text-rose-600"></i> Ada Foto
                                    </a>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-4">
                                <p class="font-semibold text-gray-800 dark:text-gray-200">
                                    <i class="far fa-calendar-alt mr-1 text-coffee-500"></i>
                                    {{ $booking->scheduled_date ? $booking->scheduled_date->locale('id')->isoFormat('D MMMM Y') : '-' }}
                                </p>
                            </td>
                            <td class="px-5 py-4">
                                @if ($booking->technician_name)
                                    <div class="flex items-center gap-2">
                                        <div class="flex h-7 w-7 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300">
                                            <i class="fas fa-user-check text-xs"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-gray-800 dark:text-gray-200">{{ $booking->technician_name }}</p>
                                            <p class="text-[11px] font-mono text-gray-500 dark:text-gray-400">{{ $booking->technician_phone ?? '-' }}</p>
                                        </div>
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full border border-amber-300 bg-amber-50 px-2.5 py-0.5 text-[11px] font-semibold text-amber-800 dark:border-amber-800 dark:bg-amber-950/40 dark:text-amber-300">
                                        <i class="fas fa-user-clock text-[9px]"></i> Belum Ditugaskan
                                    </span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-4">
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
                                    {{ $booking->statusLabel() }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    {{-- Tombol Konfirmasi & Tugaskan Teknisi --}}
                                    @if ($booking->isPending())
                                        <button type="button"
                                                onclick="openAssignModal('{{ $booking->id }}', '{{ $booking->booking_code }}', '{{ addslashes($booking->technician_name ?? '') }}', '{{ addslashes($booking->technician_phone ?? '') }}', true)"
                                                class="inline-flex items-center gap-1 rounded-xl bg-blue-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700">
                                            <i class="fas fa-user-check"></i> Konfirmasi & Tugaskan
                                        </button>
                                    @elseif ($booking->status !== \App\Models\ServiceBooking::STATUS_SELESAI)
                                        <button type="button"
                                                onclick="openAssignModal('{{ $booking->id }}', '{{ $booking->booking_code }}', '{{ addslashes($booking->technician_name ?? '') }}', '{{ addslashes($booking->technician_phone ?? '') }}', false)"
                                                class="inline-flex items-center gap-1 rounded-xl bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-800 transition hover:bg-amber-100 dark:bg-amber-950/40 dark:text-amber-300 dark:hover:bg-amber-900/60"
                                                title="Ubah Teknisi Bertugas">
                                            <i class="fas fa-user-cog"></i> Ubah Teknisi
                                        </button>

                                        <form method="POST" action="{{ route('admin.services.complete', $booking) }}" class="inline"
                                              onsubmit="return confirm('Verifikasi bahwa pengerjaan servis untuk tiket {{ $booking->booking_code }} telah selesai?')">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 rounded-xl bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700 transition hover:bg-emerald-100 dark:bg-emerald-950/40 dark:text-emerald-300 dark:hover:bg-emerald-900/60"
                                                    title="Verifikasi Servis Selesai">
                                                <i class="fas fa-check-circle"></i> Selesai
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('admin.services.show', $booking) }}"
                                       class="inline-flex items-center gap-1 rounded-xl bg-coffee-50 px-3 py-1.5 text-xs font-bold text-coffee-700 transition hover:bg-coffee-100 dark:bg-coffee-900/40 dark:text-coffee-300 dark:hover:bg-coffee-900/60">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-gray-400">
                                <i class="fas fa-wrench mb-3 text-3xl text-gray-300 dark:text-gray-600"></i>
                                <p class="text-sm font-semibold">Belum ada data booking servis.</p>
                                <p class="text-xs text-gray-400">Pengajuan booking servis dari aplikasi mobile customer akan masuk ke sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($bookings->hasPages())
            <div class="border-t border-gray-200 p-4 dark:border-gray-700">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL: VERIFIKASI & TUGASKAN TEKNISI --}}
    <div id="assign-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/60 p-4 backdrop-blur-sm">
        <div class="flex min-h-full items-center justify-center">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-800">
                <div class="mb-4 flex items-center justify-between border-b border-gray-100 pb-3 dark:border-gray-700">
                    <div>
                        <h3 id="modal-assign-title" class="text-base font-bold text-gray-800 dark:text-white">Konfirmasi & Tugaskan Teknisi</h3>
                        <p class="text-xs text-gray-400">Tiket: <span id="modal-booking-code" class="font-mono font-bold text-coffee-600"></span></p>
                    </div>
                    <button type="button" onclick="closeAssignModal()" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="assign-form" method="POST" action="" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="assign_technician_name" class="mb-1 block text-xs font-semibold text-gray-700 dark:text-gray-300">
                            Nama Teknisi yang Datang <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="technician_name" id="assign_technician_name" required
                               placeholder="Contoh: Bpk. Budi Santoso"
                               class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2.5 text-sm outline-none focus:border-coffee-500 focus:ring-2 focus:ring-coffee-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                    </div>

                    <div>
                        <label for="assign_technician_phone" class="mb-1 block text-xs font-semibold text-gray-700 dark:text-gray-300">
                            Nomor Kontak / WhatsApp Teknisi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="technician_phone" id="assign_technician_phone" required
                               placeholder="Contoh: 0812-3456-7890"
                               class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2.5 text-sm outline-none focus:border-coffee-500 focus:ring-2 focus:ring-coffee-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                        <p class="mt-1 text-[11px] text-gray-400">Nomor ini langsung muncul di tombol <strong>Hubungi</strong> pada mobile app customer agar customer bisa langsung chat WA teknisi.</p>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" onclick="closeAssignModal()"
                                class="rounded-xl border border-gray-300 px-4 py-2.5 text-xs font-bold text-gray-600 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                            Batal
                        </button>
                        <button type="submit"
                                class="rounded-xl bg-blue-600 px-5 py-2.5 text-xs font-bold text-white shadow-md transition hover:bg-blue-700 focus:ring-4 focus:ring-blue-300">
                            <i class="fas fa-check-circle mr-1"></i> Konfirmasi & Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAssignModal(id, code, name, phone, isFirstVerify) {
            document.getElementById('modal-booking-code').textContent = code;
            document.getElementById('assign-form').action = '/admin/servis/' + id + '/tugaskan-teknisi';
            document.getElementById('assign_technician_name').value = name || '';
            document.getElementById('assign_technician_phone').value = phone || '';
            document.getElementById('modal-assign-title').textContent = isFirstVerify
                ? 'Konfirmasi Booking & Tugaskan Teknisi'
                : 'Ubah Data Teknisi Servis';
            document.getElementById('assign-modal').classList.remove('hidden');
        }

        function closeAssignModal() {
            document.getElementById('assign-modal').classList.add('hidden');
        }
    </script>
@endsection
