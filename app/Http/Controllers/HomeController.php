<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Machine;
use App\Models\MachineImage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index(): View
    {
        try {
            $categories = Cache::remember('home_categories', 1800, function () {
                return Category::query()
                    ->active()
                    ->ordered()
                    ->withCount(['machines' => fn ($query) => $query->where('is_active', true)])
                    ->take(6)
                    ->get();
            });

            $featured = Cache::remember('home_featured', 1800, function () {
                return Machine::query()
                    ->active()
                    ->with(['category', 'images'])
                    ->ordered()
                    ->take(6)
                    ->get();
            });

            $stats = Cache::remember('home_stats', 1800, function () {
                return [
                    'kategori' => Category::query()->active()->count(),
                    'mesin' => Machine::query()->active()->count(),
                    'foto' => MachineImage::query()->count(),
                ];
            });

            return view('public.home', compact('categories', 'featured', 'stats'));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('HomeController@index error: ' . $e->getMessage());

            $categories = collect();
            $featured = collect();
            $stats = [
                'kategori' => 0,
                'mesin' => 0,
                'foto' => 0,
            ];

            return view('public.home', compact('categories', 'featured', 'stats'));
        }
    }
}
