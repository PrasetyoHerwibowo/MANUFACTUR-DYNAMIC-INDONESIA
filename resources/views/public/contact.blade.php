@extends('layouts.public')

@section('title', 'Kontak — '.$company->name)

@section('content')
    <x-page-header
        title="Hubungi Kami"
        subtitle="Sampaikan kebutuhan mesin Anda. Tim kami akan merespons pada hari kerja Senin–Sabtu."
        :crumbs="[['label' => 'Beranda', 'url' => route('home')], ['label' => 'Kontak']]"
    />

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
        <div class="grid gap-10 lg:grid-cols-5">
            {{-- Formulir --}}
            <div class="lg:col-span-3">
                <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
                    <h2 class="text-lg font-extrabold text-stone-900">Formulir permintaan penawaran</h2>
                    <p class="mt-2 text-sm text-stone-500">Kolom bertanda <span class="text-red-500">*</span> wajib diisi.</p>

                    <form method="POST" action="{{ route('contact.send') }}" class="mt-6 grid gap-5 sm:grid-cols-2">
                        @csrf

                        <div>
                            <label for="name" class="mb-1.5 block text-sm font-semibold text-stone-700">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
                            @error('name') <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="email" class="mb-1.5 block text-sm font-semibold text-stone-700">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                   class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
                            @error('email') <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="phone" class="mb-1.5 block text-sm font-semibold text-stone-700">Nomor Telepon / WhatsApp</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                   class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
                            @error('phone') <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="company" class="mb-1.5 block text-sm font-semibold text-stone-700">Nama Perusahaan</label>
                            <input type="text" name="company" id="company" value="{{ old('company') }}"
                                   class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
                            @error('company') <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="subject" class="mb-1.5 block text-sm font-semibold text-stone-700">Subjek</label>
                            <input type="text" name="subject" id="subject"
                                   value="{{ old('subject', request('mesin') ? 'Permintaan penawaran mesin: '.str_replace('-', ' ', request('mesin')) : '') }}"
                                   class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
                            @error('subject') <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="message" class="mb-1.5 block text-sm font-semibold text-stone-700">
                                Pesan <span class="text-red-500">*</span>
                            </label>
                            <textarea name="message" id="message" rows="6" required
                                      class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm leading-relaxed outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-200">{{ old('message') }}</textarea>
                            @error('message') <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <button type="submit"
                                    class="w-full rounded-xl bg-brand-700 px-6 py-3 text-sm font-bold text-white transition hover:bg-brand-800 sm:w-auto">
                                Kirim Pesan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Informasi kontak --}}
            <aside class="space-y-5 lg:col-span-2">
                <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                    <h2 class="text-base font-extrabold text-stone-900">Informasi kontak</h2>

                    <ul class="mt-5 space-y-4 text-sm text-stone-600">
                        @if ($company->address || $company->city)
                            <li>
                                <p class="text-xs font-bold uppercase tracking-wide text-stone-400">Alamat</p>
                                <p class="mt-1 leading-relaxed">{{ trim(($company->address ? $company->address.', ' : '').($company->city ?? ''), ', ') }}</p>
                            </li>
                        @endif

                        @if ($company->phone)
                            <li>
                                <p class="text-xs font-bold uppercase tracking-wide text-stone-400">Telepon</p>
                                <p class="mt-1">{{ $company->phone }}</p>
                            </li>
                        @endif

                        @if ($company->email)
                            <li>
                                <p class="text-xs font-bold uppercase tracking-wide text-stone-400">Email</p>
                                <a href="mailto:{{ $company->email }}" class="mt-1 block font-semibold text-brand-700">{{ $company->email }}</a>
                            </li>
                        @endif

                        @php $wa = preg_replace('/[^0-9]/', '', (string) $company->whatsapp); @endphp
                        @if ($wa)
                            <li>
                                <p class="text-xs font-bold uppercase tracking-wide text-stone-400">WhatsApp</p>
                                <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener"
                                   class="mt-2 inline-block rounded-xl bg-accent-500 px-4 py-2 text-xs font-bold text-white transition hover:bg-accent-600">
                                    Chat WhatsApp
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>

                <div class="rounded-2xl border border-stone-200 bg-stone-50 p-6">
                    <h2 class="text-base font-extrabold text-stone-900">Jam operasional</h2>
                    <ul class="mt-4 space-y-2 text-sm text-stone-600">
                        <li class="flex justify-between gap-4"><span>Senin – Jumat</span><span class="font-semibold">08.00 – 17.00</span></li>
                        <li class="flex justify-between gap-4"><span>Sabtu</span><span class="font-semibold">08.00 – 13.00</span></li>
                        <li class="flex justify-between gap-4"><span>Minggu &amp; hari libur</span><span class="font-semibold">Tutup</span></li>
                    </ul>
                </div>

                @if ($company->map_embed)
                    <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
                        {!! $company->map_embed !!}
                    </div>
                @endif
            </aside>
        </div>
    </section>
@endsection
