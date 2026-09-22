<?php

namespace App\Models;

use App\Support\Reference;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Pesanan mesin dari pelanggan beserta status pembayarannya.
 */
class Order extends Model
{
    public const STATUS_MENUNGGU_PEMBAYARAN = 'menunggu_pembayaran';

    public const STATUS_LUNAS = 'lunas';

    public const STATUS_GAGAL = 'gagal';

    public const STATUS_DIBATALKAN = 'dibatalkan';

    /** Batas waktu pembayaran (dalam jam) sejak kode pembayaran dibuat. */
    public const PAYMENT_WINDOW_HOURS = 24;

    protected $fillable = [
        'customer_id',
        'order_code',
        'total_amount',
        'status',
        'payment_deadline',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'payment_deadline' => 'datetime',
        'paid_at' => 'datetime',
    ];

    /**
     * Nilai default saat pesanan baru dibuat.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => self::STATUS_MENUNGGU_PEMBAYARAN,
        'total_amount' => 0,
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Pembayaran terakhir yang dibuat untuk pesanan ini.
     */
    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    /**
     * @return array<string, string>
     */
    public static function statusLabels(): array
    {
        return [
            self::STATUS_MENUNGGU_PEMBAYARAN => 'Menunggu Pembayaran',
            self::STATUS_LUNAS => 'Lunas',
            self::STATUS_GAGAL => 'Gagal',
            self::STATUS_DIBATALKAN => 'Dibatalkan',
        ];
    }

    public function statusLabel(): string
    {
        return self::statusLabels()[$this->status]
            ?? ucfirst(str_replace('_', ' ', (string) $this->status));
    }

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        return $query->when(filled($status), fn (Builder $sub) => $sub->where('status', $status));
    }

    /**
     * Pembayaran yang masih menunggu (belum lunas / gagal).
     */
    public function pendingPayment(): ?Payment
    {
        return $this->payments()
            ->where('status', Payment::STATUS_MENUNGGU)
            ->latest('id')
            ->first();
    }

    /**
     * Buat kode pembayaran baru.
     *
     * Dipakai saat pesanan dibuat maupun ketika pelanggan harus mengulang
     * pembayaran dari awal setelah pembayaran sebelumnya gagal / kedaluwarsa:
     * kode pembayaran, percobaan, dan batas waktunya dimulai ulang.
     */
    public function createPayment(
        string $method,
        ?CarbonInterface $expiresAt = null,
        ?float $amount = null,
        ?string $notes = null,
    ): Payment {
        $payment = $this->payments()->create([
            'payment_code' => Reference::make(Reference::PAYMENT, Payment::class, 'payment_code'),
            'attempt' => $this->payments()->count() + 1,
            'method' => $method,
            'amount' => $amount ?? (float) $this->total_amount,
            'status' => Payment::STATUS_MENUNGGU,
            'expires_at' => $expiresAt ?? now()->addHours(self::PAYMENT_WINDOW_HOURS),
            'notes' => $notes,
        ]);

        $this->forceFill([
            'status' => self::STATUS_MENUNGGU_PEMBAYARAN,
            'paid_at' => null,
            'payment_deadline' => $payment->expires_at,
        ])->save();

        return $payment;
    }

    /**
     * Selaraskan status pesanan dengan seluruh pembayaran yang tercatat.
     */
    public function syncStatusFromPayments(): void
    {
        $paid = $this->payments()->where('status', Payment::STATUS_LUNAS)->latest('paid_at')->first();

        if ($paid) {
            $this->forceFill([
                'status' => self::STATUS_LUNAS,
                'paid_at' => $paid->paid_at ?? now(),
            ])->save();

            return;
        }

        $pending = $this->payments()->where('status', Payment::STATUS_MENUNGGU)->latest('id')->first();

        if ($pending) {
            $this->forceFill([
                'status' => self::STATUS_MENUNGGU_PEMBAYARAN,
                'paid_at' => null,
                'payment_deadline' => $pending->expires_at,
            ])->save();

            return;
        }

        if ($this->payments()->exists()) {
            $this->forceFill([
                'status' => self::STATUS_GAGAL,
                'paid_at' => null,
            ])->save();
        }
    }
}
