<?php

namespace App\Http\Requests\Admin;

/**
 * Validasi saat admin menambah pelanggan baru.
 *
 * Tidak ada record yang perlu diabaikan, sehingga keunikan email maupun
 * keunikan nama + nomor telepon selalu dibandingkan dengan seluruh tabel.
 */
class StoreCustomerRequest extends CustomerRequest
{
    protected function customerId(): ?int
    {
        return null;
    }
}
