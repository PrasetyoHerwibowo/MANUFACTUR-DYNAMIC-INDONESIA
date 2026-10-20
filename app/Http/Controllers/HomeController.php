<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Machine;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(): View
    {
        try {
            $categories = Cache::remember('site_active_categories', 3600, function () {
                return Category::query()
                    ->active()
                    ->ordered()
                    ->withCount(['machines' => fn ($query) => $query->where('is_active', true)])
                    ->get();
            })->take(6);

            $featured = Cache::remember('home_featured', 3600, function () {
                return Machine::query()
                    ->active()
                    ->with(['category', 'images'])
                    ->ordered()
                    ->take(6)
                    ->get();
            });

            $stats = Cache::remember('home_stats', 3600, function () {
                $row = DB::selectOne("
                    SELECT 
                        (SELECT COUNT(*) FROM categories WHERE is_active = true) as kategori,
                        (SELECT COUNT(*) FROM machines WHERE is_active = true) as mesin,
                        (SELECT COUNT(*) FROM machine_images) as foto
                ");

                return [
                    'kategori' => (int) ($row->kategori ?? 0),
                    'mesin' => (int) ($row->mesin ?? 0),
                    'foto' => (int) ($row->foto ?? 0),
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
