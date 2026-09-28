<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Rincian model mesin pada sebuah pesanan.
 */
class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'machine_id',
        'unit_price',
        'quantity',
        'subtotal',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity' => 'integer',
        'subtotal' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }

    /**
     * Nama model mesin yang dipesan (nama item tidak lagi disimpan terpisah).
     */
    public function machineName(): string
    {
        return $this->machine?->name ?? 'Model mesin dihapus';
    }

    /**
     * Kode/tipe model mesin bila ada.
     */
    public function machineCode(): ?string
    {
        return $this->machine?->model_code;
    }
}
