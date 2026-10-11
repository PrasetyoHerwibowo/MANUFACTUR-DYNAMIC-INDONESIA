<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Machine;
use App\Models\MachineImage;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'kategori' => Category::query()->count(),
            'kategori_aktif' => Category::query()->active()->count(),
            'mesin' => Machine::query()->count(),
            'mesin_aktif' => Machine::query()->active()->count(),
            'mesin_berfoto' => Machine::query()->has('images')->count(),
            'foto' => MachineImage::query()->count(),
        ];

        $recentMachines = Machine::query()
            ->with('category')
            ->latest()
            ->take(5)
            ->get();

        /** Distribusi model mesin per jenis mesin untuk grafik donat. */
        $categoryChart = Category::query()
            ->withCount('machines')
            ->orderByDesc('machines_count')
            ->orderBy('name')
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentMachines',
            'categoryChart',
        ));
    }
}
