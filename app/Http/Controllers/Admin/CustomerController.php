<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCustomerRequest;
use App\Http\Requests\Admin\UpdateCustomerRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Support\Reference;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Pelanggan yang terdaftar beserta riwayat pesanannya.
 */
class CustomerController extends Controller
{
    /**
     * Daftar pelanggan terdaftar.
     */
    public function index(Request $request): View
    {
        $customers = Customer::query()
            ->withCount('orders')
            ->withSum(
                ['orders as total_lunas' => fn ($query) => $query->where('status', Order::STATUS_LUNAS)],
                'total_amount',
            )
            ->when($request->filled('q'), function ($query) use ($request) {
                $keyword = '%'.$request->string('q').'%';

                $query->where(function ($sub) use ($keyword) {
                    $sub->where('name', 'like', $keyword)
                        ->orWhere('email', 'like', $keyword)
                        ->orWhere('phone', 'like', $keyword)
                        ->orWhere('company', 'like', $keyword)
                        ->orWhere('customer_code', 'like', $keyword);
                });
            })
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $stats = Cache::remember('admin_customers_stats', 300, function () {
            $customerCounts = Customer::query()
                ->selectRaw('COUNT(*) as total, COUNT(CASE WHEN created_at >= ? THEN 1 END) as baru, COUNT(CASE WHEN EXISTS (SELECT 1 FROM orders WHERE orders.customer_id = customers.id) THEN 1 END) as berpesanan', [now()->startOfMonth()])
                ->first();

            $nilaiLunas = (float) Payment::query()->where('status', Payment::STATUS_LUNAS)->sum('amount');

            return [
                'total' => (int) ($customerCounts->total ?? 0),
                'baru' => (int) ($customerCounts->baru ?? 0),
                'berpesanan' => (int) ($customerCounts->berpesanan ?? 0),
                'nilai_lunas' => $nilaiLunas,
            ];
        });

        return view('admin.customers.index', compact('customers', 'stats'));
    }

    /**
     * Detail pelanggan + riwayat pesanan & pembayarannya.
     */
    public function show(Customer $customer): View
    {
        Payment::expireOverdue();

        $orders = $customer->orders()
            ->with(['payments' => fn ($query) => $query->latest('id')])
            ->latest('id')
            ->paginate(10);

        $stats = [
            'pesanan' => $customer->orders()->count(),
            'menunggu' => $customer->orders()->where('status', Order::STATUS_MENUNGGU_PEMBAYARAN)->count(),
            'lunas' => $customer->orders()->where('status', Order::STATUS_LUNAS)->count(),
            'gagal' => $customer->orders()->where('status', Order::STATUS_GAGAL)->count(),
            'nilai_lunas' => (float) $customer->orders()->where('status', Order::STATUS_LUNAS)->sum('total_amount'),
        ];

        return view('admin.customers.show', compact('customer', 'orders', 'stats'));
    }

    public function edit(Customer $customer): View
    {
        return view('admin.customers.form', compact('customer'));
    }


    /**
     * Perbarui data pelanggan.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update($request->validated());

        return redirect()
            ->route('admin.customers.show', $customer)
            ->with('success', 'Data pelanggan '.$customer->name.' berhasil diperbarui.');
    }

    /**
     * Hapus pelanggan (hanya bila belum memiliki pesanan).
     */
    public function destroy(Customer $customer): RedirectResponse
    {
        if ($customer->orders()->exists()) {
            return back()->with(
                'error',
                'Pelanggan '.$customer->name.' memiliki riwayat pesanan sehingga tidak dapat dihapus.',
            );
        }

        $customer->delete();

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Pelanggan '.$customer->name.' berhasil dihapus.');
    }
}
