@extends('layouts.admin')

@section('title', 'Pesan Masuk')
@section('page_title', 'Pesan Masuk')
@section('page_subtitle', 'Pesan dari formulir kontak pengunjung website')

@section('content')
    <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200 text-sm">
                <thead class="bg-stone-50 text-left text-xs font-bold uppercase tracking-wide text-stone-500">
                    <tr>
                        <th class="px-5 py-3.5">Pengirim</th>
                        <th class="px-5 py-3.5">Kontak</th>
                        <th class="px-5 py-3.5">Subjek</th>
                        <th class="px-5 py-3.5">Tanggal</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-stone-100">
                    @forelse ($messages as $message)
                        <tr>
                            <td class="px-5 py-3">
                                <p class="font-bold text-stone-800">{{ $message->name }}</p>
                                <p class="text-xs text-stone-500">{{ $message->company ?: '—' }}</p>
                            </td>

                            <td class="px-5 py-3 text-xs text-stone-600">
                                <p>{{ $message->email }}</p>
                                <p class="text-stone-400">{{ $message->phone ?: '—' }}</p>
                            </td>

                            <td class="px-5 py-3 text-stone-600">{{ $message->subject ?: 'Tanpa subjek' }}</td>
                            <td class="px-5 py-3 text-xs text-stone-500">{{ $message->created_at->translatedFormat('d M Y H:i') }}</td>

                            <td class="px-5 py-3">
                                @if ($message->is_read)
                                    <span class="rounded-full bg-stone-200 px-2.5 py-1 text-[11px] font-bold text-stone-600">Dibaca</span>
                                @else
                                    <span class="rounded-full bg-red-100 px-2.5 py-1 text-[11px] font-bold text-red-600">Baru</span>
                                @endif
                            </td>

                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.messages.show', $message) }}"
                                       class="rounded-lg border border-stone-300 px-3 py-1.5 text-xs font-bold text-stone-600 transition hover:bg-stone-50">
                                        Baca
                                    </a>

                                    <form method="POST" action="{{ route('admin.messages.destroy', $message) }}"
                                          data-confirm="Hapus pesan dari {{ $message->name }}?">
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
                            <td colspan="6" class="px-5 py-10 text-center text-sm text-stone-500">
                                Belum ada pesan masuk dari pengunjung website.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $messages->links() }}
    </div>
@endsection
