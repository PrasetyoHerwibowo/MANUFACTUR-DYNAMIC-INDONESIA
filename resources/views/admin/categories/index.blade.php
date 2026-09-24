@extends('layouts.admin')

@section('title', 'Jenis Mesin')
@section('page_title', 'Jenis Mesin')
@section('page_subtitle', 'Kelola kategori/ jenis mesin')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-stone-500">Total {{ $categories->total() }} jenis mesin.</p>

        <a href="{{ route('admin.categories.create') }}"
           class="rounded-xl bg-brand-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-brand-800">
            + Tambah Jenis Mesin
        </a>
    </div>

    <div class="mt-5 overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200 text-sm dark:divide-gray-700">
                <thead
                    class="border-bg-stone-50 text-left text-xs font-bold uppercase tracking-wide text-stone-500 dark:bg-gray-700/50 dark:text-gray-400">
                    <tr>
                        <th class="px-5 py-3.5">Nama</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-stone-100 dark:divide-gray-700">
                    @forelse ($categories as $category)
                        <tr class="align-middle">
                            <td class="px-5 py-3">
                                <p class="font-bold text-stone-800 dark:text-gray-300">
                                    {{ $category->name }}
                                </p>
                                <p class="text-xs text-stone-500 dark:text-slate-400">
                                    /{{ $category->slug }}
                                </p>
                            </td>

                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.categories.edit', $category) }}"
                                       class="rounded-lg border border-stone-300 px-3 py-1.5 text-xs font-bold text-stone-600 transition hover:bg-stone-50">
                                        Edit
                                    </a>

                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                          data-confirm="Hapus jenis mesin {{ $category->name }} beserta seluruh model mesin di dalamnya?">
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
                            <td colspan="2" class="px-5 py-10 text-center text-sm text-stone-500 dark:text-slate-400">
                                Belum ada jenis mesin. Klik tombol "Tambah Jenis Mesin" untuk memulai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $categories->links() }}
    </div>
@endsection
