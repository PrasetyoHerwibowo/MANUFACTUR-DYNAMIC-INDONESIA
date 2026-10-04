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
        'name'             => 100,
        'model_code'       => 50,
        'function'         => 375,
        'short_description'=> 150,
        // Nilai di bawah adalah panjang string gabungan yang digenerate, bukan input langsung.
        // capacity:  "99999999 kg/999 jam" = 20 char  → 30 aman
        // power:     "9999.99 kW / 99 phase" = 22 char → 30 aman
        // dimension: "9999.9 x 9999.9 x 9999.9 cm" = 28 char → 40 aman
        // weight:    "999999.9 kg" = 11 char → 15 aman
        'capacity'         => 30,
        'power'            => 30,
        'dimension'        => 40,
        'weight'           => 15,
        'material'         => 100,
        'specifications'   => 375,
        'description'      => 500,
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
        $capacityAmount = $this->trimmed($request->input('capacity_amount'));
        $capacityTime = $this->trimmed($request->input('capacity_time'));

        $powerKw = $this->trimmed($request->input('power_kw'));
        $powerPhase = $this->trimmed($request->input('power_phase'));

        $dimensionLength = $this->trimmed($request->input('dimension_length'));
        $dimensionWidth = $this->trimmed($request->input('dimension_width'));
        $dimensionHeight = $this->trimmed($request->input('dimension_height'));

        $weightAmount = $this->trimmed($request->input('weight_amount'));

        // Format Kapasitas: "500 kg/jam" atau "500 kg/2 jam"
        $capacity = null;
        if ($capacityAmount !== null && $capacityAmount !== '') {
            $timeInt = (int) ($capacityTime ?? 1);
            $capacity = $timeInt <= 1
                ? $capacityAmount.' kg/jam'
                : $capacityAmount.' kg/'.$timeInt.' jam';
        }

        // Format Daya: "5.5 kW / 3 phase" atau "5.5 kW" (desimal pakai titik)
        $power = null;
        if ($powerKw !== null && $powerKw !== '') {
            if ($powerPhase !== null && $powerPhase !== '') {
                $power = $powerKw.' kW / '.$powerPhase.' phase';
            } else {
                $power = $powerKw.' kW';
            }
        }

        // Format Dimensi: "180 x 90 x 140 cm" (desimal pakai titik)
        $dimension = null;
        if ($dimensionLength !== null && $dimensionWidth !== null && $dimensionHeight !== null) {
            $dimension = $dimensionLength.' x '.$dimensionWidth.' x '.$dimensionHeight.' cm';
        }

        // Format Berat: "320 kg" (desimal pakai titik)
        $weight = null;
        if ($weightAmount !== null && $weightAmount !== '') {
            $weight = $weightAmount.' kg';
        }

        $request->merge([
            'name' => $this->trimmed($request->input('name')),
            'model_code' => $this->trimmed($request->input('model_code')),
            'function' => $this->trimmed($request->input('function')),
            'short_description' => $this->trimmed($request->input('short_description')),
            'capacity_amount' => $capacityAmount,
            'capacity_time' => $capacityTime,
            'capacity' => $capacity,
            'power_kw' => $powerKw,
            'power_phase' => $powerPhase,
            'power' => $power,
            'dimension_length' => $dimensionLength,
            'dimension_width' => $dimensionWidth,
            'dimension_height' => $dimensionHeight,
            'dimension' => $dimension,
            'weight_amount' => $weightAmount,
            'weight' => $weight,
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
            'capacity_amount' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'capacity_time' => ['nullable', 'integer', 'min:1', 'max:999'],
            'capacity' => ['nullable', 'string', 'max:'.self::LENGTHS['capacity']],
            'power_kw' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'power_phase' => ['nullable', 'integer', 'min:1', 'max:99'],
            'power' => ['nullable', 'string', 'max:'.self::LENGTHS['power']],
            'dimension_length' => ['nullable', 'numeric', 'min:0', 'max:99999'],
            'dimension_width' => ['nullable', 'numeric', 'min:0', 'max:99999'],
            'dimension_height' => ['nullable', 'numeric', 'min:0', 'max:99999'],
            'dimension' => ['nullable', 'string', 'max:'.self::LENGTHS['dimension']],
            'weight_amount' => ['nullable', 'numeric', 'min:0', 'max:999999'],
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
        if ($value === null) {
            return null;
        }

        if (is_numeric($value) || is_string($value)) {
            $value = trim((string) $value);

            return $value === '' ? null : $value;
        }

        return null;
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
            'capacity_amount.numeric' => 'Kapasitas (kg) harus berupa angka.',
            'capacity_amount.min' => 'Kapasitas (kg) tidak boleh kurang dari 0.',
            'capacity_amount.max' => 'Kapasitas (kg) maksimal 99.999.999 kg.',
            'capacity_time.integer' => 'Waktu (jam) harus berupa angka bulat.',
            'capacity_time.min' => 'Waktu (jam) minimal 1 jam.',
            'capacity_time.max' => 'Waktu (jam) maksimal 999 jam.',
            'capacity.max' => 'Kapasitas maksimal '.self::LENGTHS['capacity'].' karakter.',
            'power_kw.numeric' => 'Daya (kW) harus berupa angka.',
            'power_kw.min' => 'Daya (kW) tidak boleh kurang dari 0.',
            'power_kw.max' => 'Daya (kW) maksimal 99.999 kW.',
            'power_phase.integer' => 'Phase listrik harus berupa angka.',
            'power_phase.min' => 'Phase listrik minimal 1 phase.',
            'power_phase.max' => 'Phase listrik maksimal 99 phase.',
            'power.max' => 'Daya maksimal '.self::LENGTHS['power'].' karakter.',
            'dimension_length.numeric' => 'Panjang dimensi (cm) harus berupa angka.',
            'dimension_length.min' => 'Panjang dimensi (cm) tidak boleh kurang dari 0.',
            'dimension_length.max' => 'Panjang dimensi (cm) maksimal 99.999 cm.',
            'dimension_width.numeric' => 'Lebar dimensi (cm) harus berupa angka.',
            'dimension_width.min' => 'Lebar dimensi (cm) tidak boleh kurang dari 0.',
            'dimension_width.max' => 'Lebar dimensi (cm) maksimal 99.999 cm.',
            'dimension_height.numeric' => 'Tinggi dimensi (cm) harus berupa angka.',
            'dimension_height.min' => 'Tinggi dimensi (cm) tidak boleh kurang dari 0.',
            'dimension_height.max' => 'Tinggi dimensi (cm) maksimal 99.999 cm.',
            'dimension.max' => 'Dimensi maksimal '.self::LENGTHS['dimension'].' karakter.',
            'weight_amount.numeric' => 'Berat (kg) harus berupa angka.',
            'weight_amount.min' => 'Berat (kg) tidak boleh kurang dari 0.',
            'weight_amount.max' => 'Berat (kg) maksimal 999.999 kg.',
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
