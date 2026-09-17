<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Machine;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Daftar seluruh jenis mesin beserta model mesinnya.
     */
    public function index(Request $request): View
    {
        $categories = Category::query()
            ->active()
            ->ordered()
            ->withCount(['machines' => fn ($query) => $query->where('is_active', true)])
            ->get();

        $activeCategory = null;

        $machines = Machine::query()
            ->active()
            ->with(['category', 'images'])
            ->when($request->filled('kategori'), function ($query) use ($request, &$activeCategory) {
                $activeCategory = Category::query()->where('slug', $request->string('kategori'))->first();
                $query->whereHas('category', fn ($sub) => $sub->where('slug', $request->string('kategori')));
            })
            ->when($request->filled('q'), function ($query) use ($request) {
                $keyword = '%'.$request->string('q').'%';
                $query->where(function ($sub) use ($keyword) {
                    $sub->where('name', 'like', $keyword)
                        ->orWhere('model_code', 'like', $keyword)
                        ->orWhere('function', 'like', $keyword)
                        ->orWhere('short_description', 'like', $keyword);
                });
            })
            ->ordered()
            ->paginate(9)
            ->withQueryString();

        return view('public.products.index', compact('categories', 'machines', 'activeCategory'));
    }

    /**
     * Detail sebuah model mesin: fungsi mesin, spesifikasi, dan galeri foto.
     */
    public function show(Machine $machine): View
    {
        abort_unless($machine->is_active, 404);

        $machine->load(['category', 'images']);

        $related = Machine::query()
            ->active()
            ->where('category_id', $machine->category_id)
            ->whereKeyNot($machine->getKey())
            ->with('images')
            ->ordered()
            ->take(3)
            ->get();

        return view('public.products.show', compact('machine', 'related'));
    }
}
