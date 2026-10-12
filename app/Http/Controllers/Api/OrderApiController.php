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
use Illuminate\Support\Str;

class OrderApiController extends Controller
{
    /**
     * GET /api/orders
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $customer = Customer::where('email', $user?->email)->first();

        $query = Order::with(['items.machine', 'payments', 'customer'])->latest('id');

        if ($customer) {
            $query->where('customer_id', $customer->id);
        }

        $orders = $query->get()->map(function ($order) {
            return $this->formatOrderResponse($order);
        });

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    /**
     * GET /api/orders/{id}
     */
    public function show(string $id): JsonResponse
    {
        $order = Order::with(['items.machine', 'payments', 'customer'])
            ->where('id', $id)
            ->orWhere('order_code', $id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $this->formatOrderResponse($order),
        ]);
    }

    /**
     * POST /api/orders
     * Buat Purchase Order (PO) baru dengan sistem DP 50%
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'machine_id' => ['required', 'exists:machines,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'delivery_address' => ['required', 'string'],
            'custom_notes' => ['nullable', 'string'],
            'bank_method' => ['nullable', 'string'],
            'dp_proof' => ['nullable', 'string'],
        ]);

        $machine = Machine::findOrFail($validated['machine_id']);
        $user = $request->user();
        
        $customer = Customer::firstOrCreate(
            ['email' => $user?->email ?? 'procurement@kopinusantara.co.id'],
            [
                'customer_code' => 'CUST-' . strtoupper(substr(uniqid(), -6)),
                'name' => $user?->name ?? 'Procurement Client',
                'company' => 'PT Kopi Nusantara Jaya',
                'phone' => $user?->phone ?? '0812-9876-5432',
                'address' => $validated['delivery_address'],
            ]
        );

        $unitPrice = (float) ($machine->price ?? 145000000);
        $totalAmount = $unitPrice * $validated['quantity'];
        $dpAmount = $totalAmount * 0.50;
        $pelunasanAmount = $totalAmount * 0.50;

        $orderCode = 'PO-2026-0' . rand(100, 999);

        $order = DB::transaction(function () use (
            $customer, $orderCode, $totalAmount, $dpAmount, $pelunasanAmount,
            $validated, $machine, $unitPrice
        ) {
            $order = Order::create([
                'customer_id' => $customer->id,
                'order_code' => $orderCode,
                'total_amount' => $totalAmount,
                'dp_amount' => $dpAmount,
                'pelunasan_amount' => $pelunasanAmount,
                'status' => Order::STATUS_MENUNGGU_PEMBAYARAN,
                'production_status' => Order::STAGE_VERIFIKASI_DP,
                'dp_proof' => $validated['dp_proof'] ?? null,
                'delivery_address' => $validated['delivery_address'],
                'custom_notes' => $validated['custom_notes'] ?? null,
                'payment_deadline' => now()->addHours(12),
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'machine_id' => $machine->id,
                'name' => $machine->name,
                'unit_price' => $unitPrice,
                'quantity' => $validated['quantity'],
                'subtotal' => $totalAmount,
            ]);

            // Buat record pembayaran DP 50%
            $payment = $order->payments()->create([
                'payment_code' => Reference::make(Reference::PAYMENT, Payment::class, 'payment_code'),
                'attempt' => 1,
                'method' => $validated['bank_method'] ?? Payment::METHOD_TRANSFER_BANK,
                'amount' => $dpAmount,
                'status' => filled($validated['dp_proof']) ? Payment::STATUS_LUNAS : Payment::STATUS_MENUNGGU,
                'expires_at' => now()->addHours(12),
                'paid_at' => filled($validated['dp_proof']) ? now() : null,
                'proof' => $validated['dp_proof'] ?? null,
                'notes' => 'Pembayaran Uang Muka DP 50% untuk pesanan ' . $orderCode,
            ]);

            if (filled($validated['dp_proof'])) {
                $order->update([
                    'production_status' => Order::STAGE_PERSIAPAN_BAHAN,
                ]);
            }

            return $order;
        });

        $order->load(['items.machine', 'payments', 'customer']);

        return response()->json([
            'success' => true,
            'message' => 'Purchase Order (PO) berhasil dibuat.',
            'data' => $this->formatOrderResponse($order),
        ], 201);
    }

    /**
     * POST /api/orders/{id}/pay-dp
     * Upload Bukti Pembayaran DP 50%
     */
    public function uploadDpProof(Request $request, string $id): JsonResponse
    {
        $order = Order::where('id', $id)->orWhere('order_code', $id)->firstOrFail();

        $validated = $request->validate([
            'proof' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $dpPayment = $order->payments()->first();
        if ($dpPayment) {
            $dpPayment->update([
                'proof' => $validated['proof'],
                'status' => Payment::STATUS_LUNAS,
                'paid_at' => now(),
                'notes' => $validated['notes'] ?? $dpPayment->notes,
            ]);
        }

        $order->update([
            'dp_proof' => $validated['proof'],
            'production_status' => Order::STAGE_PERSIAPAN_BAHAN,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bukti pembayaran DP 50% berhasil diunggah. Tahap produksi diperbarui ke: Persiapan Bahan Baku.',
            'data' => $this->formatOrderResponse($order->fresh(['items.machine', 'payments', 'customer'])),
        ]);
    }

    /**
     * POST /api/orders/{id}/pay-pelunasan
     * Upload Bukti Pembayaran Pelunasan Sisa 50%
     */
    public function uploadPelunasanProof(Request $request, string $id): JsonResponse
    {
        $order = Order::where('id', $id)->orWhere('order_code', $id)->firstOrFail();

        $validated = $request->validate([
            'proof' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        // Buat record pembayaran pelunasan
        $order->payments()->create([
            'payment_code' => Reference::make(Reference::PAYMENT, Payment::class, 'payment_code'),
            'attempt' => $order->payments()->count() + 1,
            'method' => Payment::METHOD_TRANSFER_BANK,
            'amount' => $order->pelunasan_amount,
            'status' => Payment::STATUS_LUNAS,
            'expires_at' => now()->addHours(12),
            'paid_at' => now(),
            'proof' => $validated['proof'],
            'notes' => 'Pembayaran Pelunasan Sisa 50% untuk pesanan ' . $order->order_code,
        ]);

        $order->update([
            'status' => Order::STATUS_LUNAS,
            'pelunasan_proof' => $validated['proof'],
            'production_status' => Order::STAGE_PENGIRIMAN_INSTALASI,
            'paid_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bukti pelunasan 50% berhasil diunggah. Tahap produksi diperbarui ke: Pengiriman & Instalasi.',
            'data' => $this->formatOrderResponse($order->fresh(['items.machine', 'payments', 'customer'])),
        ]);
    }

    /**
     * GET /api/orders/{id}/invoice
     * Generate data Invoice PDF resmi
     */
    public function invoice(string $id): JsonResponse
    {
        $order = Order::with(['items.machine', 'payments', 'customer'])
            ->where('id', $id)
            ->orWhere('order_code', $id)
            ->firstOrFail();

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
}
