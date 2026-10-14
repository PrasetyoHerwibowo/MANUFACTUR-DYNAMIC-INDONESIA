<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Machine;
use App\Models\MachineImage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index(): View
    {
        try {
            $stats = Cache::remember('admin_dashboard_stats', 300, function () {
                $categoryStats = Category::query()
                    ->selectRaw('COUNT(*) as total, COUNT(CASE WHEN is_active = true THEN 1 END) as active')
                    ->first();

                $machineStats = Machine::query()
                    ->selectRaw("COUNT(*) as total, COUNT(CASE WHEN is_active = true THEN 1 END) as active, COUNT(CASE WHEN (main_image IS NOT NULL AND main_image != '') OR EXISTS (SELECT 1 FROM machine_images WHERE machine_images.machine_id = machines.id) THEN 1 END) as berfoto")
                    ->first();

                $photoCount = MachineImage::query()->count();

                return [
                    'kategori' => (int) ($categoryStats->total ?? 0),
                    'kategori_aktif' => (int) ($categoryStats->active ?? 0),
                    'mesin' => (int) ($machineStats->total ?? 0),
                    'mesin_aktif' => (int) ($machineStats->active ?? 0),
                    'mesin_berfoto' => (int) ($machineStats->berfoto ?? 0),
                    'foto' => (int) $photoCount,
                ];
            });

            $recentMachines = Cache::remember('admin_dashboard_recent_machines', 300, function () {
                return Machine::query()
                    ->with(['category', 'images'])
                    ->latest('id')
                    ->take(5)
                    ->get();
            });

            /** Distribusi model mesin per jenis mesin untuk grafik donat (dicache 5 menit). */
            $categoryChart = Cache::remember('admin_dashboard_category_chart', 300, function () {
                return Category::query()
                    ->withCount('machines')
                    ->orderByDesc('machines_count')
                    ->orderBy('name')
                    ->take(6)
                    ->get();
            });

            return view('admin.dashboard', compact(
                'stats',
                'recentMachines',
                'categoryChart',
            ));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('DashboardController@index error: ' . $e->getMessage());

            $stats = [
                'kategori' => 0,
                'kategori_aktif' => 0,
                'mesin' => 0,
                'mesin_aktif' => 0,
                'mesin_berfoto' => 0,
                'foto' => 0,
            ];
            $recentMachines = collect();
            $categoryChart = collect();

            return view('admin.dashboard', compact(
                'stats',
                'recentMachines',
                'categoryChart',
            ))->with('error', 'Gagal memuat sebagian ringkasan data dashboard.');
        }
    }
}
