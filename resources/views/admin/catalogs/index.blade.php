@extends('layouts.admin')

@section('title', 'Katalog')
@section('page_title', 'Katalog Produk')
@section('page_subtitle', 'Kelola katalog beserta foto halaman katalog')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <form method="GET" action="{{ route('admin.catalogs.index') }}" class="flex items-end gap-2">
            <div>
                <label for="q" class="mb-1 block text-xs font-semibold text-stone-500">Cari katalog</label>
                <input type="text" name="q" id="q" value="{{ request('q') }}" placeholder="Judul katalog"
                    class="w-56 rounded-xl border border-stone-300 bg-white px-3.5 py-2 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-200 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 dark:placeholder:text-slate-500">
            </div>

            <button type="submit" class="rounded-xl bg-stone-800 px-4 py-2 text-sm font-bold text-white transition hover:bg-stone-700">
                Cari
            </button>
        </form>

        <a href="{{ route('admin.catalogs.create') }}"
           class="rounded-xl bg-brand-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-brand-800">
            + Tambah Katalog
        </a>
    </div>

    <div class="mt-5 overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200 text-sm dark:divide-gray-700">
                <thead class="bg-stone-50 text-left text-xs font-bold uppercase tracking-wide text-stone-500 dark:bg-gray-700/50 dark:text-gray-400">
                    <tr>
                        <th class="px-5 py-3.5">Sampul</th>
                        <th class="px-5 py-3.5">Judul</th>
                        <th class="px-5 py-3.5">Edisi</th>
                        <th class="px-5 py-3.5">Foto</th>
                        <th class="px-5 py-3.5">PDF</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-stone-100 dark:divide-slate-700">
                    @forelse ($catalogs as $catalog)
                        <tr class="align-top">
                            <td class="px-5 py-3">
                                <img src="{{ $catalog->photo }}" alt="{{ $catalog->title }}" class="h-14 w-20 rounded-lg object-cover">
                            </td>

                            <td class="px-5 py-3">
                                <p class="font-bold text-stone-800 dark:text-slate-200">{{ $catalog->title }}</p>
                                <p class="text-xs text-stone-500 dark:text-slate-400">/{{ $catalog->slug }}</p>
                            </td>

                                <td class="px-5 py-3 text-stone-600 dark:text-slate-300">{{ $catalog->year ?: '—' }}</td>
                                <td class="px-5 py-3 text-stone-600 dark:text-slate-300">{{ $catalog->images_count }} foto</td>

                            <td class="px-5 py-3">
                                @if ($catalog->pdf_file)
                                    <a href="{{ \App\Support\ImageUploader::url($catalog->pdf_file) }}" target="_blank"
                                       class="text-xs font-bold text-brand-700">Buka PDF</a>
                                @else
                                    <span class="text-stone-400 dark:text-slate-500">—</span>
                                @endif
                            </td>

                            <td class="px-5 py-3">
                                @if ($catalog->is_active)
                                    <span class="rounded-full bg-accent-100 px-2.5 py-1 text-[11px] font-bold text-accent-700">Aktif</span>
                                @else
                                    <span class="rounded-full bg-stone-200 px-2.5 py-1 text-[11px] font-bold text-stone-600 dark:bg-slate-700 dark:text-slate-300">Nonaktif</span>
                                @endif
                            </td>

                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('catalogs.show', $catalog) }}" target="_blank"
                                       class="rounded-lg border border-stone-300 px-3 py-1.5 text-xs font-bold text-stone-600 transition hover:bg-stone-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
                                        Lihat
                                    </a>                                    
                                    <a href="{{ route('admin.catalogs.edit', $catalog) }}"
                                       class="rounded-lg border border-stone-300 px-3 py-1.5 text-xs font-bold text-stone-600 transition hover:bg-stone-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
                                        Edit
                                    </a>

                                    <form method="POST" action="{{ route('admin.catalogs.destroy', $catalog) }}"
                                          data-confirm="Hapus katalog {{ $catalog->title }} beserta seluruh fotonya?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-bold text-red-600 transition hover:bg-red-50 dark:border-red-400/30 dark:text-red-400 dark:hover:bg-red-400/10">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-sm text-stone-500 dark:text-slate-400">
                                Belum ada katalog. Klik tombol "Tambah Katalog" untuk memulai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $catalogs->links() }}
    </div>
@endsection
