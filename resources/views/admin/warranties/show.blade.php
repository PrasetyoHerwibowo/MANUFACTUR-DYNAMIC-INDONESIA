@extends('layouts.admin')

@section('title', 'Detail Klaim Garansi ' . $claim->claim_code)
@section('page_title', 'Verifikasi Klaim Garansi')
@section('page_subtitle', 'Klaim ' . $claim->claim_code . ' · Unit: ' . ($claim->machine_model ?? 'Mesin') . ' (' . $claim->serial_number . ')')

@section('content')
    <a href="{{ route('admin.warranties.index') }}"
       class="mb-4 inline-flex items-center gap-1.5 text-xs font-bold text-coffee-600 transition hover:text-coffee-700 dark:text-coffee-400">
        <i aria-hidden="true" class="fas fa-arrow-left"></i> Kembali ke Daftar Klaim Garansi
    </a>

    {{-- Header Info Klaim --}}
    <div class="mb-5 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Nomor Registrasi Klaim</p>
            <h2 class="font-mono text-2xl font-bold text-gray-800 dark:text-white">{{ $claim->claim_code }}</h2>

            <div class="mt-2 flex flex-wrap items-center gap-2">
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
                    <i class="fas fa-circle text-[8px]"></i>
                    {{ $claim->statusLabel() }}
                </span>

                @if ($claim->warranty_status === 'dalam_garansi')
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-300 bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300">
                        <i class="fas fa-shield-alt text-emerald-600"></i> Garansi Resmi Aktif
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-red-300 bg-red-50 px-3 py-1 text-xs font-bold text-red-800 dark:border-red-800 dark:bg-red-950/40 dark:text-red-300">
                        <i class="fas fa-exclamation-triangle text-red-600"></i> Masa Garansi Lewat
                    </span>
                @endif
            </div>
        </div>

        <div class="text-right">
            <p class="text-xs text-gray-400">Tanggal Diajukan</p>
            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $claim->created_at->format('d M Y, H:i') }} WIB</p>
        </div>
    </div>

    {{-- Progress klaim: stepper status untuk admin --}}
    @php
        $claimSteps = [
            ['label' => 'Klaim Diterima', 'icon' => 'fa-inbox'],
            ['label' => 'Verifikasi', 'icon' => 'fa-clipboard-check'],
            ['label' => 'Jadwal Teknisi', 'icon' => 'fa-calendar-check'],
            ['label' => 'Teknisi OTW', 'icon' => 'fa-truck'],
            ['label' => 'Perbaikan', 'icon' => 'fa-tools'],
            ['label' => 'Selesai', 'icon' => 'fa-check'],
        ];
        $claimStepIndex = match ($claim->status) {
            \App\Models\WarrantyClaim::STATUS_DALAM_PENINJAUAN => 0,
            \App\Models\WarrantyClaim::STATUS_DISETUJUI => 1,
            \App\Models\WarrantyClaim::STATUS_KUNJUNGAN_DIJADWALKAN => 2,
            \App\Models\WarrantyClaim::STATUS_TEKNISI_OTW => 3,
            \App\Models\WarrantyClaim::STATUS_DALAM_PERBAIKAN => 4,
            \App\Models\WarrantyClaim::STATUS_SELESAI => 5,
            default => -1,
        };
    @endphp
    <section aria-label="Tahapan klaim garansi" class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-2">
            <div>
                <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100">Progres Penanganan Klaim</h3>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Pantau tahapan klaim dari penerimaan hingga perbaikan.</p>
            </div>
            @if ($claimStepIndex < 0)
                <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-700 dark:bg-red-950/50 dark:text-red-300">Klaim Ditolak</span>
            @else
                <span class="rounded-full bg-coffee-50 px-3 py-1 text-xs font-bold text-coffee-700 dark:bg-coffee-900/40 dark:text-coffee-300">Tahap {{ $claimStepIndex + 1 }} dari {{ count($claimSteps) }}</span>
            @endif
        </div>

        <ol class="flex items-start" role="list">
            @foreach ($claimSteps as $index => $step)
                @php
                    $done = $claimStepIndex >= $index;
                    $current = $claimStepIndex === $index;
                @endphp
                <li class="relative flex min-w-0 flex-1 flex-col items-center text-center" aria-current="{{ $current ? 'step' : 'false' }}">
                    @if ($index > 0)
                        <span aria-hidden="true" class="absolute right-1/2 top-4 h-0.5 w-full {{ $claimStepIndex >= $index ? 'bg-emerald-500' : 'bg-gray-200 dark:bg-gray-700' }}"></span>
                    @endif
                    <span class="relative z-10 flex h-8 w-8 items-center justify-center rounded-full border-2 {{ $done ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-gray-300 bg-white text-gray-400 dark:border-gray-600 dark:bg-gray-900' }} {{ $current ? 'ring-4 ring-emerald-100 dark:ring-emerald-900/50' : '' }}">
                        <i aria-hidden="true" class="fas {{ $done && !$current ? 'fa-check' : $step['icon'] }} text-xs"></i>
                    </span>
                    <span class="mt-2 max-w-20 text-[10px] font-semibold leading-tight {{ $done ? 'text-gray-800 dark:text-gray-200' : 'text-gray-400 dark:text-gray-500' }} sm:max-w-none sm:text-xs">{{ $step['label'] }}</span>
                </li>
            @endforeach
        </ol>
    </section>

    {{-- Grid Konten Utama --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Kolom Kiri: Rincian Klaim, Unit Mesin & BUKTI FOTO / VIDEO KERUSAKAN --}}
        <div class="space-y-6 lg:col-span-2">
            {{-- Card Bukti Fisik Foto/Video Kerusakan (Fitur Utama Verifikasi Admin) --}}
            <div class="glass-card rounded-2xl p-6 shadow-sm border border-coffee-200/50 dark:border-coffee-800/30">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-gray-800 dark:text-gray-100">
                        <i class="fas fa-photo-video text-coffee-600"></i>
                        Bukti Fisik Kerusakan Mesin (Foto / Video MP4)
                    </h3>
                    @if ($claim->mediaUrl())
                        <a href="{{ $claim->mediaUrl() }}" target="_blank"
                           class="inline-flex items-center gap-1.5 rounded-lg bg-gray-100 px-3 py-1 text-xs font-bold text-gray-700 transition hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                            <i class="fas fa-external-link-alt"></i> Buka File Asli
                        </a>
                    @endif
                </div>

                @if ($claim->mediaUrl())
                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-gray-900 p-2 text-center dark:border-gray-700">
                        @if ($claim->isVideo())
                            {{-- Pemutar Video MP4 Asli untuk Admin --}}
                            <div class="relative w-full">
                                <video controls class="mx-auto max-h-[420px] w-full rounded-lg bg-black shadow-inner" preload="metadata">
                                    <source src="{{ $claim->mediaUrl() }}" type="video/mp4">
                                    Browser Anda tidak mendukung pemutaran video HTML5 langsung.
                                </video>
                                <div class="mt-2 text-left px-2 py-1 flex items-center justify-between text-xs text-gray-400">
                                    <span><i class="fas fa-file-video mr-1 text-indigo-400"></i> Berkas Video MP4 Bukti Kerusakan</span>
                                    <span>Maks. 25 MB</span>
                                </div>
                            </div>
                        @else
                            {{-- Penampil Foto Bukti Kerusakan --}}
                            <div class="group relative">
                                <img src="{{ $claim->mediaUrl() }}" alt="Bukti Kerusakan {{ $claim->serial_number }}"
                                     class="mx-auto max-h-[420px] w-auto max-w-full rounded-lg object-contain shadow">
                                <div class="mt-2 text-left px-2 py-1 flex items-center justify-between text-xs text-gray-400">
                                    <span><i class="fas fa-file-image mr-1 text-emerald-400"></i> Berkas Foto Bukti Kerusakan</span>
                                    <span>Format Gambar JPG/PNG</span>
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="rounded-xl border border-dashed border-gray-300 p-8 text-center text-gray-400 dark:border-gray-700">
                        <i class="fas fa-file-excel mb-2 text-3xl text-gray-300 dark:text-gray-600"></i>
                        <p class="text-sm font-semibold">Tidak ada file bukti yang terlampir.</p>
                    </div>
                @endif
            </div>

            {{-- Card Kendala Kerusakan Pelanggan --}}
            <div class="glass-card rounded-2xl p-6 shadow-sm">
                <h3 class="mb-4 flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-gray-700 dark:text-gray-200">
                    <i class="fas fa-exclamation-circle text-coffee-500"></i>
                    Kendala Kerusakan yang Dilaporkan Customer
                </h3>

                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-sm leading-relaxed text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                    {{ $claim->damage_description }}
                </div>

                @if ($claim->response_notes)
                    <div class="mt-4 rounded-xl border border-blue-200 bg-blue-50/60 p-4 text-sm text-blue-900 dark:border-blue-800 dark:bg-blue-950/40 dark:text-blue-300">
                        <p class="text-xs font-bold uppercase tracking-wide text-blue-600 dark:text-blue-400 mb-1">
                            <i class="fas fa-comment-dots mr-1"></i> Catatan Respons Tim Engineering Pabrik:
                        </p>
                        {{ $claim->response_notes }}
                    </div>
                @endif
            </div>

            {{-- Card Identitas Mesin & Pelanggan --}}
            <div class="glass-card rounded-2xl p-6 shadow-sm">
                <h3 class="mb-4 flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-gray-700 dark:text-gray-200">
                    <i class="fas fa-id-card text-coffee-500"></i>
                    Identitas Mesin & Data Pelanggan
                </h3>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                        <p class="text-xs text-gray-400">Model Mesin</p>
                        <p class="mt-1 text-base font-bold text-gray-800 dark:text-gray-100">{{ $claim->machine_model ?? 'Mesin Industri MDI' }}</p>
                    </div>

                    <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                        <p class="text-xs text-gray-400">Nomor Seri Mesin (SN)</p>
                        <p class="mt-1 font-mono text-base font-bold text-coffee-600 dark:text-coffee-400">{{ $claim->serial_number }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400">Nama Pelanggan</p>
                        <p class="mt-1 font-semibold text-gray-800 dark:text-gray-100">{{ $claim->customer?->name ?? ($claim->user?->name ?? 'Pelanggan') }}</p>
                        <p class="text-xs text-gray-500">{{ $claim->customer?->phone ?? ($claim->user?->phone ?? '-') }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400">Perusahaan / Kafe</p>
                        <p class="mt-1 font-semibold text-gray-800 dark:text-gray-100">{{ $claim->customer?->company ?? ($claim->user?->company ?? '-') }}</p>
                        <p class="text-xs text-gray-500">{{ $claim->customer?->email ?? ($claim->user?->email ?? '-') }}</p>
                    </div>

                    <div class="sm:col-span-2">
                        <p class="text-xs text-gray-400">Alamat Kunjungan Perbaikan</p>
                        <div class="mt-1 rounded-xl border border-gray-200 bg-gray-50 p-3.5 text-sm text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                            {{ $claim->visit_address ?? ($claim->order?->delivery_address ?? ($claim->customer?->address ?? 'Alamat belum diatur')) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Form Verifikasi Bukti Kerusakan & Penjadwalan Kunjungan --}}
        <div class="space-y-6">
            {{-- Panel Verifikasi Admin --}}
            <div class="glass-card rounded-2xl p-6 shadow-sm">
                <h3 class="mb-4 flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-gray-700 dark:text-gray-200">
                    <i class="fas fa-clipboard-check text-coffee-500"></i>
                    Verifikasi Bukti & Jadwal Perbaikan
                </h3>

                <form method="POST" action="{{ route('admin.warranties.verify', $claim) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="mb-2 block text-xs font-semibold text-gray-600 dark:text-gray-300">Hasil Pemeriksaan Bukti Foto/Video</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border border-emerald-300 bg-emerald-50/70 p-3 text-xs font-bold text-emerald-800 transition hover:bg-emerald-100 dark:border-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300">
                                <input type="radio" name="action" value="approve" checked class="text-emerald-600 focus:ring-emerald-500">
                                <span>Bukti Valid (Setujui)</span>
                            </label>
                            <label class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border border-red-300 bg-red-50/70 p-3 text-xs font-bold text-red-800 transition hover:bg-red-100 dark:border-red-800 dark:bg-red-950/40 dark:text-red-300">
                                <input type="radio" name="action" value="reject" class="text-red-600 focus:ring-red-500">
                                <span>Tidak Valid (Tolak)</span>
                            </label>
                        </div>
                    </div>

                    <div id="visitSchedulingFields" class="space-y-4 border-t border-gray-100 pt-3 dark:border-gray-700">
                        <p class="text-xs font-bold text-gray-700 dark:text-gray-300">
                            <i class="fas fa-calendar-plus mr-1 text-coffee-600"></i> Penjadwalan Kunjungan Teknisi
                        </p>

                        <div>
                            <label for="scheduled_visit_date" class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-300">Tanggal Kunjungan Teknisi</label>
                            <input type="date" name="scheduled_visit_date" id="scheduled_visit_date"
                                   value="{{ $claim->scheduled_visit_date ? $claim->scheduled_visit_date->format('Y-m-d') : date('Y-m-d', strtotime('+2 days')) }}"
                                   class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2 text-sm outline-none focus:border-coffee-500 focus:ring-2 focus:ring-coffee-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
                        </div>

                        <div>
                            <label for="scheduled_visit_time" class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-300">Waktu Kunjungan</label>
                            <input type="text" name="scheduled_visit_time" id="scheduled_visit_time"
                                   value="{{ $claim->scheduled_visit_time ?? '10:00 WIB' }}"
                                   placeholder="Contoh: 10:00 WIB"
                                   class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2 text-sm outline-none focus:border-coffee-500 focus:ring-2 focus:ring-coffee-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
                        </div>

                        <div>
                            <label for="technician_name" class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-300">Teknisi Penanggung Jawab</label>
                            <input type="text" name="technician_name" id="technician_name"
                                   value="{{ $claim->technician_name ?? 'Tim Teknisi Garansi Pabrik' }}"
                                   class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2 text-sm outline-none focus:border-coffee-500 focus:ring-2 focus:ring-coffee-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
                        </div>

                        <div>
                            <label for="technician_phone" class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-300">No. Kontak Teknisi</label>
                            <input type="text" name="technician_phone" id="technician_phone"
                                   value="{{ $claim->technician_phone ?? '0812-3344-5566' }}"
                                   class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2 text-sm outline-none focus:border-coffee-500 focus:ring-2 focus:ring-coffee-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
                        </div>

                        <div>
                            <label for="visit_address" class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-300">Alamat Kunjungan</label>
                            <textarea name="visit_address" id="visit_address" rows="2"
                                      class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2 text-sm outline-none focus:border-coffee-500 focus:ring-2 focus:ring-coffee-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">{{ $claim->visit_address ?? ($claim->order?->delivery_address ?? $claim->customer?->address) }}</textarea>
                        </div>
                    </div>

                    <div>
                        <label for="response_notes" id="responseNotesLabel" class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-300">Catatan Persetujuan & Jadwal</label>
                        @error('response_notes')
                            <p class="mb-1 text-xs font-semibold text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                        @error('scheduled_visit_date')
                            <p class="mb-1 text-xs font-semibold text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                        <textarea name="response_notes" id="response_notes" rows="3" required
                                  placeholder="Contoh: Bukti valid. Teknisi dijadwalkan berkunjung pada tanggal dan jam yang dipilih."
                                  class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2 text-sm outline-none focus:border-coffee-500 focus:ring-2 focus:ring-coffee-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">{{ old('response_notes', $claim->response_notes) }}</textarea>
                    </div>

                    <button type="submit"
                            class="w-full rounded-xl bg-coffee-600 py-3 text-sm font-bold text-white shadow-md transition hover:bg-coffee-700 focus:ring-4 focus:ring-coffee-300 dark:bg-coffee-700 dark:hover:bg-coffee-600">
                        <i class="fas fa-paper-plane mr-1.5"></i> Simpan Hasil Verifikasi & Terbitkan Jadwal
                    </button>
                </form>

                @if ($claim->verification_status === \App\Models\WarrantyClaim::VERIFIKASI_VALID)
                    {{-- Form Update Progress Kunjungan Teknisi --}}
                    <div class="mt-6 border-t border-gray-100 pt-5 dark:border-gray-700">
                        <h4 class="mb-3 text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300">
                            <i class="fas fa-truck-moving mr-1 text-purple-600"></i> Pembaruan Status Kunjungan
                        </h4>
                        <form method="POST" action="{{ route('admin.warranties.visit.update', $claim) }}" class="space-y-3">
                            @csrf
                            @method('PUT')

                            <select name="status" required
                                    class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2 text-sm outline-none focus:border-coffee-500 focus:ring-2 focus:ring-coffee-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
                                @foreach ($statusLabels as $val => $lbl)
                                    <option value="{{ $val }}" @selected($claim->status === $val)>{{ $lbl }}</option>
                                @endforeach
                            </select>

                            <label for="visit_response_notes" class="block text-xs font-semibold text-gray-600 dark:text-gray-300">Catatan untuk customer (opsional)</label>
                            <textarea name="response_notes" id="visit_response_notes" rows="2" maxlength="1000"
                                      placeholder="Kosongkan untuk memakai catatan otomatis sesuai tahap."
                                      class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2 text-sm outline-none focus:border-coffee-500 focus:ring-2 focus:ring-coffee-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"></textarea>

                            <button type="submit"
                                    class="w-full rounded-xl border border-purple-200 bg-purple-50 px-4 py-2 text-xs font-bold text-purple-700 transition hover:bg-purple-100 dark:border-purple-900/40 dark:bg-purple-950/40 dark:text-purple-300">
                                Update Status Kunjungan
                            </button>
                        </form>
                    </div>
                @endif

                <div class="mt-6 border-t border-gray-100 pt-4 dark:border-gray-700">
                    <form method="POST" action="{{ route('admin.warranties.destroy', $claim) }}"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus arsip klaim garansi ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full rounded-xl border border-red-200 px-4 py-2 text-xs font-bold text-red-600 transition hover:bg-red-50 dark:border-red-900/40 dark:text-red-400 dark:hover:bg-red-950/30">
                            <i class="fas fa-trash-alt mr-1"></i> Hapus Berkas Klaim
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        (() => {
            const radios = document.querySelectorAll('input[name="action"]');
            const schedule = document.getElementById('visitSchedulingFields');
            const label = document.getElementById('responseNotesLabel');
            const notes = document.getElementById('response_notes');
            const button = document.querySelector('form[action*="/verifikasi"] button[type="submit"]');
            const pendingNote = 'Permintaan klaim garansi berhasil diterima dan menunggu verifikasi Tim Engineering.';
            const previousPendingNote = 'Permintaan klaim garansi Anda berhasil diterima. Tim Engineering Pabrik sedang memeriksa keabsahan bukti foto/video kerusakan yang diunggah.';
            const update = () => {
                const rejected = document.querySelector('input[name="action"]:checked')?.value === 'reject';
                schedule.hidden = rejected;
                label.textContent = rejected ? 'Alasan Penolakan Klaim' : 'Catatan Persetujuan & Jadwal';
                if (rejected && [pendingNote, previousPendingNote].includes(notes.value.trim())) {
                    notes.value = 'Klaim ditolak karena bukti foto/video tidak valid atau tidak cukup untuk memverifikasi kerusakan.';
                }
                notes.placeholder = rejected
                    ? 'Tuliskan alasan penolakan klaim.'
                    : 'Contoh: Bukti valid. Teknisi dijadwalkan berkunjung pada tanggal dan jam yang dipilih.';
                button.innerHTML = rejected
                    ? '<i class="fas fa-paper-plane mr-1.5"></i> Simpan Penolakan Klaim'
                    : '<i class="fas fa-paper-plane mr-1.5"></i> Simpan Verifikasi & Terbitkan Jadwal';
            };
            radios.forEach(radio => radio.addEventListener('change', update));
            update();
        })();
    </script>
@endsection
