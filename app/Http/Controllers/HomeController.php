<?php

namespace App\Http\Controllers;

use App\Models\Catalog;
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

        $featured = Machine::query()
            ->active()
            ->where('is_featured', true)
            ->with(['category', 'images'])
            ->ordered()
            ->take(6)
            ->get();

        if ($featured->isEmpty()) {
            $featured = Machine::query()
                ->active()
                ->with(['category', 'images'])
                ->latest()
                ->take(6)
                ->get();
        }

        $catalogs = Catalog::query()
            ->active()
            ->ordered()
            ->take(3)
            ->get();

        $stats = [
            'kategori' => Category::query()->active()->count(),
            'mesin' => Machine::query()->active()->count(),
            'katalog' => Catalog::query()->active()->count(),
            'foto' => MachineImage::query()->count(),
        ];

        return view('public.home', compact('categories', 'featured', 'catalogs', 'stats'));
    }
}
