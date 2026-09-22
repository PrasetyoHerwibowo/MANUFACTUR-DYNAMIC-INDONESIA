@php
    $whatsapp = preg_replace('/[^0-9]/', '', (string) $company->whatsapp);
@endphp

<footer class="mt-24 bg-gradient-to-br from-brand-950 via-brand-950 to-coffee-900 text-brand-100 transition-colors duration-300 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800 dark:text-gray-300">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 lg:grid-cols-4 lg:px-8">
        <div class="lg:col-span-2">
            <div class="flex items-center gap-3">
                @if ($company->logoUrl())
                    <img src="{{ $company->logoUrl() }}" alt="Logo {{ $company->name }}" class="h-12 w-12 rounded-xl bg-white/10 object-contain p-1">
                @else
                    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-coffee-500 to-coffee-700 text-lg font-extrabold text-white shadow-lg shadow-coffee-900/30">MD</span>
                @endif
                <span class="text-lg font-extrabold text-white">{{ $company->name }}</span>
            </div>

            <p class="mt-5 max-w-md text-sm leading-relaxed text-brand-200 dark:text-gray-400">
                {{ $company->tagline }}
            </p>

            @if ($company->about)
                <p class="mt-4 max-w-md text-sm leading-relaxed text-brand-200/80 dark:text-gray-400/80">
                    {{ \Illuminate\Support\Str::limit(strip_tags($company->about), 220) }}
                </p>
            @endif

            <div class="mt-6 flex flex-wrap gap-3">
                @foreach (['facebook' => 'Facebook', 'instagram' => 'Instagram', 'linkedin' => 'LinkedIn', 'youtube' => 'YouTube'] as $key => $label)
                    @if ($company->{$key})
                        <a href="{{ $company->{$key} }}" target="_blank" rel="noopener"
                           class="rounded-lg bg-white/10 px-3.5 py-2 text-xs font-semibold text-white transition hover:bg-coffee-600">
                            {{ $label }}
                        </a>
                    @endif
                @endforeach
            </div>
        </div>

        <div>
            <h3 class="text-sm font-bold uppercase tracking-wider text-white">Navigasi</h3>
            <ul class="mt-4 space-y-2.5 text-sm">
                <li><a href="{{ route('home') }}" class="transition hover:text-coffee-300">Beranda</a></li>
                <li><a href="{{ route('about') }}" class="transition hover:text-coffee-300">Tentang Kami</a></li>
                <li><a href="{{ route('products.index') }}" class="transition hover:text-coffee-300">Produk &amp; Mesin</a></li>
                <li><a href="{{ route('catalogs.index') }}" class="transition hover:text-coffee-300">Katalog</a></li>
                <li><a href="{{ route('contact') }}" class="transition hover:text-coffee-300">Kontak</a></li>
                <li><a href="{{ route('admin.login') }}" class="transition hover:text-coffee-300">Login Admin</a></li>
            </ul>
        </div>

        <div>
            <h3 class="text-sm font-bold uppercase tracking-wider text-white">Hubungi Kami</h3>
            <ul class="mt-4 space-y-3 text-sm">
                @if ($company->address || $company->city)
                    <li class="flex gap-2.5">
                        <span class="mt-0.5 text-coffee-300">📍</span>
                        <span>{{ trim(($company->address ? $company->address.', ' : '').($company->city ?? ''), ', ') }}</span>
                    </li>
                @endif
                @if ($company->phone)
                    <li class="flex gap-2.5"><span class="text-coffee-300">📞</span><span>{{ $company->phone }}</span></li>
                @endif
                @if ($whatsapp)
                    <li class="flex gap-2.5">
                        <span class="text-coffee-300">💬</span>
                        <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener" class="transition hover:text-coffee-300">WhatsApp</a>
                    </li>
                @endif
                @if ($company->email)
                    <li class="flex gap-2.5">
                        <span class="text-coffee-300">✉️</span>
                        <a href="mailto:{{ $company->email }}" class="transition hover:text-coffee-300">{{ $company->email }}</a>
                    </li>
                @endif
                @if ($company->website)
                    <li class="flex gap-2.5">
                        <span class="text-coffee-300">🌐</span>
                        <a href="{{ $company->website }}" target="_blank" rel="noopener" class="transition hover:text-coffee-300">{{ $company->website }}</a>
                    </li>
                @endif
            </ul>
        </div>
    </div>

    <div class="border-t border-white/10 py-5 dark:border-gray-800">
        <p class="text-center text-xs text-brand-200/70 dark:text-gray-500">
            &copy; {{ now()->year }} {{ $company->name }}. Seluruh hak cipta dilindungi.
        </p>
    </div>
</footer>
