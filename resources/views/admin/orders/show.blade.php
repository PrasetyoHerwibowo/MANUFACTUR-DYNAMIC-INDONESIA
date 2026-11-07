@extends('layouts.admin')

@section('title', 'Detail Pesanan ' . $order->order_code)
@section('page_title', 'Detail Pesanan')
@section('page_subtitle', 'Kode pesanan ' . $order->order_code . ' · ' . ($order->customer?->name ?? 'Pelanggan'))

@php
    $pendingPayment = $order->payments->firstWhere('status', \App\Models\Payment::STATUS_MENUNGGU);
    $activePayment = $pendingPayment ?? $order->payments->first();

    $stages = [
        \App\Models\Order::STAGE_VERIFIKASI_DP => ['step' => 1, 'label' => 'Verifikasi DP 50%', 'icon' => 'fas fa-file-invoice-dollar'],
        \App\Models\Order::STAGE_PERSIAPAN_BAHAN => ['step' => 2, 'label' => 'Persiapan Bahan', 'icon' => 'fas fa-boxes'],
        \App\Models\Order::STAGE_PROSES_PRODUKSI => ['step' => 3, 'label' => 'Proses Produksi', 'icon' => 'fas fa-industry'],
        \App\Models\Order::STAGE_QUALITY_CONTROL => ['step' => 4, 'label' => 'Quality Control', 'icon' => 'fas fa-clipboard-check'],
        \App\Models\Order::STAGE_MENUNGGU_PELUNASAN => ['step' => 5, 'label' => 'Menunggu Pelunasan', 'icon' => 'fas fa-hand-holding-usd'],
        \App\Models\Order::STAGE_PENGIRIMAN_INSTALASI => ['step' => 6, 'label' => 'Pengiriman & Instalasi', 'icon' => 'fas fa-truck-loading'],
        \App\Models\Order::STAGE_AFTER_SALES => ['step' => 7, 'label' => 'Selesai & Garansi Aktif', 'icon' => 'fas fa-shield-alt'],
    ];

    $stageOrder = array_keys($stages);
    $currentStageIndex = array_search($order->production_status, $stageOrder);
    if ($currentStageIndex === false) {
        $currentStageIndex = 0;
    }

    // 1. Resolusi Bukti Transfer DP 50%
    $rawDpProof = $order->dp_proof ?: null;
    $hasDpProof = filled($rawDpProof);
    $dpProofUrl = null;
    if ($hasDpProof) {
        if (\Illuminate\Support\Str::startsWith($rawDpProof, ['http://', 'https://', 'data:image/', 'data:application/pdf'])) {
            $dpProofUrl = $rawDpProof;
        } elseif (file_exists(public_path('uploads/payments/' . basename($rawDpProof)))) {
            $dpProofUrl = asset('uploads/payments/' . basename($rawDpProof));
        } elseif (file_exists(public_path($rawDpProof))) {
            $dpProofUrl = asset($rawDpProof);
        } else {
            $dpProofUrl = app(\App\Services\SupabaseStorageService::class)->getPublicUrl('payments/' . basename($rawDpProof));
        }
    }

    // 2. Resolusi Bukti Transfer Pelunasan Sisa 50%
    $rawPelunasanProof = $order->pelunasan_proof ?: null;
    $hasPelunasanProof = filled($rawPelunasanProof);
    $pelunasanProofUrl = null;
    if ($hasPelunasanProof) {
        if (\Illuminate\Support\Str::startsWith($rawPelunasanProof, ['http://', 'https://', 'data:image/', 'data:application/pdf'])) {
            $pelunasanProofUrl = $rawPelunasanProof;
        } elseif (file_exists(public_path('uploads/payments/' . basename($rawPelunasanProof)))) {
            $pelunasanProofUrl = asset('uploads/payments/' . basename($rawPelunasanProof));
        } elseif (file_exists(public_path($rawPelunasanProof))) {
            $pelunasanProofUrl = asset($rawPelunasanProof);
        } else {
            $pelunasanProofUrl = app(\App\Services\SupabaseStorageService::class)->getPublicUrl('payments/' . basename($rawPelunasanProof));
        }
    }

    $latestDpPayment = $order->payments
        ->filter(fn($p) => !\Illuminate\Support\Str::contains($p->notes ?? '', 'Pelunasan'))
        ->last() ?: $order->payments->first();

    $latestPelunasanPayment = $order->payments
        ->filter(fn($p) => \Illuminate\Support\Str::contains($p->notes ?? '', 'Pelunasan'))
        ->last();

    $isDpWaiting = $order->production_status === \App\Models\Order::STAGE_VERIFIKASI_DP && $hasDpProof;
    $isDpRejected = $order->production_status === \App\Models\Order::STAGE_VERIFIKASI_DP && !$hasDpProof && $latestDpPayment?->status === \App\Models\Payment::STATUS_GAGAL;

    $isPelunasanWaiting = $order->production_status === \App\Models\Order::STAGE_MENUNGGU_PELUNASAN && $hasPelunasanProof;
    $isPelunasanRejected = $order->production_status === \App\Models\Order::STAGE_MENUNGGU_PELUNASAN && !$hasPelunasanProof && $latestPelunasanPayment?->status === \App\Models\Payment::STATUS_GAGAL;

    $isDpPdf = $dpProofUrl && (\Illuminate\Support\Str::endsWith(strtolower(parse_url($dpProofUrl, PHP_URL_PATH) ?? ''), '.pdf') || \Illuminate\Support\Str::contains($dpProofUrl, 'data:application/pdf'));
    $isPelunasanPdf = $pelunasanProofUrl && (\Illuminate\Support\Str::endsWith(strtolower(parse_url($pelunasanProofUrl, PHP_URL_PATH) ?? ''), '.pdf') || \Illuminate\Support\Str::contains($pelunasanProofUrl, 'data:application/pdf'));
@endphp

@section('content')
    <a href="{{ route('admin.orders.index') }}"
       class="mb-4 inline-flex items-center gap-1.5 text-xs font-bold text-coffee-600 transition hover:text-coffee-700 dark:text-coffee-400">
        <i aria-hidden="true" class="fas fa-arrow-left"></i> Kembali ke Pesanan & Pembayaran
    </a>

    {{-- Header Info Pesanan --}}
    <div class="mb-5 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Kode Purchase Order (PO)</p>
            <h2 class="font-mono text-2xl font-bold text-gray-800 dark:text-white">{{ $order->order_code }}</h2>

            <div class="mt-2 flex flex-wrap items-center gap-2">
                <x-admin.status-badge :status="$order->status" />

                {{-- Badge Tahap Produksi --}}
                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold border
                    {{ $order->production_status === \App\Models\Order::STAGE_AFTER_SALES 
                        ? 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300 dark:border-emerald-700' 
                        : ($order->production_status === \App\Models\Order::STAGE_VERIFIKASI_DP 
                            ? 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/40 dark:text-amber-300 dark:border-amber-700' 
                            : ($order->production_status === \App\Models\Order::STAGE_MENUNGGU_PELUNASAN
                                ? 'bg-orange-100 text-orange-800 border-orange-300 dark:bg-orange-900/40 dark:text-orange-300 dark:border-orange-700'
                                : 'bg-indigo-100 text-indigo-800 border-indigo-300 dark:bg-indigo-900/40 dark:text-indigo-300 dark:border-indigo-700')) }}">
                    <i class="fas fa-industry text-[11px]"></i> {{ $order->productionStageLabel() }}
                </span>

                <span class="text-xs text-gray-500 dark:text-gray-400">
                    Dibuat {{ $order->created_at->format('d M Y H:i') }} WIB
                </span>

                @if ($order->paid_at)
                    <span class="text-xs font-medium text-green-600 dark:text-green-400">
                        • Lunas {{ $order->paid_at->format('d M Y H:i') }} WIB
                    </span>
                @endif
            </div>
        </div>

        <div class="text-right">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Total Nilai Pesanan</p>
            <p class="text-2xl font-bold text-coffee-700 dark:text-coffee-300">
                Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}
            </p>
            <div class="mt-1 flex items-center justify-end gap-3 text-xs text-gray-500 dark:text-gray-400">
                <span>DP 50%: <strong class="text-gray-700 dark:text-gray-200">Rp {{ number_format((float) ($order->dp_amount ?: $order->total_amount * 0.5), 0, ',', '.') }}</strong></span>
                <span>•</span>
                <span>Sisa 50%: <strong class="text-gray-700 dark:text-gray-200">Rp {{ number_format((float) ($order->pelunasan_amount ?: $order->total_amount * 0.5), 0, ',', '.') }}</strong></span>
            </div>
        </div>
    </div>

    {{-- Stepper Timeline 7 Tahap Produksi --}}
    <div class="glass-card mb-6 overflow-hidden rounded-2xl p-5 shadow-lg">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-2 border-b border-gray-100 pb-3 dark:border-gray-700">
            <div>
                <h3 class="text-sm font-bold text-gray-800 dark:text-white">
                    <i aria-hidden="true" class="fas fa-tasks mr-1.5 text-coffee-500"></i>
                    Progress Timeline 7 Tahap Produksi Mesin
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Status saat ini tersinkronisasi otomatis dengan aplikasi mobile pelanggan: 
                    <strong class="text-coffee-600 dark:text-coffee-400">{{ $order->productionStageLabel() }}</strong>
                </p>
            </div>
            <div>
                <span class="rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-bold text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    Tahap {{ $currentStageIndex + 1 }} dari 7
                </span>
            </div>
        </div>

        <div class="overflow-x-auto pb-2">
            <div class="flex min-w-[720px] items-center justify-between">
                @foreach ($stages as $stageKey => $stageInfo)
                    @php
                        $stepIdx = array_search($stageKey, $stageOrder);
                        $isCompleted = $stepIdx < $currentStageIndex;
                        $isCurrent = $stepIdx === $currentStageIndex;
                    @endphp

                    <div class="flex flex-1 flex-col items-center text-center relative">
                        {{-- Connector Line --}}
                        @if (! $loop->first)
                            <div class="absolute left-[-50%] top-4 h-1 w-full -translate-y-1/2 z-0
                                {{ $stepIdx <= $currentStageIndex ? 'bg-coffee-600 dark:bg-coffee-500' : 'bg-gray-200 dark:bg-gray-700' }}">
                            </div>
                        @endif

                        {{-- Node Icon --}}
                        <div class="relative z-10 flex h-9 w-9 items-center justify-center rounded-full text-xs font-bold transition shadow-sm
                            {{ $isCurrent 
                                ? 'bg-coffee-600 text-white ring-4 ring-coffee-100 dark:ring-coffee-950/60 scale-110' 
                                : ($isCompleted 
                                    ? 'bg-emerald-600 text-white' 
                                    : 'bg-gray-100 text-gray-400 border border-gray-300 dark:bg-gray-800 dark:border-gray-700') }}">
                            @if ($isCompleted)
                                <i class="fas fa-check"></i>
                            @else
                                <i class="{{ $stageInfo['icon'] }}"></i>
                            @endif
                        </div>

                        {{-- Label --}}
                        <p class="mt-2 text-[11px] font-bold leading-tight max-w-[90px]
                            {{ $isCurrent ? 'text-coffee-600 dark:text-coffee-400 font-extrabold' : ($isCompleted ? 'text-gray-800 dark:text-gray-200' : 'text-gray-400 dark:text-gray-500') }}">
                            {{ $stageInfo['label'] }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        {{-- Kolom kiri: Pelanggan, Alamat & Item --}}
        <div class="space-y-5 lg:col-span-2">
            {{-- Informasi Pelanggan --}}
            <div class="glass-card rounded-2xl p-5 shadow-lg">
                <h3 class="mb-3 text-sm font-bold text-gray-800 dark:text-white">
                    <i aria-hidden="true" class="fas fa-user mr-1.5 text-coffee-500"></i> Informasi Pelanggan
                </h3>

                @if ($order->customer)
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-coffee-500 to-coffee-700 text-sm font-bold text-white shadow">
                            {{ $order->customer->initials() }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="font-bold text-gray-800 dark:text-white">{{ $order->customer->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $order->customer->customer_code }} • {{ $order->customer->company ?: 'PT Kopi Nusantara Jaya' }}</p>

                            <dl class="mt-3 grid gap-2 text-xs sm:grid-cols-2">
                                <div>
                                    <dt class="text-gray-400">Email</dt>
                                    <dd class="font-semibold text-gray-700 dark:text-gray-200">{{ $order->customer->email }}</dd>
                                </div>
                                <div>
                                    <dt class="text-gray-400">Telepon / WhatsApp</dt>
                                    <dd class="font-semibold text-gray-700 dark:text-gray-200">{{ $order->customer->phone ?: '—' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">Data akun pelanggan telah dihapus.</p>
                @endif
            </div>

            {{-- Alamat Pengiriman & Catatan Kustomisasi Mesin --}}
            <div class="glass-card rounded-2xl p-5 shadow-lg">
                <h3 class="mb-3 text-sm font-bold text-gray-800 dark:text-white">
                    <i aria-hidden="true" class="fas fa-map-marker-alt mr-1.5 text-coffee-500"></i> Alamat Instalasi & Kustomisasi
                </h3>

                <div class="space-y-3 text-xs">
                    <div>
                        <p class="font-semibold text-gray-400">Alamat Pengiriman & Instalasi Pabrik:</p>
                        <p class="mt-1 rounded-xl bg-gray-50 p-3 font-medium text-gray-700 dark:bg-gray-800/80 dark:text-gray-200 border border-gray-200/60 dark:border-gray-700">
                            {{ $order->delivery_address ?: ($order->customer?->fullAddress() ?: 'Kawasan Industri MM2100 Blok C-4, Cikarang Barat, Bekasi') }}
                        </p>
                    </div>

                    @if ($order->custom_notes)
                        <div>
                            <p class="font-semibold text-gray-400">Catatan Custom / Laser Engraving Logo:</p>
                            <p class="mt-1 rounded-xl bg-amber-50 p-3 font-semibold text-amber-900 dark:bg-amber-950/40 dark:text-amber-200 border border-amber-200/80 dark:border-amber-800/60">
                                <i class="fas fa-pen-nib mr-1 text-amber-600"></i> {{ $order->custom_notes }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Item Pesanan --}}
            <div class="glass-card overflow-hidden rounded-2xl shadow-lg">
                <h3 class="border-b border-gray-200 px-5 py-4 text-sm font-bold text-gray-800 dark:border-gray-700 dark:text-white">
                    <i aria-hidden="true" class="fas fa-cogs mr-1.5 text-coffee-500"></i> Unit Mesin yang Dipesan
                </h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                        <thead class="bg-gray-50 text-left text-xs font-bold uppercase tracking-wide text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                            <tr>
                                <th class="px-5 py-3">Model Mesin</th>
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
                                                {{ $item->machineCode() ?: $item->machine->name }} <i class="fas fa-external-link-alt text-[10px]"></i>
                                            </a>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-300">
                                        Rp {{ number_format((float) $item->unit_price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-5 py-3 text-center font-bold text-gray-700 dark:text-gray-200">
                                        {{ $item->quantity }} Unit
                                    </td>
                                    <td class="px-5 py-3 text-right font-bold text-gray-800 dark:text-gray-100">
                                        Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                        <tfoot class="bg-gray-50 dark:bg-gray-800 font-bold">
                            <tr>
                                <td colspan="3" class="px-5 py-3 text-right text-xs uppercase text-gray-500 dark:text-gray-400">
                                    Total Nilai PO
                                </td>
                                <td class="px-5 py-3 text-right text-base text-coffee-700 dark:text-coffee-300">
                                    Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Catatan Internal / Audit Log --}}
            @if ($order->notes)
                <div class="glass-card rounded-2xl p-5 shadow-lg">
                    <h3 class="mb-2 text-sm font-bold text-gray-800 dark:text-white">
                        <i aria-hidden="true" class="fas fa-sticky-note mr-1.5 text-coffee-500"></i> Riwayat & Catatan Pesanan
                    </h3>
                    <p class="whitespace-pre-line text-xs font-mono text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-800 p-3 rounded-xl border border-gray-200 dark:border-gray-700">{{ $order->notes }}</p>
                </div>
            @endif

            {{-- Riwayat Pembayaran --}}
            <div class="glass-card overflow-hidden rounded-2xl shadow-lg">
                <h3 class="border-b border-gray-200 px-5 py-4 text-sm font-bold text-gray-800 dark:border-gray-700 dark:text-white">
                    <i aria-hidden="true" class="fas fa-history mr-1.5 text-coffee-500"></i>
                    Riwayat Pembayaran ({{ $order->payments->count() }} invoice pembayaran)
                </h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                        <thead class="bg-gray-50 text-left text-xs font-bold uppercase tracking-wide text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                            <tr>
                                <th class="px-5 py-3">Kode Bayar</th>
                                <th class="px-5 py-3">Metode</th>
                                <th class="px-5 py-3">Nominal</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3">Bukti</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                            @forelse ($order->payments as $payment)
                                <tr>
                                    <td class="px-5 py-3">
                                        <span class="font-mono text-xs font-bold text-coffee-700 dark:text-coffee-300">{{ $payment->payment_code }}</span>
                                        <span class="block text-[10px] text-gray-400">Percobaan ke-{{ $payment->attempt }}</span>
                                    </td>
                                    <td class="px-5 py-3 text-xs text-gray-600 dark:text-gray-300">{{ $payment->methodLabel() }}</td>
                                    <td class="px-5 py-3 font-semibold text-gray-800 dark:text-gray-100">
                                        Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-5 py-3">
                                        <x-admin.status-badge :status="$payment->status" />
                                        @if ($payment->paid_at)
                                            <span class="mt-0.5 block text-[10px] text-green-600 dark:text-green-400">
                                                Lunas {{ $payment->paid_at->format('d/m/Y H:i') }}
                                            </span>
                                        @elseif ($payment->failure_reason)
                                            <span class="mt-0.5 block text-[10px] text-red-500">{{ $payment->failure_reason }}</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3">
                                        @php
                                            $pProof = $payment->proof ?: $order->dp_proof;
                                        @endphp
                                        @if ($pProof)
                                            <a href="{{ $pProof }}" target="_blank"
                                               class="inline-flex items-center gap-1 text-xs font-bold text-coffee-600 hover:underline dark:text-coffee-400">
                                                <i class="fas fa-file-invoice"></i> Lihat
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-6 text-center text-xs text-gray-500">
                                        Belum ada riwayat pembayaran.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Kolom kanan: Verifikasi Bukti Real vs Palsu & Kontrol Tahap Produksi --}}
        <div class="space-y-5">

            {{-- 1. CARD UTAMA: VERIFIKASI BUKTI TRANSFER DP 50% REAL VS PALSU --}}
            <div class="glass-card rounded-2xl p-5 shadow-lg border-2 border-coffee-500/20">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <i aria-hidden="true" class="fas fa-shield-alt text-coffee-600 dark:text-coffee-400"></i>
                        Verifikasi Bukti Transfer DP 50%
                    </h3>
                    @if ($hasDpProof)
                        <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[10px] font-bold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                            Terlampir
                        </span>
                    @elseif ($isDpRejected)
                        <span class="rounded-full bg-red-100 px-2.5 py-0.5 text-[10px] font-bold text-red-800 dark:bg-red-950 dark:text-red-300">
                            Ditolak
                        </span>
                    @endif
                </div>

                @if ($hasDpProof)
                    {{-- Preview Gambar / Dokumen dari Supabase / Local --}}
                    <div class="mb-4 overflow-hidden rounded-xl border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
                        <div class="relative group">
                            @if ($isDpPdf)
                                <div class="p-6 text-center bg-slate-100 dark:bg-slate-900/50 flex flex-col items-center justify-center">
                                    <i class="fas fa-file-pdf text-red-500 text-5xl mb-2"></i>
                                    <p class="font-bold text-sm text-gray-800 dark:text-gray-100">Dokumen PDF Bukti Transfer DP 50%</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Dokumen resmi diunggah langsung oleh pelanggan via mobile</p>
                                    <a href="{{ $dpProofUrl }}" target="_blank"
                                       class="mt-3 inline-flex items-center gap-1.5 rounded-lg bg-red-600 px-4 py-2 text-xs font-bold text-white shadow hover:bg-red-700 transition">
                                        <i class="fas fa-external-link-alt text-[11px]"></i> Buka / Unduh Dokumen PDF
                                    </a>
                                </div>
                            @else
                                <div class="max-h-72 w-full overflow-hidden flex items-center justify-center p-2 bg-slate-900/5 dark:bg-black/20">
                                    <img src="{{ $dpProofUrl }}" alt="Bukti Transfer DP 50%" 
                                         class="max-h-64 w-auto rounded-lg object-contain transition duration-200 group-hover:scale-105 shadow-sm"
                                         onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'600\' height=\'400\' viewBox=\'0 0 600 400\'><rect width=\'600\' height=\'400\' fill=\'%23F1F5F9\'/><text x=\'50%25\' y=\'50%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' font-family=\'sans-serif\' font-size=\'16\' font-weight=\'bold\' fill=\'%2364748B\'>Bukti Transfer Sedang Diproses</text></svg>';" />
                                </div>
                            @endif

                            <div class="p-3 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between text-xs">
                                <div>
                                    <p class="font-bold text-gray-800 dark:text-white">Bukti Pembayaran DP 50%</p>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400">Nilai DP: Rp {{ number_format((float) ($order->dp_amount ?: $order->total_amount * 0.5), 0, ',', '.') }}</p>
                                </div>
                                <a href="{{ $dpProofUrl }}" target="_blank"
                                   class="inline-flex items-center gap-1 rounded-lg bg-coffee-50 px-2.5 py-1 text-xs font-bold text-coffee-700 hover:bg-coffee-100 dark:bg-coffee-950/60 dark:text-coffee-300">
                                    <i class="fas fa-external-link-alt text-[10px]"></i> Layar Penuh
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Form Aksi Verifikasi: Real vs Palsu --}}
                    @if ($isDpWaiting)
                        <div class="space-y-4">
                            <div class="rounded-xl bg-amber-50 p-3 text-xs text-amber-800 border border-amber-200 dark:bg-amber-950/30 dark:text-amber-300 dark:border-amber-800/50">
                                <p class="font-bold mb-1"><i class="fas fa-exclamation-triangle mr-1"></i> Perlu Verifikasi Finance</p>
                                Periksa apakah mutasi rekening penerima sesuai dengan nominal <strong>Rp {{ number_format((float) ($order->dp_amount ?: $order->total_amount * 0.5), 0, ',', '.') }}</strong>.
                            </div>

                            {{-- Tombol 1: VERIFIKASI VALID (REAL) --}}
                            <form method="POST" action="{{ route('admin.orders.proof.verify', $order) }}" class="space-y-3">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="decision" value="real">
                                <input type="hidden" name="target" value="dp">

                                <div>
                                    <label for="next_stage" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                        Tahap Produksi Selanjutnya (Bila Real):
                                    </label>
                                    <select name="next_stage" id="next_stage"
                                            class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-xs font-medium outline-none focus:border-coffee-500 focus:ring-2 focus:ring-coffee-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
                                        <option value="persiapan_bahan" selected>2. Persiapan Bahan Baku (Sesuai Alur Produksi)</option>
                                        <option value="proses_produksi">3. Dalam Proses Produksi / Assembly</option>
                                        <option value="quality_control">4. Uji Quality Control (QC)</option>
                                    </select>
                                </div>

                                <button type="submit"
                                        class="w-full rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white shadow-md transition hover:bg-emerald-700 flex items-center justify-center gap-2">
                                    <i class="fas fa-check-circle"></i> Verifikasi Valid (Real)
                                </button>
                            </form>

                            {{-- Tombol 2: TOLAK / BUKTI PALSU (FAKE) --}}
                            <form method="POST" action="{{ route('admin.orders.proof.verify', $order) }}" class="space-y-2 border-t border-gray-200 pt-3 dark:border-gray-700"
                                  data-confirm="Yakin tolak bukti transfer ini sebagai PALSU? Status pembayaran akan ditandai gagal.">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="decision" value="fake">
                                <input type="hidden" name="target" value="dp">

                                <div>
                                    <input type="text" name="reason" value="Bukti transfer tidak valid / mutasi rekening tidak ditemukan"
                                           placeholder="Alasan penolakan / bukti palsu"
                                           class="w-full rounded-xl border border-red-200 bg-red-50/50 px-3 py-1.5 text-xs text-red-900 outline-none focus:border-red-400 dark:border-red-800/60 dark:bg-red-950/20 dark:text-red-300">
                                </div>

                                <button type="submit"
                                        class="w-full rounded-xl border border-red-300 px-4 py-2 text-xs font-bold text-red-600 transition hover:bg-red-50 dark:border-red-500/40 dark:text-red-300 dark:hover:bg-red-500/10 flex items-center justify-center gap-1.5">
                                    <i class="fas fa-times-circle"></i> Tolak Bukti (Palsu)
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="rounded-xl bg-emerald-50 p-3.5 text-xs text-emerald-800 border border-emerald-200 dark:bg-emerald-950/30 dark:text-emerald-300 dark:border-emerald-800/50 flex items-center gap-2.5">
                            <i class="fas fa-check-double text-base text-emerald-600"></i>
                            <div>
                                <p class="font-bold">Bukti Transfer DP Sah (Verified)</p>
                                <p class="text-[11px] text-emerald-700 dark:text-emerald-400">Telah diverifikasi REAL oleh finance pabrik.</p>
                            </div>
                        </div>
                    @endif

                @elseif ($isDpRejected)
                    {{-- Kondisi Bukti DP Ditolak (Palsu) --}}
                    <div class="rounded-xl border border-red-300 bg-red-50 p-4 text-xs text-red-900 dark:border-red-800 dark:bg-red-950/40 dark:text-red-300 space-y-2">
                        <p class="font-bold flex items-center gap-1.5 text-sm text-red-700 dark:text-red-400">
                            <i class="fas fa-times-circle"></i> Bukti Transfer DP Ditolak (Palsu / Tidak Valid)
                        </p>
                        <p class="font-semibold text-gray-700 dark:text-gray-300">
                            Alasan Penolakan: <span class="italic text-red-600 dark:text-red-400">{{ $latestDpPayment?->failure_reason ?: 'Bukti transfer tidak valid / mutasi rekening tidak ditemukan' }}</span>
                        </p>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400">
                            <i class="fas fa-mobile-alt mr-1"></i> Status: Menunggu pelanggan mengunggah ulang bukti transfer DP 50% yang valid melalui aplikasi mobile.
                        </p>
                    </div>

                @else
                    {{-- Kondisi jika belum ada bukti transfer yang diunggah --}}
                    <div class="rounded-xl border border-dashed border-gray-300 p-6 text-center dark:border-gray-700">
                        <i class="fas fa-file-upload mb-2 text-3xl text-gray-300 dark:text-gray-600"></i>
                        <p class="text-xs font-bold text-gray-600 dark:text-gray-300">Belum Ada Bukti Transfer DP</p>
                        <p class="mt-1 text-[11px] text-gray-400">Pelanggan belum mengunggah struk/screenshot pembayaran DP 50% melalui aplikasi mobile.</p>
                    </div>
                @endif
            </div>

            {{-- 1.5. CARD TAMBAHAN: VERIFIKASI BUKTI TRANSFER PELUNASAN SISA 50% --}}
            @if ($hasPelunasanProof || $isPelunasanRejected || $order->production_status === \App\Models\Order::STAGE_MENUNGGU_PELUNASAN || $order->status === \App\Models\Order::STATUS_LUNAS)
                <div class="glass-card rounded-2xl p-5 shadow-lg border-2 border-indigo-500/20">
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-gray-800 dark:text-white flex items-center gap-2">
                            <i aria-hidden="true" class="fas fa-file-invoice-dollar text-indigo-600 dark:text-indigo-400"></i>
                            Bukti Pembayaran Pelunasan (50%)
                        </h3>
                        @if ($hasPelunasanProof)
                            <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[10px] font-bold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                Terlampir
                            </span>
                        @elseif ($isPelunasanRejected)
                            <span class="rounded-full bg-red-100 px-2.5 py-0.5 text-[10px] font-bold text-red-800 dark:bg-red-950 dark:text-red-300">
                                Ditolak
                            </span>
                        @endif
                    </div>

                    @if ($hasPelunasanProof)
                        <div class="mb-4 overflow-hidden rounded-xl border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
                            <div class="relative group">
                                @if ($isPelunasanPdf)
                                    <div class="p-6 text-center bg-slate-100 dark:bg-slate-900/50 flex flex-col items-center justify-center">
                                        <i class="fas fa-file-pdf text-red-500 text-5xl mb-2"></i>
                                        <p class="font-bold text-sm text-gray-800 dark:text-gray-100">Dokumen PDF Bukti Pelunasan 50%</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Dokumen resmi diunggah langsung oleh pelanggan via mobile</p>
                                        <a href="{{ $pelunasanProofUrl }}" target="_blank"
                                           class="mt-3 inline-flex items-center gap-1.5 rounded-lg bg-red-600 px-4 py-2 text-xs font-bold text-white shadow hover:bg-red-700 transition">
                                            <i class="fas fa-external-link-alt text-[11px]"></i> Buka / Unduh Dokumen PDF
                                        </a>
                                    </div>
                                @else
                                    <div class="max-h-72 w-full overflow-hidden flex items-center justify-center p-2 bg-slate-900/5 dark:bg-black/20">
                                        <img src="{{ $pelunasanProofUrl }}" alt="Bukti Pelunasan 50%" 
                                             class="max-h-64 w-auto rounded-lg object-contain transition duration-200 group-hover:scale-105 shadow-sm"
                                             onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'600\' height=\'400\' viewBox=\'0 0 600 400\'><rect width=\'600\' height=\'400\' fill=\'%23F1F5F9\'/><text x=\'50%25\' y=\'50%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' font-family=\'sans-serif\' font-size=\'16\' font-weight=\'bold\' fill=\'%2364748B\'>Bukti Pelunasan Sedang Diproses</text></svg>';" />
                                    </div>
                                @endif

                                <div class="p-3 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between text-xs">
                                    <div>
                                        <p class="font-bold text-gray-800 dark:text-white">Bukti Pelunasan Sisa 50%</p>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400">Nilai: Rp {{ number_format((float) ($order->pelunasan_amount ?: $order->total_amount * 0.5), 0, ',', '.') }}</p>
                                    </div>
                                    <a href="{{ $pelunasanProofUrl }}" target="_blank"
                                       class="inline-flex items-center gap-1 rounded-lg bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700 hover:bg-indigo-100 dark:bg-indigo-950/60 dark:text-indigo-300">
                                        <i class="fas fa-external-link-alt text-[10px]"></i> Layar Penuh
                                    </a>
                                </div>
                            </div>
                        </div>

                        @if ($isPelunasanWaiting || $order->status !== \App\Models\Order::STATUS_LUNAS)
                            <div class="space-y-4">
                                <div class="rounded-xl bg-indigo-50 p-3 text-xs text-indigo-800 border border-indigo-200 dark:bg-indigo-950/30 dark:text-indigo-300 dark:border-indigo-800/50">
                                    <p class="font-bold mb-1"><i class="fas fa-info-circle mr-1"></i> Verifikasi Pelunasan Pabrik</p>
                                    Jika mutasi pelunasan valid, pesanan akan ditandai <strong>LUNAS</strong> dan status garansi resmi pabrik (After-Sales) otomatis diaktifkan.
                                </div>

                                {{-- Verifikasi Pelunasan Real --}}
                                <form method="POST" action="{{ route('admin.orders.proof.verify', $order) }}" class="space-y-3">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="decision" value="real">
                                    <input type="hidden" name="target" value="pelunasan">

                                    <button type="submit"
                                            class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white shadow-md transition hover:bg-indigo-700 flex items-center justify-center gap-2">
                                        <i class="fas fa-check-double"></i> Verifikasi Pelunasan (Lunas & Garansi Aktif)
                                    </button>
                                </form>

                                {{-- Tolak Pelunasan --}}
                                <form method="POST" action="{{ route('admin.orders.proof.verify', $order) }}" class="space-y-2 border-t border-gray-200 pt-3 dark:border-gray-700"
                                      data-confirm="Yakin tolak bukti pelunasan ini sebagai PALSU?">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="decision" value="fake">
                                    <input type="hidden" name="target" value="pelunasan">

                                    <div>
                                        <input type="text" name="reason" value="Bukti pelunasan tidak valid / mutasi rekening tidak ditemukan"
                                               placeholder="Alasan penolakan bukti pelunasan"
                                               class="w-full rounded-xl border border-red-200 bg-red-50/50 px-3 py-1.5 text-xs text-red-900 outline-none focus:border-red-400 dark:border-red-800/60 dark:bg-red-950/20 dark:text-red-300">
                                    </div>

                                    <button type="submit"
                                            class="w-full rounded-xl border border-red-300 px-4 py-2 text-xs font-bold text-red-600 transition hover:bg-red-50 dark:border-red-500/40 dark:text-red-300 dark:hover:bg-red-500/10 flex items-center justify-center gap-1.5">
                                        <i class="fas fa-times-circle"></i> Tolak Bukti Pelunasan
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="rounded-xl bg-emerald-50 p-3.5 text-xs text-emerald-800 border border-emerald-200 dark:bg-emerald-950/30 dark:text-emerald-300 dark:border-emerald-800/50 flex items-center gap-2.5">
                                <i class="fas fa-check-double text-base text-emerald-600"></i>
                                <div>
                                    <p class="font-bold">Pelunasan Sah & Lunas</p>
                                    <p class="text-[11px] text-emerald-700 dark:text-emerald-400">Garansi resmi pabrik dan layanan after-sales aktif.</p>
                                </div>
                            </div>
                        @endif

                    @elseif ($isPelunasanRejected)
                        {{-- Kondisi Bukti Pelunasan Ditolak (Palsu) --}}
                        <div class="rounded-xl border border-red-300 bg-red-50 p-4 text-xs text-red-900 dark:border-red-800 dark:bg-red-950/40 dark:text-red-300 space-y-2">
                            <p class="font-bold flex items-center gap-1.5 text-sm text-red-700 dark:text-red-400">
                                <i class="fas fa-times-circle"></i> Bukti Pelunasan Ditolak (Palsu / Tidak Valid)
                            </p>
                            <p class="font-semibold text-gray-700 dark:text-gray-300">
                                Alasan Penolakan: <span class="italic text-red-600 dark:text-red-400">{{ $latestPelunasanPayment?->failure_reason ?: 'Bukti pelunasan tidak valid / mutasi rekening tidak ditemukan' }}</span>
                            </p>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400">
                                <i class="fas fa-mobile-alt mr-1"></i> Status: Menunggu pelanggan mengunggah ulang bukti transfer pelunasan 50% yang valid melalui aplikasi mobile.
                            </p>
                        </div>

                    @else
                        <div class="rounded-xl border border-dashed border-gray-300 p-5 text-center dark:border-gray-700">
                            <i class="fas fa-receipt mb-2 text-2xl text-gray-300 dark:text-gray-600"></i>
                            <p class="text-xs font-bold text-gray-600 dark:text-gray-300">Menunggu Pelunasan Sisa 50%</p>
                            <p class="mt-1 text-[11px] text-gray-400">Pelanggan belum mengunggah bukti pelunasan melalui aplikasi mobile.</p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- 2. CARD KONTROL TAHAP TIMELINE PRODUKSI --}}
            <div class="glass-card rounded-2xl p-5 shadow-lg">
                <h3 class="mb-2 text-sm font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <i aria-hidden="true" class="fas fa-sliders-h text-coffee-600 dark:text-coffee-400"></i>
                    Kontrol Tahap Produksi Pabrik
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                    Pilih status produksi untuk memajukan pesanan pelanggan di aplikasi mobile.
                </p>

                <form method="POST" action="{{ route('admin.orders.stage.update', $order) }}" class="space-y-3">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="production_status" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                            Pilih Tahap Produksi:
                        </label>
                        <select name="production_status" id="production_status"
                                class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-xs font-bold outline-none focus:border-coffee-500 focus:ring-2 focus:ring-coffee-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
                            @foreach (\App\Models\Order::productionStageLabels() as $stageValue => $stageLabel)
                                <option value="{{ $stageValue }}" @selected($order->production_status === $stageValue)>
                                    {{ $stageLabel }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="stage_notes" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                            Catatan Produksi (Opsional):
                        </label>
                        <input type="text" name="notes" id="stage_notes" placeholder="Contoh: Unit lolos uji hidrostatik 12 bar"
                               class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-xs outline-none focus:border-coffee-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
                    </div>

                    <button type="submit"
                            class="w-full rounded-xl bg-coffee-600 px-4 py-2.5 text-xs font-bold text-white shadow transition hover:bg-coffee-700 flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i> Perbarui Status Produksi
                    </button>
                </form>
            </div>

            {{-- 3. Ringkasan Kode Pembayaran --}}
            <div id="pembayaran" class="glass-card rounded-2xl p-5 shadow-lg">
                <h3 class="mb-3 text-sm font-bold text-gray-800 dark:text-white">
                    <i aria-hidden="true" class="fas fa-credit-card mr-1.5 text-coffee-500"></i> Rekening Bank Tujuan
                </h3>

                <div class="space-y-2 text-xs">
                    <div class="rounded-xl bg-gray-50 p-2.5 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700">
                        <p class="font-bold text-gray-800 dark:text-gray-100">Bank BCA Corporate</p>
                        <p class="font-mono font-bold text-coffee-600 dark:text-coffee-400">088-2930-112</p>
                        <p class="text-[11px] text-gray-400">a.n PT Manufactur Dynamic Indonesia</p>
                    </div>
                    <div class="rounded-xl bg-gray-50 p-2.5 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700">
                        <p class="font-bold text-gray-800 dark:text-gray-100">Bank Mandiri B2B</p>
                        <p class="font-mono font-bold text-coffee-600 dark:text-coffee-400">137-0099-221</p>
                        <p class="text-[11px] text-gray-400">a.n PT Manufactur Dynamic Indonesia</p>
                    </div>
                </div>
            </div>

            {{-- 4. Hapus Pesanan --}}
            <div class="glass-card rounded-2xl p-5 shadow-lg">
                <h3 class="text-sm font-bold text-gray-800 dark:text-white">
                    <i aria-hidden="true" class="fas fa-trash mr-1.5 text-red-500"></i> Hapus Pesanan
                </h3>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Menghapus pesanan juga menghapus seluruh data item dan riwayat pembayarannya.
                </p>

                <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" class="mt-4"
                      data-confirm="Hapus pesanan {{ $order->order_code }} beserta seluruh pembayarannya?">
                    @csrf
                    @method('DELETE')

                    <button type="submit"
                            class="w-full rounded-xl border border-red-200 px-4 py-2.5 text-xs font-bold text-red-600 transition hover:bg-red-50 dark:border-red-500/40 dark:text-red-300 dark:hover:bg-red-500/10">
                        Hapus Pesanan
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
