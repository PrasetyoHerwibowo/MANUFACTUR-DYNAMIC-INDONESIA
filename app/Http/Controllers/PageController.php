<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

class PageController extends Controller
{
    /**
     * Halaman "Tentang Kami".
     */
    public function about(): View
    {
        try {
            $categories = Cache::remember('site_active_categories', 3600, function () {
                return Category::query()
                    ->active()
                    ->ordered()
                    ->withCount(['machines' => fn ($query) => $query->where('is_active', true)])
                    ->get();
            });

            return view('public.about', compact('categories'));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('PageController@about error: ' . $e->getMessage());

            $categories = collect();

            return view('public.about', compact('categories'));
        }
    }

    /**
     * Halaman "Kontak".
     */
    public function contact(): View
    {
        return view('public.contact');
    }
}
