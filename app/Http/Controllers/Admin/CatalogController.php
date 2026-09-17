<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Catalog;
use App\Models\CatalogImage;
use App\Support\ImageUploader;
use App\Support\Slug;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * Daftar katalog produk.
     */
    public function index(Request $request): View
    {
        $catalogs = Catalog::query()
            ->withCount('images')
            ->when($request->filled('q'), function ($query) use ($request) {
                $keyword = '%'.$request->string('q').'%';
                $query->where('title', 'like', $keyword);
            })
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.catalogs.index', compact('catalogs'));
    }

    public function create(): View
    {
        return view('admin.catalogs.form', ['catalog' => new Catalog()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        $validated['slug'] = Slug::make(Catalog::class, $validated['title']);
        $validated['cover_image'] = ImageUploader::store($request->file('cover_image'), 'catalogs');
        $validated['pdf_file'] = ImageUploader::store($request->file('pdf_file'), 'catalogs');

        $catalog = Catalog::create($validated);

        $this->storeGalleryImages($request, $catalog);

        return redirect()
            ->route('admin.catalogs.edit', $catalog)
            ->with('success', 'Katalog berhasil ditambahkan. Silakan tambahkan foto halaman katalog.');
    }

    public function edit(Catalog $catalog): View
    {
        $catalog->load('images');

        return view('admin.catalogs.form', compact('catalog'));
    }

    public function update(Request $request, Catalog $catalog): RedirectResponse
    {
        $validated = $this->validated($request);

        $validated['slug'] = Slug::make(Catalog::class, $validated['title'], $catalog->id);

        if ($request->hasFile('cover_image')) {
            ImageUploader::delete($catalog->cover_image);
            $validated['cover_image'] = ImageUploader::store($request->file('cover_image'), 'catalogs');
        }

        if ($request->hasFile('pdf_file')) {
            ImageUploader::delete($catalog->pdf_file);
            $validated['pdf_file'] = ImageUploader::store($request->file('pdf_file'), 'catalogs');
        }

        $catalog->update($validated);

        $this->storeGalleryImages($request, $catalog);

        return redirect()
            ->route('admin.catalogs.edit', $catalog)
            ->with('success', 'Katalog berhasil diperbarui.');
    }

    public function destroy(Catalog $catalog): RedirectResponse
    {
        ImageUploader::delete($catalog->cover_image);
        ImageUploader::delete($catalog->pdf_file);

        foreach ($catalog->images as $image) {
            ImageUploader::delete($image->path);
        }

        $catalog->delete();

        return redirect()
            ->route('admin.catalogs.index')
            ->with('success', 'Katalog berhasil dihapus.');
    }

    /**
     * Perbarui keterangan sebuah foto halaman katalog.
     */
    public function updateImage(Request $request, Catalog $catalog, CatalogImage $image): RedirectResponse
    {
        abort_unless($image->catalog_id === $catalog->id, 404);

        $validated = $request->validate([
            'caption' => ['nullable', 'string', 'max:150'],
        ]);

        $image->update($validated);

        return redirect()
            ->route('admin.catalogs.edit', $catalog)
            ->with('success', 'Keterangan foto berhasil diperbarui.');
    }

    /**
     * Hapus satu foto halaman katalog.
     */
    public function destroyImage(Catalog $catalog, CatalogImage $image): RedirectResponse
    {
        abort_unless($image->catalog_id === $catalog->id, 404);

        ImageUploader::delete($image->path);
        $image->delete();

        return redirect()
            ->route('admin.catalogs.edit', $catalog)
            ->with('success', 'Foto katalog berhasil dihapus.');
    }

    private function storeGalleryImages(Request $request, Catalog $catalog): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $captions = (array) $request->input('captions', []);
        $lastOrder = (int) $catalog->images()->max('sort_order');

        foreach ($request->file('images') as $index => $file) {
            $path = ImageUploader::store($file, 'catalogs');

            if (! $path) {
                continue;
            }

            $catalog->images()->create([
                'path' => $path,
                'caption' => $captions[$index] ?? null,
                'sort_order' => ++$lastOrder,
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:5000'],
            'year' => ['nullable', 'string', 'max:10'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
            'pdf_file' => ['nullable', 'file', 'mimes:pdf', 'max:20480'],
            'images' => ['nullable', 'array', 'max:30'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
            'captions' => ['nullable', 'array'],
            'captions.*' => ['nullable', 'string', 'max:150'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        unset($validated['images'], $validated['captions']);

        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}
