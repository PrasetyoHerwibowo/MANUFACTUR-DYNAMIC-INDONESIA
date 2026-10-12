<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentApiController extends Controller
{
    /**
     * GET /api/payments
     * Menampilkan semua log transaksi dan pembayaran (DP & Pelunasan)
     */
    public function index(Request $request): JsonResponse
    {
        $payments = Payment::with(['order.items', 'order.customer'])
            ->latest('id')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'payment_code' => $p->payment_code,
                    'po_number' => $p->order?->order_code ?? '-',
                    'product_name' => $p->order?->items->first()?->name ?? 'Mesin Industri Kopi',
                    'company_name' => $p->order?->customer?->company ?? 'PT Kopi Nusantara Jaya',
                    'method' => $p->methodLabel(),
                    'amount' => (float) $p->amount,
                    'status' => $p->statusLabel(),
                    'is_success' => $p->status === Payment::STATUS_LUNAS,
                    'is_expired' => $p->isExpired(),
                    'expires_at' => $p->expires_at?->format('d M Y H:i'),
                    'paid_at' => $p->paid_at?->format('d M Y H:i'),
                    'proof' => $p->proof,
                    'notes' => $p->notes,
                    'created_at' => $p->created_at->format('d M Y H:i'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $payments,
        ]);
    }
}
