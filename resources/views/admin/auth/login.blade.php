<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel Administrator — {{ $company->name }}</title>

    <link rel="icon" href="{{ $company->logoUrl() ?? asset('uploads/placeholder.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .font-serif-title {
            font-family: 'Playfair Display', Georgia, serif;
        }
        .glass-login-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.62) 0%, rgba(240, 244, 248, 0.45) 50%, rgba(220, 227, 236, 0.55) 100%);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.7);
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.4) inset;
        }
        .glass-info-card {
            background: rgba(18, 18, 20, 0.65);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.5);
        }
    </style>
</head>
<body class="min-h-screen w-full relative overflow-x-hidden antialiased text-zinc-900 selection:bg-zinc-900 selection:text-white">

    <!-- Background Image Fullscreen -->
    <div class="fixed inset-0 z-0">
        <img src="{{ asset('images/GambarBG.jpg') }}" 
             onerror="this.src='{{ asset('images/bg.png') }}'" 
             alt="{{ $company->name }} Architecture" 
             class="w-full h-full object-cover object-center scale-[1.01]">
        <!-- Subtle gradient overlay to enhance readability -->
        <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-black/20 pointer-events-none"></div>
    </div>

    <!-- Tombol Kembali ke Beranda (Top-Left) -->
    <div class="relative z-20 p-6 sm:p-8">
        <a href="{{ route('home') }}" 
           class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-black/40 hover:bg-black/60 text-white text-xs font-semibold backdrop-blur-md transition-all duration-200 border border-white/15 shadow-sm hover:scale-[1.02]">
            <i class="fas fa-arrow-left text-[11px]"></i>
            <span>Kembali ke Beranda</span>
        </a>
    </div>

    <!-- Content Wrapper: 2 Kolom (Kiri Bawah Info Mesin, Kanan Card Login) -->
    <div class="relative z-10 max-w-7xl mx-auto px-6 sm:px-8 pb-10 pt-2 sm:pt-4 min-h-[calc(100vh-100px)] flex flex-col justify-between">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center my-auto">
            
            <!-- Kolom Kiri: Headline / Tagline Card di Kiri Bawah -->
            <div class="lg:col-span-7 flex flex-col justify-end mt-auto pt-8 lg:pt-0">
                <div class="glass-info-card rounded-3xl p-6 sm:p-8 max-w-xl text-white">
                    <div class="inline-block px-3 py-1 rounded-full bg-white/10 border border-white/15 text-[11px] font-semibold text-zinc-200 tracking-wide mb-4">
                        Industrial Machinery System
                    </div>
                    
                    <h2 class="text-2xl sm:text-3xl font-extrabold leading-snug text-white mb-2.5 tracking-tight">
                        Solusi Mesin Pengolahan Presisi Berkualitas
                    </h2>
                    
                    <p class="text-xs sm:text-sm text-zinc-300 leading-relaxed mb-6 font-normal">
                        Spesialis Perancangan & Produksi Mesin Pengolahan Kopi dan Kakao
                    </p>
                    
                    <!-- Carousel Indicator Dots -->
                    <div class="flex items-center gap-1.5">
                        <span class="w-8 h-1.5 rounded-full bg-white"></span>
                        <span class="w-1.5 h-1.5 rounded-full bg-white/40"></span>
                        <span class="w-1.5 h-1.5 rounded-full bg-white/40"></span>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Login Form Glass Card -->
            <div class="lg:col-span-5 flex justify-center lg:justify-end">
                <div class="glass-login-card w-full max-w-[430px] rounded-[32px] p-7 sm:p-9">
                    
                    <!-- Logo Eiko dalam Container Hitam -->
                    <div class="flex justify-center mb-5">
                        <div class="inline-flex items-center justify-center bg-black px-6 py-2.5 rounded-2xl shadow-md border border-zinc-800">
                            <img src="{{ asset('images/logo_eiko.png') }}" 
                                 alt="EIKO Coffee Roaster Machine" 
                                 class="h-6 w-auto object-contain brightness-0 invert">
                        </div>
                    </div>

                    <!-- Welcome Title -->
                    <div class="text-center mb-6">
                        <h1 class="font-serif-title text-xl sm:text-2xl font-bold text-zinc-900 tracking-tight leading-snug">
                            Welcome Back to<br>
                            <span>{{ $company->name }}</span>
                        </h1>
                        <p class="text-xs text-zinc-600 mt-1.5 font-medium">
                            Masuk untuk mengelola operasional sistem
                        </p>
                    </div>

                    <!-- Flash Messages & Error Handling -->
                    @if ($errors->any())
                        <div class="mb-4 rounded-xl bg-red-500/10 border border-red-500/25 p-3 text-xs text-red-700 backdrop-blur-sm">
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('status'))
                        <div class="mb-4 rounded-xl bg-emerald-500/10 border border-emerald-500/25 p-3 text-xs text-emerald-800 backdrop-blur-sm">
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- Form Login -->
                    <form method="POST" action="{{ route('admin.login.attempt') }}" class="space-y-4">
                        @csrf

                        <!-- Email Administrator -->
                        <div>
                            <label for="email" class="block text-xs font-semibold text-zinc-700 mb-1.5">
                                Email Administrator
                            </label>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autofocus
                                   placeholder="admin@example.com"
                                   class="w-full px-4 py-3 rounded-xl bg-white/70 border border-white/80 focus:bg-white focus:border-zinc-500 focus:ring-2 focus:ring-zinc-400/20 text-sm text-zinc-900 placeholder-zinc-400 outline-none transition duration-150 shadow-sm">
                        </div>

                        <!-- Password Field with Toggle -->
                        <div>
                            <label for="password" class="block text-xs font-semibold text-zinc-700 mb-1.5">
                                Password
                            </label>
                            <div class="relative">
                                <input type="password" 
                                       name="password" 
                                       id="password" 
                                       required
                                       placeholder="Masukkan password Anda"
                                       class="w-full pl-4 pr-11 py-3 rounded-xl bg-white/70 border border-white/80 focus:bg-white focus:border-zinc-500 focus:ring-2 focus:ring-zinc-400/20 text-sm text-zinc-900 placeholder-zinc-400 outline-none transition duration-150 shadow-sm">
                                
                                <button type="button" 
                                        id="togglePasswordBtn" 
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-500 hover:text-zinc-800 transition p-1.5"
                                        aria-label="Tampilkan password">
                                    <i class="fa-regular fa-eye text-sm" id="togglePasswordIcon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Checkbox Remember -->
                        <div class="flex items-center text-xs pt-1 pb-1">
                            <label class="inline-flex items-center gap-2 cursor-pointer text-zinc-600 select-none">
                                <input type="checkbox" 
                                       name="remember" 
                                       id="remember" 
                                       value="1" 
                                       class="rounded border-zinc-400 text-zinc-900 focus:ring-zinc-600">
                                <span class="font-normal text-xs text-zinc-600">Ingat saya di perangkat ini</span>
                            </label>
                        </div>

                        <!-- Submit Button (Di tengah, lebar, rapi) -->
                        <div class="pt-3">
                            <button type="submit"
                                    class="w-full py-3.5 px-6 rounded-xl bg-[#0f172a] hover:bg-black text-white font-semibold text-sm transition-all duration-200 shadow-md hover:shadow-lg active:scale-[0.99] flex items-center justify-center tracking-wide">
                                Login
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>

    </div>

    <!-- Toggle Password Visibility Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('togglePasswordBtn');
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');

            if (toggleBtn && passwordInput && icon) {
                toggleBtn.addEventListener('click', function () {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    
                    if (isPassword) {
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            }
        });
    </script>
</body>
</html>
