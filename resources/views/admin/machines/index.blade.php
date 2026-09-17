@extends('layouts.admin')

@section('title', 'Model Mesin')
@section('page_title', 'Model Mesin')
@section('page_subtitle', 'Kelola model mesin, fungsi, spesifikasi, dan galeri foto')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <form method="GET" action="{{ route('admin.machines.index') }}" class="flex flex-wrap items-end gap-2">
            <div>
                <label for="q" class="mb-1 block text-xs font-semibold text-stone-500">Cari</label>
                <input type="text" name="q" id="q" value="{{ request('q') }}" placeholder="Nama atau kode model"
                       class="w-52 rounded-xl border border-stone-300 bg-white px-3.5 py-2 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
            </div>

            <div>
                <label for="kategori" class="mb-1 block text-xs font-semibold text-stone-500">Jenis mesin</label>
                <select name="kategori" id="kategori"
                        class="w-52 rounded-xl border border-stone-300 bg-white px-3.5 py-2 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
                    <option value="">Semua jenis</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('kategori') === (string) $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="rounded-xl bg-stone-800 px-4 py-2 text-sm font-bold text-white transition hover:bg-stone-700">
                Filter
            </button>

            @if (request()->filled('q') || request()->filled('kategori'))
                <a href="{{ route('admin.machines.index') }}" class="rounded-xl border border-stone-300 px-4 py-2 text-sm font-bold text-stone-600">
                    Reset
                </a>
            @endif
        </form>

        <a href="{{ route('admin.machines.create') }}"
           class="rounded-xl bg-brand-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-brand-800">
            + Tambah Model Mesin
        </a>
    </div>

    <div class="mt-5 overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200 text-sm">
                <thead class="bg-stone-50 text-left text-xs font-bold uppercase tracking-wide text-stone-500">
                    <tr>
                        <th class="px-5 py-3.5">Foto</th>
                        <th class="px-5 py-3.5">Model Mesin</th>
                        <th class="px-5 py-3.5">Jenis Mesin</th>
                        <th class="px-5 py-3.5">Fungsi Mesin</th>
                        <th class="px-5 py-3.5">Galeri</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-stone-100">
                    @forelse ($machines as $machine)
                        <tr class="align-top">
                            <td class="px-5 py-3">
                                <img src="{{ $machine->photo }}" alt="{{ $machine->name }}" class="h-14 w-20 rounded-lg object-cover">
                            </td>

                            <td class="px-5 py-3">
                                <p class="font-bold text-stone-800">{{ $machine->name }}</p>
                                @if ($machine->model_code)
                                    <p class="text-xs text-stone-500">Kode: {{ $machine->model_code }}</p>
                                @endif
                                @if ($machine->is_featured)
                                    <span class="mt-1 inline-block rounded-full bg-accent-100 px-2 py-0.5 text-[10px] font-bold text-accent-700">Unggulan</span>
                                @endif
                            </td>

                            <td class="px-5 py-3 text-stone-600">{{ $machine->category?->name ?? '—' }}</td>

                            <td class="max-w-xs px-5 py-3 text-xs leading-relaxed text-stone-500">
                                {{ \Illuminate\Support\Str::limit($machine->function, 90) ?: '—' }}
                            </td>

                            <td class="px-5 py-3 text-stone-600">{{ $machine->images_count }} foto</td>

                            <td class="px-5 py-3">
                                @if ($machine->is_active)
                                    <span class="rounded-full bg-accent-100 px-2.5 py-1 text-[11px] font-bold text-accent-700">Aktif</span>
                                @else
                                    <span class="rounded-full bg-stone-200 px-2.5 py-1 text-[11px] font-bold text-stone-600">Nonaktif</span>
                                @endif
                            </td>

                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('products.show', $machine) }}" target="_blank"
                                       class="rounded-lg border border-stone-300 px-3 py-1.5 text-xs font-bold text-stone-600 transition hover:bg-stone-50">
                                        Lihat
                                    </a>

                                    <a href="{{ route('admin.machines.edit', $machine) }}"
                                       class="rounded-lg border border-stone-300 px-3 py-1.5 text-xs font-bold text-stone-600 transition hover:bg-stone-50">
                                        Edit
                                    </a>

                                    <form method="POST" action="{{ route('admin.machines.destroy', $machine) }}"
                                          data-confirm="Hapus model mesin {{ $machine->name }} beserta seluruh fotonya?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-bold text-red-600 transition hover:bg-red-50">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-sm text-stone-500">
                                Belum ada model mesin yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $machines->links() }}
    </div>
@endsection