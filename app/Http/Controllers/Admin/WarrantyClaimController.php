<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WarrantyClaim;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class WarrantyClaimController extends Controller
{
    /**
     * Daftar klaim garansi pelanggan.
     */
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $claims = WarrantyClaim::query()
            ->with(['customer', 'user', 'order'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $keyword = '%' . $request->string('q')->toString() . '%';
                $query->where(function ($sub) use ($keyword) {
                    $sub->where('claim_code', 'like', $keyword)
                        ->orWhere('serial_number', 'like', $keyword)
                        ->orWhere('machine_model', 'like', $keyword)
                        ->orWhereHas('customer', function ($customer) use ($keyword) {
                            $customer->where('name', 'like', $keyword)
                                ->orWhere('email', 'like', $keyword)
                                ->orWhere('company', 'like', $keyword);
                        });
                });
            })
            ->when(array_key_exists($status, WarrantyClaim::statusLabels()), fn ($query) => $query->where('status', $status))
            ->when($request->date('dari'), fn ($query, $date) => $query->where('created_at', '>=', $date->startOfDay()))
            ->when($request->date('sampai'), fn ($query, $date) => $query->where('created_at', '<', $date->addDay()->startOfDay()))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        // Hitung ringkasan statistik klaim garansi dengan cache
        $stats = Cache::remember('admin_warranties_stats', 60, function () {
            $counts = WarrantyClaim::query()
                ->selectRaw("COUNT(*) as total,
                    COUNT(CASE WHEN status = ? THEN 1 END) as menunggu,
                    COUNT(CASE WHEN status IN (?, ?, ?) THEN 1 END) as dijadwalkan,
                    COUNT(CASE WHEN status = ? THEN 1 END) as selesai,
                    COUNT(CASE WHEN status = ? THEN 1 END) as ditolak",
                    [
                        WarrantyClaim::STATUS_DALAM_PENINJAUAN,
                        WarrantyClaim::STATUS_DISETUJUI,
                        WarrantyClaim::STATUS_KUNJUNGAN_DIJADWALKAN,
                        WarrantyClaim::STATUS_TEKNISI_OTW,
                        WarrantyClaim::STATUS_SELESAI,
                        WarrantyClaim::STATUS_DITOLAK,
                    ])
                ->first();

            return [
                'total' => (int) ($counts->total ?? 0),
                'menunggu' => (int) ($counts->menunggu ?? 0),
                'dijadwalkan' => (int) ($counts->dijadwalkan ?? 0),
                'selesai' => (int) ($counts->selesai ?? 0),
                'ditolak' => (int) ($counts->ditolak ?? 0),
            ];
        });

        return view('admin.warranties.index', [
            'claims' => $claims,
            'stats' => $stats,
            'statusLabels' => WarrantyClaim::statusLabels(),
        ]);
    }

    /**
     * Tampilkan detail klaim garansi, foto/video bukti kerusakan, dan form verifikasi.
     */
    public function show(WarrantyClaim $warranty): View
    {
        $warranty->load(['customer', 'user', 'order.items.machine']);

        return view('admin.warranties.show', [
            'claim' => $warranty,
            'statusLabels' => WarrantyClaim::statusLabels(),
        ]);
    }

    /**
     * Verifikasi keabsahan bukti foto/video klaim garansi & jadwalkan kunjungan perbaikan teknisi jika valid.
     */
    public function verify(Request $request, WarrantyClaim $warranty): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'string', 'in:approve,reject'],
            'scheduled_visit_date' => ['required_if:action,approve', 'nullable', 'date'],
            'scheduled_visit_time' => ['nullable', 'string', 'max:50'],
            'visit_address' => ['nullable', 'string', 'max:500'],
            'technician_name' => ['nullable', 'string', 'max:150'],
            'technician_phone' => ['nullable', 'string', 'max:50'],
            'response_notes' => ['required', 'string', 'max:1000'],
        ]);

        if ($warranty->verification_status !== WarrantyClaim::VERIFIKASI_MENUNGGU) {
            return back()->with('error', 'Klaim ini sudah diverifikasi dan tidak dapat diverifikasi ulang.');
        }
        if ($validated['action'] === 'approve' && $warranty->warranty_status !== 'dalam_garansi') {
            return back()->with('error', 'Klaim di luar masa garansi tidak dapat disetujui.');
        }
        if ($validated['action'] === 'reject' && mb_strlen(trim($validated['response_notes'])) < 5) {
            return back()->withErrors(['response_notes' => 'Tuliskan alasan penolakan minimal 5 karakter.'])->withInput();
        }
        if ($validated['action'] === 'approve' && $request->date('scheduled_visit_date')?->lt(today())) {
            return back()->withErrors(['scheduled_visit_date' => 'Jadwal kunjungan harus hari ini atau setelahnya.']);
        }

        try {
            DB::beginTransaction();

            if ($validated['action'] === 'approve') {
                $warranty->update([
                    'verification_status' => WarrantyClaim::VERIFIKASI_VALID,
                    'status' => WarrantyClaim::STATUS_KUNJUNGAN_DIJADWALKAN,
                    'scheduled_visit_date' => $validated['scheduled_visit_date'] ?? now()->addDays(2)->toDateString(),
                    'scheduled_visit_time' => $validated['scheduled_visit_time'] ?? '10:00 WIB',
                    'visit_address' => $validated['visit_address'] ?: ($warranty->order?->delivery_address ?? $warranty->customer?->address),
                    'technician_name' => $validated['technician_name'] ?? 'Tim Teknisi Garansi Pabrik',
                    'technician_phone' => $validated['technician_phone'] ?? '0812-3344-5566',
                    'response_notes' => $validated['response_notes'],
                ]);

                $message = "Klaim garansi unit {$warranty->serial_number} berhasil diverifikasi valid. Jadwal kunjungan teknisi telah diterbitkan ke aplikasi customer.";
            } else {
                $warranty->update([
                    'verification_status' => WarrantyClaim::VERIFIKASI_TIDAK_VALID,
                    'status' => WarrantyClaim::STATUS_DITOLAK,
                    'response_notes' => $validated['response_notes'],
                ]);

                $message = "Klaim garansi unit {$warranty->serial_number} telah ditolak dengan catatan verifikasi.";
            }

            DB::commit();

            // Bersihkan cache
            Cache::forget('admin_pending_warranties_count');
            Cache::forget('admin_warranties_stats');
            if ($warranty->user_id) {
                Cache::forget("api_warranty_claims_user_{$warranty->user_id}");
            }

            return redirect()->route('admin.warranties.show', $warranty)->with('status', $message);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('WarrantyClaimController@verify error: ' . $e->getMessage());

            return back()->with('error', 'Gagal memproses verifikasi klaim: ' . $e->getMessage());
        }
    }

    /**
     * Perbarui status kunjungan teknisi garansi (misal: teknisi otw, pengerjaan, selesai).
     */
    public function updateVisitStatus(Request $request, WarrantyClaim $warranty): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', array_keys(WarrantyClaim::statusLabels()))],
            'response_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $stageNotes = [
                WarrantyClaim::STATUS_DISETUJUI => 'Klaim telah disetujui dan bukti dinyatakan valid.',
                WarrantyClaim::STATUS_KUNJUNGAN_DIJADWALKAN => 'Kunjungan teknisi telah dijadwalkan.',
                WarrantyClaim::STATUS_TEKNISI_OTW => 'Teknisi sedang dalam perjalanan menuju lokasi.',
                WarrantyClaim::STATUS_DALAM_PERBAIKAN => 'Teknisi sedang melakukan perbaikan mesin.',
                WarrantyClaim::STATUS_SELESAI => 'Perbaikan garansi telah selesai.',
            ];
            $transitions = [
                WarrantyClaim::STATUS_DISETUJUI => [WarrantyClaim::STATUS_KUNJUNGAN_DIJADWALKAN],
                WarrantyClaim::STATUS_KUNJUNGAN_DIJADWALKAN => [WarrantyClaim::STATUS_TEKNISI_OTW, WarrantyClaim::STATUS_DALAM_PERBAIKAN],
                WarrantyClaim::STATUS_TEKNISI_OTW => [WarrantyClaim::STATUS_DALAM_PERBAIKAN],
                WarrantyClaim::STATUS_DALAM_PERBAIKAN => [WarrantyClaim::STATUS_SELESAI],
            ];
            if (!in_array($validated['status'], $transitions[$warranty->status] ?? [], true)) {
                return back()->with('error', 'Perubahan status tidak sesuai alur penanganan klaim.');
            }
            $warranty->update(array_filter([
                'status' => $validated['status'],
                'response_notes' => filled($validated['response_notes'] ?? null)
                    ? $validated['response_notes']
                    : ($stageNotes[$validated['status']] ?? $warranty->response_notes),
            ]));

            Cache::forget('admin_pending_warranties_count');
            Cache::forget('admin_warranties_stats');
            if ($warranty->user_id) {
                Cache::forget("api_warranty_claims_user_{$warranty->user_id}");
            }

            return redirect()->route('admin.warranties.show', $warranty)
                ->with('status', 'Status penanganan garansi berhasil diperbarui ke: ' . $warranty->statusLabel());
        } catch (Throwable $e) {
            Log::error('WarrantyClaimController@updateVisitStatus error: ' . $e->getMessage());

            return back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    /**
     * Hapus berkas klaim garansi.
     */
    public function destroy(WarrantyClaim $warranty): RedirectResponse
    {
        try {
            $warranty->delete();

            Cache::forget('admin_pending_warranties_count');
            Cache::forget('admin_warranties_stats');
            if ($warranty->user_id) {
                Cache::forget("api_warranty_claims_user_{$warranty->user_id}");
            }

            return redirect()->route('admin.warranties.index')
                ->with('status', 'Klaim garansi berhasil dihapus.');
        } catch (Throwable $e) {
            Log::error('WarrantyClaimController@destroy error: ' . $e->getMessage());

            return back()->with('error', 'Gagal menghapus klaim garansi.');
        }
    }
}
