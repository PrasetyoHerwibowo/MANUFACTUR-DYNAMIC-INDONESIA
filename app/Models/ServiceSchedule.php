<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ServiceSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'is_available',
        'technician_standby_count',
        'max_quota',
        'notes',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'is_available' => 'boolean',
        'technician_standby_count' => 'integer',
        'max_quota' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('api_available_service_dates');
        });

        static::deleted(function () {
            Cache::forget('api_available_service_dates');
        });
    }

    /**
     * Ambil kalender ketersediaan servis untuk rentang hari ke depan.
     * Menggabungkan data pengaturan admin dan kuota booking terisi.
     */
    public static function getUpcomingSchedule(int $days = 21): Collection
    {
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays($days);

        // Ambil custom schedule admin yang sudah tersimpan
        $savedSchedules = self::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy(fn ($item) => Carbon::parse($item->date)->format('Y-m-d'));

        // Hitung booking aktif per tanggal
        $bookedCounts = ServiceBooking::whereBetween('scheduled_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->whereNotIn('status', [ServiceBooking::STATUS_DIBATALKAN])
            ->selectRaw('DATE(scheduled_date) as booking_date, COUNT(*) as count')
            ->groupBy('booking_date')
            ->pluck('count', 'booking_date');

        $period = CarbonPeriod::create($startDate, $endDate);
        $result = collect();

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $saved = $savedSchedules->get($dateStr);

            // Kuota maksimal 3 servis per hari sesuai aturan pabrik
            $isSunday = $date->isSunday();
            $defaultAvailable = ! $isSunday;
            $defaultTechnicians = $isSunday ? 0 : 2;
            $defaultQuota = $isSunday ? 0 : 3;

            $isAvailable = $saved ? (bool) $saved->is_available : $defaultAvailable;
            $technicianCount = $saved ? (int) $saved->technician_standby_count : $defaultTechnicians;
            $maxQuota = $saved && $saved->max_quota ? min(3, (int) $saved->max_quota) : $defaultQuota;
            $notes = $saved?->notes;

            $booked = (int) ($bookedCounts[$dateStr] ?? 0);
            $remaining = max(0, $maxQuota - $booked);
            $isFull = $booked >= $maxQuota || $remaining <= 0;

            // Hari dinyatakan dapat dipilih customer jika:
            // 1. Admin menandai buka (is_available = true)
            // 2. Ada teknisi standby (technicianCount > 0)
            // 3. Masih ada slot kuota kosong (remaining > 0 dan tidak penuh)
            $canBook = $isAvailable && ($technicianCount > 0) && ! $isFull;

            $result->push([
                'date' => $dateStr,
                'day_name' => $date->locale('id')->isoFormat('dddd'),
                'formatted_date' => $date->locale('id')->isoFormat('D MMMM Y'),
                'short_date' => $date->format('d M'),
                'is_open' => $isAvailable,
                'technician_standby_count' => $technicianCount,
                'max_quota' => $maxQuota,
                'booked_count' => $booked,
                'remaining_quota' => $remaining,
                'is_full' => $isFull,
                'can_book' => $canBook,
                'notes' => ! $isAvailable ? ($notes ?: 'Tutup / Libur') : ($isFull ? 'Penuh' : ($notes ?: "Tersedia {$remaining} dari {$maxQuota} slot")),
            ]);
        }

        return $result;
    }
}

