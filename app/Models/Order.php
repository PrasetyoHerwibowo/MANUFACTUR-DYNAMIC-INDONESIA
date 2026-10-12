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
 * Pesanan mesin dari pelanggan beserta status pembayarannya dan timeline manufaktur.
 */
class Order extends Model
{
    public const STATUS_MENUNGGU_PEMBAYARAN = 'menunggu_pembayaran';
    public const STATUS_LUNAS = 'lunas';
    public const STATUS_GAGAL = 'gagal';
    public const STATUS_DIBATALKAN = 'dibatalkan';

    // 7 Tahap Timeline Produksi Manufaktur
    public const STAGE_VERIFIKASI_DP = 'verifikasi_dp';
    public const STAGE_PERSIAPAN_BAHAN = 'persiapan_bahan';
    public const STAGE_PROSES_PRODUKSI = 'proses_produksi';
    public const STAGE_QUALITY_CONTROL = 'quality_control';
    public const STAGE_MENUNGGU_PELUNASAN = 'menunggu_pelunasan';
    public const STAGE_PENGIRIMAN_INSTALASI = 'pengiriman_instalasi';
    public const STAGE_AFTER_SALES = 'after_sales_service';

    /** Batas waktu pembayaran (dalam jam) sejak kode pembayaran dibuat. */
    public const PAYMENT_WINDOW_HOURS = 12;

    protected $fillable = [
        'customer_id',
        'order_code',
        'total_amount',
        'dp_amount',
        'pelunasan_amount',
        'status',
        'production_status',
        'dp_proof',
        'pelunasan_proof',
        'delivery_address',
        'custom_notes',
        'payment_deadline',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'dp_amount' => 'decimal:2',
        'pelunasan_amount' => 'decimal:2',
        'payment_deadline' => 'datetime',
        'paid_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => self::STATUS_MENUNGGU_PEMBAYARAN,
        'production_status' => self::STAGE_VERIFIKASI_DP,
        'total_amount' => 0,
        'dp_amount' => 0,
        'pelunasan_amount' => 0,
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

    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_MENUNGGU_PEMBAYARAN => 'Menunggu Pembayaran',
            self::STATUS_LUNAS => 'Lunas',
            self::STATUS_GAGAL => 'Gagal',
            self::STATUS_DIBATALKAN => 'Dibatalkan',
        ];
    }

    public static function productionStageLabels(): array
    {
        return [
            self::STAGE_VERIFIKASI_DP => '1. Verifikasi DP',
            self::STAGE_PERSIAPAN_BAHAN => '2. Persiapan Bahan Baku',
            self::STAGE_PROSES_PRODUKSI => '3. Proses Produksi / Assembly',
            self::STAGE_QUALITY_CONTROL => '4. Quality Control (QC)',
            self::STAGE_MENUNGGU_PELUNASAN => '5. Menunggu Pelunasan',
            self::STAGE_PENGIRIMAN_INSTALASI => '6. Pengiriman & Instalasi',
            self::STAGE_AFTER_SALES => '7. After-Sales Service (Layanan Purna Jual)',
        ];
    }

    public function statusLabel(): string
    {
        return self::statusLabels()[$this->status]
            ?? ucfirst(str_replace('_', ' ', (string) $this->status));
    }

    public function productionStageLabel(): string
    {
        return self::productionStageLabels()[$this->production_status]
            ?? ucfirst(str_replace('_', ' ', (string) $this->production_status));
    }

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        return $query->when(filled($status), fn (Builder $sub) => $sub->where('status', $status));
    }

    public function pendingPayment(): ?Payment
    {
        return $this->payments()
            ->where('status', Payment::STATUS_MENUNGGU)
            ->latest('id')
            ->first();
    }

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
            'amount' => $amount ?? (float) ($this->dp_amount > 0 ? $this->dp_amount : $this->total_amount),
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
