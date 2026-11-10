<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Machine;
use App\Models\Order;
use App\Models\Payment;
use App\Support\ImageUploader;
use App\Support\Reference;
use App\Support\Sanitize;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Pesanan pelanggan beserta pembayarannya (satu daftar gabungan).
 */
class OrderController extends Controller
{
    /** Batas teks catatan pesanan & aturan karakternya. */
    private const NOTES_MAX = 250;

    /** Catatan pesanan: huruf, angka, spasi, dan tanda baca (tanpa @ # $ % ^ *). */
    private const NOTES_PATTERN = '/^[\p{L}\p{N}\s.,;:!?\'"()\[\]{}_\/+&=|~-]*$/u';

    /** Harga satuan dalam rupiah: maksimal 9 digit (skala juta sampai miliar). */
    private const UNIT_PRICE_MAX = 999999999;

    /** Jumlah unit yang dipesan: maksimal 2 digit. */
    private const QUANTITY_MAX = 99;

    /**
     * Daftar pesanan + pembayaran.
     */
    public function index(Request $request): View
    {
        try {
            // Pembayaran yang melewati batas waktu diperiksa secara berkala (dibatasi 2 menit).
            Cache::remember('admin_payments_last_expired_check', 120, function () {
                return Payment::expireOverdue();
            });

            $status = $request->string('status')->toString();
            $method = $request->string('metode')->toString();

            $orders = Order::query()
                ->with(['customer', 'latestPayment', 'items.machine'])
                ->when($request->filled('q'), function ($query) use ($request) {
                    $keyword = '%'.$request->string('q').'%';

                    $query->where(function ($sub) use ($keyword) {
                        $sub->where('order_code', 'like', $keyword)
                            ->orWhereHas('customer', function ($customer) use ($keyword) {
                                $customer->where('name', 'like', $keyword)
                                    ->orWhere('email', 'like', $keyword)
                                    ->orWhere('customer_code', 'like', $keyword);
                            })
                            ->orWhereHas('payments', fn ($payment) => $payment->where('payment_code', 'like', $keyword));
                    });
                })
                ->when(array_key_exists($status, Order::statusLabels()), fn ($query) => $query->where('status', $status))
                ->when(array_key_exists($method, Payment::methodLabels()), function ($query) use ($method) {
                    $query->whereHas('payments', fn ($payment) => $payment->where('method', $method));
                })
                ->when($request->filled('dari'), fn ($query) => $query->whereDate('created_at', '>=', $request->date('dari')))
                ->when($request->filled('sampai'), fn ($query) => $query->whereDate('created_at', '<=', $request->date('sampai')))
                ->latest('id')
                ->paginate(15)
                ->withQueryString();

            // Hitung statistik pesanan secara real-time langsung dari database
            Cache::forget('admin_orders_stats');
            $orderCounts = Order::query()
                ->selectRaw("COUNT(*) as total,
                    COUNT(CASE WHEN status = ? THEN 1 END) as menunggu,
                    COUNT(CASE WHEN status = ? THEN 1 END) as lunas,
                    COUNT(CASE WHEN status = ? THEN 1 END) as gagal",
                    [Order::STATUS_MENUNGGU_PEMBAYARAN, Order::STATUS_LUNAS, Order::STATUS_GAGAL])
                ->first();

            $paymentSums = Payment::query()
                ->selectRaw("SUM(CASE WHEN status = ? THEN amount ELSE 0 END) as nilai_menunggu,
                    SUM(CASE WHEN status = ? THEN amount ELSE 0 END) as nilai_lunas",
                    [Payment::STATUS_MENUNGGU, Payment::STATUS_LUNAS])
                ->first();

            $stats = [
                'total' => (int) ($orderCounts->total ?? 0),
                'menunggu' => (int) ($orderCounts->menunggu ?? 0),
                'lunas' => (int) ($orderCounts->lunas ?? 0),
                'gagal' => (int) ($orderCounts->gagal ?? 0),
                'nilai_menunggu' => (float) ($paymentSums->nilai_menunggu ?? 0),
                'nilai_lunas' => (float) ($paymentSums->nilai_lunas ?? 0),
            ];

            return view('admin.orders.index', [
                'orders' => $orders,
                'stats' => $stats,
                'statusLabels' => Order::statusLabels(),
                'methodLabels' => Payment::methodLabels(),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('OrderController@index error: ' . $e->getMessage());

            $orders = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            $stats = [
                'total' => 0,
                'menunggu' => 0,
                'lunas' => 0,
                'gagal' => 0,
                'nilai_menunggu' => 0,
                'nilai_lunas' => 0,
            ];

            return view('admin.orders.index', [
                'orders' => $orders,
                'stats' => $stats,
                'statusLabels' => Order::statusLabels(),
                'methodLabels' => Payment::methodLabels(),
            ])->with('error', 'Gagal memuat daftar pesanan.');
        }
    }


    /**
     * Detail pesanan: rincian item + seluruh riwayat pembayaran.
     */
    public function show(Order $order): View
    {
        try {
            Payment::expireOverdue();

            $order->load([
                'customer',
                'items.machine',
                'payments' => fn ($query) => $query->latest('id'),
            ]);

            return view('admin.orders.show', [
                'order' => $order,
                'methodLabels' => Payment::methodLabels(),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('OrderController@show error: ' . $e->getMessage());

            return view('admin.orders.show', [
                'order' => $order,
                'methodLabels' => Payment::methodLabels(),
            ])->with('error', 'Gagal memuat detail pesanan.');
        }
    }

    /**
     * Hapus pesanan beserta item dan pembayarannya.
     */
    public function destroy(Order $order): RedirectResponse
    {
        try {
            $code = $order->order_code;

            DB::transaction(function () use ($order) {
                // Hapus bukti transfer file jika ada
                if ($order->dp_proof) {
                    ImageUploader::delete($order->dp_proof);
                }
                if ($order->pelunasan_proof) {
                    ImageUploader::delete($order->pelunasan_proof);
                }

                foreach ($order->payments as $payment) {
                    if ($payment->proof) {
                        ImageUploader::delete($payment->proof);
                    }
                }

                $order->payments()->delete();
                $order->items()->delete();
                $order->delete();
            });

            Cache::forget('admin_orders_stats');

            return redirect()
                ->route('admin.orders.index')
                ->with('success', 'Pesanan '.$code.' beserta seluruh pembayarannya berhasil dihapus.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('OrderController@destroy error: ' . $e->getMessage());

            return back()->with('error', 'Gagal menghapus pesanan: '.$e->getMessage());
        }
    }

    /**
     * Verifikasi pembayaran menjadi lunas.
     */
    public function verifyPayment(Request $request, Payment $payment): RedirectResponse
    {
        $validated = $request->validate([
            'proof' => ['nullable', 'image', 'max:4096'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $proof = null;
        try {
            if ($request->hasFile('proof')) {
                $proof = ImageUploader::store($request->file('proof'), 'payments');
                if ($payment->proof) {
                    ImageUploader::delete($payment->proof);
                }
            }

            $payment->markAsPaid($proof, $validated['notes'] ?? null);

            return back()->with('success', 'Pembayaran '.$payment->payment_code.' berhasil diverifikasi sebagai lunas.');
        } catch (\Throwable $e) {
            if ($proof && $request->hasFile('proof')) {
                ImageUploader::delete($proof);
            }
            \Illuminate\Support\Facades\Log::error('OrderController@verifyPayment error: ' . $e->getMessage());

            return back()->with('error', 'Gagal memverifikasi pembayaran: '.$e->getMessage());
        }
    }

    /**
     * Tandai pembayaran gagal tanpa menunggu batas waktu.
     */
    public function failPayment(Request $request, Payment $payment): RedirectResponse
    {
        $validated = $request->validate([
            'failure_reason' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $payment->markAsFailed($validated['failure_reason'] ?: 'Ditandai gagal oleh admin');

            return back()->with(
                'success',
                'Pembayaran '.$payment->payment_code.' ditandai gagal. Pelanggan harus mengulang pembayaran dari awal.',
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('OrderController@failPayment error: ' . $e->getMessage());

            return back()->with('error', 'Gagal menandai pembayaran gagal: '.$e->getMessage());
        }
    }

    /**
     * Buat kode pembayaran baru: pelanggan mengulang pembayaran dari awal.
     *
     * Batas pembayaran mengikuti Order::PAYMENT_WINDOW_HOURS jam sejak kode
     * dibuat, sehingga tidak perlu diisi manual oleh admin.
     */
    public function renewPayment(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'method' => ['required', Rule::in(array_keys(Payment::methodLabels()))],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($order->status === Order::STATUS_LUNAS) {
            return back()->with(
                'error',
                'Pesanan '.$order->order_code.' sudah lunas sehingga tidak perlu kode pembayaran baru.',
            );
        }

        if ($order->pendingPayment() !== null) {
            return back()->with(
                'error',
                'Masih ada pembayaran yang menunggu pada pesanan ini. Verifikasi lunas atau tandai gagal terlebih dahulu.',
            );
        }

        try {
            $payment = $order->createPayment(
                $validated['method'],
                null,
                null,
                $validated['notes'] ?? null,
            );

            return back()->with(
                'success',
                'Kode pembayaran baru '.$payment->payment_code.' dibuat. Pelanggan mengulang pembayaran dari awal.',
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('OrderController@renewPayment error: ' . $e->getMessage());

            return back()->with('error', 'Gagal membuat kode pembayaran baru: '.$e->getMessage());
        }
    }

    /**
     * Verifikasi bukti transfer DP 50% atau Pelunasan (Real vs Palsu).
     */
    public function verifyProof(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'decision' => ['required', 'in:real,fake'],
            'target' => ['nullable', 'in:dp,pelunasan,any'],
            'next_stage' => ['nullable', 'string', Rule::in(array_keys(Order::productionStageLabels()))],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $decision = $validated['decision'];
        $isPelunasan = ($validated['target'] ?? null) === 'pelunasan' 
            || $order->production_status === Order::STAGE_MENUNGGU_PELUNASAN;
        $reason = ($validated['reason'] ?? null) ?: ($decision === 'fake' 
            ? ($isPelunasan ? 'Bukti transfer pelunasan tidak valid / mutasi rekening tidak ditemukan' : 'Bukti transfer DP tidak valid / mutasi rekening tidak ditemukan')
            : null);

        try {
            DB::transaction(function () use ($order, $decision, $validated, $reason, $isPelunasan) {
                if ($isPelunasan) {
                    $targetPayment = $order->payments()->where('notes', 'like', '%Pelunasan%')->latest('id')->first()
                        ?: $order->payments()->where('status', Payment::STATUS_MENUNGGU)->latest('id')->first();
                } else {
                    $targetPayment = $order->payments()->where('notes', 'not like', '%Pelunasan%')->latest('id')->first()
                        ?: $order->payments()->where('status', Payment::STATUS_MENUNGGU)->latest('id')->first();
                }

                if ($decision === 'real') {
                    // Jika verifikasi valid (REAL)
                    if ($targetPayment) {
                        $noteUpdate = trim(($targetPayment->notes ? $targetPayment->notes . ' ' : '') . '(Diverifikasi REAL oleh finance)');
                        $targetPayment->markAsPaid(null, $noteUpdate);
                    }

                    if ($isPelunasan) {
                        // Jika bukti pelunasan disetujui: Order berstatus LUNAS & Tahap: After-Sales Service (Selesai Garansi Resmi Aktif)
                        $order->update([
                            'status' => Order::STATUS_LUNAS,
                            'production_status' => Order::STAGE_AFTER_SALES,
                            'paid_at' => now(),
                            'notes' => trim(($order->notes ? $order->notes . "\n" : '') . '[VERIFIKASI FINANCE ' . date('d/m/Y H:i') . ']: Bukti Pelunasan disetujui REAL. Pesanan LUNAS & garansi resmi 2 tahun aktif.'),
                        ]);
                    } else {
                        // Jika bukti DP 50% disetujui: Majukan ke tahap produksi berikutnya
                        $targetStage = ($validated['next_stage'] ?? null) ?: Order::STAGE_PERSIAPAN_BAHAN;
                        $order->update([
                            'production_status' => $targetStage,
                            'notes' => trim(($order->notes ? $order->notes . "\n" : '') . '[VERIFIKASI FINANCE ' . date('d/m/Y H:i') . ']: Bukti DP 50% disetujui REAL. Memasuki tahap produksi: ' . (Order::productionStageLabels()[$targetStage] ?? $targetStage) . '.'),
                        ]);
                    }
                } else {
                    // Jika ditolak (PALSU / FAKE)
                    if ($targetPayment) {
                        $targetPayment->markAsFailed($reason);
                    }

                    if ($isPelunasan) {
                        $order->update([
                            'pelunasan_proof' => null,
                            'production_status' => Order::STAGE_MENUNGGU_PELUNASAN,
                            'notes' => trim(($order->notes ? $order->notes . "\n" : '') . '[VERIFIKASI FINANCE ' . date('d/m/Y H:i') . ']: DITOLAK (BUKTI PELUNASAN PALSU) - ' . $reason . '. Pelanggan diminta mengunggah ulang bukti pelunasan.'),
                        ]);
                    } else {
                        $order->update([
                            'dp_proof' => null,
                            'status' => Order::STATUS_MENUNGGU_PEMBAYARAN,
                            'production_status' => Order::STAGE_VERIFIKASI_DP,
                            'notes' => trim(($order->notes ? $order->notes . "\n" : '') . '[VERIFIKASI FINANCE ' . date('d/m/Y H:i') . ']: DITOLAK (BUKTI DP PALSU) - ' . $reason . '. Pelanggan diminta mengunggah ulang bukti transfer DP.'),
                        ]);
                    }
                }
            });

            if ($decision === 'real') {
                $order->refresh();
                return back()->with('success', 'Bukti transfer diverifikasi REAL (VALID)! Status tahap produksi diperbarui menjadi: ' . $order->productionStageLabel() . '.');
            } else {
                return back()->with('error', 'Bukti transfer ditolak sebagai PALSU / TIDAK VALID. Catatan penolakan tersimpan dan pelanggan diminta mengunggah bukti valid.');
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('OrderController@verifyProof error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memproses verifikasi bukti transfer: ' . $e->getMessage());
        }
    }

    /**
     * Update tahap produksi pesanan secara langsung (7 Tahap Manufaktur).
     */
    public function updateProductionStage(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'production_status' => ['required', Rule::in(array_keys(Order::productionStageLabels()))],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $newStage = $validated['production_status'];
            $updateData = [
                'production_status' => $newStage,
            ];

            if ($newStage === Order::STAGE_AFTER_SALES) {
                $updateData['status'] = Order::STATUS_LUNAS;
                if (! $order->paid_at) {
                    $updateData['paid_at'] = now();
                }
            }

            if (! empty($validated['notes'])) {
                $updateData['notes'] = trim(($order->notes ? $order->notes . "\n" : '') . '[UPDATE TAHAP ' . date('d/m/Y H:i') . ']: ' . $validated['notes']);
            }

            $order->update($updateData);

            return back()->with('success', 'Tahap produksi pesanan ' . $order->order_code . ' berhasil diubah menjadi: ' . $order->productionStageLabel() . '.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('OrderController@updateProductionStage error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui tahap produksi: ' . $e->getMessage());
        }
    }
}

