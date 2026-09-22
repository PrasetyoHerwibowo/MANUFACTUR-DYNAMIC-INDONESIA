<?php

namespace App\Console\Commands;

use App\Models\Payment;
use Illuminate\Console\Command;

/**
 * Menandai pembayaran yang melewati batas pembayaran sebagai gagal.
 *
 * Pelanggan yang pembayarannya gagal harus mengulang pembayaran dari awal
 * (kode pembayaran baru) melalui pesanan terkait.
 */
class ExpireOverduePayments extends Command
{
    protected $signature = 'payments:expire';

    protected $description = 'Menandai pembayaran yang melewati batas waktu sebagai gagal';

    public function handle(): int
    {
        $total = Payment::expireOverdue();

        $this->info($total > 0
            ? $total.' pembayaran ditandai gagal karena melewati batas waktu pembayaran.'
            : 'Tidak ada pembayaran yang melewati batas waktu pembayaran.');

        return self::SUCCESS;
    }
}
