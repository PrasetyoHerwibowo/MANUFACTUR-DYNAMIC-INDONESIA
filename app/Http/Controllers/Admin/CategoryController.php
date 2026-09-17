<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Support\ImageUploader;
use App\Support\Slug;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Daftar jenis mesin.
     */
    public function index(): View
    {
        $categories = Category::query()
            ->withCount('machines')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.form', ['category' => new Category()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        $validated['slug'] = Slug::make(Category::class, $validated['name']);
        $validated['image'] = ImageUploader::store($request->file('image'), 'categories');

        Category::create($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Jenis mesin berhasil ditambahkan.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.form', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $this->validated($request);

        $validated['slug'] = Slug::make(Category::class, $validated['name'], $category->id);

        if ($request->hasFile('image')) {
            ImageUploader::delete($category->image);
            $validated['image'] = ImageUploader::store($request->file('image'), 'categories');
        }

        $category->update($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Jenis mesin berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        ImageUploader::delete($category->image);

        foreach ($category->machines as $machine) {
            ImageUploader::delete($machine->main_image);

            foreach ($machine->images as $image) {
                ImageUploader::delete($image->path);
            }
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Jenis mesin beserta data mesin di dalamnya berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'tagline' => ['nullable', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:5000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}
