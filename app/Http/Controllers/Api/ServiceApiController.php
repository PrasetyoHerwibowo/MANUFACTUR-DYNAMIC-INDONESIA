<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceBooking;
use App\Models\WarrantyClaim;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceApiController extends Controller
{
    /**
     * GET /api/services/bookings
     */
    public function listBookings(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = ServiceBooking::latest('id');

        if ($user) {
            $query->where('user_id', $user->id);
        }

        $bookings = $query->get()->map(function ($b) {
            return [
                'id' => (string) $b->id,
                'ticket_number' => $b->booking_code,
                'company_name' => $b->company_name ?? 'PT Kopi Nusantara Jaya',
                'serial_number' => $b->serial_number,
                'machine_model' => $b->machine_model,
                'service_type' => $b->service_type,
                'preferred_date' => $b->scheduled_date?->format('d M Y') ?? '-',
                'preferred_time' => $b->scheduled_time,
                'address' => $b->address,
                'issue_description' => $b->issue_description,
                'status' => $b->status,
                'technician_name' => $b->technician_name ?? 'Teknisi Senior Pabrik',
                'technician_phone' => $b->technician_phone ?? '0812-3344-5566',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $bookings,
        ]);
    }

    /**
     * POST /api/services/bookings
     * Form Jadwal Teknisi Datang (Booking Maintenance/Servis)
     */
    public function bookingService(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'serial_number' => ['required', 'string'],
            'machine_model' => ['required', 'string'],
            'service_type' => ['nullable', 'string'],
            'scheduled_date' => ['required', 'string'],
            'scheduled_time' => ['nullable', 'string'],
            'address' => ['required', 'string'],
            'issue_description' => ['nullable', 'string'],
            'company_name' => ['nullable', 'string'],
        ]);

        $user = $request->user();
        $bookingCode = 'SRV-2026-0' . rand(100, 999);

        $booking = ServiceBooking::create([
            'user_id' => $user?->id,
            'booking_code' => $bookingCode,
            'company_name' => $validated['company_name'] ?? 'PT Kopi Nusantara Jaya',
            'serial_number' => $validated['serial_number'],
            'machine_model' => $validated['machine_model'],
            'service_type' => $validated['service_type'] ?? 'maintenance_rutin',
            'scheduled_date' => now()->addDays(2),
            'scheduled_time' => $validated['scheduled_time'] ?? '10:00 WIB',
            'address' => $validated['address'],
            'issue_description' => $validated['issue_description'] ?? 'Pengecekan rutin dan kalibrasi unit',
            'status' => 'dijadwalkan',
            'technician_name' => 'Teknisi Slamet R.',
            'technician_phone' => '0813-1122-3344',
        ]);

        return response()->json([
            'success' => true,
            'message' => "Jadwal kunjungan teknisi untuk unit {$booking->serial_number} berhasil dikonfirmasi.",
            'data' => [
                'id' => (string) $booking->id,
                'ticket_number' => $booking->booking_code,
                'company_name' => $booking->company_name,
                'serial_number' => $booking->serial_number,
                'machine_model' => $booking->machine_model,
                'preferred_date' => $booking->scheduled_date->format('d M Y'),
                'preferred_time' => $booking->scheduled_time,
                'address' => $booking->address,
                'issue_description' => $booking->issue_description,
                'status' => $booking->status,
                'technician_name' => $booking->technician_name,
                'technician_phone' => $booking->technician_phone,
            ],
        ], 201);
    }

    /**
     * GET /api/services/warranty-claims
     */
    public function listWarrantyClaims(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = WarrantyClaim::latest('id');

        if ($user) {
            $query->where('user_id', $user->id);
        }

        $claims = $query->get()->map(function ($c) {
            return [
                'id' => (string) $c->id,
                'claim_number' => $c->claim_code,
                'serial_number' => $c->serial_number,
                'purchase_date' => $c->purchase_date ?? '15 Jan 2026',
                'damage_description' => $c->damage_description,
                'photo_path' => $c->photo_path,
                'status' => $c->status,
                'response_notes' => $c->response_notes,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $claims,
        ]);
    }

    /**
     * POST /api/services/warranty-claims
     * Form Klaim Garansi + Foto Kerusakan + Serial Number
     */
    public function submitWarrantyClaim(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'serial_number' => ['required', 'string'],
            'purchase_date' => ['nullable', 'string'],
            'damage_description' => ['required', 'string'],
            'photo' => ['nullable', 'string'],
        ]);

        $user = $request->user();
        $claimCode = 'CLM-2026-0' . rand(100, 999);

        $claim = WarrantyClaim::create([
            'user_id' => $user?->id,
            'claim_code' => $claimCode,
            'serial_number' => $validated['serial_number'],
            'purchase_date' => $validated['purchase_date'] ?? '15 Jan 2026',
            'damage_description' => $validated['damage_description'],
            'photo_path' => $validated['photo'] ?? 'bukti_kerusakan_mesin.jpg',
            'status' => 'Dalam Peninjauan Tim Engineering',
            'response_notes' => 'Permintaan klaim garansi Anda sedang diverifikasi dengan database serial number pabrik.',
        ]);

        return response()->json([
            'success' => true,
            'message' => "Pengajuan klaim garansi untuk no seri {$claim->serial_number} berhasil dikirim.",
            'data' => [
                'id' => (string) $claim->id,
                'claim_number' => $claim->claim_code,
                'serial_number' => $claim->serial_number,
                'purchase_date' => $claim->purchase_date,
                'damage_description' => $claim->damage_description,
                'photo_path' => $claim->photo_path,
                'status' => $claim->status,
            ],
        ], 201);
    }
}
