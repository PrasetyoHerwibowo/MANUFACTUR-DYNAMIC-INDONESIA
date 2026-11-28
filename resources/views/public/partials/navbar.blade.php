<header class="main-nav" id="mainNav">
    <div class="nav-inner">
        <a href="{{ route('home') }}" class="nav-logo" title="{{ $company->name }}">
            <img src="{{ asset('images/logo_eiko_white.png') }}" alt="Logo {{ $company->name }}" class="nav-brand-logo logo-white">
            <img src="{{ asset('images/logo_eiko.png') }}" alt="Logo {{ $company->name }}" class="nav-brand-logo logo-dark">
        </a>

        <ul class="nav-menu">
            <li class="nav-item">
                <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                    Beranda
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('about') }}" class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}">
                    Company
                </a>
            </li>
            <li class="nav-item has-dropdown">
                <button type="button"
                        class="nav-link nav-dropdown-btn {{ request()->routeIs('products.*') ? 'active' : '' }}"
                        id="navProductBtn"
                        aria-expanded="false"
                        aria-haspopup="true">
                    <span>Product</span>
                    <i class="fas fa-chevron-down nav-chevron"></i>
                </button>
                <div class="dropdown dropdown-products" id="navProductDropdown" aria-labelledby="navProductBtn">
                    <a href="{{ route('products.cocoa') }}"
                       class="dd-card {{ request()->routeIs('products.cocoa') ? 'active' : '' }}">
                        <div class="dd-card-icon">
                            <i class="fas fa-seedling"></i>
                        </div>
                        <div class="dd-card-content">
                            <span class="dd-card-title">Cocoa Series</span>
                            <span class="dd-card-desc">Mesin Pengolahan Kakao</span>
                        </div>
                    </a>
                    <a href="{{ route('products.coffee') }}"
                       class="dd-card {{ request()->routeIs('products.coffee') ? 'active' : '' }}">
                        <div class="dd-card-icon">
                            <i class="fas fa-mug-hot"></i>
                        </div>
                        <div class="dd-card-content">
                            <span class="dd-card-title">Coffee Series</span>
                            <span class="dd-card-desc">Mesin Pengolahan Kopi</span>
                        </div>
                    </a>
                </div>
            </li>
            <li class="nav-item">
                <a href="{{ route('contact') }}" class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">
                    Contact Us
                </a>
            </li>
            @auth
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}"
                       class="nav-link nav-btn-login {{ request()->routeIs('admin.*') ? 'active' : '' }}"
                       title="Panel Admin">
                        Panel Admin
                    </a>
                </li>
            @else
                <li class="nav-item">
                    <a href="{{ route('admin.login') }}"
                       class="nav-link nav-btn-login {{ request()->routeIs('admin.login') ? 'active' : '' }}"
                       title="Login">
                        Login
                    </a>
                </li>
            @endauth
        </ul>

        <button class="mobile-toggle" id="mobileToggle" aria-label="Menu Navigasi">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<!-- Backdrop overlay for mobile & tablet sidebar -->
<div class="sidebar-backdrop" id="sidebarBackdrop" style="display: none;"></div>

<!-- Mobile & Tablet Sidebar Drawer -->
<aside class="mobile-sidebar" id="mobileMenu" aria-label="Sidebar Navigasi" style="display: none;">
        <div class="sidebar-header">
            <a href="{{ route('home') }}" class="sidebar-brand">
                <img src="{{ asset('images/logo_eiko.png') }}" alt="Logo {{ $company->name }}" class="sidebar-brand-img">
            </a>
            <button type="button" class="sidebar-close-btn" id="sidebarClose" aria-label="Tutup Menu">&times;</button>
        </div>

        <div class="sidebar-body">
            <div class="sidebar-section-title">Navigasi Utama</div>
            <nav class="sidebar-nav">
                <a href="{{ route('home') }}" class="sidebar-link {{ request()->routeIs('home') ? 'active' : '' }}">
                    <span>Beranda</span>
                </a>
                <a href="{{ route('about') }}" class="sidebar-link {{ request()->routeIs('about') ? 'active' : '' }}">
                    <span>Company</span>
                </a>

                <div class="sidebar-accordion">
                    <button type="button" class="sidebar-toggle-sub mobile-toggle-sub {{ request()->routeIs('products.*') ? 'active open' : '' }}" data-target="mobProduct">
                        <span>Product</span>
                        <i class="fas fa-chevron-up toggle-chevron"></i>
                    </button>
                    <div class="mobile-submenu {{ request()->routeIs('products.*') ? 'open' : '' }}" id="mobProduct">
                        <a href="{{ route('products.cocoa') }}" class="mob-product-card {{ request()->routeIs('products.cocoa') ? 'active' : '' }}">
                            <div class="mob-card-icon">
                                <i class="fas fa-seedling"></i>
                            </div>
                            <div class="mob-card-content">
                                <span class="mob-card-title">Cocoa Series</span>
                                <span class="mob-card-desc">Mesin Pengolahan Kakao</span>
                            </div>
                        </a>

                        <a href="{{ route('products.coffee') }}" class="mob-product-card {{ request()->routeIs('products.coffee') ? 'active' : '' }}">
                            <div class="mob-card-icon">
                                <i class="fas fa-mug-hot"></i>
                            </div>
                            <div class="mob-card-content">
                                <span class="mob-card-title">Coffee Series</span>
                                <span class="mob-card-desc">Mesin Pengolahan Kopi</span>
                            </div>
                        </a>
                    </div>
                </div>

                <a href="{{ route('contact') }}" class="sidebar-link {{ request()->routeIs('contact') ? 'active' : '' }}">
                    <span>Contact Us</span>
                </a>
            </nav>
        </div>

        <div class="sidebar-footer">
            @auth
                <a href="{{ route('admin.dashboard') }}" class="sidebar-btn-admin">
                    Panel Admin
                </a>
            @else
                <a href="{{ route('admin.login') }}" class="sidebar-btn-login">
                    Login
                </a>
            @endauth
        </div>
    </aside>

    <style>
        /* ===== 3D CARD DROPDOWN & SIDEBAR (COCOA & COFFEE SERIES) ===== */
        .dropdown.dropdown-products {
            position: absolute !important;
            top: calc(100% + 14px) !important;
            left: 50% !important;
            transform: translateX(-50%) translateY(-8px) !important;
            width: 270px !important;
            min-width: 270px !important;
            max-width: calc(100vw - 32px) !important;
            padding: 10px !important;
            border-radius: 16px !important;
            background: #ffffff !important;
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
            box-shadow: 0 20px 42px -10px rgba(0, 0, 0, 0.16), 0 6px 16px -4px rgba(0, 0, 0, 0.06), inset 0 1px 0 rgba(255, 255, 255, 1) !important;
            opacity: 0 !important;
            visibility: hidden !important;
            pointer-events: none !important;
            transition: opacity 0.22s cubic-bezier(0.16, 1, 0.3, 1), transform 0.22s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.22s !important;
            z-index: 1000 !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 8px !important;
        }

        .nav-item.open .dropdown.dropdown-products {
            opacity: 1 !important;
            visibility: visible !important;
            pointer-events: auto !important;
            transform: translateX(-50%) translateY(0) !important;
        }

        .dropdown.dropdown-products::before {
            content: '' !important;
            position: absolute !important;
            top: -6px !important;
            left: 50% !important;
            margin-left: -6px !important;
            width: 12px !important;
            height: 12px !important;
            background: #ffffff !important;
            border-left: 1px solid rgba(0, 0, 0, 0.08) !important;
            border-top: 1px solid rgba(0, 0, 0, 0.08) !important;
            transform: rotate(45deg) !important;
        }

        /* 3D Elevated Cards (Desktop) */
        .dd-card {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            padding: 11px 13px !important;
            border-radius: 12px !important;
            text-decoration: none !important;
            background: linear-gradient(180deg, #ffffff 0%, #f9fafb 100%) !important;
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
            border-bottom: 2.5px solid rgba(0, 0, 0, 0.15) !important;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05), inset 0 1px 0 rgba(255, 255, 255, 0.95) !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
            cursor: pointer !important;
        }

        .dd-card:hover {
            transform: translateY(-2px) !important;
            background: linear-gradient(180deg, #ffffff 0%, #f4f4f5 100%) !important;
            border-color: rgba(9, 9, 11, 0.22) !important;
            border-bottom: 2.5px solid #09090b !important;
            box-shadow: 0 8px 18px -4px rgba(0, 0, 0, 0.12), 0 3px 6px rgba(0, 0, 0, 0.04), inset 0 1px 0 rgba(255, 255, 255, 1) !important;
        }

        .dd-card:active {
            transform: translateY(1px) !important;
            border-bottom-width: 1px !important;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.08) !important;
        }

        .dd-card.active {
            border-color: #09090b !important;
            border-bottom: 2.5px solid #09090b !important;
            background: linear-gradient(180deg, #ffffff 0%, #f4f4f5 100%) !important;
        }

        .dd-card-icon {
            width: 38px !important;
            height: 38px !important;
            border-radius: 10px !important;
            background: linear-gradient(145deg, #f4f4f5 0%, #e4e4e7 100%) !important;
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
            border-bottom: 2px solid rgba(0, 0, 0, 0.15) !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.95), 0 2px 4px rgba(0, 0, 0, 0.06) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 15px !important;
            color: #18181b !important;
            flex-shrink: 0 !important;
            transition: all 0.2s ease !important;
        }

        .dd-card:hover .dd-card-icon,
        .dd-card.active .dd-card-icon {
            background: #09090b !important;
            color: #ffffff !important;
            border-color: #000000 !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.22) !important;
            transform: scale(1.05) !important;
        }

        .dd-card-content {
            display: flex !important;
            flex-direction: column !important;
            gap: 2px !important;
            flex: 1 !important;
        }

        .dd-card-title {
            font-size: 13.5px !important;
            font-weight: 700 !important;
            color: #09090b !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            line-height: 1.25 !important;
            letter-spacing: -0.01em !important;
        }

        .dd-card-desc {
            font-size: 11px !important;
            font-weight: 500 !important;
            color: #71717a !important;
            font-family: 'Inter', sans-serif !important;
            line-height: 1.2 !important;
        }

        /* 3D Elevated Cards (Mobile Sidebar) */
        .mob-product-card {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            padding: 12px 14px !important;
            border-radius: 12px !important;
            text-decoration: none !important;
            background: linear-gradient(180deg, #ffffff 0%, #f9fafb 100%) !important;
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
            border-bottom: 2.5px solid rgba(0, 0, 0, 0.15) !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05), inset 0 1px 0 rgba(255, 255, 255, 0.95) !important;
            margin-bottom: 8px !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }

        .mob-product-card:hover {
            transform: translateY(-2px) !important;
            background: linear-gradient(180deg, #ffffff 0%, #f4f4f5 100%) !important;
            border-color: rgba(9, 9, 11, 0.22) !important;
            border-bottom: 2.5px solid #09090b !important;
            box-shadow: 0 8px 18px -4px rgba(0, 0, 0, 0.12), inset 0 1px 0 rgba(255, 255, 255, 1) !important;
        }

        .mob-product-card:active {
            transform: translateY(1px) !important;
            border-bottom-width: 1px !important;
        }

        .mob-product-card.active {
            border-color: #09090b !important;
            border-bottom: 2.5px solid #09090b !important;
        }

        .mob-card-icon {
            width: 38px !important;
            height: 38px !important;
            border-radius: 10px !important;
            background: linear-gradient(145deg, #f4f4f5 0%, #e4e4e7 100%) !important;
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
            border-bottom: 2px solid rgba(0, 0, 0, 0.15) !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.95), 0 2px 4px rgba(0, 0, 0, 0.06) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 15px !important;
            color: #18181b !important;
            flex-shrink: 0 !important;
            transition: all 0.2s ease !important;
        }

        .mob-product-card:hover .mob-card-icon,
        .mob-product-card.active .mob-card-icon {
            background: #09090b !important;
            color: #ffffff !important;
            border-color: #000000 !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.22) !important;
        }

        .mob-card-content {
            display: flex !important;
            flex-direction: column !important;
            gap: 2px !important;
            flex: 1 !important;
        }

        .mob-card-title {
            font-size: 13.5px !important;
            font-weight: 700 !important;
            color: #09090b !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            line-height: 1.25 !important;
        }

        .mob-card-desc {
            font-size: 11px !important;
            font-weight: 500 !important;
            color: #71717a !important;
            font-family: 'Inter', sans-serif !important;
            line-height: 1.2 !important;
        }

        /* ===== DARK MODE 3D CARDS ===== */
        .dark .dropdown.dropdown-products {
            background: #18181b !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
            box-shadow: 0 24px 48px -10px rgba(0, 0, 0, 0.7), inset 0 1px 0 rgba(255, 255, 255, 0.08) !important;
        }

        .dark .dropdown.dropdown-products::before {
            background: #18181b !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
        }

        .dark .dd-card,
        .dark .mob-product-card {
            background: linear-gradient(180deg, #27272a 0%, #1f1f23 100%) !important;
            border: 1px solid rgba(255, 255, 255, 0.09) !important;
            border-bottom: 2.5px solid rgba(0, 0, 0, 0.45) !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.08) !important;
        }

        .dark .dd-card:hover,
        .dark .mob-product-card:hover {
            background: linear-gradient(180deg, #323238 0%, #27272a 100%) !important;
            border-color: rgba(255, 255, 255, 0.22) !important;
            border-bottom: 2.5px solid #ffffff !important;
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.55), inset 0 1px 0 rgba(255, 255, 255, 0.18) !important;
        }

        .dark .dd-card.active,
        .dark .mob-product-card.active {
            border-color: #ffffff !important;
            border-bottom: 2.5px solid #ffffff !important;
        }

        .dark .dd-card-title,
        .dark .mob-card-title {
            color: #f4f4f5 !important;
        }

        .dark .dd-card-desc,
        .dark .mob-card-desc {
            color: #a1a1aa !important;
        }

        .dark .dd-card-icon,
        .dark .mob-card-icon {
            background: linear-gradient(145deg, #3f3f46 0%, #27272a 100%) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-bottom: 2px solid rgba(0, 0, 0, 0.4) !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12), 0 2px 4px rgba(0, 0, 0, 0.3) !important;
            color: #f4f4f5 !important;
        }

        .dark .dd-card:hover .dd-card-icon,
        .dark .dd-card.active .dd-card-icon,
        .dark .mob-product-card:hover .mob-card-icon,
        .dark .mob-product-card.active .mob-card-icon {
            background: #ffffff !important;
            color: #09090b !important;
            border-color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(255, 255, 255, 0.25) !important;
        }

        /* Sidebar Accordion Chevron (^ when closed, rotates down when open) */
        .toggle-chevron {
            font-size: 11px !important;
            color: #71717a !important;
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), color 0.2s ease !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .sidebar-toggle-sub:hover .toggle-chevron {
            color: #09090b !important;
        }

        .sidebar-toggle-sub.open .toggle-chevron {
            transform: rotate(180deg) !important;
            color: #09090b !important;
        }

        .dark .toggle-chevron {
            color: #a1a1aa !important;
        }

        .dark .sidebar-toggle-sub:hover .toggle-chevron,
        .dark .sidebar-toggle-sub.open .toggle-chevron {
            color: #ffffff !important;
        }

        /* ===== CONTACT US OVAL BLACK BUTTON ===== */
        .nav-link.nav-link-contact {
            background: #09090b !important;
            color: #ffffff !important;
            border-radius: 9999px !important;
            padding: 8px 20px !important;
            font-weight: 600 !important;
            font-size: 13.5px !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.14) !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        .nav-link.nav-link-contact::after {
            display: none !important;
        }

        .nav-link.nav-link-contact:hover {
            background: #27272a !important;
            color: #ffffff !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.22) !important;
        }

        .nav-link.nav-link-contact:active {
            transform: translateY(1px) !important;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.15) !important;
        }

        .nav-link.nav-link-contact.active {
            background: #000000 !important;
            color: #ffffff !important;
            box-shadow: 0 0 0 2px rgba(9, 9, 11, 0.25), 0 2px 8px rgba(0, 0, 0, 0.2) !important;
        }

        .sidebar-link.sidebar-link-contact {
            background: #09090b !important;
            color: #ffffff !important;
            border-radius: 9999px !important;
            padding: 11px 20px !important;
            margin-top: 8px !important;
            font-weight: 600 !important;
            font-size: 14px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            text-align: center !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.14) !important;
            transition: all 0.2s ease !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        .sidebar-link.sidebar-link-contact:hover {
            background: #27272a !important;
            color: #ffffff !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.22) !important;
        }

        .sidebar-link.sidebar-link-contact.active {
            background: #000000 !important;
            color: #ffffff !important;
        }

        /* Dark Mode for Contact Us Oval */
        .dark .nav-link.nav-link-contact {
            background: #ffffff !important;
            color: #09090b !important;
            border-color: transparent !important;
            box-shadow: 0 2px 8px rgba(255, 255, 255, 0.12) !important;
        }

        .dark .nav-link.nav-link-contact:hover {
            background: #f4f4f5 !important;
            color: #000000 !important;
            box-shadow: 0 4px 14px rgba(255, 255, 255, 0.2) !important;
        }

        .dark .sidebar-link.sidebar-link-contact {
            background: #ffffff !important;
            color: #09090b !important;
            border-color: transparent !important;
            box-shadow: 0 2px 8px rgba(255, 255, 255, 0.12) !important;
        }

        .dark .sidebar-link.sidebar-link-contact:hover {
            background: #f4f4f5 !important;
            color: #000000 !important;
        }

        /* Sidebar Logo & Header Compact */
        .sidebar-header {
            padding: 14px 18px !important;
        }

        .sidebar-brand-img {
            height: 22px !important;
            width: auto !important;
            max-width: 110px !important;
            object-fit: contain !important;
            display: block !important;
        }

        .sidebar-close-btn {
            width: 32px !important;
            height: 32px !important;
            font-size: 20px !important;
        }
    </style>
