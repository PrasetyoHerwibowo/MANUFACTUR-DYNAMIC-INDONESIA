<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Catalog;
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
            'mesin' => Machine::query()->count(),
            'katalog' => Catalog::query()->count(),
            'foto' => MachineImage::query()->count(),
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

        return view('admin.dashboard', compact('stats', 'recentMachines', 'recentMessages'));
    }
}
