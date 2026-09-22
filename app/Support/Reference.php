<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;

/**
 * Pembuat kode referensi berurutan per hari.
 *
 * Format: PREFIX-YYYYMMDD-0001 — contoh ORD-20260922-0001 (kode pesanan),
 * PAY-20260922-0001 (kode pembayaran), dan CUST-20260922-0001 (kode pelanggan).
 */
class Reference
{
    public const ORDER = 'ORD';

    public const PAYMENT = 'PAY';

    public const CUSTOMER = 'CUST';

    /**
     * @param  class-string<Model>  $modelClass
     */
    public static function make(string $prefix, string $modelClass, string $column, ?string $date = null): string
    {
        $base = $prefix.'-'.($date ?? now()->format('Ymd')).'-';

        $sequence = $modelClass::query()
            ->where($column, 'like', $base.'%')
            ->count() + 1;

        do {
            $code = $base.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
            $sequence++;
        } while ($modelClass::query()->where($column, $code)->exists());

        return $code;
    }
}
