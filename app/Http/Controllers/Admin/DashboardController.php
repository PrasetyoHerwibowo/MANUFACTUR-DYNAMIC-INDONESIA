<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Catalog;
use App\Models\CatalogImage;
use App\Models\Category;
use App\Models\ContactMessage;
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
            'katalog' => Catalog::query()->count(),
            'katalog_berfoto' => Catalog::query()->has('images')->count(),
            'foto' => MachineImage::query()->count(),
            'foto_katalog' => CatalogImage::query()->count(),
            'pesan' => ContactMessage::query()->count(),
            'pesan_baru' => ContactMessage::query()->unread()->count(),
        ];

        $recentMachines = Machine::query()
            ->with('category')
            ->latest()
            ->take(5)
            ->get();

        $recentMessages = ContactMessage::query()
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

        /** Jumlah pesan masuk selama enam bulan terakhir untuk grafik garis. */
        $pesanPerBulan = ContactMessage::query()
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->get(['created_at'])
            ->countBy(fn (ContactMessage $message): string => $message->created_at->format('Y-m'));

        $namaBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $monthLabels = [];
        $monthCounts = [];

        for ($i = 5; $i >= 0; $i--) {
            $bulan = now()->subMonths($i);

            $monthLabels[] = $namaBulan[(int) $bulan->format('n') - 1].' '.$bulan->format('y');
            $monthCounts[] = (int) $pesanPerBulan->get($bulan->format('Y-m'), 0);
        }

        return view('admin.dashboard', compact(
            'stats',
            'recentMachines',
            'recentMessages',
            'categoryChart',
            'monthLabels',
            'monthCounts',
        ));
    }
}
