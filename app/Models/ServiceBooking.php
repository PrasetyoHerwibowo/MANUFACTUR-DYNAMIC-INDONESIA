<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ServiceBooking extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_MENUNGGU = 'menunggu_verifikasi';
    public const STATUS_DIJADWALKAN = 'dijadwalkan';
    public const STATUS_TEKNISI_OTW = 'teknisi_otw';
    public const STATUS_DALAM_PENGERJAAN = 'dalam_pengerjaan';
    public const STATUS_SELESAI = 'selesai';
    public const STATUS_DIBATALKAN = 'dibatalkan';

    protected $fillable = [
        'user_id',
        'customer_id',
        'order_id',
        'booking_code',
        'company_name',
        'serial_number',
        'machine_model',
        'service_type',
        'scheduled_date',
        'scheduled_time',
        'address',
        'issue_description',
        'status',
        'technician_name',
        'technician_phone',
        'admin_notes',
        'photo_path',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
    ];

    protected $attributes = [
        'status' => self::STATUS_MENUNGGU,
    ];

    protected static function booted(): void
    {
        static::saved(function (ServiceBooking $booking) {
            Cache::forget('admin_pending_services_count');
            Cache::forget('admin_services_stats');
            if ($booking->user_id) {
                Cache::forget("api_service_bookings_user_{$booking->user_id}");
            }
        });

        static::deleted(function (ServiceBooking $booking) {
            Cache::forget('admin_pending_services_count');
            Cache::forget('admin_services_stats');
            if ($booking->user_id) {
                Cache::forget("api_service_bookings_user_{$booking->user_id}");
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

    public static function statusLabels(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_MENUNGGU => 'Pending',
            self::STATUS_DIJADWALKAN => 'Confirmed',
            self::STATUS_TEKNISI_OTW => 'Teknisi OTW',
            self::STATUS_DALAM_PENGERJAAN => 'Dalam Pengerjaan',
            self::STATUS_SELESAI => 'Selesai',
            self::STATUS_DIBATALKAN => 'Dibatalkan',
        ];
    }

    public function isPending(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_MENUNGGU], true);
    }

    public function isConfirmed(): bool
    {
        return in_array($this->status, [self::STATUS_CONFIRMED, self::STATUS_DIJADWALKAN], true);
    }

    public function statusLabel(): string
    {
        return self::statusLabels()[$this->status]
            ?? ucfirst(str_replace('_', ' ', (string) $this->status));
    }

    public static function serviceTypeLabels(): array
    {
        return [
            'maintenance_rutin' => 'Maintenance Rutin (6 Bulan)',
            'kalibrasi' => 'Kalibrasi & Descaling Boiler',
            'repair' => 'Perbaikan / Repair Mesin',
            'troubleshooting' => 'Troubleshooting Mesin & Kelistrikan',
            'emergency' => 'Emergency Service 24/7',
        ];
    }

    public function serviceTypeLabel(): string
    {
        return self::serviceTypeLabels()[$this->service_type]
            ?? ucfirst(str_replace('_', ' ', (string) $this->service_type));
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

        if (file_exists(public_path('uploads/services/' . basename($path)))) {
            return asset('uploads/services/' . basename($path));
        }

        if (file_exists(public_path($path))) {
            return asset($path);
        }

        return app(\App\Services\SupabaseStorageService::class)->getPublicUrl('services/' . basename($path));
    }
}
