<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Machine;
use App\Support\ImageUploader;
use App\Support\Slug;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MachineController extends Controller
{
    /** Batas panjang teks setiap kolom formulir. */
    private const LENGTHS = [
        'name' => 100,
        'model_code' => 50,
        'function' => 100,
        'short_description' => 150,
        'capacity' => 20,
        'power' => 20,
        'dimension' => 20,
        'weight' => 10,
        'material' => 100,
        'specifications' => 100,
        'description' => 500,
    ];

    /**
     * Daftar model mesin.
     */
    public function index(Request $request): View
    {
        $machines = Machine::query()
            ->with('category')
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

        // Urutan tampil dan status tampil tidak lagi diatur dari form: data
        // baru langsung tampil di website dan diletakkan paling belakang.
        $validated['sort_order'] = (int) Machine::query()->max('sort_order') + 1;
        $validated['is_active'] = true;

        Machine::create($validated);

        return redirect()
            ->route('admin.machines.index')
            ->with('success', 'Model mesin berhasil ditambahkan.');
    }

    public function edit(Machine $machine): View
    {
        return view('admin.machines.form', [
            'machine' => $machine,
            'categories' => Category::query()->ordered()->get(),
        ]);
    }

    public function update(Request $request, Machine $machine): RedirectResponse
    {
        $validated = $this->validated($request, $machine);

        $validated['slug'] = Slug::make(Machine::class, $validated['name'], $machine->id);

        if ($request->hasFile('main_image')) {
            ImageUploader::delete($machine->main_image);
            $validated['main_image'] = ImageUploader::store($request->file('main_image'), 'machines');
        }

        // Status tampil tidak lagi diatur dari form: data yang disunting selalu
        // tampil di website. Urutan tampil dan penandaan unggulan dibiarkan.
        $validated['is_active'] = true;

        $machine->update($validated);

        return redirect()
            ->route('admin.machines.index')
            ->with('success', 'Model mesin berhasil diperbarui.');
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
     * Rapikan input, lalu validasi sesuai aturan pengisian form.
     *
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Machine $machine = null): array
    {
        $request->merge([
            'name' => $this->trimmed($request->input('name')),
            'model_code' => $this->trimmed($request->input('model_code')),
            'function' => $this->trimmed($request->input('function')),
            'short_description' => $this->trimmed($request->input('short_description')),
            'capacity' => $this->trimmed($request->input('capacity')),
            'power' => $this->trimmed($request->input('power')),
            'dimension' => $this->trimmed($request->input('dimension')),
            'weight' => $this->trimmed($request->input('weight')),
            'material' => $this->trimmed($request->input('material')),
            'specifications' => $this->trimmed($request->input('specifications')),
            'description' => $this->trimmed($request->input('description')),
        ]);

        $categoryId = $request->integer('category_id');

        return $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => array_merge(
                ['required', 'string', 'max:'.self::LENGTHS['name']],
                $this->uniqueRules('name', $categoryId, $machine, $request->input('name')),
            ),
            'model_code' => array_merge(
                ['nullable', 'string', 'max:'.self::LENGTHS['model_code']],
                $this->uniqueRules('model_code', $categoryId, $machine, $request->input('model_code')),
            ),
            'function' => ['nullable', 'string', 'max:'.self::LENGTHS['function']],
            'short_description' => ['nullable', 'string', 'max:'.self::LENGTHS['short_description']],
            'capacity' => ['nullable', 'string', 'max:'.self::LENGTHS['capacity']],
            'power' => ['nullable', 'string', 'max:'.self::LENGTHS['power']],
            'dimension' => ['nullable', 'string', 'max:'.self::LENGTHS['dimension']],
            'weight' => ['nullable', 'string', 'max:'.self::LENGTHS['weight']],
            'material' => ['nullable', 'string', 'max:'.self::LENGTHS['material']],
            'specifications' => ['nullable', 'string', 'max:'.self::LENGTHS['specifications']],
            'description' => ['nullable', 'string', 'max:'.self::LENGTHS['description']],
            'main_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
        ], $this->messages());
    }

    /**
     * Aturan nama/kode model tidak boleh sama dengan model mesin lain pada
     * jenis mesin (kategori) yang sama.
     *
     * Saat menyunting, pemeriksaan dilewati bila admin tidak mengubah nama,
     * kode, maupun jenis mesinnya. Dengan begitu baris yang sudah kadung
     * duplikat (mis. input sebelum aturan ini ada) tetap dapat diperbaiki,
     * misalnya untuk memendekkan teks yang melebihi batas baru.
     *
     * @return array<int, mixed>
     */
    private function uniqueRules(string $column, int $categoryId, ?Machine $machine, ?string $value): array
    {
        if ($machine !== null
            && (int) $machine->getAttribute('category_id') === $categoryId
            && (string) $machine->getAttribute($column) === (string) $value) {
            return [];
        }

        $rule = Rule::unique('machines', $column)->where('category_id', $categoryId);

        return [$machine === null ? $rule : $rule->ignore($machine->getKey())];
    }

    /** Rapikan spasi di ujung teks dan ubah teks kosong menjadi null. */
    private function trimmed(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    /**
     * Pesan validasi berbahasa Indonesia agar admin tahu aturan pengisian.
     *
     * @return array<string, string>
     */
    private function messages(): array
    {
        return [
            'category_id.required' => 'Jenis mesin wajib dipilih.',
            'category_id.exists' => 'Jenis mesin yang dipilih tidak ditemukan.',
            'name.required' => 'Nama model mesin wajib diisi.',
            'name.max' => 'Nama model mesin maksimal '.self::LENGTHS['name'].' karakter.',
            'name.unique' => 'Model mesin dengan nama ini sudah ada pada jenis mesin yang sama.',
            'model_code.max' => 'Kode / tipe model maksimal '.self::LENGTHS['model_code'].' karakter.',
            'model_code.unique' => 'Kode model ":input" sudah dipakai model mesin lain pada jenis mesin yang sama.',
            'function.max' => 'Fungsi mesin maksimal '.self::LENGTHS['function'].' karakter.',
            'short_description.max' => 'Deskripsi singkat maksimal '.self::LENGTHS['short_description'].' karakter.',
            'capacity.max' => 'Kapasitas maksimal '.self::LENGTHS['capacity'].' karakter.',
            'power.max' => 'Daya maksimal '.self::LENGTHS['power'].' karakter.',
            'dimension.max' => 'Dimensi maksimal '.self::LENGTHS['dimension'].' karakter.',
            'weight.max' => 'Berat maksimal '.self::LENGTHS['weight'].' karakter.',
            'material.max' => 'Material maksimal '.self::LENGTHS['material'].' karakter.',
            'specifications.max' => 'Spesifikasi tambahan maksimal '.self::LENGTHS['specifications'].' karakter.',
            'description.max' => 'Deskripsi lengkap maksimal '.self::LENGTHS['description'].' karakter.',
            'main_image.image' => 'Foto mesin harus berupa berkas gambar.',
            'main_image.mimes' => 'Foto mesin harus berformat JPG, PNG, atau WEBP.',
            'main_image.max' => 'Ukuran foto mesin maksimal 6 MB.',
        ];
    }
}
