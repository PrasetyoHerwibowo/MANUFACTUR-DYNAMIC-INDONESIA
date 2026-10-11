<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    /**
     * Halaman "Tentang Kami".
     */
    public function about(): View
    {
        $categories = Category::query()
            ->active()
            ->ordered()
            ->withCount(['machines' => fn ($query) => $query->where('is_active', true)])
            ->get();

        return view('public.about', compact('categories'));
    }

    /**
     * Halaman "Kontak".
     */
    public function contact(): View
    {
        return view('public.contact');
    }
}
