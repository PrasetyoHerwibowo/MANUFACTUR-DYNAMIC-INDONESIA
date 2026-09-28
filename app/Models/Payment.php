<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Pembayaran sebuah pesanan.
 *
 * Setiap percobaan pembayaran memiliki kode pembayaran sendiri. Pembayaran
 * yang masih "menunggu_pembayaran" dan melewati batas waktu (expires_at)
 * otomatis menjadi "gagal" sehingga pelanggan harus mengulang pembayaran
 * dari awal dengan kode pembayaran baru.
 */
class Payment extends Model
{
    public const STATUS_MENUNGGU = 'menunggu_pembayaran';

    public const STATUS_LUNAS = 'lunas';

    public const STATUS_GAGAL = 'gagal';

    public const METHOD_TRANSFER_BANK = 'transfer_bank';

    public const METHOD_VIRTUAL_ACCOUNT = 'virtual_account';

    public const METHOD_QRIS = 'qris';

    public const METHOD_TUNAI = 'tunai';

    protected $fillable = [
        'order_id',
        'payment_code',
        'attempt',
        'method',
        'amount',
        'status',
        'expires_at',
        'paid_at',
        'failed_at',
        'failure_reason',
        'proof',
        'notes',
    ];

    protected $casts = [
        'attempt' => 'integer',
        'amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'paid_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    /**
     * Nilai default saat pembayaran baru dibuat.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => self::STATUS_MENUNGGU,
        'attempt' => 1,
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return array<string, string>
     */
    public static function statusLabels(): array
    {
        return [
            self::STATUS_MENUNGGU => 'Menunggu Pembayaran',
            self::STATUS_LUNAS => 'Lunas',
            self::STATUS_GAGAL => 'Gagal',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function methodLabels(): array
    {
        return [
            self::METHOD_TRANSFER_BANK => 'Transfer Bank',
            self::METHOD_VIRTUAL_ACCOUNT => 'Virtual Account',
            self::METHOD_QRIS => 'QRIS',
            self::METHOD_TUNAI => 'Tunai / Cash',
        ];
    }

    public function statusLabel(): string
    {
        return self::statusLabels()[$this->status]
            ?? ucfirst(str_replace('_', ' ', (string) $this->status));
    }

    public function methodLabel(): string
    {
        return self::methodLabels()[$this->method]
            ?? ucfirst(str_replace('_', ' ', (string) $this->method));
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_MENUNGGU);
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->pending()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now());
    }

    /**
     * Pembayaran menunggu yang sudah melewati batas pembayaran.
     */
    public function isExpired(): bool
    {
        return $this->status === self::STATUS_MENUNGGU
            && $this->expires_at !== null
            && $this->expires_at->isPast();
    }

    /**
     * Sisa waktu pembayaran sebagai teks (null bila tidak ada batas waktu).
     */
    public function remainingTime(): ?string
    {
        if ($this->status !== self::STATUS_MENUNGGU || ! $this->expires_at) {
            return null;
        }

        return $this->expires_at->diffForHumans(now(), [
            'parts' => 2,
            'short' => true,
            'syntax' => CarbonInterface::DIFF_ABSOLUTE,
        ]);
    }

    /**
     * Tandai pembayaran lunas dan selaraskan status pesanan.
     */
    public function markAsPaid(?string $proof = null, ?string $note = null): void
    {
        $this->forceFill([
            'status' => self::STATUS_LUNAS,
            'paid_at' => now(),
            'failed_at' => null,
            'failure_reason' => null,
            'proof' => filled($proof) ? $proof : $this->proof,
            'notes' => filled($note) ? $note : $this->notes,
        ])->save();

        // Sisa pembayaran lain pada pesanan yang sama tidak diperlukan lagi.
        $this->order?->payments()
            ->whereKeyNot($this->getKey())
            ->pending()
            ->update([
                'status' => self::STATUS_GAGAL,
                'failed_at' => now(),
                'failure_reason' => 'Digantikan pembayaran '.$this->payment_code.' yang sudah lunas',
            ]);

        $this->order?->syncStatusFromPayments();
    }

    /**
     * Tandai pembayaran gagal (mis. melewati batas pembayaran).
     */
    public function markAsFailed(string $reason): void
    {
        if ($this->status !== self::STATUS_MENUNGGU) {
            return;
        }

        $this->forceFill([
            'status' => self::STATUS_GAGAL,
            'failed_at' => now(),
            'failure_reason' => $reason,
        ])->save();

        $this->order?->syncStatusFromPayments();
    }

    /**
     * Tandai semua pembayaran yang melewati batas pembayaran sebagai gagal.
     *
     * @return int jumlah pembayaran yang diubah statusnya
     */
    public static function expireOverdue(): int
    {
        $total = 0;

        static::query()
            ->expired()
            ->with('order')
            ->get()
            ->each(function (Payment $payment) use (&$total): void {
                $payment->markAsFailed('Melewati batas waktu pembayaran');
                $total++;
            });

        return $total;
    }
}
