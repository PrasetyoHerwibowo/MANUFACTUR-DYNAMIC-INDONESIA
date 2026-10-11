@extends('layouts.public')

@section('title', 'Kontak — '.$company->name)

@section('content')
    <x-page-header
        title="Hubungi Kami"
        subtitle="Sampaikan kebutuhan mesin Anda. Tim kami akan merespons pada hari kerja Senin–Sabtu."
        :crumbs="[['label' => 'Beranda', 'url' => route('home')], ['label' => 'Kontak']]" />

    <section class="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
        <div class="grid gap-8 md:grid-cols-2">
            {{-- Informasi kontak --}}
            <div class="rounded-2xl border border-stone-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow-sm sm:p-8">
                <h2 class="text-lg font-extrabold text-stone-900 dark:text-white">Informasi Kontak</h2>
                <p class="mt-1 text-sm text-stone-500 dark:text-gray-400">Hubungi langsung tim kami melalui kontak di bawah ini.</p>

                <ul class="mt-6 space-y-5 text-sm text-stone-600 dark:text-gray-300">
                    @if ($company->address || $company->city)
                        <li class="flex items-start gap-3.5">
                            <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400">
                                <i class="fa-solid fa-location-dot"></i>
                            </span>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-stone-400 dark:text-gray-500">Alamat</p>
                                <p class="mt-1 leading-relaxed">{{ trim(($company->address ? $company->address.', ' : '').($company->city ?? ''), ', ') }}</p>
                            </div>
                        </li>
                    @endif

                    @if ($company->phone)
                        <li class="flex items-start gap-3.5">
                            <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400">
                                <i class="fa-solid fa-phone"></i>
                            </span>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-stone-400 dark:text-gray-500">Telepon Perusahaan</p>
                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $company->phone) }}" class="mt-1 block font-bold text-stone-800 dark:text-white transition hover:text-red-600">
                                    {{ $company->phone }}
                                </a>
                            </div>
                        </li>
                    @endif

                    @if ($company->email)
                        <li class="flex items-start gap-3.5">
                            <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400">
                                <i class="fa-solid fa-envelope"></i>
                            </span>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-stone-400 dark:text-gray-500">Email</p>
                                <a href="mailto:{{ $company->email }}" class="mt-1 block font-semibold text-red-600 dark:text-red-400 hover:underline">
                                    {{ $company->email }}
                                </a>
                            </div>
                        </li>
                    @endif

                    @php $wa = preg_replace('/[^0-9]/', '', (string) $company->whatsapp); @endphp
                    @if ($wa)
                        <li class="flex items-start gap-3.5">
                            <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400">
                                <i class="fa-brands fa-whatsapp text-lg"></i>
                            </span>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-stone-400 dark:text-gray-500">WhatsApp</p>
                                <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener"
                                   class="mt-1.5 inline-flex items-center gap-2 rounded-xl bg-green-600 px-4 py-2 text-xs font-bold text-white transition hover:bg-green-700">
                                    <i class="fa-brands fa-whatsapp"></i> Chat WhatsApp
                                </a>
                            </div>
                        </li>
                    @endif
                </ul>
            </div>

            {{-- Jam Operasional & Map --}}
            <div class="space-y-6">
                <div class="rounded-2xl border border-stone-200 dark:border-gray-700 bg-stone-50 dark:bg-gray-800/40 p-6">
                    <h2 class="text-base font-extrabold text-stone-900 dark:text-white">Jam Operasional</h2>
                    <ul class="mt-4 space-y-2 text-sm text-stone-600 dark:text-gray-300">
                        <li class="flex justify-between gap-4"><span>Senin – Jumat</span><span class="font-semibold text-stone-800 dark:text-white">08.00 – 17.00</span></li>
                        <li class="flex justify-between gap-4"><span>Sabtu</span><span class="font-semibold text-stone-800 dark:text-white">08.00 – 13.00</span></li>
                        <li class="flex justify-between gap-4"><span>Minggu &amp; Hari Libur</span><span class="font-semibold text-red-600 dark:text-red-400">Tutup</span></li>
                    </ul>
                </div>

                @if ($company->map_embed)
                    <div class="overflow-hidden rounded-2xl border border-stone-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
                        {!! $company->map_embed !!}
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
