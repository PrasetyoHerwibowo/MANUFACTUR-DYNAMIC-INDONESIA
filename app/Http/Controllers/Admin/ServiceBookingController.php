<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceBooking;
use App\Models\ServiceSchedule;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ServiceBookingController extends Controller
{
    /**
     * Daftar seluruh booking servis teknisi.
     */
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $bookings = ServiceBooking::query()
            ->with(['customer', 'user', 'order'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $keyword = '%' . $request->string('q') . '%';
                $query->where(function ($sub) use ($keyword) {
                    $sub->where('booking_code', 'like', $keyword)
                        ->orWhere('serial_number', 'like', $keyword)
                        ->orWhere('machine_model', 'like', $keyword)
                        ->orWhere('company_name', 'like', $keyword)
                        ->orWhereHas('customer', function ($customer) use ($keyword) {
                            $customer->where('name', 'like', $keyword)
                                ->orWhere('email', 'like', $keyword);
                        });
                });
            })
            ->when(array_key_exists($status, ServiceBooking::statusLabels()), fn ($query) => $query->where('status', $status))
            ->when($request->filled('dari'), fn ($query) => $query->whereDate('scheduled_date', '>=', $request->date('dari')))
            ->when($request->filled('sampai'), fn ($query) => $query->whereDate('scheduled_date', '<=', $request->date('sampai')))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        // Hitung statistik booking servis dengan cache efisien
        $stats = Cache::remember('admin_services_stats', 60, function () {
            $counts = ServiceBooking::query()
                ->selectRaw("COUNT(*) as total,
                    COUNT(CASE WHEN status IN ('pending', 'menunggu_verifikasi') THEN 1 END) as menunggu,
                    COUNT(CASE WHEN status IN ('confirmed', 'dijadwalkan', 'teknisi_otw', 'dalam_pengerjaan') THEN 1 END) as berjalan,
                    COUNT(CASE WHEN status = 'selesai' THEN 1 END) as selesai")
                ->first();

            return [
                'total' => (int) ($counts->total ?? 0),
                'menunggu' => (int) ($counts->menunggu ?? 0),
                'berjalan' => (int) ($counts->berjalan ?? 0),
                'selesai' => (int) ($counts->selesai ?? 0),
            ];
        });

        return view('admin.services.index', [
            'bookings' => $bookings,
            'stats' => $stats,
            'statusLabels' => ServiceBooking::statusLabels(),
        ]);
    }

    /**
     * Tampilkan detail booking servis & form verifikasi jadwal teknisi.
     */
    public function show(ServiceBooking $service): View
    {
        $service->load(['customer', 'user', 'order.items.machine']);

        return view('admin.services.show', [
            'booking' => $service,
            'statusLabels' => ServiceBooking::statusLabels(),
            'serviceTypeLabels' => ServiceBooking::serviceTypeLabels(),
        ]);
    }

    /**
     * Verifikasi, jadwalkan, atau ubah status booking servis & tugaskan teknisi.
     */
    public function verify(Request $request, ServiceBooking $service): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', array_keys(ServiceBooking::statusLabels()))],
            'scheduled_date' => ['nullable', 'date'],
            'scheduled_time' => ['nullable', 'string', 'max:50'],
            'technician_name' => ['nullable', 'string', 'max:150'],
            'technician_phone' => ['nullable', 'string', 'max:50'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            DB::beginTransaction();

            $updateData = [
                'status' => $validated['status'],
                'admin_notes' => $validated['admin_notes'] ?? $service->admin_notes,
            ];

            if (! empty($validated['scheduled_date'])) {
                $updateData['scheduled_date'] = $validated['scheduled_date'];
            }
            if (! empty($validated['scheduled_time'])) {
                $updateData['scheduled_time'] = $validated['scheduled_time'];
            }
            if (! empty($validated['technician_name'])) {
                $updateData['technician_name'] = $validated['technician_name'];
            }
            if (! empty($validated['technician_phone'])) {
                $updateData['technician_phone'] = $validated['technician_phone'];
            }

            $service->update($updateData);

            DB::commit();

            // Bersihkan cache
            Cache::forget('admin_pending_services_count');
            Cache::forget('admin_services_stats');
            if ($service->user_id) {
                Cache::forget("api_service_bookings_user_{$service->user_id}");
            }

            return redirect()->route('admin.services.show', $service)
                ->with('status', 'Jadwal dan status servis unit ' . $service->serial_number . ' berhasil diperbarui.');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('ServiceBookingController@verify error: ' . $e->getMessage());

            return back()->with('error', 'Gagal memperbarui status servis: ' . $e->getMessage());
        }
    }

    /**
     * Tugaskan teknisi & nomor kontak langsung dari panel admin.
     * Hanya mengatur nama dan nomor HP/WhatsApp teknisi, langsung sinkron ke mobile app customer.
     */
    public function assignTechnician(Request $request, ServiceBooking $service): RedirectResponse
    {
        $validated = $request->validate([
            'technician_name' => ['required', 'string', 'max:150'],
            'technician_phone' => ['required', 'string', 'max:50'],
        ]);

        try {
            $updateData = [
                'technician_name' => $validated['technician_name'],
                'technician_phone' => $validated['technician_phone'],
            ];

            // Otomatis ubah status menjadi Confirmed jika status masih pending / menunggu
            if ($service->isPending()) {
                $updateData['status'] = ServiceBooking::STATUS_CONFIRMED;
            }

            $service->update($updateData);

            Cache::forget('admin_pending_services_count');
            Cache::forget('admin_services_stats');
            if ($service->user_id) {
                Cache::forget("api_service_bookings_user_{$service->user_id}");
            }

            return back()->with('status', "Teknisi '{$validated['technician_name']}' ({$validated['technician_phone']}) berhasil ditugaskan & jadwal dikonfirmasi. Customer dapat langsung menghubungi via WhatsApp/telepon.");
        } catch (Throwable $e) {
            Log::error('ServiceBookingController@assignTechnician error: ' . $e->getMessage());

            return back()->with('error', 'Gagal menugaskan teknisi: ' . $e->getMessage());
        }
    }

    /**
     * Verifikasi servis telah selesai dari dashboard admin.
     * Langsung sinkron ke mobile app customer menjadi status Selesai.
     */
    public function completeService(ServiceBooking $service): RedirectResponse
    {
        try {
            $service->update([
                'status' => ServiceBooking::STATUS_SELESAI,
            ]);

            Cache::forget('admin_pending_services_count');
            Cache::forget('admin_services_stats');
            if ($service->user_id) {
                Cache::forget("api_service_bookings_user_{$service->user_id}");
            }

            return back()->with('status', "Servis mesin unit {$service->serial_number} (Tiket: {$service->booking_code}) berhasil diverifikasi selesai.");
        } catch (Throwable $e) {
            Log::error('ServiceBookingController@completeService error: ' . $e->getMessage());

            return back()->with('error', 'Gagal memverifikasi servis selesai: ' . $e->getMessage());
        }
    }

    /**
     * Simpan / Perbarui ketersediaan hari, kuota, & teknisi standby.
     */
    public function updateSchedule(Request $request)
    {
        $validated = $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
            'is_available' => ['required'],
            'technician_standby_count' => ['required', 'integer', 'min:0', 'max:20'],
            'max_quota' => ['required', 'integer', 'min:0', 'max:50'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            ServiceSchedule::updateOrCreate(
                ['date' => $validated['date']],
                [
                    'is_available' => filter_var($validated['is_available'], FILTER_VALIDATE_BOOLEAN),
                    'technician_standby_count' => (int) $validated['technician_standby_count'],
                    'max_quota' => (int) $validated['max_quota'],
                    'notes' => $validated['notes'] ?? null,
                ]
            );

            Cache::forget('api_available_service_dates');
            Cache::forget('api_available_service_dates_21');

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Jadwal servis tanggal ' . $validated['date'] . ' berhasil disimpan.',
                ]);
            }

            return back()->with('status', 'Jadwal tanggal ' . $validated['date'] . ' dan teknisi standby berhasil diperbarui.');
        } catch (Throwable $e) {
            Log::error('ServiceBookingController@updateSchedule error: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui jadwal: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Gagal memperbarui jadwal servis: ' . $e->getMessage());
        }
    }

    /**
     * Hapus tiket booking servis.
     */
    public function destroy(ServiceBooking $service): RedirectResponse
    {
        try {
            $service->delete();

            Cache::forget('admin_pending_services_count');
            Cache::forget('admin_services_stats');
            if ($service->user_id) {
                Cache::forget("api_service_bookings_user_{$service->user_id}");
            }

            return redirect()->route('admin.services.index')
                ->with('status', 'Tiket booking servis berhasil dihapus.');
        } catch (Throwable $e) {
            Log::error('ServiceBookingController@destroy error: ' . $e->getMessage());

            return back()->with('error', 'Gagal menghapus tiket servis.');
        }
    }
}

