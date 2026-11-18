<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class WarrantyClaim extends Model
{
    use HasFactory;

    public const STATUS_DALAM_PENINJAUAN = 'dalam_peninjauan';
    public const STATUS_DISETUJUI = 'disetujui';
    public const STATUS_KUNJUNGAN_DIJADWALKAN = 'kunjungan_dijadwalkan';
    public const STATUS_TEKNISI_OTW = 'teknisi_otw';
    public const STATUS_DALAM_PERBAIKAN = 'dalam_perbaikan';
    public const STATUS_DITOLAK = 'ditolak';
    public const STATUS_SELESAI = 'selesai';

    public const VERIFIKASI_MENUNGGU = 'menunggu_verifikasi';
    public const VERIFIKASI_VALID = 'valid';
    public const VERIFIKASI_TIDAK_VALID = 'tidak_valid';

    protected $fillable = [
        'user_id',
        'customer_id',
        'order_id',
        'claim_code',
        'serial_number',
        'machine_model',
        'purchase_date',
        'damage_description',
        'photo_path',
        'file_type',
        'status',
        'verification_status',
        'warranty_status',
        'scheduled_visit_date',
        'scheduled_visit_time',
        'visit_address',
        'technician_name',
        'technician_phone',
        'response_notes',
    ];

    protected $casts = [
        'scheduled_visit_date' => 'date',
    ];

    protected $attributes = [
        'status' => self::STATUS_DALAM_PENINJAUAN,
        'verification_status' => self::VERIFIKASI_MENUNGGU,
        'warranty_status' => 'dalam_garansi',
    ];

    protected static function booted(): void
    {
        static::saved(function (WarrantyClaim $claim) {
            Cache::forget('admin_pending_warranties_count');
            Cache::forget('admin_warranties_stats');
            if ($claim->user_id) {
                Cache::forget("api_warranty_claims_user_{$claim->user_id}");
            }
        });

        static::deleted(function (WarrantyClaim $claim) {
            Cache::forget('admin_pending_warranties_count');
            Cache::forget('admin_warranties_stats');
            if ($claim->user_id) {
                Cache::forget("api_warranty_claims_user_{$claim->user_id}");
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function isVideo(): bool
    {
        if ($this->file_type === 'video') {
            return true;
        }

        $path = strtolower((string) $this->photo_path);
        return Str::endsWith($path, ['.mp4', '.mov', '.webm', '.avi']);
    }

    public function mediaUrl(): ?string
    {
        if (empty($this->photo_path)) {
            return null;
        }

        $path = $this->photo_path;
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (file_exists(public_path('uploads/warranty/' . basename($path)))) {
            return asset('uploads/warranty/' . basename($path));
        }

        if (file_exists(public_path($path))) {
            return asset($path);
        }

        return app(\App\Services\SupabaseStorageService::class)->getPublicUrl('warranty/' . basename($path));
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_DALAM_PENINJAUAN => 'Menunggu Verifikasi Bukti',
            self::STATUS_DISETUJUI => 'Bukti Valid & Disetujui',
            self::STATUS_KUNJUNGAN_DIJADWALKAN => 'Kunjungan Teknisi Dijadwalkan',
            self::STATUS_TEKNISI_OTW => 'Teknisi Sedang Menuju Lokasi',
            self::STATUS_DALAM_PERBAIKAN => 'Dalam Proses Perbaikan',
            self::STATUS_DITOLAK => 'Klaim Tidak Valid / Ditolak',
            self::STATUS_SELESAI => 'Perbaikan Selesai',
        ];
    }

    public function statusLabel(): string
    {
        return self::statusLabels()[$this->status]
            ?? ucfirst(str_replace('_', ' ', (string) $this->status));
    }
}
