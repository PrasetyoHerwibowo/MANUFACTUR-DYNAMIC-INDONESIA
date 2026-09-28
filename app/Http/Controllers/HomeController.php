<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Machine;
use App\Models\MachineImage;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->active()
            ->ordered()
            ->withCount(['machines' => fn ($query) => $query->where('is_active', true)])
            ->take(6)
            ->get();

        // Model mesin unggulan dipilih otomatis: enam model aktif pertama
        // menurut urutan tampil, karena penandaan unggulan tidak lagi diatur
        // dari form admin.
        $featured = Machine::query()
            ->active()
            ->with(['category', 'images'])
            ->ordered()
            ->take(6)
            ->get();

        $stats = [
            'kategori' => Category::query()->active()->count(),
            'mesin' => Machine::query()->active()->count(),
            'foto' => MachineImage::query()->count(),
        ];

        return view('public.home', compact('categories', 'featured', 'stats'));
    }
}
