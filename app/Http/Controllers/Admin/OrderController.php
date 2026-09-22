<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Machine;
use App\Models\Order;
use App\Models\Payment;
use App\Support\ImageUploader;
use App\Support\Reference;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Pesanan pelanggan beserta pembayarannya (satu daftar gabungan).
 */
class OrderController extends Controller
{
    /**
     * Daftar pesanan + pembayaran.
     */
    public function index(Request $request): View
    {
        // Pembayaran yang melewati batas waktu langsung ditandai gagal.
        Payment::expireOverdue();

        $status = $request->string('status')->toString();
        $method = $request->string('metode')->toString();

        $orders = Order::query()
            ->with(['customer', 'latestPayment'])
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

        $stats = [
            'total' => Order::query()->count(),
            'menunggu' => Order::query()->where('status', Order::STATUS_MENUNGGU_PEMBAYARAN)->count(),
            'lunas' => Order::query()->where('status', Order::STATUS_LUNAS)->count(),
            'gagal' => Order::query()->where('status', Order::STATUS_GAGAL)->count(),
            'nilai_menunggu' => (float) Payment::query()->pending()->sum('amount'),
            'nilai_lunas' => (float) Payment::query()->where('status', Payment::STATUS_LUNAS)->sum('amount'),
        ];

        return view('admin.orders.index', [
            'orders' => $orders,
            'stats' => $stats,
            'statusLabels' => Order::statusLabels(),
            'methodLabels' => Payment::methodLabels(),
        ]);
    }

    /**
     * Form pesanan baru.
     */
    public function create(): View
    {
        return view('admin.orders.create', [
            'customers' => Customer::query()->orderBy('name')->get(['id', 'customer_code', 'name', 'email']),
            'machines' => Machine::query()->ordered()->get(['id', 'name', 'model_code']),
            'methodLabels' => Payment::methodLabels(),
            'defaultDeadline' => now()->addHours(Order::PAYMENT_WINDOW_HOURS)->format('Y-m-d\TH:i'),
        ]);
    }

    /**
     * Simpan pesanan baru beserta kode pembayarannya.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.machine_id' => ['nullable', 'integer', 'exists:machines,id'],
            'items.*.name' => ['required', 'string', 'max:255'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:999'],
            'method' => ['required', Rule::in(array_keys(Payment::methodLabels()))],
            'payment_deadline' => ['nullable', 'date', 'after:now'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ], [], [
            'customer_id' => 'pelanggan',
            'items.*.name' => 'nama item',
            'items.*.unit_price' => 'harga satuan',
            'items.*.quantity' => 'jumlah',
            'method' => 'metode pembayaran',
            'payment_deadline' => 'batas pembayaran',
        ]);

        $order = DB::transaction(function () use ($validated): Order {
            $order = Order::query()->create([
                'customer_id' => $validated['customer_id'],
                'order_code' => Reference::make(Reference::ORDER, Order::class, 'order_code'),
                'status' => Order::STATUS_MENUNGGU_PEMBAYARAN,
                'notes' => $validated['notes'] ?? null,
            ]);

            $total = 0.0;

            foreach ($validated['items'] as $item) {
                $subtotal = round((float) $item['unit_price'] * (int) $item['quantity'], 2);
                $total += $subtotal;

                $order->items()->create([
                    'machine_id' => $item['machine_id'] ?? null,
                    'name' => $item['name'],
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal,
                ]);
            }

            $order->forceFill(['total_amount' => $total])->save();

            $order->createPayment(
                $validated['method'],
                filled($validated['payment_deadline'] ?? null)
                    ? CarbonImmutable::parse($validated['payment_deadline'])
                    : null,
            );

            $order->customer?->forceFill(['last_order_at' => now()])->save();

            return $order;
        });

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Pesanan '.$order->order_code.' berhasil dibuat beserta kode pembayarannya.');
    }

    /**
     * Detail pesanan: rincian item + seluruh riwayat pembayaran.
     */
    public function show(Order $order): View
    {
        Payment::expireOverdue();

        $order->load([
            'customer',
            'items.machine',
            'payments' => fn ($query) => $query->latest('id'),
        ]);

        return view('admin.orders.show', [
            'order' => $order,
            'methodLabels' => Payment::methodLabels(),
            'defaultDeadline' => now()->addHours(Order::PAYMENT_WINDOW_HOURS)->format('Y-m-d\TH:i'),
        ]);
    }

    /**
     * Hapus pesanan beserta item dan pembayarannya.
     */
    public function destroy(Order $order): RedirectResponse
    {
        $code = $order->order_code;

        $order->delete();

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'Pesanan '.$code.' beserta seluruh pembayarannya berhasil dihapus.');
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

        if ($request->hasFile('proof')) {
            $proof = ImageUploader::store($request->file('proof'), 'payments');
            ImageUploader::delete($payment->proof);
        }

        $payment->markAsPaid($proof, $validated['notes'] ?? null);

        return back()->with('success', 'Pembayaran '.$payment->payment_code.' berhasil diverifikasi sebagai lunas.');
    }

    /**
     * Tandai pembayaran gagal tanpa menunggu batas waktu.
     */
    public function failPayment(Request $request, Payment $payment): RedirectResponse
    {
        $validated = $request->validate([
            'failure_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $payment->markAsFailed($validated['failure_reason'] ?: 'Ditandai gagal oleh admin');

        return back()->with(
            'success',
            'Pembayaran '.$payment->payment_code.' ditandai gagal. Pelanggan harus mengulang pembayaran dari awal.',
        );
    }

    /**
     * Buat kode pembayaran baru: pelanggan mengulang pembayaran dari awal.
     */
    public function renewPayment(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'method' => ['required', Rule::in(array_keys(Payment::methodLabels()))],
            'payment_deadline' => ['nullable', 'date', 'after:now'],
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

        $payment = $order->createPayment(
            $validated['method'],
            filled($validated['payment_deadline'] ?? null)
                ? CarbonImmutable::parse($validated['payment_deadline'])
                : null,
            null,
            $validated['notes'] ?? null,
        );

        return back()->with(
            'success',
            'Kode pembayaran baru '.$payment->payment_code.' dibuat. Pelanggan mengulang pembayaran dari awal.',
        );
    }

    /**
     * Ubah batas pembayaran (perpanjang / percepat).
     */
    public function updateDeadline(Request $request, Payment $payment): RedirectResponse
    {
        $validated = $request->validate([
            'expires_at' => ['required', 'date', 'after:now'],
        ], [], ['expires_at' => 'batas pembayaran']);

        $payment->extendDeadline(CarbonImmutable::parse($validated['expires_at']));

        return back()->with(
            'success',
            'Batas pembayaran '.$payment->payment_code.' diperbarui menjadi '
                .$payment->fresh()->expires_at->format('d M Y H:i').' WIB.',
        );
    }
}
