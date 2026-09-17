<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Machine;
use App\Models\MachineImage;
use App\Support\ImageUploader;
use App\Support\Slug;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MachineController extends Controller
{
    /**
     * Daftar model mesin.
     */
    public function index(Request $request): View
    {
        $machines = Machine::query()
            ->with('category')
            ->withCount('images')
            ->when($request->filled('kategori'), fn ($query) => $query->where('category_id', $request->integer('kategori')))
            ->when($request->filled('q'), function ($query) use ($request) {
                $keyword = '%'.$request->string('q').'%';
                $query->where(function ($sub) use ($keyword) {
                    $sub->where('name', 'like', $keyword)
                        ->orWhere('model_code', 'like', $keyword)
                        ->orWhere('function', 'like', $keyword);
                });
            })
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $categories = Category::query()->ordered()->get();

        return view('admin.machines.index', compact('machines', 'categories'));
    }

    public function create(): View
    {
        return view('admin.machines.form', [
            'machine' => new Machine(),
            'categories' => Category::query()->ordered()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        $validated['slug'] = Slug::make(Machine::class, $validated['name']);
        $validated['main_image'] = ImageUploader::store($request->file('main_image'), 'machines');

        $machine = Machine::create($validated);

        $this->storeGalleryImages($request, $machine);

        return redirect()
            ->route('admin.machines.edit', $machine)
            ->with('success', 'Model mesin berhasil ditambahkan. Silakan tambahkan foto lainnya bila diperlukan.');
    }

    public function edit(Machine $machine): View
    {
        $machine->load('images');

        return view('admin.machines.form', [
            'machine' => $machine,
            'categories' => Category::query()->ordered()->get(),
        ]);
    }

    public function update(Request $request, Machine $machine): RedirectResponse
    {
        $validated = $this->validated($request);

        $validated['slug'] = Slug::make(Machine::class, $validated['name'], $machine->id);

        if ($request->hasFile('main_image')) {
            ImageUploader::delete($machine->main_image);
            $validated['main_image'] = ImageUploader::store($request->file('main_image'), 'machines');
        }

        $machine->update($validated);

        $this->storeGalleryImages($request, $machine);

        return redirect()
            ->route('admin.machines.edit', $machine)
            ->with('success', 'Data mesin berhasil diperbarui.');
    }

    public function destroy(Machine $machine): RedirectResponse
    {
        ImageUploader::delete($machine->main_image);

        foreach ($machine->images as $image) {
            ImageUploader::delete($image->path);
        }

        $machine->delete();

        return redirect()
            ->route('admin.machines.index')
            ->with('success', 'Model mesin berhasil dihapus.');
    }

    /**
     * Perbarui keterangan sebuah foto galeri mesin.
     */
    public function updateImage(Request $request, Machine $machine, MachineImage $image): RedirectResponse
    {
        abort_unless($image->machine_id === $machine->id, 404);

        $validated = $request->validate([
            'caption' => ['nullable', 'string', 'max:150'],
        ]);

        $image->update($validated);

        return redirect()
            ->route('admin.machines.edit', $machine)
            ->with('success', 'Keterangan foto berhasil diperbarui.');
    }

    /**
     * Hapus satu foto pada galeri mesin.
     */
    public function destroyImage(Machine $machine, MachineImage $image): RedirectResponse
    {
        abort_unless($image->machine_id === $machine->id, 404);

        ImageUploader::delete($image->path);
        $image->delete();

        return redirect()
            ->route('admin.machines.edit', $machine)
            ->with('success', 'Foto mesin berhasil dihapus.');
    }

    /**
     * Simpan semua foto galeri yang diunggah (boleh lebih dari satu).
     */
    private function storeGalleryImages(Request $request, Machine $machine): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $captions = (array) $request->input('captions', []);
        $lastOrder = (int) $machine->images()->max('sort_order');

        foreach ($request->file('images') as $index => $file) {
            $path = ImageUploader::store($file, 'machines');

            if (! $path) {
                continue;
            }

            $machine->images()->create([
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
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:180'],
            'model_code' => ['nullable', 'string', 'max:80'],
            'function' => ['nullable', 'string', 'max:2000'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:20000'],
            'specifications' => ['nullable', 'string', 'max:20000'],
            'capacity' => ['nullable', 'string', 'max:120'],
            'power' => ['nullable', 'string', 'max:120'],
            'dimension' => ['nullable', 'string', 'max:120'],
            'weight' => ['nullable', 'string', 'max:120'],
            'material' => ['nullable', 'string', 'max:120'],
            'main_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
            'images' => ['nullable', 'array', 'max:12'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
            'captions' => ['nullable', 'array'],
            'captions.*' => ['nullable', 'string', 'max:150'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        unset($validated['images'], $validated['captions']);

        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}
