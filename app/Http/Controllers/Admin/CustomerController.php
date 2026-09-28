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
        $status = $request->string('status')->toString();

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
            ->when($status === 'aktif', fn ($query) => $query->where('is_active', true))
            ->when($status === 'nonaktif', fn ($query) => $query->where('is_active', false))
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $stats = [
            'total' => Customer::query()->count(),
            'aktif' => Customer::query()->active()->count(),
            'baru' => Customer::query()->where('created_at', '>=', now()->startOfMonth())->count(),
            'berpesanan' => Customer::query()->has('orders')->count(),
            'nilai_lunas' => (float) Payment::query()->where('status', Payment::STATUS_LUNAS)->sum('amount'),
        ];

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

    public function create(): View
    {
        return view('admin.customers.form', ['customer' => new Customer()]);
    }

    public function edit(Customer $customer): View
    {
        return view('admin.customers.form', compact('customer'));
    }

    /**
     * Tambah pelanggan baru.
     */
    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['customer_code'] = Reference::make(Reference::CUSTOMER, Customer::class, 'customer_code');

        $customer = Customer::query()->create($validated);

        return redirect()
            ->route('admin.customers.show', $customer)
            ->with('success', 'Pelanggan '.$customer->name.' berhasil ditambahkan.');
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
     * Aktifkan / nonaktifkan pelanggan.
     */
    public function toggleStatus(Customer $customer): RedirectResponse
    {
        $customer->forceFill(['is_active' => ! $customer->is_active])->save();

        return back()->with(
            'success',
            'Pelanggan '.$customer->name.' kini '.($customer->is_active ? 'aktif' : 'nonaktif').'.',
        );
    }

    /**
     * Hapus pelanggan (hanya bila belum memiliki pesanan).
     */
    public function destroy(Customer $customer): RedirectResponse
    {
        if ($customer->orders()->exists()) {
            return back()->with(
                'error',
                'Pelanggan '.$customer->name.' memiliki riwayat pesanan sehingga tidak dapat dihapus. Nonaktifkan saja akunnya.',
            );
        }

        $customer->delete();

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Pelanggan '.$customer->name.' berhasil dihapus.');
    }
}
