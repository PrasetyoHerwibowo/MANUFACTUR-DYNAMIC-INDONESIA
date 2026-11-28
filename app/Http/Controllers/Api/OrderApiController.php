<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Machine;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Support\Reference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class OrderApiController extends Controller
{
    /**
     * Resolusi Pelanggan B2B dari Sesi Login Sanctum atau Data Permintaan
     */
    private function resolveCustomer(Request $request): ?Customer
    {
        $user = auth('sanctum')->user() ?? $request->user();
        if ($user && filled($user->email)) {
            $customer = Customer::where('email', $user->email)->first();
            if ($customer) {
                return $customer;
            }
        }

        $email = $request->input('email') ?? $request->query('email');
        if (filled($email)) {
            $customer = Customer::where('email', trim($email))->first();
            if ($customer) {
                return $customer;
            }
        }

        $phone = $request->input('phone') ?? $request->input('pic_phone') ?? $request->query('phone');
        if (filled($phone)) {
            $customer = Customer::where('phone', trim($phone))->first();
            if ($customer) {
                return $customer;
            }
        }

        $company = $request->input('company_name') ?? $request->input('company') ?? $request->query('company');
        if (filled($company)) {
            $customer = Customer::where('company', 'ilike', '%' . trim($company) . '%')->first();
            if ($customer) {
                return $customer;
            }
        }

        return null;
    }

    /**
     * GET /api/orders
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $customer = $this->resolveCustomer($request);

            if ($customer) {
                $orders = Cache::remember("api_orders_customer_{$customer->id}", 15, function () use ($customer) {
                    return Order::with(['items.machine.category', 'payments', 'customer'])
                        ->where('customer_id', $customer->id)
                        ->latest('id')
                        ->get()
                        ->map(fn($order) => $this->formatOrderResponse($order))
                        ->toArray();
                });
            } else {
                // Tampilkan order terbaru untuk sesi perangkat/guest
                $orders = Order::with(['items.machine.category', 'payments', 'customer'])
                    ->latest('id')
                    ->take(15)
                    ->get()
                    ->map(fn($order) => $this->formatOrderResponse($order))
                    ->toArray();
            }

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
            $customer = $this->resolveCustomer($request);

            $order = Order::with(['items.machine.category', 'payments', 'customer'])
                ->when($customer, fn($q) => $q->where('customer_id', $customer->id))
                ->where(function ($q) use ($id) {
                    $q->where('id', is_numeric($id) ? (int) $id : 0)
                        ->orWhere('order_code', $id);
                })
                ->first();

            if (!$order) {
                // Fallback pencarian tanpa filter customer jika menggunakan order_code unik
                $order = Order::with(['items.machine.category', 'payments', 'customer'])
                    ->where('order_code', $id)
                    ->first();
            }

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $this->formatOrderResponse($order),
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
            'model_code' => ['nullable', 'string'],
            'product_name' => ['nullable', 'string'],
            'machine_name' => ['nullable', 'string'],
            'unit_price' => ['nullable', 'numeric'],
            'price' => ['nullable', 'numeric'],
            'quantity' => ['required', 'integer', 'min:1'],
            'delivery_address' => ['required', 'string'],
            'custom_notes' => ['nullable', 'string'],
            'bank_method' => ['nullable', 'string'],
            'dp_proof' => ['nullable', 'string'],
            'dp_proof_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'contact_name' => ['nullable', 'string', 'max:150'],
            'pic_name' => ['nullable', 'string', 'max:150'],
            'name' => ['nullable', 'string', 'max:150'],
            'company_name' => ['nullable', 'string', 'max:150'],
            'company' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'pic_phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'string', 'max:150'],
        ]);

        try {
            $rawMachineId = $validated['machine_id'];
            $rawModelCode = $request->input('model_code');
            $rawMachineName = $request->input('product_name') ?? $request->input('machine_name');

            $machine = null;

            // 1. Cari berdasarkan ID jika numerik dan ada di database
            if (is_numeric($rawMachineId) && (int) $rawMachineId > 0) {
                $machine = Machine::with('category')->find((int) $rawMachineId);
            }

            // 2. Cari berdasarkan model_code
            if (!$machine && filled($rawModelCode)) {
                $machine = Machine::with('category')->where('model_code', trim($rawModelCode))->first();
            }

            // 3. Cari jika machine_id adalah string model_code atau slug
            if (!$machine && filled($rawMachineId) && is_string($rawMachineId)) {
                $machine = Machine::with('category')
                    ->where('model_code', trim($rawMachineId))
                    ->orWhere('slug', trim($rawMachineId))
                    ->first();
            }

            // 4. Cari berdasarkan nama mesin (Exact Match & Case-Insensitive)
            if (!$machine && filled($rawMachineName)) {
                $machine = Machine::with('category')
                    ->whereRaw('LOWER(name) = ?', [strtolower(trim($rawMachineName))])
                    ->first();
            }

            // 5. Cari berdasarkan potongan nama (Partial Match)
            if (!$machine && filled($rawMachineName)) {
                $machine = Machine::with('category')
                    ->where('name', 'ilike', '%' . trim($rawMachineName) . '%')
                    ->first();
            }

            // 6. Jika masih tidak ditemukan (misal produk baru dari katalog):
            // Daftarkan record Machine baru secara otomatis & dinamis
            if (!$machine) {
                $catName = $request->input('category') ?? 'Mesin Industri';
                $category = Category::where('name', 'ilike', '%' . $catName . '%')->first() ?: Category::first();

                $cleanName = trim($rawMachineName ?: 'Mesin Industri Manufaktur');
                $cleanModel = trim($rawModelCode ?: ('MDI-' . strtoupper(Str::limit(Str::slug($cleanName, ''), 8, ''))));
                $slug = Str::slug($cleanName);
                if (Machine::where('slug', $slug)->exists()) {
                    $slug .= '-' . Str::lower(Str::random(5));
                }

                $initialPrice = (float) ($request->input('unit_price') ?? $request->input('price') ?? 25000000);

                $machine = Machine::create([
                    'name' => $cleanName,
                    'slug' => $slug,
                    'model_code' => $cleanModel,
                    'category_id' => $category?->id ?? 1,
                    'price' => $initialPrice,
                    'is_active' => true,
                ]);
                $machine->load('category');
            }

            // Customer Resolution: Mendukung Customer Terotentikasi & Tamu B2B
            $user = auth('sanctum')->user() ?? $request->user();

            $contactName = trim($request->input('contact_name') ?? $request->input('pic_name') ?? $request->input('name') ?? $user?->name ?? 'Pelanggan Procurement');
            $companyName = trim($request->input('company_name') ?? $request->input('company') ?? $user?->company ?? ('Mitra ' . $contactName));
            $phone = trim($request->input('phone') ?? $request->input('pic_phone') ?? $user?->phone ?? '');
            $email = trim($request->input('email') ?? $user?->email ?? '');

            if (empty($email)) {
                $slugContact = Str::slug($contactName ?: 'customer', '.');
                $email = $slugContact . '@client.manufaktur.id';
            }

            $customer = null;
            if ($user && filled($user->email)) {
                $customer = Customer::where('email', $user->email)->first();
            }
            if (!$customer && filled($email)) {
                $customer = Customer::where('email', $email)->first();
            }
            if (!$customer && filled($phone)) {
                $customer = Customer::where('phone', $phone)->first();
            }
            if (!$customer && filled($companyName)) {
                $customer = Customer::where('company', 'ilike', '%' . $companyName . '%')->first();
            }

            if (!$customer) {
                $customer = Customer::create([
                    'customer_code' => Reference::make(Reference::CUSTOMER, Customer::class, 'customer_code'),
                    'name' => $contactName,
                    'company' => $companyName,
                    'email' => $email,
                    'phone' => $phone ?: null,
                    'address' => $validated['delivery_address'],
                ]);
            } else {
                $updates = [];
                if ($contactName && $contactName !== 'Pelanggan Procurement' && (empty($customer->name) || $customer->name === 'Procurement Client')) {
                    $updates['name'] = $contactName;
                }
                if ($companyName && (empty($customer->company) || $customer->company === 'PT Kopi Nusantara Jaya')) {
                    $updates['company'] = $companyName;
                }
                if ($phone && empty($customer->phone)) {
                    $updates['phone'] = $phone;
                }
                if ($validated['delivery_address'] && empty($customer->address)) {
                    $updates['address'] = $validated['delivery_address'];
                }
                if (!empty($updates)) {
                    $customer->update($updates);
                }
            }

            // Tentukan harga satuan secara dinamis & akurat untuk semua produk
            $inputPrice = $request->filled('unit_price')
                ? (float) $request->input('unit_price')
                : ($request->filled('price') ? (float) $request->input('price') : 0.0);
            $dbPrice = (float) $machine->price;

            if ($inputPrice > 0) {
                $unitPrice = $inputPrice;
                if ($dbPrice <= 0) {
                    $machine->update(['price' => $unitPrice]);
                }
            } elseif ($dbPrice > 0) {
                $unitPrice = $dbPrice;
            } else {
                $unitPrice = 25000000.0;
            }

            $totalAmount = $unitPrice * $validated['quantity'];
            $dpAmount = $totalAmount * 0.50;
            $pelunasanAmount = $totalAmount * 0.50;

            // Generate nomor PO / Order Code resmi menggunakan format Reference sistem
            $orderCode = Reference::make(Reference::ORDER, Order::class, 'order_code');

            // Generate nomor seri unik khusus untuk produk mesin yang dipesan
            $modelCodeClean = preg_replace('/[^A-Za-z0-9]/', '', $machine->model_code ?: 'MDI');
            $serialNumber = 'SN-' . date('Y') . '-' . $modelCodeClean . '-' . strtoupper(Str::random(4));

            // Upload bukti transfer DP 50% ke Supabase Storage / Penyimpanan Lokal
            $dpProofUrl = $this->resolveAndStoreProof(
                $validated['dp_proof'] ?? null,
                $request->file('dp_proof_file'),
                $orderCode,
                $dpAmount,
                $validated['bank_method'] ?? 'Bank BCA Corporate'
            );

            $order = DB::transaction(function () use ($customer, $orderCode, $serialNumber, $totalAmount, $dpAmount, $pelunasanAmount, $validated, $machine, $unitPrice, $dpProofUrl) {
                // Status awal saat ada bukti DP: Menunggu Pembayaran & Tahap Timeline: 1. Verifikasi DP
                $order = Order::create([
                    'customer_id' => $customer->id,
                    'order_code' => $orderCode,
                    'serial_number' => $serialNumber,
                    'total_amount' => $totalAmount,
                    'dp_amount' => $dpAmount,
                    'pelunasan_amount' => $pelunasanAmount,
                    'status' => Order::STATUS_MENUNGGU_PEMBAYARAN,
                    'production_status' => Order::STAGE_VERIFIKASI_DP,
                    'dp_proof' => $dpProofUrl,
                    'delivery_address' => $validated['delivery_address'],
                    'custom_notes' => $validated['custom_notes'] ?? null,
                    'payment_deadline' => now()->addHours(Order::PAYMENT_WINDOW_HOURS),
                ]);

                OrderItem::create([
                    'order_id' => $order->id,
                    'machine_id' => $machine->id,
                    'unit_price' => $unitPrice,
                    'quantity' => $validated['quantity'],
                    'subtotal' => $totalAmount,
                ]);

                // Buat record pembayaran DP 50%
                $order->payments()->create([
                    'payment_code' => Reference::make(Reference::PAYMENT, Payment::class, 'payment_code'),
                    'attempt' => 1,
                    'method' => Payment::METHOD_TRANSFER_BANK,
                    'amount' => $dpAmount,
                    'status' => Payment::STATUS_MENUNGGU,
                    'expires_at' => now()->addHours(Order::PAYMENT_WINDOW_HOURS),
                    'paid_at' => null,
                    'proof' => $dpProofUrl,
                    'notes' => 'Pembayaran Uang Muka DP 50% via ' . ($validated['bank_method'] ?? 'Transfer Bank') . ' untuk pesanan ' . $orderCode,
                ]);

                return $order;
            });

            // Invalidate Cache agar admin dashboard dan API mobile langsung terupdate seketika
            Cache::forget('admin_orders_stats');
            Cache::forget('admin_payments_last_expired_check');
            Cache::forget("api_orders_customer_{$customer->id}");

            $order->load(['items.machine.category', 'payments', 'customer']);

            return response()->json([
                'success' => true,
                'message' => 'Purchase Order (PO) berhasil dibuat. Bukti transfer DP 50% tersimpan dan menunggu verifikasi finance.',
                'data' => $this->formatOrderResponse($order),
            ], 201);
        } catch (Throwable $e) {
            Log::error('OrderApiController store error: ' . $e->getMessage(), [
                'machine_id' => $validated['machine_id'] ?? null,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat Purchase Order (PO): ' . $e->getMessage(),
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
            'proof_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'notes' => ['nullable', 'string'],
            'bank_method' => ['nullable', 'string'],
        ]);

        try {
            $customer = $this->resolveCustomer($request);

            $order = Order::where(function ($q) use ($id) {
                $q->where('id', is_numeric($id) ? (int) $id : 0)
                    ->orWhere('order_code', $id);
            })->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan.',
                ], 404);
            }

            if ($customer && $order->customer_id !== $customer->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki hak akses untuk pesanan ini.',
                ], 403);
            }

            $bankMethod = $validated['bank_method'] ?? 'Bank BCA Corporate';

            $proofUrl = $this->resolveAndStoreProof(
                $validated['proof'] ?? null,
                $request->file('proof_file'),
                $order->order_code,
                (float) $order->dp_amount,
                $bankMethod
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
                        'expires_at' => now()->addHours(Order::PAYMENT_WINDOW_HOURS),
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

            Cache::forget('admin_orders_stats');
            Cache::forget("api_orders_customer_{$order->customer_id}");

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
            'proof_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'notes' => ['nullable', 'string'],
            'bank_method' => ['nullable', 'string'],
        ]);

        try {
            $customer = $this->resolveCustomer($request);

            $order = Order::where(function ($q) use ($id) {
                $q->where('id', is_numeric($id) ? (int) $id : 0)
                    ->orWhere('order_code', $id);
            })->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan.',
                ], 404);
            }

            if ($customer && $order->customer_id !== $customer->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki hak akses untuk pesanan ini.',
                ], 403);
            }

            $bankMethod = $validated['bank_method'] ?? 'Bank BCA Corporate';

            $proofUrl = $this->resolveAndStoreProof(
                $validated['proof'] ?? null,
                $request->file('proof_file'),
                $order->order_code . '-PELUNASAN',
                (float) $order->pelunasan_amount,
                $bankMethod
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
                        'expires_at' => now()->addHours(Order::PAYMENT_WINDOW_HOURS),
                        'proof' => $proofUrl,
                        'notes' => 'Pembayaran Pelunasan Sisa 50% untuk pesanan ' . $order->order_code,
                    ]);
                }

                $order->update([
                    'pelunasan_proof' => $proofUrl,
                    'production_status' => Order::STAGE_MENUNGGU_PELUNASAN,
                ]);
            });

            Cache::forget('admin_orders_stats');
            Cache::forget("api_orders_customer_{$order->customer_id}");

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
     * POST /api/orders/{id}/confirm-installation
     * Konfirmasi penerimaan dan pemasangan unit mesin oleh customer (Tahap 6 -> Tahap 7).
     */
    public function confirmInstallation(Request $request, string $id): JsonResponse
    {
        try {
            $customer = $this->resolveCustomer($request);

            $order = Order::where(function ($q) use ($id) {
                $q->where('id', is_numeric($id) ? (int)$id : 0)
                  ->orWhere('order_code', $id);
            })->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan.',
                ], 404);
            }

            if ($customer && $order->customer_id !== $customer->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki hak akses untuk pesanan ini.',
                ], 403);
            }

            // Validasi alur: Unit hanya dapat dikonfirmasi bila sudah masuk tahap pengiriman atau pelunasan
            if (! in_array($order->production_status, [Order::STAGE_PENGIRIMAN_INSTALASI, Order::STAGE_AFTER_SALES])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unit mesin belum dalam tahap Pengiriman & Instalasi Teknisi.',
                ], 422);
            }

            // Update status pesanan ke Tahap 7: After-Sales Service & Garansi Resmi Aktif
            DB::transaction(function () use ($order) {
                $order->update([
                    'production_status' => Order::STAGE_AFTER_SALES,
                    'status' => Order::STATUS_LUNAS,
                    'paid_at' => $order->paid_at ?: now(),
                    'notes' => trim(($order->notes ? $order->notes . "\n" : '') . '[KONFIRMASI PELANGGAN ' . date('d/m/Y H:i') . ']: Unit mesin telah diterima & dikonfirmasi terpasang oleh pelanggan. Layanan After-Sales Service dan garansi resmi 2 tahun aktif.'),
                ]);
            });

            // Invalidate cache
            Cache::forget('admin_orders_stats');
            Cache::forget("api_orders_customer_{$order->customer_id}");
            Cache::forget("api_order_detail_{$order->id}");
            Cache::forget("api_order_detail_{$order->order_code}");

            return response()->json([
                'success' => true,
                'message' => 'Instalasi selesai! Unit telah terpasang dan layanan After-Sales serta garansi resmi 2 tahun kini aktif.',
                'data' => $this->formatOrderResponse($order->fresh(['items.machine', 'payments', 'customer'])),
            ]);
        } catch (Throwable $e) {
            Log::error('OrderApiController confirmInstallation error: ' . $e->getMessage(), ['id' => $id]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses konfirmasi unit terpasang.',
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
            $customer = $this->resolveCustomer($request);

            $order = Order::with(['items.machine', 'payments', 'customer'])
                ->where(function ($q) use ($id) {
                    $q->where('id', is_numeric($id) ? (int) $id : 0)
                        ->orWhere('order_code', $id);
                })
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan untuk invoice tidak ditemukan.',
                ], 404);
            }

            if ($customer && $order->customer_id !== $customer->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke invoice pesanan ini.',
                ], 403);
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

        $productName = $firstItem?->machine?->name ?: ($firstItem?->name ?: 'Mesin Industri Manufaktur');
        $categoryName = $firstItem?->machine?->category?->name ?? 'Mesin Industri';
        $serialNumber = $order->serial_number ?: ('SN-' . date('Y') . '-' . ($firstItem?->machine?->model_code ?: 'MDI') . '-' . strtoupper(substr(md5($order->id . $order->order_code), 0, 4)));

        return [
            'id' => (string) $order->id,
            'po_number' => $order->order_code,
            'company_name' => $order->customer?->company ?? 'PT Kopi Nusantara Jaya',
            'contact_person' => $order->customer?->name ?? 'Bpk. Hendra Wijaya',
            'product_name' => $productName,
            'category' => $categoryName,
            'model_code' => $firstItem?->machine?->model_code,
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
            'serial_number' => $serialNumber,
            'warranty_period_months' => (int) ($order->warranty_period_months ?: 24),
            'warranty_expiry_date' => $order->warranty_expiry_date ? $order->warranty_expiry_date->format('d M Y') : $order->created_at->addMonths(24)->format('d M Y'),
            'is_warranty_active' => $order->isWarrantyActive(),
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
     * Helper untuk upload bukti transfer LANGSUNG ke Supabase Storage (tanpa membebani server lokal).
     * Menerima UploadedFile atau Base64 data URI (gambar JPG/PNG atau dokumen PDF).
     * Memvalidasi Magic Bytes MIME asli secara ketat & menolak SVG / Video / file berbahaya.
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

        // Batas maksimal berkas bukti transfer: 5 MB
        $maxSizeBytes = 5 * 1024 * 1024;

        // 1. Jika ada file upload multipart langsung dari form request
        if ($file instanceof \Illuminate\Http\UploadedFile && $file->isValid()) {
            if ($file->getSize() > $maxSizeBytes) {
                Log::warning('resolveAndStoreProof: Berkas melebihi batas maksimal 5 MB.', ['size' => $file->getSize()]);
                return null;
            }

            // Baca header 512 byte awal untuk inspeksi Magic Bytes
            $handle = fopen($file->getRealPath(), 'rb');
            $header = fread($handle, 512);
            fclose($handle);

            // Tolak SVG dan XML secara mutlak
            if (stripos($header, '<svg') !== false || stripos($header, '<?xml') !== false) {
                Log::warning('resolveAndStoreProof: Format SVG/XML terdeteksi dan ditolak.');
                return null;
            }

            // Tolak Video (MP4)
            if (strlen($header) >= 8 && substr($header, 4, 4) === 'ftyp') {
                Log::warning('resolveAndStoreProof: File terdeteksi sebagai MP4 Video dan ditolak.');
                return null;
            }

            $isPdf = strncmp($header, "%PDF-", 5) === 0;
            $isJpeg = strncmp($header, "\xFF\xD8\xFF", 3) === 0;
            $isPng = strncmp($header, "\x89PNG\r\n\x1a\n", 8) === 0;

            if (!$isPdf && !$isJpeg && !$isPng) {
                Log::warning('resolveAndStoreProof: Magic bytes tidak sesuai untuk PDF atau JPG/PNG.');
                return null;
            }

            $ext = $isPdf ? 'pdf' : ($isPng ? 'png' : 'jpg');
            $mimeType = $isPdf ? 'application/pdf' : ($isPng ? 'image/png' : 'image/jpeg');
            $filename = "proof_{$safeOrderCode}_" . time() . '.' . $ext;

            // Baca konten binary langsung dari memori/temp file PHP tanpa menyimpannya ke server
            $rawBytes = file_get_contents($file->getRealPath());

            // Upload LANGSUNG ke Supabase Storage
            $supabaseUrl = $storage->uploadRaw($rawBytes, $filename, $mimeType, 'payments');
            if ($supabaseUrl) {
                return $supabaseUrl;
            }

            Log::error('resolveAndStoreProof: Gagal mengunggah file bukti ke Supabase Storage.');
            return null;
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
            // Tolak jika metadata string mengindikasikan SVG atau Video
            if (Str::startsWith($proofString, ['data:image/svg', 'data:video/'])) {
                Log::warning('resolveAndStoreProof: Base64 tipe SVG atau Video ditolak.');
                return null;
            }

            try {
                [$meta, $data] = explode(',', $proofString, 2);
                $decoded = base64_decode($data, true);

                if ($decoded !== false && strlen($decoded) > 0) {
                    if (strlen($decoded) > $maxSizeBytes) {
                        Log::warning('resolveAndStoreProof: Base64 decode melebihi 5 MB.');
                        return null;
                    }

                    $headerSample = substr($decoded, 0, 512);

                    // Tolak SVG dan XML
                    if (stripos($headerSample, '<svg') !== false || stripos($headerSample, '<?xml') !== false) {
                        Log::warning('resolveAndStoreProof: Base64 terdeteksi sebagai SVG/XML.');
                        return null;
                    }

                    // Tolak Video MP4
                    if (strlen($headerSample) >= 8 && substr($headerSample, 4, 4) === 'ftyp') {
                        Log::warning('resolveAndStoreProof: Base64 terdeteksi sebagai video MP4.');
                        return null;
                    }

                    $isPdf = strncmp($headerSample, "%PDF-", 5) === 0;
                    $isJpeg = strncmp($headerSample, "\xFF\xD8\xFF", 3) === 0;
                    $isPng = strncmp($headerSample, "\x89PNG\r\n\x1a\n", 8) === 0;

                    if (!$isPdf && !$isJpeg && !$isPng) {
                        Log::warning('resolveAndStoreProof: Base64 magic bytes bukan JPG, PNG, atau PDF.');
                        return null;
                    }

                    $ext = $isPdf ? 'pdf' : ($isPng ? 'png' : 'jpg');
                    $mimeType = $isPdf ? 'application/pdf' : ($isPng ? 'image/png' : 'image/jpeg');
                    $filename = "proof_{$safeOrderCode}_" . time() . '.' . $ext;

                    // Upload LANGSUNG ke Supabase Storage (tanpa menyimpan file ke server lokal)
                    $supabaseUrl = $storage->uploadRaw(
                        $decoded,
                        $filename,
                        $mimeType,
                        'payments'
                    );

                    if ($supabaseUrl) {
                        return $supabaseUrl;
                    }

                    Log::error('resolveAndStoreProof: Gagal mengunggah base64 bukti ke Supabase Storage.');
                    return null;
                }
            } catch (Throwable $e) {
                Log::warning('Base64 decode failed: ' . $e->getMessage());
            }
        }

        return null;
    }
}

