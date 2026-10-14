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
            $categories = Cache::remember('products_categories', 1800, function () {
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
