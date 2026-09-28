<?php

namespace App\Http\Requests\Admin;

use App\Models\Customer;

/**
 * Validasi saat admin menyunting pelanggan.
 *
 * Record yang sedang di-edit diabaikan pada aturan keunikan, sehingga admin
 * tidak dianggap bentrok dengan datanya sendiri.
 */
class UpdateCustomerRequest extends CustomerRequest
{
    /**
     * ID pelanggan dari route model binding (admin/pelanggan/{customer}).
     *
     * Diambil dari route, bukan dari body, supaya klien tidak bisa
     * memalsukan pelanggan mana yang "diabaikan".
     */
    protected function customerId(): ?int
    {
        $customer = $this->route('customer');

        return $customer instanceof Customer ? (int) $customer->getKey() : null;
    }
}
