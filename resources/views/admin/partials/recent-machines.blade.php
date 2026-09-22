{{-- Tabel model mesin terbaru (dipakai di dashboard) --}}
<div class="glass-card overflow-hidden rounded-2xl">
    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-200 p-6 dark:border-gray-700">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Model Mesin Terbaru</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Lima model yang terakhir diperbarui</p>
        </div>

        <a href="{{ route('admin.machines.create') }}"
           class="flex shrink-0 items-center gap-2 rounded-lg bg-coffee-600 px-4 py-2 text-sm font-medium text-white transition-all hover:bg-coffee-700">
            <i aria-hidden="true" class="fas fa-plus"></i>
            Tambah Model
        </a>
    </div>

    @if ($recentMachines->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Model</th>
                        <th class="hidden px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 md:table-cell">Jenis Mesin</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="hidden px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 lg:table-cell">Diperbarui</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($recentMachines as $machine)
                        <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $machine->photo }}" alt="{{ $machine->name }}"
                                         class="h-10 w-10 shrink-0 rounded-lg object-cover">

                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-bold text-gray-900 dark:text-white">{{ $machine->name }}</p>
                                        <p class="truncate text-xs text-gray-500 dark:text-gray-400">
                                            {{ $machine->model_code ?: 'Tanpa kode model' }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="hidden px-6 py-4 text-sm text-gray-600 dark:text-gray-300 md:table-cell">
                                {{ $machine->category?->name ?: 'Tanpa jenis mesin' }}
                            </td>

                            <td class="px-6 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-bold {{ $machine->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
                                    {{ $machine->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>

                            <td class="hidden px-6 py-4 text-sm text-gray-500 dark:text-gray-400 lg:table-cell">
                                {{ $machine->updated_at?->diffForHumans() }}
                            </td>

                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.machines.edit', $machine) }}"
                                   class="inline-flex items-center gap-1.5 text-sm font-medium text-coffee-600 transition hover:text-coffee-700 dark:text-coffee-400">
                                    <i aria-hidden="true" class="fas fa-pen"></i> Edit
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="flex flex-col items-center justify-center gap-2 py-12 text-center">
            <i aria-hidden="true" class="fas fa-cogs text-3xl text-gray-300 dark:text-gray-600"></i>
            <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada data mesin.</p>
            <a href="{{ route('admin.machines.create') }}"
               class="text-xs font-semibold text-coffee-600 hover:text-coffee-700 dark:text-coffee-400">
                Tambah model mesin &rarr;
            </a>
        </div>
    @endif
</div>

