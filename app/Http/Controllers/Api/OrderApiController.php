<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Machine;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Support\Reference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class OrderApiController extends Controller
{
    /**
     * GET /api/orders
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if (! $user) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $customer = \Illuminate\Support\Facades\Cache::remember("customer_email_{$user->email}", 3600, function () use ($user) {
                return Customer::where('email', $user->email)->first();
            });

            if (! $customer) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                ]);
            }

            $orders = \Illuminate\Support\Facades\Cache::remember("api_orders_customer_{$customer->id}", 60, function () use ($customer) {
                return Order::with(['items.machine', 'payments', 'customer'])
                    ->where('customer_id', $customer->id)
                    ->latest('id')
                    ->get()
                    ->map(function ($order) {
                        return $this->formatOrderResponse($order);
                    })
                    ->toArray();
            });

            return response()->json([
                'success' => true,
                'data' => $orders,
            ]);
        } catch (Throwable $e) {
            Log::error('OrderApiController index error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat daftar pesanan.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * GET /api/orders/{id}
     */
    public function show(Request $request, string $id): JsonResponse
    {
        try {
            $user = $request->user();
            if (! $user) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $customer = \Illuminate\Support\Facades\Cache::remember("customer_email_{$user->email}", 3600, function () use ($user) {
                return Customer::where('email', $user->email)->first();
            });

            if (! $customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan.',
                ], 404);
            }

            $orderData = \Illuminate\Support\Facades\Cache::remember("api_order_detail_{$id}", 60, function () use ($customer, $id) {
                $order = Order::with(['items.machine', 'payments', 'customer'])
                    ->where('customer_id', $customer->id)
                    ->where(function ($q) use ($id) {
                        $q->where('id', is_numeric($id) ? (int)$id : 0)
                          ->orWhere('order_code', $id);
                    })
                    ->first();

                return $order ? $this->formatOrderResponse($order) : null;
            });

            if (! $orderData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $orderData,
            ]);
        } catch (Throwable $e) {
            Log::error('OrderApiController show error: ' . $e->getMessage(), ['id' => $id]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail pesanan.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * POST /api/orders
     * Buat Purchase Order (PO) baru dengan sistem DP 50%
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'machine_id' => ['required'],
            'quantity' => ['required', 'integer', 'min:1'],
            'delivery_address' => ['required', 'string'],
            'custom_notes' => ['nullable', 'string'],
            'bank_method' => ['nullable', 'string'],
            'dp_proof' => ['nullable', 'string'],
            'dp_proof_file' => ['nullable', 'file', 'image', 'max:5120'],
        ]);

        try {
            $rawMachineId = $validated['machine_id'];
            $machine = null;
            if (is_numeric($rawMachineId)) {
                $machine = Machine::find((int) $rawMachineId);
            }
            if (! $machine) {
                $machine = Machine::where('model_code', $rawMachineId)
                    ->orWhere('slug', $rawMachineId)
                    ->first();
            }
            if (! $machine && is_string($rawMachineId)) {
                if (str_contains(strtolower($rawMachineId), 'roast')) {
                    $machine = Machine::where('name', 'ilike', '%MicroRoast%')->first()
                        ?: Machine::where('name', 'ilike', '%roast%')->first();
                }
            }
            if (! $machine) {
                $machine = Machine::firstOrFail();
            }

            $user = $request->user();
            
            $customer = Customer::firstOrCreate(
                ['email' => $user?->email ?? 'procurement@kopinusantara.co.id'],
                [
                    'customer_code' => 'CUST-' . strtoupper(substr(uniqid(), -6)),
                    'name' => $user?->name ?? 'Procurement Client',
                    'company' => 'PT Kopi Nusantara Jaya',
                    'phone' => $user?->phone ?? null,
                    'address' => $validated['delivery_address'],
                ]
            );

            $unitPrice = (float) ($machine->price > 0 ? $machine->price : ($machine->id == 1 ? 18500000 : 25000000));
            $totalAmount = $unitPrice * $validated['quantity'];
            $dpAmount = $totalAmount * 0.50;
            $pelunasanAmount = $totalAmount * 0.50;

            do {
                $orderCode = 'PO-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            } while (Order::where('order_code', $orderCode)->exists());

            // Upload bukti transfer DP 50% ke Supabase Storage
            $dpProofUrl = $this->resolveAndStoreProof(
                $validated['dp_proof'] ?? null,
                $request->file('dp_proof_file'),
                $orderCode,
                $dpAmount,
                $validated['bank_method'] ?? 'Bank BCA Corporate'
            );

            $order = DB::transaction(function () use (
                $customer, $orderCode, $totalAmount, $dpAmount, $pelunasanAmount,
                $validated, $machine, $unitPrice, $dpProofUrl
            ) {
                // Status awal saat ada bukti DP: Menunggu Pembayaran & Tahap Timeline: Verifikasi DP
                $order = Order::create([
                    'customer_id' => $customer->id,
                    'order_code' => $orderCode,
                    'total_amount' => $totalAmount,
                    'dp_amount' => $dpAmount,
                    'pelunasan_amount' => $pelunasanAmount,
                    'status' => Order::STATUS_MENUNGGU_PEMBAYARAN,
                    'production_status' => Order::STAGE_VERIFIKASI_DP,
                    'dp_proof' => $dpProofUrl,
                    'delivery_address' => $validated['delivery_address'],
                    'custom_notes' => $validated['custom_notes'] ?? null,
                    'payment_deadline' => now()->addHours(24),
                ]);

                OrderItem::create([
                    'order_id' => $order->id,
                    'machine_id' => $machine->id,
                    'unit_price' => $unitPrice,
                    'quantity' => $validated['quantity'],
                    'subtotal' => $totalAmount,
                ]);

                // Buat record pembayaran DP 50% (status menunggu sampai diverifikasi oleh admin)
                $order->payments()->create([
                    'payment_code' => Reference::make(Reference::PAYMENT, Payment::class, 'payment_code'),
                    'attempt' => 1,
                    'method' => Payment::METHOD_TRANSFER_BANK,
                    'amount' => $dpAmount,
                    'status' => Payment::STATUS_MENUNGGU,
                    'expires_at' => now()->addHours(24),
                    'paid_at' => null,
                    'proof' => $dpProofUrl,
                    'notes' => 'Pembayaran Uang Muka DP 50% via ' . ($validated['bank_method'] ?? 'Transfer Bank') . ' untuk pesanan ' . $orderCode,
                ]);

                return $order;
            });

            $order->load(['items.machine', 'payments', 'customer']);

            return response()->json([
                'success' => true,
                'message' => 'Purchase Order (PO) berhasil dibuat. Bukti transfer DP 50% tersimpan di Supabase dan menunggu verifikasi finance.',
                'data' => $this->formatOrderResponse($order),
            ], 201);
        } catch (Throwable $e) {
            Log::error('OrderApiController store error: ' . $e->getMessage(), [
                'machine_id' => $validated['machine_id'] ?? null,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat Purchase Order (PO). Silakan periksa kembali data Anda.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * POST /api/orders/{id}/pay-dp
     * Upload Bukti Pembayaran DP 50%
     */
    public function uploadDpProof(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'proof' => ['nullable', 'string'],
            'proof_file' => ['nullable', 'file', 'image', 'max:5120'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $user = $request->user();
            $customer = Customer::where('email', $user?->email)->first();

            if (! $customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan.',
                ], 404);
            }

            $order = Order::where('customer_id', $customer->id)
                ->where(function ($q) use ($id) {
                    $q->where('id', is_numeric($id) ? (int)$id : 0)
                      ->orWhere('order_code', $id);
                })
                ->first();

            if (! $order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan atau Anda tidak memiliki akses ke pesanan ini.',
                ], 404);
            }

            $proofUrl = $this->resolveAndStoreProof(
                $validated['proof'] ?? null,
                $request->file('proof_file'),
                $order->order_code,
                (float) $order->dp_amount,
                'Bank BCA Corporate'
            );

            DB::transaction(function () use ($order, $proofUrl, $validated) {
                // Cari record pembayaran DP (pembayaran yang bukan pelunasan)
                $dpPayment = $order->payments()
                    ->where('notes', 'not like', '%Pelunasan%')
                    ->latest('id')
                    ->first() ?: $order->payments()->first();

                if ($dpPayment) {
                    $dpPayment->update([
                        'proof' => $proofUrl,
                        'status' => Payment::STATUS_MENUNGGU,
                        'failure_reason' => null,
                        'attempt' => $dpPayment->attempt + 1,
                        'notes' => $validated['notes'] ?? ('Unggah ulang bukti transfer DP 50% untuk pesanan ' . $order->order_code),
                    ]);
                } else {
                    $order->payments()->create([
                        'payment_code' => Reference::make(Reference::PAYMENT, Payment::class, 'payment_code'),
                        'attempt' => 1,
                        'method' => Payment::METHOD_TRANSFER_BANK,
                        'amount' => $order->dp_amount,
                        'status' => Payment::STATUS_MENUNGGU,
                        'expires_at' => now()->addHours(24),
                        'proof' => $proofUrl,
                        'notes' => 'Pembayaran Uang Muka DP 50% untuk pesanan ' . $order->order_code,
                    ]);
                }

                $order->update([
                    'dp_proof' => $proofUrl,
                    'status' => Order::STATUS_MENUNGGU_PEMBAYARAN,
                    'production_status' => Order::STAGE_VERIFIKASI_DP,
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Bukti pembayaran DP 50% berhasil diunggah ke Supabase. Menunggu verifikasi admin finance.',
                'data' => $this->formatOrderResponse($order->fresh(['items.machine', 'payments', 'customer'])),
            ]);
        } catch (Throwable $e) {
            Log::error('OrderApiController uploadDpProof error: ' . $e->getMessage(), ['id' => $id]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah bukti pembayaran DP.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * POST /api/orders/{id}/pay-pelunasan
     * Upload Bukti Pembayaran Pelunasan Sisa 50%
     */
    public function uploadPelunasanProof(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'proof' => ['nullable', 'string'],
            'proof_file' => ['nullable', 'file', 'image', 'max:5120'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $user = $request->user();
            $customer = Customer::where('email', $user?->email)->first();

            if (! $customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan.',
                ], 404);
            }

            $order = Order::where('customer_id', $customer->id)
                ->where(function ($q) use ($id) {
                    $q->where('id', is_numeric($id) ? (int)$id : 0)
                      ->orWhere('order_code', $id);
                })
                ->first();

            if (! $order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan atau Anda tidak memiliki akses ke pesanan ini.',
                ], 404);
            }

            $proofUrl = $this->resolveAndStoreProof(
                $validated['proof'] ?? null,
                $request->file('proof_file'),
                $order->order_code . '-PELUNASAN',
                (float) $order->pelunasan_amount,
                'Bank BCA Corporate'
            );

            DB::transaction(function () use ($order, $proofUrl, $validated) {
                // Cari apakah sudah ada pembayaran pelunasan sebelumnya (misal yang ditolak)
                $pelunasanPayment = $order->payments()
                    ->where('notes', 'like', '%Pelunasan%')
                    ->latest('id')
                    ->first();

                if ($pelunasanPayment && $pelunasanPayment->status === Payment::STATUS_GAGAL) {
                    $pelunasanPayment->update([
                        'proof' => $proofUrl,
                        'status' => Payment::STATUS_MENUNGGU,
                        'failure_reason' => null,
                        'attempt' => $pelunasanPayment->attempt + 1,
                        'notes' => $validated['notes'] ?? ('Unggah ulang bukti pelunasan sisa 50% untuk pesanan ' . $order->order_code),
                    ]);
                } else {
                    $order->payments()->create([
                        'payment_code' => Reference::make(Reference::PAYMENT, Payment::class, 'payment_code'),
                        'attempt' => $order->payments()->count() + 1,
                        'method' => Payment::METHOD_TRANSFER_BANK,
                        'amount' => $order->pelunasan_amount,
                        'status' => Payment::STATUS_MENUNGGU,
                        'expires_at' => now()->addHours(24),
                        'proof' => $proofUrl,
                        'notes' => 'Pembayaran Pelunasan Sisa 50% untuk pesanan ' . $order->order_code,
                    ]);
                }

                $order->update([
                    'pelunasan_proof' => $proofUrl,
                    'production_status' => Order::STAGE_MENUNGGU_PELUNASAN,
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Bukti pelunasan 50% berhasil diunggah ke Supabase. Menunggu verifikasi admin finance.',
                'data' => $this->formatOrderResponse($order->fresh(['items.machine', 'payments', 'customer'])),
            ]);
        } catch (Throwable $e) {
            Log::error('OrderApiController uploadPelunasanProof error: ' . $e->getMessage(), ['id' => $id]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah bukti pembayaran pelunasan.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * GET /api/orders/{id}/invoice
     * Generate data Invoice PDF resmi
     */
    public function invoice(Request $request, string $id): JsonResponse
    {
        try {
            $user = $request->user();
            $customer = Customer::where('email', $user?->email)->first();

            if (! $customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan untuk invoice tidak ditemukan.',
                ], 404);
            }

            $order = Order::with(['items.machine', 'payments', 'customer'])
                ->where('customer_id', $customer->id)
                ->where(function ($q) use ($id) {
                    $q->where('id', is_numeric($id) ? (int)$id : 0)
                      ->orWhere('order_code', $id);
                })
                ->first();

            if (! $order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan untuk invoice tidak ditemukan atau Anda tidak memiliki akses.',
                ], 404);
            }

            $invoiceData = [
                'invoice_number' => 'INV/' . date('Ymd') . '/' . $order->order_code,
                'po_number' => $order->order_code,
                'date' => $order->created_at->format('d F Y'),
                'company_name' => 'PT MANUFACTUR DYNAMIC INDONESIA',
                'company_address' => 'Kawasan Industri Manufaktur Kopi, Jawa Barat, Indonesia',
                'company_phone' => '+62 812-3456-7890',
                'company_email' => 'finance@manufacturdynamic.com',
                'customer_name' => $order->customer?->name ?? 'Pelanggan',
                'customer_company' => $order->customer?->company ?? 'PT Kopi Nusantara Jaya',
                'customer_address' => $order->delivery_address ?? $order->customer?->address,
                'items' => $order->items->map(fn($item) => [
                    'name' => $item->name,
                    'quantity' => $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'subtotal' => (float) $item->subtotal,
                ]),
                'total_amount' => (float) $order->total_amount,
                'dp_amount' => (float) $order->dp_amount,
                'pelunasan_amount' => (float) $order->pelunasan_amount,
                'is_dp_paid' => filled($order->dp_proof),
                'is_fully_paid' => $order->status === Order::STATUS_LUNAS || filled($order->pelunasan_proof),
                'bank_accounts' => [
                    ['bank' => 'BCA Corporate', 'account' => '088-2930-112', 'holder' => 'PT Manufactur Dynamic Indonesia'],
                    ['bank' => 'Mandiri B2B', 'account' => '137-009-881-221', 'holder' => 'PT Manufactur Dynamic Indonesia'],
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $invoiceData,
            ]);
        } catch (Throwable $e) {
            Log::error('OrderApiController invoice error: ' . $e->getMessage(), ['id' => $id]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data invoice.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    private function formatOrderResponse(Order $order): array
    {
        $firstItem = $order->items->first();

        // Stage index calculation (0-6)
        $stages = [
            Order::STAGE_VERIFIKASI_DP => 0,
            Order::STAGE_PERSIAPAN_BAHAN => 1,
            Order::STAGE_PROSES_PRODUKSI => 2,
            Order::STAGE_QUALITY_CONTROL => 3,
            Order::STAGE_MENUNGGU_PELUNASAN => 4,
            Order::STAGE_PENGIRIMAN_INSTALASI => 5,
            Order::STAGE_AFTER_SALES => 6,
        ];

        $latestPayment = $order->latestPayment ?: $order->payments->sortBy('id')->last();
        $isDpPhase = $order->production_status === Order::STAGE_VERIFIKASI_DP;
        $isPelunasanPhase = $order->production_status === Order::STAGE_MENUNGGU_PELUNASAN;

        // DP rejected jika di tahap verifikasi DP dan pembayaran terakhir gagal
        $isDpRejected = $isDpPhase && $latestPayment && $latestPayment->status === Payment::STATUS_GAGAL;

        // Pelunasan rejected jika di tahap menunggu pelunasan dan pembayaran terakhir gagal
        $isPelunasanRejected = $isPelunasanPhase && $latestPayment && $latestPayment->status === Payment::STATUS_GAGAL;

        $rejectionReason = null;
        if ($isDpRejected || $isPelunasanRejected) {
            $rejectionReason = $latestPayment?->failure_reason ?: 'Bukti transfer ditolak sebagai tidak valid/palsu oleh finance admin.';
        }

        return [
            'id' => (string) $order->id,
            'po_number' => $order->order_code,
            'company_name' => $order->customer?->company ?? 'PT Kopi Nusantara Jaya',
            'contact_person' => $order->customer?->name ?? 'Bpk. Hendra Wijaya',
            'product_name' => $firstItem?->name ?? 'Mesin Pengolahan Kopi',
            'category' => $firstItem?->machine?->category?->name ?? 'Commercial Espresso',
            'quantity' => $firstItem?->quantity ?? 1,
            'price_per_unit' => (float) ($firstItem?->unit_price ?? $order->total_amount),
            'total_amount' => (float) $order->total_amount,
            'dp_amount' => (float) $order->dp_amount,
            'pelunasan_amount' => (float) $order->pelunasan_amount,
            'order_date' => $order->created_at->format('d M Y'),
            'estimated_completion_date' => $order->created_at->addDays(18)->format('d M Y'),
            'status' => $order->status,
            'production_status' => $order->production_status,
            'production_stage_index' => $stages[$order->production_status] ?? 0,
            'production_stage_title' => Order::productionStageLabels()[$order->production_status] ?? 'Verifikasi DP',
            'dp_proof_path' => $order->dp_proof,
            'pelunasan_proof_path' => $order->pelunasan_proof,
            'is_dp_rejected' => $isDpRejected,
            'is_pelunasan_rejected' => $isPelunasanRejected,
            'rejection_reason' => $rejectionReason,
            'delivery_address' => $order->delivery_address ?? 'Jl. Sudirman No. 45, Jakarta Selatan',
            'custom_notes' => $order->custom_notes ?? '',
            'payments' => $order->payments->map(fn($p) => [
                'payment_code' => $p->payment_code,
                'method' => $p->method,
                'amount' => (float) $p->amount,
                'status' => $p->status,
                'paid_at' => $p->paid_at?->format('d M Y H:i'),
                'proof' => $p->proof,
                'notes' => $p->notes,
            ]),
        ];
    }

    /**
     * Helper untuk upload bukti transfer ke Supabase Storage dengan fallback lokal otomatis.
     * Menerima UploadedFile atau Base64 data URI (gambar JPG/PNG/WebP atau dokumen PDF).
     * TIDAK PERNAH membuat SVG dummy / palsu. Hanya menyimpan file asli yang dikirim oleh pelanggan.
     */
    private function resolveAndStoreProof(
        ?string $proofString,
        mixed $file,
        string $orderCode,
        float $amount,
        string $bankMethod
    ): ?string {
        $storage = app(\App\Services\SupabaseStorageService::class);
        $safeOrderCode = Str::slug($orderCode, '_');

        // Pastikan folder penyimpanan lokal selalu tersedia
        $localDir = public_path('uploads/payments');
        if (! file_exists($localDir)) {
            @mkdir($localDir, 0755, true);
        }

        // 1. Jika ada file upload multipart langsung dari form request
        if ($file instanceof \Illuminate\Http\UploadedFile && $file->isValid()) {
            $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
            $filename = "proof_{$safeOrderCode}_" . time() . '.' . $ext;

            try {
                $file->move($localDir, $filename);
                $localUrl = asset('uploads/payments/' . $filename);
            } catch (Throwable $moveEx) {
                $localUrl = null;
            }

            // Sinkronkan ke Supabase Storage
            try {
                $supabaseUrl = $storage->upload($file, 'payments');
                if ($supabaseUrl) {
                    return $supabaseUrl;
                }
            } catch (Throwable $e) {
                Log::warning('Supabase upload failed: ' . $e->getMessage());
            }

            if ($localUrl) {
                return $localUrl;
            }
        }

        if (empty($proofString)) {
            return null;
        }

        // 2. Jika sudah berupa URL publik Supabase atau web
        if (Str::startsWith($proofString, ['http://', 'https://'])) {
            return $proofString;
        }

        // 3. Jika berupa Base64 dari aplikasi mobile (data:image/... atau data:application/pdf)
        if (Str::startsWith($proofString, ['data:image/', 'data:application/pdf'])) {
            try {
                [$meta, $data] = explode(',', $proofString, 2);
                $decoded = base64_decode($data);

                if ($decoded !== false) {
                    if (Str::startsWith($proofString, 'data:application/pdf')) {
                        $ext = 'pdf';
                        $mimeType = 'application/pdf';
                    } else {
                        preg_match('/data:image\/([a-zA-Z0-9\+\-]+);base64/', $meta, $matches);
                        $subType = strtolower($matches[1] ?? 'png');
                        $ext = ($subType === 'jpeg') ? 'jpg' : $subType;
                        $mimeType = 'image/' . ($matches[1] ?? 'png');
                    }

                    $filename = "proof_{$safeOrderCode}_" . time() . '.' . $ext;
                    file_put_contents($localDir . '/' . $filename, $decoded);
                    $localUrl = asset('uploads/payments/' . $filename);

                    // Sinkronisasi file asli ke Supabase Storage
                    $supabaseUrl = $storage->uploadRaw(
                        $decoded,
                        $filename,
                        $mimeType,
                        'payments'
                    );

                    return $supabaseUrl ?: $localUrl;
                }
            } catch (Throwable $e) {
                Log::warning('Base64 decode failed: ' . $e->getMessage());
            }
        }

        // 4. Jika nama file sudah ada di folder uploads lokal
        $cleanBase = basename($proofString);
        if (file_exists($localDir . '/' . $cleanBase)) {
            return asset('uploads/payments/' . $cleanBase);
        }

        return null;
    }
}

