@php
    $whatsapp = preg_replace('/[^0-9]/', '', (string) $company->whatsapp);
@endphp

<footer class="mt-24 bg-brand-950 text-brand-100">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 lg:grid-cols-4 lg:px-8">
        <div class="lg:col-span-2">
            <div class="flex items-center gap-3">
                @if ($company->logoUrl())
                    <img src="{{ $company->logoUrl() }}" alt="Logo {{ $company->name }}" class="h-12 w-12 rounded-xl bg-white/10 object-contain p-1">
                @else
                    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-700 text-lg font-extrabold text-white">MD</span>
                @endif
                <span class="text-lg font-extrabold text-white">{{ $company->name }}</span>
            </div>

            <p class="mt-5 max-w-md text-sm leading-relaxed text-brand-200">
                {{ $company->tagline }}
            </p>

            @if ($company->about)
                <p class="mt-4 max-w-md text-sm leading-relaxed text-brand-200/80">
                    {{ \Illuminate\Support\Str::limit(strip_tags($company->about), 220) }}
                </p>
            @endif

            <div class="mt-6 flex flex-wrap gap-3">
                @foreach (['facebook' => 'Facebook', 'instagram' => 'Instagram', 'linkedin' => 'LinkedIn', 'youtube' => 'YouTube'] as $key => $label)
                    @if ($company->{$key})
                        <a href="{{ $company->{$key} }}" target="_blank" rel="noopener"
                           class="rounded-lg bg-white/10 px-3.5 py-2 text-xs font-semibold text-white transition hover:bg-white/20">
                            {{ $label }}
                        </a>
                    @endif
                @endforeach
            </div>
        </div>

        <div>
            <h3 class="text-sm font-bold uppercase tracking-wider text-white">Navigasi</h3>
            <ul class="mt-4 space-y-2.5 text-sm">
                <li><a href="{{ route('home') }}" class="transition hover:text-white">Beranda</a></li>
                <li><a href="{{ route('about') }}" class="transition hover:text-white">Tentang Kami</a></li>
                <li><a href="{{ route('products.index') }}" class="transition hover:text-white">Produk &amp; Mesin</a></li>
                <li><a href="{{ route('catalogs.index') }}" class="transition hover:text-white">Katalog</a></li>
                <li><a href="{{ route('contact') }}" class="transition hover:text-white">Kontak</a></li>
                <li><a href="{{ route('admin.login') }}" class="transition hover:text-white">Login Admin</a></li>
            </ul>
        </div>

        <div>
            <h3 class="text-sm font-bold uppercase tracking-wider text-white">Hubungi Kami</h3>
            <ul class="mt-4 space-y-3 text-sm">
                @if ($company->address || $company->city)
                    <li class="flex gap-2.5">
                        <span class="mt-0.5 text-brand-300">📍</span>
                        <span>{{ trim(($company->address ? $company->address.', ' : '').($company->city ?? ''), ', ') }}</span>
                    </li>
                @endif
                @if ($company->phone)
                    <li class="flex gap-2.5"><span class="text-brand-300">📞</span><span>{{ $company->phone }}</span></li>
                @endif
                @if ($whatsapp)
                    <li class="flex gap-2.5">
                        <span class="text-brand-300">💬</span>
                        <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener" class="transition hover:text-white">WhatsApp</a>
                    </li>
                @endif
                @if ($company->email)
                    <li class="flex gap-2.5">
                        <span class="text-brand-300">✉️</span>
                        <a href="mailto:{{ $company->email }}" class="transition hover:text-white">{{ $company->email }}</a>
                    </li>
                @endif
                @if ($company->website)
                    <li class="flex gap-2.5">
                        <span class="text-brand-300">🌐</span>
                        <a href="{{ $company->website }}" target="_blank" rel="noopener" class="transition hover:text-white">{{ $company->website }}</a>
                    </li>
                @endif
            </ul>
        </div>
    </div>

    <div class="border-t border-white/10 py-5">
        <p class="text-center text-xs text-brand-200/70">
            &copy; {{ now()->year }} {{ $company->name }}. Seluruh hak cipta dilindungi.
        </p>
    </div>
</footer>
