<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_id',
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
    ];

    protected $casts = [
        'scheduled_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
