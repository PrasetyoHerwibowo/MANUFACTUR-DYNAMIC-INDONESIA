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

            <div class="mt-6 flex flex-wrap items-center gap-3">
                <a href="mailto:{{ $message->email }}?subject={{ urlencode('Re: '.($message->subject ?: 'Pesan dari website')) }}"
                   class="rounded-xl bg-brand-700 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-brand-800">
                    Balas via Email
                </a>

                @php $wa = preg_replace('/[^0-9]/', '', (string) $message->phone); @endphp
                @if (strlen($wa) >= 9)
                    <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener"
                       class="rounded-xl border border-accent-300 bg-accent-100 px-5 py-2.5 text-sm font-bold text-accent-700 transition hover:bg-accent-300/60">
                        Balas via WhatsApp
                    </a>
                @endif

                <a href="{{ route('admin.messages.index') }}"
                   class="rounded-xl border border-stone-300 px-5 py-2.5 text-sm font-bold text-stone-600 transition hover:bg-stone-50">
                    Kembali
                </a>

                <form method="POST" action="{{ route('admin.messages.destroy', $message) }}"
                      data-confirm="Hapus pesan ini secara permanen?">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="rounded-xl border border-red-200 px-5 py-2.5 text-sm font-bold text-red-600 transition hover:bg-red-50">
                        Hapus Pesan
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
