<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\WarrantyClaim;
use App\Support\MediaUploader;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class WarrantyClaimApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $customer = Customer::where('email', $user->email)->first();
            $claims = WarrantyClaim::where(function ($query) use ($user, $customer) {
                $query->where('user_id', $user->id);
                if ($customer) {
                    $query->orWhere('customer_id', $customer->id);
                }
            })->latest('id')->take(50)->get()->map(fn (WarrantyClaim $claim) => [
                'id' => (string) $claim->id,
                'order_id' => $claim->order_id ? (string) $claim->order_id : null,
                'claim_number' => $claim->claim_code,
                'serial_number' => $claim->serial_number,
                'machine_model' => $claim->machine_model ?? 'Mesin Industri MDI',
                'purchase_date' => $claim->purchase_date ?? '-',
                'damage_description' => $claim->damage_description,
                'photo_path' => $claim->mediaUrl(),
                'file_type' => $claim->file_type,
                'status' => $claim->status,
                'status_label' => $claim->statusLabel(),
                'verification_status' => $claim->verification_status,
                'warranty_status' => $claim->warranty_status,
                'scheduled_visit_date' => $claim->scheduled_visit_date?->format('d M Y'),
                'scheduled_visit_time' => $claim->scheduled_visit_time,
                'visit_address' => $claim->visit_address,
                'technician_name' => $claim->technician_name,
                'technician_phone' => $claim->technician_phone,
                'response_notes' => $claim->response_notes,
                'created_at' => $claim->created_at->format('d M Y H:i'),
            ])->values();

            return response()->json(['success' => true, 'data' => $claims]);
        } catch (Throwable $e) {
            Log::error('WarrantyClaimApiController@index error: ' . $e->getMessage(), ['user_id' => $request->user()?->id]);
            return response()->json(['success' => false, 'message' => 'Gagal memuat klaim garansi.'], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => ['nullable', 'max:100'],
            'serial_number' => ['required', 'string', 'max:100'],
            'machine_model' => ['nullable', 'string', 'max:150'],
            'purchase_date' => ['required', 'date'],
            'damage_description' => ['required', 'string', 'max:2000'],
            'photo' => $request->hasFile('photo')
                ? ['nullable', 'file', 'mimes:jpg,jpeg,png,mp4', 'max:25600']
                : ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $photo = $request->file('photo');
        if ($request->hasFile('photo') && (!$photo || !$photo->isValid())) {
            return response()->json(['success' => false, 'message' => 'Unggahan bukti gagal diterima. Silakan unggah ulang.'], 422);
        }

        try {
            $user = $request->user();
            $customer = Customer::where('email', $user->email)->first();
            if (!$customer) {
                return response()->json(['success' => false, 'message' => 'Data customer untuk akun ini tidak ditemukan.'], 422);
            }

            $query = Order::where('customer_id', $customer->id);
            if (filled($validated['order_id'] ?? null)) {
                is_numeric($validated['order_id'])
                    ? $query->whereKey((int) $validated['order_id'])
                    : $query->where('order_code', $validated['order_id']);
            } else {
                $query->where('serial_number', $validated['serial_number']);
            }
            $order = $query->first();
            if (!$order || !hash_equals((string) $order->serial_number, $validated['serial_number'])) {
                return response()->json(['success' => false, 'message' => 'Pesanan atau nomor seri tidak cocok dengan akun customer.'], 422);
            }
            if (!$order->isWarrantyActive()) {
                return response()->json(['success' => false, 'message' => 'Masa garansi pesanan sudah berakhir.'], 422);
            }
            if (WarrantyClaim::where('order_id', $order->id)->whereIn('status', [
                WarrantyClaim::STATUS_DALAM_PENINJAUAN,
                WarrantyClaim::STATUS_DISETUJUI,
                WarrantyClaim::STATUS_KUNJUNGAN_DIJADWALKAN,
                WarrantyClaim::STATUS_TEKNISI_OTW,
                WarrantyClaim::STATUS_DALAM_PERBAIKAN,
            ])->exists()) {
                return response()->json(['success' => false, 'message' => 'Pesanan ini sudah memiliki klaim yang masih diproses.'], 422);
            }

            $media = $photo ? MediaUploader::store($photo, 'warranty') : null;
            if ($photo && !$media) {
                return response()->json(['success' => false, 'message' => 'Bukti gagal disimpan. Silakan unggah ulang.'], 422);
            }

            do {
                $claimCode = 'CLM-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            } while (WarrantyClaim::where('claim_code', $claimCode)->exists());

            $machineModel = $validated['machine_model'] ?? $order->items->first()?->name ?? 'Mesin Industri MDI';
            $claim = WarrantyClaim::create([
                'user_id' => $user->id,
                'customer_id' => $customer->id,
                'order_id' => $order->id,
                'claim_code' => $claimCode,
                'serial_number' => $validated['serial_number'],
                'machine_model' => $machineModel,
                'purchase_date' => $validated['purchase_date'],
                'damage_description' => $validated['damage_description'],
                'photo_path' => $media['path'] ?? null,
                'file_type' => $media['file_type'] ?? null,
                'status' => WarrantyClaim::STATUS_DALAM_PENINJAUAN,
                'verification_status' => WarrantyClaim::VERIFIKASI_MENUNGGU,
                'warranty_status' => 'dalam_garansi',
                'visit_address' => $validated['address'] ?? $order->delivery_address ?? $customer->address,
                'response_notes' => 'Klaim berhasil diterima dan sedang menunggu verifikasi Tim Engineering.',
            ]);

            Cache::forget("api_warranty_claims_user_{$user->id}");
            Cache::forget('admin_pending_warranties_count');
            Cache::forget('admin_warranties_stats');

            return response()->json([
                'success' => true,
                'message' => "Pengajuan klaim garansi untuk nomor seri {$claim->serial_number} berhasil dikirim.",
                'data' => [
                    'id' => (string) $claim->id,
                    'order_id' => (string) $claim->order_id,
                    'claim_number' => $claim->claim_code,
                    'serial_number' => $claim->serial_number,
                    'machine_model' => $claim->machine_model,
                    'purchase_date' => $claim->purchase_date,
                    'damage_description' => $claim->damage_description,
                    'photo_path' => $claim->mediaUrl(),
                    'file_type' => $claim->file_type,
                    'status' => $claim->status,
                    'status_label' => $claim->statusLabel(),
                    'verification_status' => $claim->verification_status,
                    'warranty_status' => $claim->warranty_status,
                    'created_at' => $claim->created_at->format('d M Y H:i'),
                ],
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (Throwable $e) {
            Log::error('WarrantyClaimApiController@store error: ' . $e->getMessage(), ['user_id' => $request->user()?->id]);
            return response()->json(['success' => false, 'message' => 'Gagal memproses klaim garansi. Silakan coba kembali.'], 500);
        }
    }
}
