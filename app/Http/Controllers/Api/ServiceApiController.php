<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\ServiceBooking;
use App\Models\ServiceSchedule;
use App\Services\SupabaseStorageService;
use App\Support\MediaUploader;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ServiceApiController extends Controller
{
    /**
     * GET /api/services/available-dates
     * Kalender tanggal ketersediaan jadwal servis, status teknisi standby, dan sisa kuota.
     */
    public function availableDates(Request $request): JsonResponse
    {
        try {
            $days = (int) $request->input('days', 21);
            if ($days < 7) {
                $days = 7;
            } elseif ($days > 60) {
                $days = 60;
            }

            $cacheKey = "api_available_service_dates_{$days}";
            $schedules = Cache::remember($cacheKey, 30, function () use ($days) {
                return ServiceSchedule::getUpcomingSchedule($days);
            });

            return response()->json([
                'success' => true,
                'data' => $schedules,
            ]);
        } catch (Throwable $e) {
            Log::error('ServiceApiController@availableDates error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat ketersediaan jadwal servis: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Resolusi Pelanggan B2B dari Sesi Login Sanctum atau Parameter Permintaan
     */
    private function resolveCustomer(Request $request): ?Customer
    {
        $user = auth('sanctum')->user() ?? $request->user();
        if ($user && filled($user->email)) {
            $customer = Customer::where('email', $user->email)->first();
            if ($customer) {
                return $customer;
            }
        }

        $email = $request->input('email') ?? $request->query('email');
        if (filled($email)) {
            $customer = Customer::where('email', trim($email))->first();
            if ($customer) {
                return $customer;
            }
        }

        $phone = $request->input('phone') ?? $request->input('pic_phone') ?? $request->query('phone');
        if (filled($phone)) {
            $customer = Customer::where('phone', trim($phone))->first();
            if ($customer) {
                return $customer;
            }
        }

        $company = $request->input('company_name') ?? $request->input('company') ?? $request->query('company');
        if (filled($company)) {
            $customer = Customer::where('company', 'ilike', '%' . trim($company) . '%')->first();
            if ($customer) {
                return $customer;
            }
        }

        return null;
    }

    /**
     * GET /api/services/my-machines
     * Ambil daftar mesin yang dimiliki / dipesan oleh customer.
     */
    public function myMachines(Request $request): JsonResponse
    {
        try {
            $customer = $this->resolveCustomer($request);

            $query = Order::with(['items.machine.category', 'customer']);
            if ($customer) {
                $query->where('customer_id', $customer->id);
            }

            $machines = $query->latest('id')
                ->take(10)
                ->get()
                ->map(function (Order $order) {
                    $firstItem = $order->items->first();
                    $model = $firstItem?->machine?->name ?: ($firstItem?->name ?: 'Mesin Industri Manufaktur');
                    $sn = $order->serial_number ?: ('SN-' . $order->order_code);

                    return [
                        'order_id' => (string) $order->id,
                        'order_code' => $order->order_code,
                        'model' => $model,
                        'serial_number' => $sn,
                        'model_code' => $firstItem?->machine?->model_code ?: 'MDI-UNIT',
                        'category' => $firstItem?->machine?->category?->name ?? 'Mesin Industri',
                        'delivery_address' => $order->delivery_address ?? '',
                        'is_warranty_active' => $order->isWarrantyActive(),
                        'warranty_expiry_date' => $order->warranty_expiry_date ? $order->warranty_expiry_date->format('d M Y') : $order->created_at->addMonths(24)->format('d M Y'),
                        'production_status' => $order->production_status,
                        'purchase_date' => $order->created_at->format('Y-m-d'),
                    ];
                })
                ->values()
                ->toArray();

            return response()->json([
                'success' => true,
                'data' => $machines,
            ]);
        } catch (Throwable $e) {
            Log::error('ServiceApiController@myMachines error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat daftar mesin: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/services/bookings
     * Ambil daftar riwayat booking servis dari user/customer yang sedang login.
     */
    public function listBookings(Request $request): JsonResponse
    {
        try {
            $user = auth('sanctum')->user() ?? $request->user();
            $customer = $this->resolveCustomer($request);

            $bookings = ServiceBooking::when($user || $customer, function ($q) use ($user, $customer) {
                $q->where(function ($sub) use ($user, $customer) {
                    if ($user) $sub->where('user_id', $user->id);
                    if ($customer) $sub->orWhere('customer_id', $customer->id);
                });
            })
            ->latest('id')
            ->take(15)
            ->get()
            ->map(function (ServiceBooking $b) {
                return [
                    'id' => (string) $b->id,
                    'order_id' => $b->order_id ? (string) $b->order_id : null,
                    'ticket_number' => $b->booking_code,
                    'company_name' => $b->company_name ?? 'PT Kopi Nusantara Jaya',
                    'serial_number' => $b->serial_number,
                    'machine_model' => $b->machine_model,
                    'service_type' => $b->service_type,
                    'service_type_label' => $b->serviceTypeLabel(),
                    'preferred_date' => $b->scheduled_date ? $b->scheduled_date->format('d M Y') : '-',
                    'preferred_time' => $b->scheduled_time ?? '-',
                    'address' => $b->address,
                    'issue_description' => $b->issue_description,
                    'status' => $b->isPending() ? 'pending' : ($b->isConfirmed() ? 'confirmed' : $b->status),
                    'status_label' => $b->isPending() ? 'Pending' : ($b->isConfirmed() ? 'Confirmed' : $b->statusLabel()),
                    'technician_name' => $b->technician_name,
                    'technician_phone' => $b->technician_phone,
                    'is_technician_assigned' => !empty($b->technician_name),
                    'admin_notes' => $b->admin_notes,
                    'photo_url' => $b->mediaUrl() ?? $b->photo_path,
                    'has_photo' => !empty($b->photo_path),
                    'created_at' => $b->created_at->format('d M Y H:i'),
                ];
            })
            ->toArray();

            return response()->json([
                'success' => true,
                'data' => $bookings,
            ]);
        } catch (Throwable $e) {
            Log::error('ServiceApiController@listBookings error: ' . $e->getMessage(), [
                'user_id' => $request->user()?->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat daftar servis: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/services/bookings
     * Form Jadwal Teknisi Datang (Booking Maintenance / Servis Mesin)
     * Otomatis terkonfirmasi/dijadwalkan tanpa verifikasi manual.
     */
    public function bookingService(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => ['nullable'],
            'serial_number' => ['required', 'string', 'max:100'],
            'machine_model' => ['required', 'string', 'max:150'],
            'service_type' => ['nullable', 'string', 'max:50'],
            'scheduled_date' => ['required', 'string'],
            'scheduled_time' => ['nullable', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:500'],
            'issue_description' => ['nullable', 'string', 'max:1000'],
            'company_name' => ['nullable', 'string', 'max:150'],
            'photo' => ['nullable', 'string'],
            'photo_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        try {
            $user = auth('sanctum')->user() ?? $request->user();
            $customer = $this->resolveCustomer($request);

            // Parse dan normalisasi tanggal kunjungan yang diajukan customer
            try {
                $parsedDate = \Carbon\Carbon::parse($validated['scheduled_date']);
            } catch (Throwable) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format tanggal kunjungan tidak valid.',
                ], 422);
            }

            // Validasi kuota harian: Maksimal 3 servis per hari
            $bookedOnDate = ServiceBooking::whereDate('scheduled_date', $parsedDate)
                ->whereNotIn('status', [ServiceBooking::STATUS_DIBATALKAN])
                ->count();

            if ($bookedOnDate >= 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kuota booking servis pada tanggal ' . $parsedDate->locale('id')->isoFormat('D MMMM Y') . ' sudah penuh (maksimal 3 servis per hari). Silakan pilih tanggal lain yang masih tersedia.',
                ], 422);
            }

            DB::beginTransaction();

            // Generate kode tiket unik: SRV-YYYYMMDD-XXXXXX
            do {
                $bookingCode = 'SRV-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            } while (ServiceBooking::where('booking_code', $bookingCode)->exists());

            // Hubungkan dengan order jika diberikan
            $orderId = null;
            if (!empty($validated['order_id'])) {
                $order = Order::where('id', is_numeric($validated['order_id']) ? (int) $validated['order_id'] : 0)
                    ->orWhere('order_code', $validated['order_id'])
                    ->first();
                $orderId = $order?->id;
            }

            // Upload foto bukti kerusakan mesin langsung ke Supabase Storage (opsional, tanpa beban disk server)
            $photoUrl = $this->resolveAndStoreServicePhoto(
                $request->input('photo'),
                $request->file('photo_file'),
                $bookingCode
            );

            // Booking awal berstatus Pending (Admin pabrik akan mengonfirmasi jadwal & menugaskan teknisi)
            $booking = ServiceBooking::create([
                'user_id' => $user?->id,
                'customer_id' => $customer?->id,
                'order_id' => $orderId,
                'booking_code' => $bookingCode,
                'company_name' => $validated['company_name'] ?? ($customer?->company ?? ($user?->company ?? 'PT Kopi Nusantara Jaya')),
                'serial_number' => $validated['serial_number'],
                'machine_model' => $validated['machine_model'],
                'service_type' => $validated['service_type'] ?? 'maintenance_rutin',
                'scheduled_date' => $parsedDate,
                'scheduled_time' => !empty($validated['scheduled_time']) ? $validated['scheduled_time'] : 'Fleksibel',
                'address' => $validated['address'],
                'issue_description' => $validated['issue_description'] ?? 'Pengecekan rutin dan servis unit mesin',
                'photo_path' => $photoUrl,
                'status' => ServiceBooking::STATUS_PENDING,
                'technician_name' => null,
                'technician_phone' => null,
            ]);

            DB::commit();

            // Bersihkan cache
            if ($user) {
                Cache::forget("api_service_bookings_user_{$user->id}");
            }
            Cache::forget('api_available_service_dates_21');
            Cache::forget('api_available_service_dates_30');
            Cache::forget('admin_pending_services_count');
            Cache::forget('admin_services_stats');

            return response()->json([
                'success' => true,
                'message' => "Pengajuan booking servis berhasil dikirim! Status saat ini Pending menunggu konfirmasi admin pabrik.",
                'data' => [
                    'id' => (string) $booking->id,
                    'order_id' => $booking->order_id ? (string) $booking->order_id : null,
                    'ticket_number' => $booking->booking_code,
                    'company_name' => $booking->company_name,
                    'serial_number' => $booking->serial_number,
                    'machine_model' => $booking->machine_model,
                    'service_type' => $booking->service_type,
                    'service_type_label' => $booking->serviceTypeLabel(),
                    'preferred_date' => $booking->scheduled_date->format('d M Y'),
                    'preferred_time' => '-',
                    'address' => $booking->address,
                    'issue_description' => $booking->issue_description,
                    'photo_url' => $booking->mediaUrl() ?? $booking->photo_path,
                    'has_photo' => !empty($booking->photo_path),
                    'status' => 'pending',
                    'status_label' => 'Pending',
                    'status_label' => $booking->statusLabel(),
                    'technician_name' => $booking->technician_name,
                    'technician_phone' => $booking->technician_phone,
                    'created_at' => $booking->created_at->format('d M Y H:i'),
                ],
            ], 201);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('ServiceApiController@bookingService error: ' . $e->getMessage(), [
                'user_id' => $request->user()?->id,
                'data' => $validated,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat booking servis: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Simpan foto bukti kerusakan mesin langsung ke Supabase Storage (tanpa beban server lokal).
     * Menerima Base64 string atau UploadedFile (JPG, JPEG, PNG saja, tolak SVG dan Video, maks 5 MB).
     */
    private function resolveAndStoreServicePhoto(?string $photoString, ?UploadedFile $file, string $bookingCode): ?string
    {
        $storage = app(SupabaseStorageService::class);
        $maxSizeBytes = 5 * 1024 * 1024; // 5 MB
        $safeCode = Str::slug($bookingCode);

        // 1. Jika dikirim melalui multipart UploadedFile
        if ($file && $file->isValid()) {
            if ($file->getSize() > $maxSizeBytes) {
                Log::warning('resolveAndStoreServicePhoto: Berkas melebihi 5 MB.');
                return null;
            }

            $rawBytes = file_get_contents($file->getRealPath());
            $headerSample = substr($rawBytes, 0, 512);

            // Tolak SVG & XML
            if (stripos($headerSample, '<svg') !== false || stripos($headerSample, '<?xml') !== false) {
                Log::warning('resolveAndStoreServicePhoto: Format SVG ditolak.');
                return null;
            }

            // Tolak MP4
            if (strlen($headerSample) >= 8 && substr($headerSample, 4, 4) === 'ftyp') {
                Log::warning('resolveAndStoreServicePhoto: Format video ditolak.');
                return null;
            }

            $isJpeg = strncmp($headerSample, "\xFF\xD8\xFF", 3) === 0;
            $isPng = strncmp($headerSample, "\x89PNG\r\n\x1a\n", 8) === 0;

            if (!$isJpeg && !$isPng) {
                Log::warning('resolveAndStoreServicePhoto: Magic bytes bukan JPG atau PNG.');
                return null;
            }

            $ext = $isPng ? 'png' : 'jpg';
            $mimeType = $isPng ? 'image/png' : 'image/jpeg';
            $filename = "service_issue_{$safeCode}_" . time() . '.' . $ext;

            $supabaseUrl = $storage->uploadRaw($rawBytes, $filename, $mimeType, 'services');
            if ($supabaseUrl) {
                return $supabaseUrl;
            }

            Log::error('resolveAndStoreServicePhoto: Gagal mengunggah ke Supabase Storage.');
            return null;
        }

        if (empty($photoString)) {
            return null;
        }

        // 2. Jika sudah berupa URL Supabase atau URL web
        if (Str::startsWith($photoString, ['http://', 'https://'])) {
            return $photoString;
        }

        // 3. Jika berupa Base64 (data:image/jpeg;base64,... atau data:image/png;base64,...)
        if (Str::startsWith($photoString, 'data:image/')) {
            if (Str::startsWith($photoString, 'data:image/svg')) {
                Log::warning('resolveAndStoreServicePhoto: Base64 SVG ditolak.');
                return null;
            }

            try {
                [$meta, $data] = explode(',', $photoString, 2);
                $decoded = base64_decode($data, true);

                if ($decoded !== false && strlen($decoded) > 0) {
                    if (strlen($decoded) > $maxSizeBytes) {
                        Log::warning('resolveAndStoreServicePhoto: Base64 decode melebihi 5 MB.');
                        return null;
                    }

                    $headerSample = substr($decoded, 0, 512);

                    if (stripos($headerSample, '<svg') !== false || stripos($headerSample, '<?xml') !== false) {
                        Log::warning('resolveAndStoreServicePhoto: SVG/XML terdeteksi.');
                        return null;
                    }

                    if (strlen($headerSample) >= 8 && substr($headerSample, 4, 4) === 'ftyp') {
                        Log::warning('resolveAndStoreServicePhoto: Video MP4 terdeteksi.');
                        return null;
                    }

                    $isJpeg = strncmp($headerSample, "\xFF\xD8\xFF", 3) === 0;
                    $isPng = strncmp($headerSample, "\x89PNG\r\n\x1a\n", 8) === 0;

                    if (!$isJpeg && !$isPng) {
                        Log::warning('resolveAndStoreServicePhoto: Magic bytes bukan JPG/PNG.');
                        return null;
                    }

                    $ext = $isPng ? 'png' : 'jpg';
                    $mimeType = $isPng ? 'image/png' : 'image/jpeg';
                    $filename = "service_issue_{$safeCode}_" . time() . '.' . $ext;

                    $supabaseUrl = $storage->uploadRaw($decoded, $filename, $mimeType, 'services');
                    if ($supabaseUrl) {
                        return $supabaseUrl;
                    }

                    Log::error('resolveAndStoreServicePhoto: Gagal mengunggah base64 ke Supabase Storage.');
                    return null;
                }
            } catch (Throwable $e) {
                Log::warning('resolveAndStoreServicePhoto Base64 decode failed: ' . $e->getMessage());
            }
        }

        return null;
    }
}
