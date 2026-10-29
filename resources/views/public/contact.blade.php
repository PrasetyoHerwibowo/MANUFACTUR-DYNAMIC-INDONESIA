@extends('layouts.public')

@section('title', 'Hubungi Kami — '.$company->name)
@section('meta_description', 'Hubungi '.$company->name.' untuk konsultasi spesifikasi mesin pengolahan kakao dan kopi, penawaran harga (RFQ), suku cadang, dan kunjungan workshop.')

@push('styles')
<style>
    /* ==========================================================================
       CONTACT PAGE — MONOCHROME INDUSTRIAL DESIGN SYSTEM
       Strict Zero-Black-Stroke Rule: All borders #e4e4e7, #27272a, or rgba(...)
       ========================================================================== */

    /* Hero Section - Fullscreen */
    .contact-hero-wrap {
        position: relative;
        min-height: 100vh;
        min-height: 100dvh;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        overflow: hidden;
        padding: 140px 24px 90px;
        background: #000000;
    }

    .contact-hero-bg {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        background-image: url('{{ asset('images/GambarBG_GIF.gif') }}'), url('{{ asset('images/GambarBG.jpg') }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        filter: grayscale(100%) contrast(115%);
        opacity: 0.40;
        z-index: 0;
    }

    .contact-hero-overlay {
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at center, rgba(0, 0, 0, 0.40) 0%, rgba(9, 9, 11, 0.92) 100%),
                    linear-gradient(180deg, rgba(0, 0, 0, 0.65) 0%, rgba(9, 9, 11, 0.98) 100%);
        z-index: 1;
    }

    .contact-hero-inner {
        position: relative;
        z-index: 2;
        max-width: 860px;
        margin: 0 auto;
    }

    .contact-pill-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 16px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.16);
        color: #ffffff;
        font-size: 12.5px;
        font-weight: 700;
        letter-spacing: 0.5px;
        margin-bottom: 20px;
        backdrop-filter: blur(8px);
    }

    .contact-hero-title {
        font-size: clamp(32px, 5vw, 52px);
        font-weight: 800;
        line-height: 1.18;
        color: #ffffff;
        letter-spacing: -0.8px;
        margin-bottom: 16px;
    }

    .contact-hero-title span {
        color: #d4d4d8;
        font-weight: 800;
    }

    .contact-hero-desc {
        color: #a1a1aa;
        font-size: clamp(14px, 1.8vw, 16px);
        line-height: 1.7;
        max-width: 660px;
        margin: 0 auto 32px;
    }

    .contact-hero-stats {
        display: inline-flex;
        align-items: center;
        gap: 24px;
        padding: 12px 28px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.10);
        border-radius: 999px;
        backdrop-filter: blur(8px);
    }

    .c-hero-stat-item {
        display: flex;
        align-items: center;
        gap: 10px;
        text-align: left;
    }

    .c-hero-stat-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 13px;
        flex-shrink: 0;
    }

    .c-hero-stat-text strong {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: #ffffff;
        line-height: 1.2;
    }

    .c-hero-stat-text span {
        display: block;
        font-size: 11px;
        color: #a1a1aa;
    }

    .c-hero-stat-div {
        width: 1px;
        height: 24px;
        background: rgba(255, 255, 255, 0.15);
    }

    .contact-scroll-indicator {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 10;
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        text-decoration: none;
        color: rgba(255, 255, 255, 0.7);
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        transition: all 0.25s ease;
    }

    .contact-scroll-indicator:hover {
        color: #ffffff;
        transform: translateX(-50%) translateY(3px);
    }

    /* Main Section */
    .contact-main-wrap {
        padding: 80px 24px;
        background: #fafafa;
    }

    .dark .contact-main-wrap {
        background: #09090b;
    }

    .contact-container {
        max-width: 1280px;
        margin: 0 auto;
    }

    .contact-grid {
        display: grid;
        grid-template-columns: 1fr 1.28fr;
        gap: 48px;
        align-items: start;
    }

    /* Left Column: Info Hub */
    .contact-hub-card {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .hub-header {
        margin-bottom: 8px;
    }

    .hub-tag {
        display: inline-block;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        color: #71717a;
        margin-bottom: 8px;
    }

    .dark .hub-tag {
        color: #a1a1aa;
    }

    .hub-header h2 {
        font-size: 28px;
        font-weight: 800;
        color: #09090b;
        letter-spacing: -0.5px;
        line-height: 1.25;
        margin-bottom: 10px;
    }

    .dark .hub-header h2 {
        color: #ffffff;
    }

    .hub-header p {
        font-size: 14.5px;
        color: #71717a;
        line-height: 1.65;
    }

    .dark .hub-header p {
        color: #a1a1aa;
    }

    /* Contact Detail Item (Bento Style) */
    .hub-item {
        background: #ffffff;
        border-radius: 18px;
        padding: 22px;
        border: 1px solid #e4e4e7;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        transition: all 0.25s ease;
        display: flex;
        gap: 18px;
        align-items: flex-start;
        text-decoration: none;
        color: inherit;
    }

    .dark .hub-item {
        background: #18181b;
        border-color: #27272a;
        box-shadow: none;
    }

    .hub-item:hover {
        border-color: #d4d4d8;
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    }

    .dark .hub-item:hover {
        border-color: #3f3f46;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
    }

    .hub-icon-box {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        background: #f4f4f5;
        color: #09090b;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
        transition: all 0.25s ease;
    }

    .dark .hub-icon-box {
        background: #27272a;
        color: #ffffff;
    }

    .hub-item:hover .hub-icon-box {
        background: #09090b;
        color: #ffffff;
    }

    .dark .hub-item:hover .hub-icon-box {
        background: #ffffff;
        color: #09090b;
    }

    .hub-item-content {
        flex: 1;
        min-width: 0;
    }

    .hub-item-label {
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #71717a;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .dark .hub-item-label {
        color: #a1a1aa;
    }

    .hub-item-val {
        font-size: 15px;
        font-weight: 700;
        color: #09090b;
        line-height: 1.4;
        word-break: break-word;
    }

    .dark .hub-item-val {
        color: #ffffff;
    }

    .hub-item-sub {
        font-size: 12.5px;
        color: #71717a;
        margin-top: 4px;
        line-height: 1.5;
    }

    .dark .hub-item-sub {
        color: #a1a1aa;
    }

    .hub-badge-live {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 8px;
        border-radius: 999px;
        background: #ecfdf5;
        border: 1px solid #d1fae5;
        color: #059669;
        font-size: 11px;
        font-weight: 700;
        margin-top: 6px;
    }

    .dark .hub-badge-live {
        background: rgba(16, 185, 129, 0.12);
        border-color: rgba(16, 185, 129, 0.25);
        color: #34d399;
    }

    .live-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #10b981;
        animation: pulse-dot 1.8s infinite ease-in-out;
    }

    @keyframes pulse-dot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(0.85); }
    }

    .btn-route-action {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 700;
        color: #09090b;
        background: #f4f4f5;
        border: 1px solid #e4e4e7;
        padding: 6px 14px;
        border-radius: 8px;
        margin-top: 10px;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .dark .btn-route-action {
        background: #27272a;
        border-color: #3f3f46;
        color: #ffffff;
    }

    .btn-route-action:hover {
        background: #09090b;
        border-color: #09090b;
        color: #ffffff;
    }

    .dark .btn-route-action:hover {
        background: #ffffff;
        border-color: #ffffff;
        color: #09090b;
    }

    /* Social Links Strip */
    .hub-social-box {
        background: #ffffff;
        border-radius: 18px;
        padding: 18px 22px;
        border: 1px solid #e4e4e7;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .dark .hub-social-box {
        background: #18181b;
        border-color: #27272a;
        box-shadow: none;
    }

    .hub-social-text strong {
        display: block;
        font-size: 13.5px;
        font-weight: 700;
        color: #09090b;
    }

    .dark .hub-social-text strong {
        color: #ffffff;
    }

    .hub-social-text span {
        font-size: 12px;
        color: #71717a;
    }

    .dark .hub-social-text span {
        color: #a1a1aa;
    }

    .hub-social-links {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .hub-social-btn {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: #f4f4f5;
        border: 1px solid #e4e4e7;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #27272a;
        font-size: 14px;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .dark .hub-social-btn {
        background: #27272a;
        border-color: #3f3f46;
        color: #e4e4e7;
    }

    .hub-social-btn:hover {
        background: #09090b;
        border-color: #09090b;
        color: #ffffff;
        transform: translateY(-2px);
    }

    .dark .hub-social-btn:hover {
        background: #ffffff;
        border-color: #ffffff;
        color: #09090b;
    }

    /* Right Column: Contact & RFQ Form */
    .contact-form-card {
        background: #ffffff;
        border-radius: 24px;
        padding: 40px;
        border: 1px solid #e4e4e7;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    }

    .dark .contact-form-card {
        background: #18181b;
        border-color: #27272a;
        box-shadow: none;
    }

    .form-card-header {
        margin-bottom: 28px;
    }

    .form-tag {
        display: inline-block;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        color: #71717a;
        margin-bottom: 8px;
    }

    .dark .form-tag {
        color: #a1a1aa;
    }

    .form-card-header h3 {
        font-size: 24px;
        font-weight: 800;
        color: #09090b;
        letter-spacing: -0.4px;
        margin-bottom: 8px;
    }

    .dark .form-card-header h3 {
        color: #ffffff;
    }

    .form-card-header p {
        font-size: 14px;
        color: #71717a;
        line-height: 1.6;
    }

    .dark .form-card-header p {
        color: #a1a1aa;
    }

    /* Floating Machine Alert when ?mesin= parameter is present */
    .inquiry-notice-banner {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border-radius: 12px;
        background: #f4f4f5;
        border: 1px solid #e4e4e7;
        margin-bottom: 24px;
        font-size: 13px;
        color: #27272a;
    }

    .dark .inquiry-notice-banner {
        background: #27272a;
        border-color: #3f3f46;
        color: #e4e4e7;
    }

    .inquiry-notice-banner i {
        font-size: 16px;
        color: #09090b;
    }

    .dark .inquiry-notice-banner i {
        color: #ffffff;
    }

    .inquiry-notice-banner strong {
        color: #09090b;
        font-weight: 700;
    }

    .dark .inquiry-notice-banner strong {
        color: #ffffff;
    }

    /* Form Fields */
    .form-field-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 18px;
    }

    .form-field-group {
        margin-bottom: 18px;
        display: flex;
        flex-direction: column;
    }

    .form-field-group label {
        font-size: 13px;
        font-weight: 700;
        color: #27272a;
        margin-bottom: 7px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .dark .form-field-group label {
        color: #e4e4e7;
    }

    .form-field-group label .req {
        color: #ef4444;
        font-size: 12px;
    }

    .form-field-control {
        width: 100%;
        padding: 12px 16px;
        border-radius: 12px;
        border: 1px solid #e4e4e7;
        background: #fafafa;
        font-size: 14px;
        color: #09090b;
        font-family: inherit;
        outline: none;
        transition: all 0.2s ease;
    }

    .dark .form-field-control {
        background: #27272a;
        border-color: #3f3f46;
        color: #ffffff;
    }

    .form-field-control:focus {
        background: #ffffff;
        border-color: #a1a1aa;
        box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.05);
    }

    .dark .form-field-control:focus {
        background: #18181b;
        border-color: #71717a;
        box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.08);
    }

    select.form-field-control {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2371717a' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        padding-right: 40px;
    }

    textarea.form-field-control {
        resize: vertical;
        min-height: 125px;
        line-height: 1.6;
    }

    .form-submit-btn {
        width: 100%;
        padding: 14px 24px;
        border-radius: 12px;
        background: #09090b;
        color: #ffffff;
        border: 1px solid transparent;
        font-size: 14.5px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: all 0.25s ease;
        margin-top: 6px;
    }

    .dark .form-submit-btn {
        background: #ffffff;
        color: #09090b;
    }

    .form-submit-btn:hover {
        background: #27272a;
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
    }

    .dark .form-submit-btn:hover {
        background: #e4e4e7;
        box-shadow: 0 6px 18px rgba(255, 255, 255, 0.12);
    }

    .form-trust-note {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 12px;
        color: #71717a;
        margin-top: 16px;
        text-align: center;
    }

    .dark .form-trust-note {
        color: #a1a1aa;
    }

    /* Map Section */
    .contact-map-section {
        padding: 0 24px 80px;
        background: #fafafa;
    }

    .dark .contact-map-section {
        background: #09090b;
    }

    .map-header {
        text-align: center;
        max-width: 680px;
        margin: 0 auto 36px;
    }

    .map-tag {
        display: inline-block;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        color: #71717a;
        margin-bottom: 8px;
    }

    .dark .map-tag {
        color: #a1a1aa;
    }

    .map-header h2 {
        font-size: 28px;
        font-weight: 800;
        color: #09090b;
        letter-spacing: -0.5px;
        line-height: 1.25;
        margin-bottom: 10px;
    }

    .dark .map-header h2 {
        color: #ffffff;
    }

    .map-header p {
        font-size: 14.5px;
        color: #71717a;
        line-height: 1.6;
    }

    .dark .map-header p {
        color: #a1a1aa;
    }

    .map-card-wrapper {
        border-radius: 24px;
        overflow: hidden;
        border: 1px solid #e4e4e7;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        background: #ffffff;
        position: relative;
    }

    .dark .map-card-wrapper {
        background: #18181b;
        border-color: #27272a;
        box-shadow: none;
    }

    .map-frame-container {
        width: 100%;
        height: 420px;
        position: relative;
    }

    .map-frame-container iframe {
        width: 100% !important;
        height: 100% !important;
        border: 0 !important;
        display: block;
    }

    .map-footer-bar {
        padding: 18px 26px;
        background: #ffffff;
        border-top: 1px solid #e4e4e7;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .dark .map-footer-bar {
        background: #18181b;
        border-top-color: #27272a;
    }

    .map-footer-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .map-footer-info i {
        font-size: 16px;
        color: #09090b;
    }

    .dark .map-footer-info i {
        color: #ffffff;
    }

    .map-footer-info span {
        font-size: 13.5px;
        font-weight: 600;
        color: #27272a;
    }

    .dark .map-footer-info span {
        color: #e4e4e7;
    }

    .btn-open-gmaps {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 18px;
        border-radius: 10px;
        background: #09090b;
        color: #ffffff;
        border: 1px solid transparent;
        font-size: 12.5px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .dark .btn-open-gmaps {
        background: #ffffff;
        color: #09090b;
    }

    .btn-open-gmaps:hover {
        background: #27272a;
        transform: translateY(-2px);
    }

    .dark .btn-open-gmaps:hover {
        background: #e4e4e7;
    }

    /* Switcher Banner: Direct to Products */
    .contact-switcher-wrap {
        padding: 0 24px 90px;
        background: #fafafa;
    }

    .dark .contact-switcher-wrap {
        background: #09090b;
    }

    .contact-switcher-card {
        background: #09090b;
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 24px;
        padding: 44px 40px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 32px;
        flex-wrap: wrap;
    }

    .c-switcher-content {
        max-width: 640px;
    }

    .c-switcher-badge {
        display: inline-block;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: #a1a1aa;
        margin-bottom: 8px;
    }

    .c-switcher-content h3 {
        font-size: clamp(20px, 2.5vw, 26px);
        font-weight: 800;
        color: #ffffff;
        letter-spacing: -0.4px;
        margin-bottom: 8px;
        line-height: 1.25;
    }

    .c-switcher-content p {
        font-size: 14px;
        color: #a1a1aa;
        line-height: 1.6;
        margin: 0;
    }

    .c-switcher-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .c-switcher-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 20px;
        border-radius: 12px;
        font-size: 13.5px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.25s ease;
    }

    .c-switcher-btn.primary {
        background: #ffffff;
        color: #09090b;
        border: 1px solid transparent;
    }

    .c-switcher-btn.primary:hover {
        background: #e4e4e7;
        transform: translateY(-2px);
    }

    .c-switcher-btn.outline {
        background: rgba(255, 255, 255, 0.06);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.16);
    }

    .c-switcher-btn.outline:hover {
        background: rgba(255, 255, 255, 0.12);
        border-color: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
    }

    /* ==========================================================================
       RESPONSIVE BREAKPOINTS (TABLET & MOBILE)
       ========================================================================== */
    @media (max-width: 1024px) {
        .contact-grid {
            grid-template-columns: 1fr;
            gap: 40px;
        }

        .contact-form-card {
            padding: 32px 26px;
        }
    }

    @media (max-width: 768px) {
        .contact-hero-wrap {
            padding: 110px 20px 70px;
            min-height: 100vh;
            min-height: 100dvh;
        }

        .contact-hero-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 4px;
            border-radius: 16px;
            padding: 12px 6px;
            width: 100%;
            max-width: 380px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(10px);
        }

        .c-hero-stat-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            gap: 6px;
            padding: 2px 4px;
            position: relative;
        }

        .c-hero-stat-item:not(:last-child)::after {
            content: '';
            position: absolute;
            right: -2px;
            top: 15%;
            bottom: 15%;
            width: 1px;
            background: rgba(255, 255, 255, 0.12);
        }

        .c-hero-stat-icon {
            width: 28px;
            height: 28px;
            font-size: 11px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.10);
            border-radius: 50%;
        }

        .c-hero-stat-text {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .c-hero-stat-text strong {
            font-size: 11px;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.25;
            text-align: center;
        }

        .c-hero-stat-text span {
            font-size: 9.5px;
            color: #a1a1aa;
            line-height: 1.2;
            margin-top: 2px;
            text-align: center;
        }

        .c-hero-stat-div {
            display: none;
        }

        .contact-main-wrap {
            padding: 50px 16px;
        }

        .hub-header h2 {
            font-size: 24px;
        }

        .contact-form-card {
            padding: 26px 18px;
            border-radius: 20px;
        }

        .form-field-grid {
            grid-template-columns: 1fr;
            gap: 0;
        }

        .map-frame-container {
            height: 320px;
        }

        .contact-map-section {
            padding: 0 16px 50px;
        }

        .contact-switcher-wrap {
            padding: 0 16px 60px;
        }

        .contact-switcher-card {
            padding: 30px 22px;
            border-radius: 20px;
        }

        .c-switcher-actions {
            width: 100%;
        }

        .c-switcher-btn {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .contact-hero-stats {
            max-width: 350px;
            padding: 10px 4px;
        }

        .c-hero-stat-icon {
            width: 26px;
            height: 26px;
            font-size: 10.5px;
        }

        .c-hero-stat-text strong {
            font-size: 10.5px;
        }

        .c-hero-stat-text span {
            font-size: 9px;
        }

        .hub-item {
            padding: 16px 14px;
            gap: 12px;
            border-radius: 16px;
        }

        .hub-icon-box {
            width: 40px;
            height: 40px;
            font-size: 16px;
            border-radius: 12px;
        }

        .hub-item-val {
            font-size: 14px;
        }

        .hub-social-box {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
            padding: 16px 14px;
            border-radius: 16px;
        }

        .hub-social-links {
            width: 100%;
            justify-content: flex-start;
        }
    }
</style>
@endpush

@section('content')
    @php
        $wa = preg_replace('/[^0-9]/', '', (string) ($company->whatsapp ?? ''));
        $cleanPhone = preg_replace('/[^0-9+]/', '', (string) ($company->phone ?? ''));
        $fullAddress = trim(($company->address ? $company->address . ', ' : '') . ($company->city ?? ''), ', ');
        $mapsSearchUrl = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($fullAddress ?: 'PT Manufactur Dynamic Indonesia Malang');
        $prefilledMachine = request('mesin');
    @endphp

    {{-- ============ 1. CONTACT HERO (MONOCHROME INDUSTRIAL) ============ --}}
    <section class="contact-hero-wrap">
        <div class="contact-hero-bg"></div>
        <div class="contact-hero-overlay"></div>

        <div class="contact-hero-inner">

            <h1 class="contact-hero-title">
                Hubungi Tim <span>Rekayasa Manufaktur</span> Kami
            </h1>

            <p class="contact-hero-desc">
                Diskusikan kebutuhan spesifikasi mesin pengolahan kakao dan kopi, permintaan penawaran harga (RFQ), kustomisasi rancang bangun, serta penjadwalan kunjungan uji coba di workshop kami.
            </p>

            <div class="contact-hero-stats">
                <div class="c-hero-stat-item">
                    <div class="c-hero-stat-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div class="c-hero-stat-text">
                        <strong>1x24 Jam</strong>
                        <span>Respons Cepat</span>
                    </div>
                </div>

                <div class="c-hero-stat-div"></div>

                <div class="c-hero-stat-item">
                    <div class="c-hero-stat-icon">
                        <i class="fas fa-screwdriver-wrench"></i>
                    </div>
                    <div class="c-hero-stat-text">
                        <strong>Konsultasi Teknis</strong>
                        <span>Spesifikasi &amp; Kapasitas</span>
                    </div>
                </div>

                <div class="c-hero-stat-div"></div>

                <div class="c-hero-stat-item">
                    <div class="c-hero-stat-icon">
                        <i class="fas fa-truck-fast"></i>
                    </div>
                    <div class="c-hero-stat-text">
                        <strong>Seluruh Indonesia</strong>
                        <span>Pengiriman &amp; Instalasi</span>
                    </div>
                </div>
            </div>
        </div>

        <a href="#kontak-utama" class="contact-scroll-indicator" aria-label="Gulir ke formulir kontak">
            <span>Explore</span>
            <i class="fas fa-chevron-down"></i>
        </a>
    </section>

    {{-- ============ 2. MAIN CONTACT SECTION (INFO HUB & FORM) ============ --}}
    <section class="contact-main-wrap" id="kontak-utama">
        <div class="contact-container">
            <div class="contact-grid">

                {{-- SISI KIRI: SALURAN KOMUNIKASI LANGSUNG (BENTO CARDS) --}}
                <div class="contact-hub-card">
                    <div class="hub-header">
                        <h2>Terhubung Langsung dengan Representatif Kami</h2>
                        <p>Pilih saluran komunikasi resmi {{ $company->name }} yang paling nyaman untuk respon cepat dan konsultasi teknis yang komprehensif.</p>
                    </div>

                    {{-- Card WhatsApp --}}
                    @if ($wa)
                        <a href="https://wa.me/{{ $wa }}?text={{ urlencode('Halo ' . $company->name . ', saya ingin konsultasi terkait kebutuhan mesin pengolahan.') }}"
                           target="_blank" rel="noopener noreferrer" class="hub-item">
                            <div class="hub-icon-box">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <div class="hub-item-content">
                                <span class="hub-item-label">
                                    WhatsApp Bisnis Resmi
                                </span>
                                <div class="hub-item-val">+{{ $wa }}</div>
                                <div class="hub-item-sub">Respons cepat untuk konsultasi spesifikasi mesin &amp; jadwal workshop</div>
                            
                            </div>
                        </a>
                    @endif

                    {{-- Card Telepon --}}
                    @if ($company->phone)
                        <a href="tel:{{ $cleanPhone }}" class="hub-item">
                            <div class="hub-icon-box">
                                <i class="fas fa-phone-volume"></i>
                            </div>
                            <div class="hub-item-content">
                                <span class="hub-item-label">Telepon Kantor &amp; Fabrikasi</span>
                                <div class="hub-item-val">{{ $company->phone }}</div>
                                <div class="hub-item-sub">Layanan sambungan telepon kantor dan diskusi penawaran resmi</div>
                            </div>
                        </a>
                    @endif

                    {{-- Card Email --}}
                    @if ($company->email)
                        <a href="mailto:{{ $company->email }}" class="hub-item">
                            <div class="hub-icon-box">
                                <i class="fas fa-envelope-open-text"></i>
                            </div>
                            <div class="hub-item-content">
                                <span class="hub-item-label">Email Penawaran &amp; Pengadaan (RFQ)</span>
                                <div class="hub-item-val">{{ $company->email }}</div>
                                
                            </div>
                        </a>
                    @endif

                    {{-- Card Alamat Workshop --}}
                    @if ($company->address || $company->city)
                        <div class="hub-item">
                            <div class="hub-icon-box">
                                <i class="fas fa-location-dot"></i>
                            </div>
                            <div class="hub-item-content">
                                <span class="hub-item-label">Alamat Kantor &amp; Workshop Fabrikasi</span>
                                <div class="hub-item-val">{{ $fullAddress }}</div>
                                <a href="{{ $mapsSearchUrl }}" target="_blank" rel="noopener noreferrer" class="btn-route-action">
                                    <i class="fas fa-diamond-turn-right"></i> Petunjuk Rute Google Maps
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- Card Jam Operasional --}}
                    <div class="hub-item">
                        <div class="hub-icon-box">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="hub-item-content">
                            <span class="hub-item-label">Waktu Operasional Layanan</span>
                            <div class="hub-item-val">Senin – Jumat: 08.00 – 17.00 WIB</div>
                            <div class="hub-item-sub">Sabtu: 08.00 – 12.00 WIB &bull; Minggu &amp; Hari Libur: Tutup</div>
                        </div>
                    </div>

                    {{-- Media Sosial --}}
                    <div class="hub-social-box">
                        <div class="hub-social-text">
                            <strong>Ikuti Kanal Informasi Kami</strong>
                            <span>Update dokumentasi uji mesin &amp; pengiriman</span>
                        </div>
                        <div class="hub-social-links">
                            @if ($company->facebook)
                                <a href="{{ $company->facebook }}" target="_blank" rel="noopener" class="hub-social-btn" aria-label="Facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                            @endif
                            @if ($company->instagram)
                                <a href="{{ $company->instagram }}" target="_blank" rel="noopener" class="hub-social-btn" aria-label="Instagram">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            @endif
                            @if ($company->linkedin)
                                <a href="{{ $company->linkedin }}" target="_blank" rel="noopener" class="hub-social-btn" aria-label="LinkedIn">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            @endif
                            @if ($company->youtube)
                                <a href="{{ $company->youtube }}" target="_blank" rel="noopener" class="hub-social-btn" aria-label="YouTube">
                                    <i class="fab fa-youtube"></i>
                                </a>
                            @endif
                            @if (!$company->facebook && !$company->instagram && !$company->linkedin && !$company->youtube)
                                <a href="#" class="hub-social-btn" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" class="hub-social-btn" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                                <a href="#" class="hub-social-btn" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                            @endif
                        </div>
                    </div>

                </div>

                {{-- SISI KANAN: FORMULIR KONSULTASI & RFQ --}}
                <div class="contact-form-card">
                    <div class="form-card-header">
                        <h3>Kirim Formulir Konsultasi</h3>
                    

                    @if ($prefilledMachine)
                        <div class="inquiry-notice-banner">
                            <i class="fas fa-circle-info"></i>
                            <div>
                                Anda sedang mengonsultasikan model mesin: <strong>"{{ $prefilledMachine }}"</strong>. Formulir di bawah telah disesuaikan secara otomatis.
                            </div>
                        </div>
                    @endif

                    <form id="contactForm" onsubmit="handleContactSubmit(event)">
                        <div class="form-field-grid">
                            <div class="form-field-group">
                                <label for="contactName">Nama Lengkap <span class="req">*</span></label>
                                <input type="text" id="contactName" class="form-field-control" placeholder="Contoh: Budi Santoso" required>
                            </div>
                            <div class="form-field-group">
                                <label for="contactEmail">Alamat Email <span class="req">*</span></label>
                                <input type="email" id="contactEmail" class="form-field-control" placeholder="email@perusahaan.com" required>
                            </div>
                        </div>

                        <div class="form-field-grid">
                            <div class="form-field-group">
                                <label for="contactPhone">No. WhatsApp / Telepon <span class="req">*</span></label>
                                <input type="tel" id="contactPhone" class="form-field-control" placeholder="+62 812 xxxx xxxx" required>
                            </div>
                            <div class="form-field-group">
                                <label for="contactCompany">Nama Perusahaan / Kelompok Tani</label>
                                <input type="text" id="contactCompany" class="form-field-control" placeholder="PT / CV / Koperasi / UKM">
                            </div>
                        </div>

                        <div class="form-field-group">
                            <label for="contactProduct">Lini Produk / Kategori Solusi <span class="req">*</span></label>
                            <select id="contactProduct" class="form-field-control" required>
                                <option value="">-- Pilih Kategori Kebutuhan --</option>
                                <option value="Mesin Kakao (MD-Cocoa Series)" {{ stripos((string)$prefilledMachine, 'kakao') !== false || stripos((string)$prefilledMachine, 'cocoa') !== false ? 'selected' : '' }}>
                                    Mesin Pengolahan Kakao (MD-Cocoa Series)
                                </option>
                                <option value="Mesin Kopi (MD-Coffee Series)" {{ stripos((string)$prefilledMachine, 'kopi') !== false || stripos((string)$prefilledMachine, 'coffee') !== false ? 'selected' : '' }}>
                                    Mesin Pengolahan Kopi (MD-Coffee Series)
                                </option>
                                <option value="Pabrik Kakao Skala Industri Terpadu">Pabrik Kakao Skala Industri Terpadu</option>
                                <option value="Integrated Roastery & Coffee Processing Line">Integrated Roastery &amp; Coffee Processing Line</option>
                                <option value="Custom Engineering & Rancang Bangun Mesin">Custom Engineering &amp; Rancang Bangun Mesin</option>
                                <option value="Suku Cadang Asli & Layanan Purna Jual">Suku Cadang Asli &amp; Layanan Purna Jual</option>
                                <option value="Konsultasi Teknis Lainnya">Konsultasi Teknis Lainnya</option>
                            </select>
                        </div>

                        <div class="form-field-group">
                            <label for="contactMessage">Rincian Kebutuhan / Pertanyaan Teknis <span class="req">*</span></label>
                            <textarea id="contactMessage" class="form-field-control"
                                      placeholder="Sampaikan kapasitas produksi yang diinginkan (kg/jam atau kg/batch), daya listrik yang tersedia, spesifikasi material, atau batas timeline proyek Anda."
                                      required>{{ $prefilledMachine ? 'Halo, saya tertarik untuk berkonsultasi mengenai unit mesin: ' . $prefilledMachine . '. Mohon informasi spesifikasi teknis dan penawaran harganya.' : '' }}</textarea>
                        </div>

                        <button type="submit" class="form-submit-btn" id="submitBtn">
                            <i class="fas fa-paper-plane"></i> Kirim Pesan &amp; Mulai Konsultasi
                        </button>

                        <div class="form-trust-note">
                            <i class="fas fa-shield-halved"></i>
                            <span>Data kontak dan rencana teknis Anda dijaga kerahasiaannya untuk keperluan penawaran resmi.</span>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </section>

    {{-- ============ 3. MAP EMBED SECTION (WORKSHOP LOCATION) ============ --}}
    <section class="contact-map-section">
        <div class="contact-container">
            <div class="map-header">
                
                <h2>Kunjungi Fasilitas Fabrikasi Kami</h2>
                <p>Kami menyambut kunjungan Anda untuk inspeksi unit mesin, trial bahan baku (uji coba hasil giling/roasting), atau audiensi teknis bersama teknisi rancang bangun kami.</p>
            </div>

            <div class="map-card-wrapper">
                <div class="map-frame-container">
                    @if ($company->map_embed)
                        {!! $company->map_embed !!}
                    @else
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126438.33878193836!2d112.5617424!3d-7.9786395!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd6282206340f29%3A0x3027a76e352bea0!2sMalang%2C%20Kota%20Malang%2C%20Jawa%20Timur!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid"
                                allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Peta Lokasi Workshop {{ $company->name }}"></iframe>
                    @endif
                </div>

                <div class="map-footer-bar">
                    <div class="map-footer-info">
                        <i class="fas fa-map-location-dot"></i>
                        <span>{{ $fullAddress ?: 'Malang, Jawa Timur, Indonesia' }}</span>
                    </div>
                    <a href="{{ $mapsSearchUrl }}" target="_blank" rel="noopener noreferrer" class="btn-open-gmaps">
                        <span>Buka di Google Maps</span>
                        <i class="fas fa-arrow-up-right-from-square"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ 4. SWITCHER BANNER (EXPLORE CATALOG) ============ --}}
    <section class="contact-switcher-wrap">
        <div class="contact-container">
            <div class="contact-switcher-card">
                <div class="c-switcher-content">
                   
                    <h3>Ingin Mempelajari Spesifikasi Lengkap Setiap Model Mesin?</h3>
                    <p>Telusuri katalog terpisah untuk mesin pengolahan biji kakao atau lini sangrai biji kopi kami, lengkap dengan parameter kapasitas dan fitur pendukung.</p>
                </div>
                <div class="c-switcher-actions">
                    <a href="{{ route('products.cocoa') }}" class="c-switcher-btn primary">
                        <i class="fas fa-seedling"></i> MD-Cocoa Series
                    </a>
                    <a href="{{ route('products.coffee') }}" class="c-switcher-btn outline">
                        <i class="fas fa-mug-hot"></i> MD-Coffee Series
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
<script>
function handleContactSubmit(e) {
    e.preventDefault();
    const name = document.getElementById('contactName')?.value.trim() || '';
    const email = document.getElementById('contactEmail')?.value.trim() || '';
    const phone = document.getElementById('contactPhone')?.value.trim() || '';
    const comp = document.getElementById('contactCompany')?.value.trim() || '-';
    const product = document.getElementById('contactProduct')?.value.trim() || 'Konsultasi Umum';
    const message = document.getElementById('contactMessage')?.value.trim() || '';

    @if ($wa)
        const waNumber = '{{ $wa }}';
        const waText = encodeURIComponent(
            `*KONSULTASI MESIN — ${product}*\n\n` +
            `*Nama Lengkap:* ${name}\n` +
            `*Perusahaan/Usaha:* ${comp}\n` +
            `*Email:* ${email}\n` +
            `*No. Kontak:* ${phone}\n` +
            `*Produk / Kebutuhan:* ${product}\n\n` +
            `*Rincian Kebutuhan & Spesifikasi:*\n${message}\n\n` +
            `_Dikirim melalui formulir kontak resmi PT Manufactur Dynamic Indonesia_`
        );

        const confirmRedirect = confirm(
            'Terima kasih, ' + name + '!\n\nPesan konsultasi Anda telah siap. Apakah Anda ingin langsung meneruskan ke WhatsApp resmi untuk respon cepat oleh perwakilan teknis kami?'
        );

        if (confirmRedirect) {
            window.open(`https://wa.me/${waNumber}?text=${waText}`, '_blank');
        }
    @else
        alert('Terima kasih, ' + name + '! Pesan Anda telah kami terima. Tim perwakilan kami akan segera menghubungi Anda dalam 1x24 jam kerja.');
    @endif

    e.target.reset();
}
</script>
@endpush
