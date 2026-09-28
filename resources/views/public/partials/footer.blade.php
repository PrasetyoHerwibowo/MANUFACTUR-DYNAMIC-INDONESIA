@php
    $whatsapp = preg_replace('/[^0-9]/', '', (string) $company->whatsapp);
@endphp

<footer class="mt-24 bg-gray-100 text-slate-600 transition-colors duration-300 dark:bg-[#0B111A] dark:text-gray-300">
    <div
        class="mx-auto grid max-w-7xl gap-10 border-t border-gray-200 px-4 py-14 dark:border-slate-800 sm:px-6 lg:grid-cols-4 lg:px-8">
        <div class="lg:col-span-2">
            <div class="flex items-center gap-3">
                @if ($company->logoUrl())
                    <img src="{{ $company->logoUrl() }}" alt="Logo {{ $company->name }}"
                        class="h-12 w-12 rounded-xl bg-white p-1 object-contain shadow-sm dark:bg-gray-900">
                @else
                    <span
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-red-700 text-lg font-extrabold text-white shadow-lg shadow-red-900/20">MD</span>
                @endif
                <span class="text-lg font-extrabold text-slate-900 dark:text-white">{{ $company->name }}</span>
            </div>

            <p class="mt-5 max-w-md text-sm leading-relaxed text-slate-600 dark:text-gray-400">
                {{ $company->tagline }}
            </p>

            @if ($company->about)
                <p class="mt-4 max-w-md text-sm leading-relaxed text-slate-500 dark:text-gray-400/80">
                    {{ \Illuminate\Support\Str::limit(strip_tags($company->about), 220) }}
                </p>
            @endif

            <div class="mt-6 flex flex-wrap gap-3">
                @foreach (['facebook' => 'Facebook', 'instagram' => 'Instagram', 'linkedin' => 'LinkedIn', 'youtube' => 'YouTube'] as $key => $label)
                    @if ($company->{$key})
                        <a href="{{ $company->{$key} }}" target="_blank" rel="noopener"
                            class="rounded-lg bg-white px-2 py-1 text-lg font-semibold text-slate-600 shadow-sm transition hover:bg-red-600 hover:text-white dark:bg-slate-800 dark:text-gray-300 dark:hover:bg-red-600 dark:hover:text-white">
                            <i class="fa-brands fa-{{ $key }}"></i>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>

        <div>
            <h3
                class="border-l-2 border-red-600 pl-2.5 text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-white">
                Navigasi</h3>
            <ul class="mt-4 space-y-2.5 text-sm">
                <li><a href="{{ route('home') }}"
                        class="transition hover:text-red-600 dark:hover:text-red-400">Beranda</a></li>
                <li><a href="{{ route('about') }}" class="transition hover:text-red-600 dark:hover:text-red-400">Tentang
                        Kami</a></li>
                <li><a href="{{ route('products.index') }}"
                        class="transition hover:text-red-600 dark:hover:text-red-400">Produk &amp; Mesin</a></li>
                <li><a href="{{ route('contact') }}"
                        class="transition hover:text-red-600 dark:hover:text-red-400">Kontak</a></li>
                <li><a href="{{ route('admin.login') }}"
                        class="transition hover:text-red-600 dark:hover:text-red-400">Login Admin</a></li>
            </ul>
        </div>

        <div>
            <h3
                class="border-l-2 border-red-600 pl-2.5 text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-white">
                Hubungi Kami</h3>
            <ul class="mt-4 space-y-3 text-sm">
                @if ($company->address || $company->city)
                    <li class="flex gap-2.5">
                        <span
                            class="mt-1 flex h-5 w-5 shrink-0 items-center justify-center text-lg text-red-600 dark:text-red-500">
                            <i class="fa-solid fa-location-dot"></i>
                        </span>
                        <span>{{ trim(($company->address ? $company->address . ', ' : '') . ($company->city ?? ''), ', ') }}</span>
                    </li>
                @endif
                @if ($company->phone)
                    <li class="flex gap-2.5"><span
                            class="text-red-600 dark:text-red-500"><span
                            class="mt-1 flex h-5 w-5 shrink-0 items-center justify-center text-lg text-red-600 dark:text-red-500">
                            <i class="fa-solid fa-phone"></i>
                        </span></span><span>{{ $company->phone }}</span></li>
                @endif
                @if ($whatsapp)
                    <li class="flex gap-2.5">
                        <span
                            class="mt-1 flex h-5 w-5 shrink-0 items-center justify-center text-lg text-red-600 dark:text-red-500">
                            <i class="fa-brands fa-square-whatsapp"></i>
                        </span>
                        <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener"
                            class="transition hover:text-red-600 dark:hover:text-red-400">WhatsApp</a>
                    </li>
                @endif
                @if ($company->email)
                    <li class="flex gap-2.5">
                        <span
                            class="mt-1 flex h-5 w-5 shrink-0 items-center justify-center text-lg text-red-600 dark:text-red-500">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <a href="mailto:{{ $company->email }}"
                            class="transition hover:text-red-600 dark:hover:text-red-400">{{ $company->email }}</a>
                    </li>
                @endif
                @if ($company->website)
                    <li class="flex gap-2.5">
                        <span
                            class="mt-1 flex h-5 w-5 shrink-0 items-center justify-center text-lg text-red-600 dark:text-red-500">
                           <i class="fa-solid fa-globe"></i>
                        </span>
                        <a href="{{ $company->website }}" target="_blank" rel="noopener"
                            class="transition hover:text-red-600 dark:hover:text-red-400">{{ $company->website }}</a>
                    </li>
                @endif
            </ul>
        </div>
    </div>

    <div class="border-t border-gray-200 py-5 dark:border-slate-800">
        <p class="text-center text-xs text-slate-500 dark:text-gray-500">
            &copy; {{ now()->year }} {{ $company->name }}. Seluruh hak cipta dilindungi.
        </p>
    </div>
</footer>