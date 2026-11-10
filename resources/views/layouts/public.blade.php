<!DOCTYPE html>
<html lang="id" class="scroll-smooth overflow-x-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('meta_description', $company->name.' — '.$company->tagline)">
    <title>@yield('title', $company->name.' — '.$company->tagline)</title>

    <link rel="icon" href="{{ $company->logoUrl() ?? asset('uploads/placeholder.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.31.0/dist/tabler-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/css/public.css', 'resources/js/app.js'])

    <style>
        :root {
            --bg-hero: url('{{ asset('images/GambarBG_GIF.gif') }}');
        }
        .home-hero {
            position: relative !important;
            min-height: 100vh !important;
            min-height: 100dvh !important;
            width: 100% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            overflow: hidden !important;
            background: #000000 !important;
        }
        .hero-bg {
            position: absolute !important;
            inset: 0 !important;
            width: 100% !important;
            height: 100% !important;
            background-image: url('{{ asset('images/GambarBG_GIF.gif') }}'), url('{{ asset('images/GambarBG.jpg') }}') !important;
            background-size: cover !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
        }
        .hero-scroll-indicator {
            position: absolute;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            text-decoration: none;
            color: rgba(255, 255, 255, 0.7);
            transition: color 0.25s ease, transform 0.25s ease;
        }
        .hero-scroll-indicator:hover {
            color: #ffffff;
            transform: translateX(-50%) translateY(2px);
        }
        html, body {
            overflow-x: hidden !important;
            max-width: 100vw !important;
            width: 100% !important;
        }
        *, *::before, *::after {
            box-sizing: border-box;
        }

        /* ===== ENLARGED NAVBAR ===== */
        .nav-inner {
            max-width: 1360px !important;
            height: 86px !important;
            padding: 0 32px !important;
        }
        .nav-brand-logo {
            height: 42px !important;
            max-height: 44px !important;
            max-width: 210px !important;
        }
        @media (max-width: 640px) {
            .nav-inner {
                height: 74px !important;
                padding: 0 20px !important;
            }
            .nav-brand-logo {
                height: 34px !important;
                max-width: 160px !important;
            }
        }
        .nav-menu {
            padding: 6px 8px 6px 18px !important;
            gap: 6px !important;
        }
        .nav-link {
            font-size: 15px !important;
            font-weight: 600 !important;
            padding: 9px 18px !important;
        }
        .nav-link.nav-btn-login {
            font-size: 14px !important;
            padding: 8px 22px !important;
        }

        /* ===== ENLARGED FOOTER ===== */
        .newsletter-bar {
            padding: 72px 32px !important;
        }
        .newsletter-inner {
            max-width: 1360px !important;
        }
        .newsletter-inner h3 {
            font-size: 26px !important;
        }
        .footer-main {
            padding: 76px 32px 48px !important;
        }
        .footer-inner {
            max-width: 1360px !important;
            gap: 48px !important;
        }
        .footer-brand .nav-logo-icon {
            height: 48px !important;
            min-width: 76px !important;
            padding: 7px 16px !important;
        }
        .footer-brand .nav-logo-img {
            height: 32px !important;
            max-height: 34px !important;
            max-width: 125px !important;
        }
        .footer-brand .nav-logo-text {
            font-size: 19px !important;
        }
        .footer-brand p {
            font-size: 15px !important;
            line-height: 1.8 !important;
            max-width: 360px !important;
        }
        .footer-col h4 {
            font-size: 14px !important;
            letter-spacing: 1.6px !important;
            margin-bottom: 22px !important;
        }
        .footer-col ul li {
            margin-bottom: 12px !important;
        }
        .footer-col ul li a {
            font-size: 15px !important;
        }
        .footer-bottom {
            max-width: 1360px !important;
            padding: 28px 32px !important;
        }
        .footer-bottom p {
            font-size: 14px !important;
        }
    </style>

    @stack('styles')
</head>
<body class="public-site min-h-screen bg-white font-sans text-zinc-800 antialiased overflow-x-hidden w-full max-w-full">

    @include('public.partials.navbar')

    @include('public.partials.flash')

    <main class="w-full max-w-full overflow-x-hidden">
        @yield('content')
    </main>

    @include('public.partials.footer')

    {{-- Floating Circular WhatsApp Button --}}
    @php
        $globalWa = isset($company) && $company->whatsapp ? preg_replace('/[^0-9]/', '', (string) $company->whatsapp) : null;
    @endphp

    @if ($globalWa)
        <a href="https://wa.me/{{ $globalWa }}?text={{ urlencode('Halo Manufactur Dynamic, saya ingin konsultasi kebutuhan mesin pengolahan.') }}"
           target="_blank"
           rel="noopener noreferrer"
           class="floating-wa-btn"
           aria-label="Hubungi WhatsApp"
           title="Hubungi WhatsApp">
            <i class="fab fa-whatsapp"></i>
        </a>
    @endif

    <script>
        // Navbar scrolled state
        const mainNav = document.getElementById('mainNav');
        if (mainNav) {
            const updateNavScroll = () => {
                if (window.scrollY > 20) mainNav.classList.add('scrolled');
                else mainNav.classList.remove('scrolled');
            };
            window.addEventListener('scroll', updateNavScroll, { passive: true });
            updateNavScroll();
        }

        // Mobile & Tablet sidebar drawer toggle
        const mobileToggle = document.getElementById('mobileToggle');
        const mobileMenu = document.getElementById('mobileMenu');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');
        const sidebarClose = document.getElementById('sidebarClose');

        function openSidebar() {
            if (mobileToggle) mobileToggle.classList.add('active');
            if (mobileMenu) {
                mobileMenu.style.display = 'flex';
                void mobileMenu.offsetWidth;
                mobileMenu.classList.add('active');
            }
            if (sidebarBackdrop) {
                sidebarBackdrop.style.display = 'block';
                void sidebarBackdrop.offsetWidth;
                sidebarBackdrop.classList.add('active');
            }
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            if (mobileToggle) mobileToggle.classList.remove('active');
            if (mobileMenu) {
                mobileMenu.classList.remove('active');
                setTimeout(() => {
                    if (!mobileMenu.classList.contains('active')) {
                        mobileMenu.style.display = 'none';
                    }
                }, 350);
            }
            if (sidebarBackdrop) {
                sidebarBackdrop.classList.remove('active');
                setTimeout(() => {
                    if (!sidebarBackdrop.classList.contains('active')) {
                        sidebarBackdrop.style.display = 'none';
                    }
                }, 350);
            }
            document.body.style.overflow = '';
        }

        if (mobileToggle && mobileMenu) {
            mobileToggle.addEventListener('click', () => {
                if (mobileMenu.classList.contains('active')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });
        }

        if (sidebarClose) {
            sidebarClose.addEventListener('click', closeSidebar);
        }

        if (sidebarBackdrop) {
            sidebarBackdrop.addEventListener('click', closeSidebar);
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && mobileMenu && mobileMenu.classList.contains('active')) {
                closeSidebar();
            }
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth > 1024 && mobileMenu && mobileMenu.classList.contains('active')) {
                closeSidebar();
            }
        });

        // Mobile submenu toggle
        document.querySelectorAll('.mobile-toggle-sub').forEach(t => {
            t.addEventListener('click', () => {
                const target = document.getElementById(t.dataset.target);
                if (!target) return;
                const isOpen = target.classList.contains('open');
                document.querySelectorAll('.mobile-submenu').forEach(s => s.classList.remove('open'));
                document.querySelectorAll('.mobile-toggle-sub').forEach(x => x.classList.remove('open'));
                if (!isOpen) {
                    target.classList.add('open');
                    t.classList.add('open');
                }
            });
        });

        // Dropdown click/tap support
        document.querySelectorAll('.nav-item').forEach(item => {
            const dropdown = item.querySelector('.dropdown');
            const toggle = item.querySelector('.nav-dropdown-btn') || item.querySelector('.nav-link');
            if (dropdown && toggle) {
                toggle.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    const isOpen = item.classList.contains('open');
                    document.querySelectorAll('.nav-item.open').forEach(i => {
                        i.classList.remove('open');
                        const t = i.querySelector('.nav-dropdown-btn') || i.querySelector('.nav-link');
                        if (t) t.setAttribute('aria-expanded', 'false');
                    });
                    if (!isOpen) {
                        item.classList.add('open');
                        toggle.setAttribute('aria-expanded', 'true');
                    }
                });
            }
        });

        document.addEventListener('click', (e) => {
            if (!e.target.closest('.nav-item')) {
                document.querySelectorAll('.nav-item.open').forEach(i => {
                    i.classList.remove('open');
                    const t = i.querySelector('.nav-dropdown-btn') || i.querySelector('.nav-link');
                    if (t) t.setAttribute('aria-expanded', 'false');
                });
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.querySelectorAll('.nav-item.open').forEach(i => {
                    i.classList.remove('open');
                    const t = i.querySelector('.nav-dropdown-btn') || i.querySelector('.nav-link');
                    if (t) t.setAttribute('aria-expanded', 'false');
                });
            }
        });

        // Product accordion toggle helper
        window.toggleAccordion = function(header) {
            const item = header.parentElement;
            const wasOpen = item.classList.contains('open');
            item.parentElement.querySelectorAll('.accordion-item').forEach(i => i.classList.remove('open'));
            if (!wasOpen) item.classList.add('open');
        };
    </script>

    @stack('scripts')
</body>
</html>
