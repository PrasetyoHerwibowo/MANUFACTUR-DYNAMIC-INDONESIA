<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin — {{ $company->name }}</title>

    <link rel="icon" href="{{ $company->logoUrl() ?? asset('uploads/placeholder.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        html, body {
            width: 100vw !important;
            height: 100vh !important;
            overflow: hidden !important;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #ffffff;
        }
        .split-wrapper {
            display: flex !important;
            flex-direction: row !important;
            width: 100vw !important;
            height: 100vh !important;
        }
        .split-left {
            width: 50% !important;
            height: 100% !important;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 40px;
            box-sizing: border-box;
            color: #ffffff;
        }
        .split-right {
            width: 50% !important;
            height: 100% !important;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 40px 60px;
            box-sizing: border-box;
            background-color: #ffffff;
        }
        @media (max-width: 768px) {
            .split-left { display: none !important; }
            .split-right { width: 100% !important; padding: 24px; }
        }
    </style>
</head>
<body>
    <!-- Container Utama Murni Split Screen 50:50 Kiri Kanan -->
    <div class="split-wrapper">
        <!-- SISI KIRI: Hero Image (Pasti 50% Lebar & 100% Tinggi) -->
        <div class="split-left">
            <img src="{{ asset('images/hero-factory.jpg') }}" 
                 onerror="this.src='https://images.unsplash.com/photo-1511537190424-bbbab87ac5eb?q=80&w=1000&auto=format&fit=crop'" 
                 alt="Factory" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; z-index: 1;">
            <div style="position: absolute; inset: 0; background-color: rgba(0, 0, 0, 0.6); z-index: 2;"></div>

            <!-- Logo & Title Kiri -->
            <div style="position: relative; z-index: 3; display: flex; align-items: center; gap: 12px;">
                @if ($company->logoUrl())
                    <img src="{{ asset('images/logo-admin.png') }}" alt="Logo Admin" style="width: 40px; height: 40px; object-fit: contain;">
                @else
                    <span style="width: 36px; height: 36px; background-color: #ea580c; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 14px;">MD</span>
                @endif
                <h3 style="font-size: 14px; font-weight: 700; color: #ffffff;">{{ $company->name }}</h3>
            </div>

            <!-- Text Tagline -->
            <div style="position: relative; z-index: 3; margin-top: auto; margin-bottom: auto;">
                <h2 style="font-size: 26px; font-weight: 700; line-height: 1.3; color: #ffffff;">Solusi Mesin Pengolahan Kopi & Kakao Berkualitas</h2>
                <p style="margin-top: 8px; font-size: 13px; color: #d6d3d1;">Mendukung industri pangan dengan teknologi yang handal dan efisien.</p>
            </div>

            <div style="position: relative; z-index: 3;"></div>
        </div>

        <!-- SISI KANAN: Form Login (Pasti 50% Lebar & 100% Tinggi) -->
        <div class="split-right">
            
            <!-- Logo Mini -->
            <div>
                @if ($company->logoUrl())
                    <img src="{{ $company->logoUrl() }}" alt="Logo" style="width: 36px; height: 36px; object-fit: contain; border-radius: 8px;">
                @else
                    <span style="width: 36px; height: 36px; background-color: #f97316; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12px; color: #ffffff;">MD</span>
                @endif
            </div>

            <!-- Content Form -->
            <div style="margin-top: auto; margin-bottom: auto; width: 100%; max-w: 400px;">
                <h2 style="font-size: 22px; font-weight: 700; color: #1c1917;">Panel Admin</h2>
                <p style="font-size: 12px; color: #78716c; margin-top: 2px;">Masuk untuk mengakses sistem.</p>

                @include('public.partials.flash')

                @if ($errors->any())
                    <div style="margin-top: 12px; background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 10px;">
                        <ul style="font-size: 12px; color: #dc2626; padding-left: 16px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.attempt') }}" style="margin-top: 20px;">
                    @csrf
                
                    <!-- Email -->
                    <div style="margin-bottom: 14px;">
                        <label for="email" style="display: block; font-size: 12px; font-weight: 600; color: #44403c; margin-bottom: 4px;">Email</label>
                        <div style="position: relative;">
                            <span style="position: absolute; top: 0; bottom: 0; left: 12px; display: flex; align-items: center; pointer-events: none; color: #9ca3af;">
                                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </span>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                                placeholder="admin@example.com"
                                style="width: 100%; box-sizing: border-box; height: 40px; padding-left: 40px; padding-right: 12px; border-radius: 10px; border: 1px solid #e7e5e4; background-color: #fafaf9; font-size: 13px; outline: none;">
                        </div>
                    </div>

                    <!-- Password -->
                    <div style="margin-bottom: 14px;">
                        <label for="password" style="display: block; font-size: 12px; font-weight: 600; color: #44403c; margin-bottom: 4px;">Password</label>
                        <div style="position: relative;">
                            <span style="position: absolute; top: 0; bottom: 0; left: 12px; display: flex; align-items: center; pointer-events: none; color: #9ca3af;">
                                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </span>
                            <input type="password" name="password" id="password" required
                                placeholder="••••••••"
                                style="width: 100%; box-sizing: border-box; height: 40px; padding-left: 40px; padding-right: 12px; border-radius: 10px; border: 1px solid #e7e5e4; background-color: #fafaf9; font-size: 13px; outline: none;">
                        </div>
                    </div>

                    <!-- Checkbox -->
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
                        <input type="checkbox" name="remember" id="remember" value="1" style="width: 16px; height: 16px; cursor: pointer;">
                        <label for="remember" style="font-size: 12px; color: #57534e; cursor: pointer;">Ingat saya di perangkat ini</label>
                    </div>

                    <!-- Tombol Submit -->
                    <button type="submit"
                        style="width: 100%; height: 42px; background-color: #8B4513; color: #ffffff; border: none; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                       <span>Masuk</span> 
                       <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </form>

                <p style="margin-top: 16px; text-align: center; font-size: 11px; color: #a8a29e;">
                    Hanya administrator yang dapat mengakses panel ini.
                </p>
            </div>

            <!-- Footer -->
            <div style="text-align: center; font-size: 11px; color: #a8a29e;">
                &copy; {{ now()->year }} {{ $company->name }}
            </div>

        </div>

    </div>
</body>
</html>