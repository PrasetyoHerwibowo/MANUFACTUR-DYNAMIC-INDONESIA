@php
    $whatsapp = preg_replace('/[^0-9]/', '', (string) $company->whatsapp);
@endphp

<footer class="site-footer">
    <div class="newsletter-bar">
        <div class="newsletter-inner">
            <div>
                <h3 class="text-white font-bold text-xl sm:text-2xl">Follow the latest news from Manufactur Dynamic</h3>
                <p class="mt-1 text-sm text-zinc-400">Dapatkan informasi inovasi teknologi permesinan kopi & kakao terbaru.</p>
            </div>
            <form class="newsletter-form" onsubmit="event.preventDefault(); alert('Terima kasih telah berlangganan newsletter Manufactur Dynamic!'); this.reset();">
                <input type="email" placeholder="Your Email" required>
                <button type="submit">Subscribe</button>
            </form>
        </div>
    </div>

    <div class="footer-main">
        <div class="footer-inner">
            <div class="footer-brand">
                <a href="{{ route('home') }}" class="nav-logo">
                    <div class="nav-logo-icon">
                        <img src="{{ asset('images/logo_eiko_white.png') }}" alt="Logo {{ $company->name }}" class="nav-logo-img">
                    </div>
                    <div class="nav-logo-text text-white">
                        Manufactur<span class="text-zinc-400">_Dynamic</span>
                    </div>
                </a>
                <p class="mt-4">
                    {{ $company->about ? \Illuminate\Support\Str::limit(strip_tags($company->about), 180) : 'Mitra manufaktur terpercaya yang berkomitmen memberikan solusi produksi berkualitas tinggi dengan teknologi modern dan tenaga ahli berpengalaman.' }}
                </p>

                @if ($company->address || $company->city)
                    <p class="mt-3 text-xs text-slate-300/80 flex items-start gap-2">
                        <i class="fas fa-location-dot mt-1 text-zinc-400"></i>
                        <span>{{ trim(($company->address ? $company->address . ', ' : '') . ($company->city ?? ''), ', ') }}</span>
                    </p>
                @endif
            </div>

            <div class="footer-col">
                <h4>Company</h4>
                <ul>
                    <li><a href="{{ route('about') }}">Company Overview</a></li>
                    <li><a href="{{ route('about') }}#vision">Our Vision &amp; Mission</a></li>
                    <li><a href="{{ route('about') }}#values">Core Values</a></li>
                    <li><a href="{{ route('about') }}#journey">Company Journey</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Products</h4>
                <ul>
                    <li><a href="{{ route('products.cocoa') }}">Mesin Kakao (MD-Cocoa)</a></li>
                    <li><a href="{{ route('products.coffee') }}">Mesin Kopi (MD-Coffee)</a></li>
                    <li><a href="{{ route('products.index') }}">Semua Model Mesin</a></li>
                    <li><a href="{{ route('contact') }}">Custom Manufacturing</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Connect</h4>
                <ul>
                    <li><a href="{{ route('about') }}#sustainability">Sustainability</a></li>
                    <li><a href="{{ route('contact') }}">Contact Us</a></li>
                    @if ($whatsapp)
                        <li><a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener">WhatsApp Sales</a></li>
                    @endif
                    <li><a href="{{ auth()->check() ? route('admin.dashboard') : route('admin.login') }}">Panel Admin</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; {{ now()->year }} {{ $company->name }}. All rights reserved.</p>

        <div class="footer-bottom-right">
            <div class="lang-switch">
                <a href="#" class="active">IND</a> | <a href="#">EN</a>
            </div>

            <div class="footer-bottom-social">
                @if ($company->facebook)
                    <a href="{{ $company->facebook }}" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                @endif
                @if ($company->instagram)
                    <a href="{{ $company->instagram }}" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                @endif
                @if ($company->linkedin)
                    <a href="{{ $company->linkedin }}" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                @endif
                @if ($company->youtube)
                    <a href="{{ $company->youtube }}" target="_blank" rel="noopener" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                @endif
                @if (!$company->facebook && !$company->instagram && !$company->linkedin && !$company->youtube)
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                @endif
            </div>
        </div>
    </div>
</footer>