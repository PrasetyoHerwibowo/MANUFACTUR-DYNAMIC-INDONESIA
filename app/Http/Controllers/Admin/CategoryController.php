<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Support\ImageUploader;
use App\Support\Sanitize;
use App\Support\Slug;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Nama jenis mesin: huruf, spasi, tanda hubung, dan tanda &.
     *
     * Tanda & tetap diizinkan karena data lama seperti "Mesin Sangrai &
     * Penepung" harus dapat disunting kembali. Angka maupun karakter lain
     * (termasuk <, >, ", ') ditolak.
     */
    private const NAME_PATTERN = '/^[\p{L}][\p{L}\s&\-]*$/u';

    /**
     * Tagline singkat: hanya huruf dan angka (spasi sebagai pemisah kata).
     */
    private const TAGLINE_PATTERN = '/^[\p{L}\p{N}][\p{L}\p{N}\s]*$/u';

    /**
     * Daftar jenis mesin.
     */
    public function index(): View
    {
        $categories = Category::query()
            ->ordered()
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

        // Foto jenis mesin sudah dihapus dari form, sehingga kolom "image"
        // tidak lagi diisi di sini.
        // Jenis mesin yang ditambahkan admin langsung tampil di website.
        // Urutan tidak lagi diatur manual: data baru diletakkan paling belakang.
        $validated['is_active'] = true;
        $validated['sort_order'] = (int) Category::query()->max('sort_order') + 1;

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

        // Foto jenis mesin sudah dihapus dari form dan dari struktur tabel,
        // jadi tidak ada berkas foto yang perlu diurus saat menyunting.
        // Jenis mesin yang disunting admin selalu tampil di website; opsi
        // urutan dan "tampilkan di website" tidak lagi ditampilkan di form.
        $validated['is_active'] = true;

        $category->update($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Jenis mesin berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
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
     * Bersihkan input, lalu validasi sesuai aturan pengisian form.
     *
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        // Sanitasi dijalankan lebih dahulu (tag HTML, entitas, dan karakter
        // kendali dibuang) supaya pola karakter di bawah memeriksa teks bersih.
        $request->merge([
            'name' => Sanitize::text($request->input('name')),
            'tagline' => Sanitize::text($request->input('tagline')),
            'description' => Sanitize::text($request->input('description')),
        ]);

        return $request->validate([
            'name' => ['required', 'string', 'max:100', 'regex:'.self::NAME_PATTERN],
            'tagline' => ['nullable', 'string', 'max:50', 'regex:'.self::TAGLINE_PATTERN],
            'description' => ['nullable', 'string', 'max:150'],
        ], $this->messages());
    }

    /**
     * Pesan validasi berbahasa Indonesia agar admin tahu aturan pengisian.
     *
     * @return array<string, string>
     */
    private function messages(): array
    {
        return [
            'name.required' => 'Nama jenis mesin wajib diisi.',
            'name.max' => 'Nama jenis mesin maksimal 100 karakter.',
            'name.regex' => 'Nama jenis mesin hanya boleh berisi huruf, spasi, tanda hubung (-), dan tanda &.',
            'tagline.max' => 'Tagline singkat maksimal 50 karakter.',
            'tagline.regex' => 'Tagline singkat hanya boleh berisi huruf dan angka.',
            'description.max' => 'Deskripsi maksimal 150 karakter.',
        ];
    }
}
