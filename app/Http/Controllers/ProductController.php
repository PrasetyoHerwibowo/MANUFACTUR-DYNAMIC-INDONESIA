<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Machine;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    /**
     * Daftar seluruh jenis mesin beserta model mesinnya,
     * dikelompokkan ke dalam MD-Cocoa Series dan MD-Coffee Series.
     */
    public function index(Request $request)
    {
        try {
            $categories = Cache::remember('products_categories', 1800, function () {
                return Category::query()
                    ->active()
                    ->ordered()
                    ->withCount(['machines' => fn ($query) => $query->where('is_active', true)])
                    ->get();
            });

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
                            ->orWhere('function', 'like', $keyword);
                    });
                })
                ->ordered()
                ->paginate(9)
                ->withQueryString();

            return view('public.products.index', compact(
                'cocoaMachines',
                'coffeeMachines',
                'cocoaCategories',
                'coffeeCategories',
                'keyword',
                'selectedSeries'
            ));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('ProductController@index error: ' . $e->getMessage());

            return view('public.products.index', [
                'cocoaMachines' => collect(),
                'coffeeMachines' => collect(),
                'cocoaCategories' => collect(),
                'coffeeCategories' => collect(),
                'keyword' => '',
                'selectedSeries' => '',
            ]);
        }
    }

    /**
     * Halaman khusus lini mesin pengolahan kakao (MD-Cocoa Series).
     */
    public function cocoaSeries(Request $request): View
    {
        try {
            $keyword = $request->string('q')->trim()->toString();
            $categorySlug = $request->string('kategori')->trim()->toString();

            $query = Machine::query()
                ->active()
                ->forCocoa()
                ->with(['category', 'images'])
                ->ordered();

            if ($keyword !== '') {
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                      ->orWhere('model_code', 'like', "%{$keyword}%")
                      ->orWhere('function', 'like', "%{$keyword}%")
                      ->orWhereHas('category', fn ($c) => $c->where('name', 'like', "%{$keyword}%"));
                });
            }

            if ($categorySlug !== '') {
                $query->whereHas('category', fn ($c) => $c->where('slug', $categorySlug));
            }

            $machines = $query->paginate(6)->withQueryString()->fragment('katalog-kakao');

            $categories = Category::query()
                ->active()
                ->forCocoa()
                ->ordered()
                ->withCount(['machines' => fn ($q) => $q->active()->forCocoa()])
                ->get();

            $activeCategory = $categorySlug !== '' ? $categories->firstWhere('slug', $categorySlug) : null;

            return view('public.products.cocoa_series', compact(
                'machines',
                'categories',
                'keyword',
                'categorySlug',
                'activeCategory'
            ));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('ProductController@cocoaSeries error: ' . $e->getMessage());

            return view('public.products.cocoa_series', [
                'machines' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 6),
                'categories' => collect(),
                'keyword' => '',
                'categorySlug' => '',
                'activeCategory' => null,
            ]);
        }
    }

    /**
     * Halaman khusus lini mesin pengolahan kopi (MD-Coffee Series).
     */
    public function coffeeSeries(Request $request): View
    {
        try {
            $keyword = $request->string('q')->trim()->toString();
            $categorySlug = $request->string('kategori')->trim()->toString();

            $query = Machine::query()
                ->active()
                ->forCoffee()
                ->with(['category', 'images'])
                ->ordered();

            if ($keyword !== '') {
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                      ->orWhere('model_code', 'like', "%{$keyword}%")
                      ->orWhere('function', 'like', "%{$keyword}%")
                      ->orWhereHas('category', fn ($c) => $c->where('name', 'like', "%{$keyword}%"));
                });
            }

            if ($categorySlug !== '') {
                $query->whereHas('category', fn ($c) => $c->where('slug', $categorySlug));
            }

            $machines = $query->paginate(6)->withQueryString()->fragment('katalog-kopi');

            $categories = Category::query()
                ->active()
                ->forCoffee()
                ->ordered()
                ->withCount(['machines' => fn ($q) => $q->active()->forCoffee()])
                ->get();

            $activeCategory = $categorySlug !== '' ? $categories->firstWhere('slug', $categorySlug) : null;

            return view('public.products.coffe_series', compact(
                'machines',
                'categories',
                'keyword',
                'categorySlug',
                'activeCategory'
            ));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('ProductController@coffeeSeries error: ' . $e->getMessage());

            return view('public.products.coffe_series', [
                'machines' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 6),
                'categories' => collect(),
                'keyword' => '',
                'categorySlug' => '',
                'activeCategory' => null,
            ]);
        }
    }

    /**
     * Detail sebuah model mesin: fungsi mesin, spesifikasi, dan galeri foto.
     */
    public function show(Machine $machine): View
    {
        abort_unless($machine->is_active, 404);

        try {
            if (! $machine->relationLoaded('category') || ! $machine->relationLoaded('images')) {
                $machine->load(['category', 'images']);
            }

            $related = Cache::remember("machine_related_{$machine->id}", 3600, function () use ($machine) {
                return Machine::query()
                    ->active()
                    ->where('category_id', $machine->category_id)
                    ->whereKeyNot($machine->getKey())
                    ->with(['category', 'images'])
                    ->ordered()
                    ->take(3)
                    ->get();
            });

            return view('public.products.show', compact('machine', 'related'));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('ProductController@show error: ' . $e->getMessage());

            $related = collect();

            return view('public.products.show', compact('machine', 'related'));
        }
    }
}
