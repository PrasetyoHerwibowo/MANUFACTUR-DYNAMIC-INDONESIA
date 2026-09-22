{{-- Daftar pesan terbaru (dipakai di dashboard) --}}
@if ($recentMessages->isNotEmpty())
    <ul class="space-y-3">
        @foreach ($recentMessages as $message)
            <li class="flex items-start gap-3 rounded-lg border border-gray-200 p-3 transition-all hover:border-coffee-400 hover:bg-coffee-50/60 dark:border-gray-700 dark:hover:border-coffee-500 dark:hover:bg-coffee-900/10">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-coffee-500 to-coffee-700 text-sm font-bold text-white">
                    {{ strtoupper(substr($message->name, 0, 1)) }}
                </span>

                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2">
                        <p class="truncate text-sm font-bold text-gray-900 dark:text-white">{{ $message->name }}</p>

                        @if (! $message->is_read)
                            <span class="shrink-0 rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-600 dark:bg-red-900/30 dark:text-red-300">
                                Baru
                            </span>
                        @endif
                    </div>

                    <p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ $message->email }}</p>

                    <p class="mt-1 truncate text-xs text-gray-600 dark:text-gray-300">
                        {{ $message->subject ?: 'Tanpa subjek' }}
                    </p>

                    <p class="mt-1 text-[11px] text-gray-400 dark:text-gray-500">{{ $message->created_at?->diffForHumans() }}</p>
                </div>

                <a href="{{ route('admin.messages.show', $message) }}"
                   class="shrink-0 rounded-lg px-2 py-1 text-xs font-medium text-coffee-600 transition hover:bg-coffee-100 hover:text-coffee-700 dark:text-coffee-400 dark:hover:bg-coffee-900/30"
                   title="Baca pesan">
                    <i aria-hidden="true" class="fas fa-eye"></i>
                </a>
            </li>
        @endforeach
    </ul>
@else
    <div class="flex flex-col items-center justify-center gap-2 py-12 text-center">
        <i aria-hidden="true" class="fas fa-envelope-open text-3xl text-gray-300 dark:text-gray-600"></i>
        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada pesan masuk.</p>
    </div>
@endif

