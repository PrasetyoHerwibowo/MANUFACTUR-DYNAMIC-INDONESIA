<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Pelanggan (pembeli mesin) yang terdaftar.
 */
class Customer extends Model
{
    protected $fillable = [
        'customer_code',
        'name',
        'email',
        'phone',
        'company',
        'address',
        'city',
        'province',
        'postal_code',
        'notes',
        'is_active',
        'last_order_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_order_at' => 'datetime',
    ];

    /**
     * Nilai default form saat menambah pelanggan baru.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_active' => true,
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Inisial nama pelanggan untuk avatar.
     */
    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim((string) $this->name)) ?: [];
        $initials = '';

        foreach (array_slice($parts, 0, 2) as $part) {
            $initials .= mb_strtoupper(mb_substr($part, 0, 1));
        }

        return $initials === '' ? '?' : $initials;
    }

    /**
     * Alamat lengkap satu baris (untuk tampilan tabel).
     */
    public function fullAddress(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->province,
            $this->postal_code,
        ]);

        return $parts === [] ? '—' : implode(', ', $parts);
    }
}
