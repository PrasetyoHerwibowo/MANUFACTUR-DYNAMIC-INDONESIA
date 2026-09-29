@extends('layouts.admin')

@section('title', 'Detail Pesan')
@section('page_title', 'Detail Pesan')
@section('page_subtitle', 'Pesan dari '.$message->name)

@section('content')
    <div class="max-w-3xl">
        <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-7">
            <div class="flex flex-wrap items-start justify-between gap-4 border-b border-stone-200 pb-5">
                <div>
                    <h2 class="text-lg font-extrabold text-stone-900">{{ $message->subject ?: 'Tanpa subjek' }}</h2>
                    <p class="mt-1 text-xs text-stone-500">{{ $message->created_at->translatedFormat('d F Y, H:i') }} WIB</p>
                </div>

                @if ($message->is_read)
                    <span class="rounded-full bg-stone-200 px-3 py-1 text-[11px] font-bold text-stone-600">Sudah dibaca</span>
                @else
                    <span class="rounded-full bg-red-100 px-3 py-1 text-[11px] font-bold text-red-600">Baru</span>
                @endif
            </div>

            <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                @foreach (array_filter([
                    'Nama' => $message->name,
                    'Perusahaan' => $message->company,
                    'Email' => $message->email,
                    'Telepon' => $message->phone,
                ]) as $label => $value)
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-stone-400">{{ $label }}</dt>
                        <dd class="mt-1 text-sm font-semibold text-stone-800">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>

            <div class="mt-6 rounded-xl bg-stone-50 p-5">
                <p class="text-xs font-bold uppercase tracking-wide text-stone-400">Isi pesan</p>
                <div class="mt-3 space-y-3 text-sm leading-relaxed text-stone-700">
                    @foreach (preg_split('/\r\n|\r|\n/', $message->message) as $line)
                        @if (trim($line) !== '')
                            <p>{{ trim($line) }}</p>
                        @endif
                    @endforeach
                </div>
            </div>

            @php
                $recipient = $message->name.($message->company ? ' ('.$message->company.')' : '');
                $gmailLink = 'https://mail.google.com/mail/?view=cm&fs=1'
                    .'&to='.urlencode($message->email)
                    .'&su='.urlencode($replySubject)
                    .'&body='.urlencode($replyBody);
            @endphp

            <div class="mt-6 rounded-2xl border border-stone-200 bg-stone-50/50 p-5">
                <h3 class="text-sm font-extrabold text-stone-900">Tulis Balasan</h3>
                <p class="mt-1 text-xs text-stone-500">
                    Edit langsung di sini, lalu simpan sebagai draf atau kirim langsung ke
                    <span class="font-semibold text-stone-700">{{ $message->email }}</span>.
                </p>

                <form method="POST" action="{{ route('admin.messages.replies.store', $message) }}"
                      class="mt-4 grid gap-4">
                    @csrf

                    <div>
                        <label for="reply-subject" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-stone-400">
                            Subjek
                        </label>
                        <input type="text" name="subject" id="reply-subject" value="{{ old('subject', $replySubject) }}"
                               maxlength="200" required
                               class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm font-semibold outline-none focus:border-brand-700 focus:ring-2 focus:ring-brand-700/20">
                        @error('subject') <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="reply-body" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-stone-400">
                            Isi balasan
                        </label>
                        <textarea name="body" id="reply-body" rows="14" required
                                  maxlength="{{ $bodyMax }}"
                                  class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 font-mono text-xs leading-relaxed outline-none focus:border-brand-700 focus:ring-2 focus:ring-brand-700/20">{{ old('body', $replyBody) }}</textarea>
                        @error('body') <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                        <p class="mt-1.5 text-[11px] text-stone-400">Maksimal {{ number_format($bodyMax, 0, ',', '.') }} karakter.</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <button type="submit"
                                class="inline-flex items-center gap-2 rounded-xl bg-brand-700 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-brand-700 focus:ring-offset-2">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan Draf
                        </button>

                        <button type="submit"
                                formaction="{{ route('admin.messages.replies.send', $message) }}" formnovalidate
                                class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                onclick="return confirm('Kirim balasan ini ke {{ $message->email }}?')">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 12l16-8-6 8 6 8-16-8Z" />
                            </svg>
                            Kirim Sekarang
                        </button>

                        <a href="{{ $gmailLink }}" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-2 rounded-xl border border-stone-300 bg-white px-5 py-2.5 text-sm font-bold text-stone-600 shadow-sm transition hover:bg-stone-50 focus:outline-none focus:ring-2 focus:ring-stone-400 focus:ring-offset-2">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M2 5.5A1.5 1.5 0 0 1 3.5 4h17A1.5 1.5 0 0 1 22 5.5v13a1.5 1.5 0 0 1-1.5 1.5h-17A1.5 1.5 0 0 1 2 18.5v-13Zm2 .4v.9l8 4.9 8-4.9v-.9l-8 4.9-8-4.9Zm0 2.6v9.9c0 .3.2.5.5.5h17c.3 0 .5-.2.5-.5V8.5L12 13.4 4 8.5Z" />
                            </svg>
                            Buka di Gmail
                        </a>

                        <button type="button" data-copy-target="#reply-body"
                                class="inline-flex items-center gap-2 rounded-xl border border-stone-300 bg-white px-5 py-2.5 text-sm font-bold text-stone-600 shadow-sm transition hover:bg-stone-50 focus:outline-none focus:ring-2 focus:ring-stone-400 focus:ring-offset-2">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 9V5h10v10h-4M5 9h10v10H5V9Z" />
                            </svg>
                            Salin Teks
                        </button>
                    </div>
                </form>
            </div>

            @if ($replies->isNotEmpty())
                <div class="mt-6">
                    <h3 class="text-sm font-extrabold text-stone-900">Riwayat Balasan</h3>

                    <ul class="mt-3 space-y-3">
                        @foreach ($replies as $reply)
                            @php
                                $styles = match ($reply->status) {
                                    \App\Models\ContactMessageReply::STATUS_SENT => 'bg-emerald-50 text-emerald-700',
                                    \App\Models\ContactMessageReply::STATUS_FAILED => 'bg-red-50 text-red-700',
                                    default => 'bg-stone-200 text-stone-600',
                                };
                            @endphp

                            <li class="rounded-xl border border-stone-200 bg-white p-4">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="rounded-full px-2.5 py-0.5 text-[11px] font-bold {{ $styles }}">
                                            {{ $statusLabels[$reply->status] ?? $reply->statusLabel() }}
                                        </span>
                                        <span class="text-xs text-stone-500">
                                            {{ ($reply->sent_at ?? $reply->created_at)->translatedFormat('d F Y, H:i') }} WIB
                                        </span>
                                        @if ($reply->user)
                                            <span class="text-xs text-stone-400">oleh {{ $reply->user->name }}</span>
                                        @endif
                                    </div>

                                    <form method="POST" action="{{ route('admin.messages.replies.destroy', [$message, $reply]) }}"
                                          data-confirm="Hapus catatan balasan ini?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-bold text-red-600 hover:underline">
                                            Hapus
                                        </button>
                                    </form>
                                </div>

                                <p class="mt-2 text-sm font-semibold text-stone-800">{{ $reply->subject }}</p>

                                <details class="group mt-2">
                                    <summary class="cursor-pointer list-none text-xs font-bold text-stone-500 hover:text-stone-700">
                                        Lihat isi
                                    </summary>
                                    <pre class="mt-2 max-h-64 overflow-y-auto whitespace-pre-wrap break-words rounded-lg bg-stone-50 p-3 font-mono text-xs leading-relaxed text-stone-700">{{ $reply->body }}</pre>
                                </details>

                                @if ($reply->error)
                                    <p class="mt-2 rounded-lg bg-red-50 px-3 py-2 text-xs text-red-700">
                                        {{ $reply->error }}
                                    </p>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mt-6 flex flex-wrap items-center gap-3 border-t border-stone-200 pt-6">
                <a href="mailto:{{ $message->email }}?subject={{ urlencode($replySubject) }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-stone-300 bg-white px-5 py-2.5 text-sm font-bold text-stone-600 shadow-sm transition hover:bg-stone-50 focus:outline-none focus:ring-2 focus:ring-stone-400 focus:ring-offset-2">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5A1.5 1.5 0 0 1 4.5 6h15A1.5 1.5 0 0 1 21 7.5v9A1.5 1.5 0 0 1 19.5 18h-15A1.5 1.5 0 0 1 3 16.5v-9Zm2.3-.5 6.7 4 6.7-4H5.3Z" />
                    </svg>
                    Balas via Email Default
                </a>

                @php $wa = preg_replace('/[^0-9]/', '', (string) $message->phone); @endphp
                @if (strlen($wa) >= 9)
                    <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 rounded-xl border border-accent-300 bg-accent-100 px-5 py-2.5 text-sm font-bold text-accent-700 transition hover:bg-accent-300/60 focus:outline-none focus:ring-2 focus:ring-accent-400 focus:ring-offset-2">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Zm0 2a8 8 0 1 1-4.1 14.9l-.4-.2-2.5.6.7-2.4-.2-.4A8 8 0 0 1 12 4Zm-3.4 4.2c-.2 0-.5.1-.7.4-.2.3-.9.9-.9 2.1s.9 2.4 1 2.6c.2.2 1.7 2.7 4.2 3.7 2 .8 2.5.7 3 .6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.6-.3-1.5-.7c-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1-.2-.1-1.2-.4-2.3-1.4-.9-.8-1.4-1.7-1.6-2-.2-.2 0-.4.1-.5l.4-.5.3-.5v-.5l-.7-1.6c-.2-.4-.4-.4-.6-.4Z" />
                        </svg>
                        Balas via WhatsApp
                    </a>
                @endif

                <a href="{{ route('admin.messages.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-stone-300 px-5 py-2.5 text-sm font-bold text-stone-600 transition hover:bg-stone-50 focus:outline-none focus:ring-2 focus:ring-stone-400 focus:ring-offset-2">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19 5 14m0 0 5-5m-5 5h14" />
                    </svg>
                    Kembali
                </a>

                <form method="POST" action="{{ route('admin.messages.destroy', $message) }}"
                      class="sm:ml-auto"
                      data-confirm="Hapus pesan ini secara permanen?">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl border border-red-200 px-5 py-2.5 text-sm font-bold text-red-600 transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-300 focus:ring-offset-2">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M9 7V5h6v2m-8 0 1 12h8l1-12" />
                        </svg>
                        Hapus Pesan
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Tombol "Salin Teks" menyalin isi textarea balasan ke papan klip.
    document.querySelectorAll('[data-copy-target]').forEach((button) => {
        button.addEventListener('click', async () => {
            const field = document.querySelector(button.dataset.copyTarget);
            if (!field) {
                return;
            }

            try {
                await navigator.clipboard.writeText(field.value);
            } catch (error) {
                // Fallback untuk browser lama atau konteks non-HTTPS.
                field.select();
                document.execCommand('copy');
            }

            const original = button.textContent;
            button.textContent = 'Tersalin';

            setTimeout(() => {
                button.textContent = original;
            }, 1500);
        });
    });
</script>
@endpush
