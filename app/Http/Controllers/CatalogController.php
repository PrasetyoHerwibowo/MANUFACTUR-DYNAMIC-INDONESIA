<?php

namespace App\Http\Controllers;

use App\Models\Catalog;
use Illuminate\Contracts\View\View;

class CatalogController extends Controller
{
    /**
     * Daftar katalog produk.
     */
    public function index(): View
    {
        $catalogs = Catalog::query()
            ->active()
            ->ordered()
            ->withCount('images')
            ->paginate(9);

        return view('public.catalogs.index', compact('catalogs'));
    }

    /**
     * Isi sebuah katalog (foto-foto halaman katalog).
     */
    public function show(Catalog $catalog): View
    {
        abort_unless($catalog->is_active, 404);

        $catalog->load('images');

        return view('public.catalogs.show', compact('catalog'));
    }
}
