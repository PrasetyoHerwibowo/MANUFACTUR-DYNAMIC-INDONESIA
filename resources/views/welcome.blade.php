<!DOCTYPE html>
<html class="scroll-smooth" lang="id">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>EIKO COFFEE ROASTER - Mesin Roasting Kopi Berkualitas untuk Indonesia</title>
<!-- Tailwind CSS v3 with Plugins -->
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<!-- FontAwesome Icons -->
<link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" rel="stylesheet"/>
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600;700&amp;family=Plus+Jakarta+Sans:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<!-- Tailwind Custom Config -->
<script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            brand: {
              red: '#DC2626',
              redHover: '#B91C1C',
              dark: '#0B111A',
              navy: '#131B26',
              slate: '#1E293B',
              steel: '#334155',
              grayLight: '#F1F5F9'
            }
          },
          fontFamily: {
            sans: ['"Plus Jakarta Sans"', 'sans-serif'],
            display: ['"Oswald"', 'sans-serif'],
          }
        }
      }
    }
</script>
<!-- Custom Styles: Typography & Atmosphere -->
<style data-purpose="typography">
    .font-condensed {
      font-family: 'Oswald', sans-serif;
      letter-spacing: -0.01em;
    }
    .text-shadow-sm {
      text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    html:not(.dark) .text-shadow-sm {
      text-shadow: none;
    }
    .text-shadow-lg {
      text-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    html:not(.dark) .text-shadow-lg {
      text-shadow: none;
    }
</style>
<!-- Custom Styles: Layout & Interactive Carousel -->
<style data-purpose="carousel-and-fx">
    .carousel-container::-webkit-scrollbar {
      display: none;
    }
    .carousel-container {
      -ms-overflow-style: none;
      scrollbar-width: none;
    }
    .hero-gradient-overlay {
      background: linear-gradient(90deg, rgba(8, 14, 23, 0.95) 0%, rgba(13, 22, 36, 0.82) 48%, rgba(11, 20, 34, 0.45) 100%);
    }
    .hero-bottom-fade {
      background: linear-gradient(180deg, rgba(11, 17, 26, 0) 0%, rgba(11, 17, 26, 0.85) 85%, #0B111A 100%);
    }
    /* Light mode hero gradient override */
    html:not(.dark) .hero-gradient-overlay {
      background: linear-gradient(90deg, rgba(255, 255, 255, 0.92) 0%, rgba(249, 250, 251, 0.80) 48%, rgba(249, 250, 251, 0.40) 100%);
    }
    html:not(.dark) .hero-bottom-fade {
      background: linear-gradient(180deg, rgba(255, 255, 255, 0) 0%, rgba(249, 250, 251, 0.85) 85%, #F9FAFB 100%);
    }
</style>
</head>
<body class="bg-gray-50 text-slate-900 dark:bg-brand-dark dark:text-slate-100 font-sans antialiased overflow-x-hidden transition-colors duration-300">
<!-- BEGIN: HeaderAndNavigation -->
<header class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 backdrop-blur-md bg-white/85 dark:bg-brand-dark/85 border-b border-gray-200 dark:border-slate-800/80">
<nav aria-label="Main Navigation" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
<!-- Brand Logo -->
<div class="flex items-center gap-3">
<a class="flex items-center gap-2 group" href="#">
<div class="w-10 h-10 rounded-lg bg-red-600 flex items-center justify-center text-white font-black text-xl shadow-lg shadow-red-600/30 group-hover:scale-105 transition-transform">
<i class="ti ti-flame"></i>
</div>
<span class="font-condensed text-2xl md:text-3xl tracking-wider text-slate-900 dark:text-white font-bold uppercase group-hover:text-red-500 transition-colors">
            EIKO <span class="text-slate-500 dark:text-slate-300 font-normal">COFFEE ROASTER</span>
</span>
</a>
</div>
<!-- Navigation Menu -->
<div class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600 dark:text-slate-300">
<a class="text-slate-900 dark:text-white hover:text-red-600 dark:hover:text-red-500 transition-colors py-1 relative after:absolute after:bottom-0 after:left-0 after:right-0 after:h-0.5 after:bg-red-600" href="#hero">Home</a>
<a class="hover:text-slate-900 dark:hover:text-white transition-colors py-1" href="#about">About Us</a>
<a class="hover:text-slate-900 dark:hover:text-white transition-colors py-1" href="#products">Product</a>
<a class="hover:text-slate-900 dark:hover:text-white transition-colors py-1" href="#services">Services</a>
<a class="hover:text-slate-900 dark:hover:text-white transition-colors py-1" href="#contact">Contact</a>
</div>
<!-- Utility & Language -->
<div class="flex items-center gap-4">
<a class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors" href="#login">Sign in</a>
<button class="flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1.5 rounded-full border border-gray-300 dark:border-slate-700 bg-gray-100 dark:bg-slate-800/60 text-slate-700 dark:text-slate-200 hover:border-red-500 transition-colors" title="Ubah Bahasa">
<i class="ti ti-world text-red-500 text-sm"></i>
<span>Ind</span>
<i class="ti ti-chevron-down text-[10px] text-slate-400"></i>
</button>
<!-- Theme Toggle Button -->
<button id="themeToggle" class="flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1.5 rounded-full border border-gray-300 dark:border-slate-700 bg-gray-100 dark:bg-slate-800/60 text-slate-700 dark:text-slate-200 hover:border-red-500 transition-colors" title="Ubah Tema">
<i class="ti ti-moon text-indigo-500 dark:hidden text-sm"></i>
<i class="ti ti-sun text-yellow-500 hidden dark:block text-sm"></i>
<span class="dark:hidden">Mode Gelap</span>
<span class="hidden dark:block">Mode Terang</span>
</button>
<!-- Mobile hamburger toggle button -->
<button aria-label="Open Menu" class="md:hidden text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white text-xl p-1 focus:outline-none" id="mobileMenuBtn">
<i class="ti ti-menu-2"></i>
</button>
</div>
</nav>
</header>
<!-- END: HeaderAndNavigation -->

<!-- BEGIN: HeroSection -->
<section class="relative min-h-[720px] lg:min-h-[820px] flex items-center pt-24 pb-16 bg-gray-100 dark:bg-slate-950 overflow-hidden" id="hero">
<!-- Hero Background Image With Coffee Roaster Machine -->
<div class="absolute inset-0 z-0">
<img alt="Industrial Coffee Roasting Machine Drum and Cooling Tray in Workshop" class="w-full h-full object-cover object-right md:object-center filter brightness-90 dark:brightness-65 contrast-110" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAVwJV2QCmuwwVKkuxtcGFRIyZF2qKwjTNfVGHvYv40FkmmbKCn8T99edmvT-6f7hzmHXeeKHfQkjD3IOyFPYsJLUMj0CmDTmkh9Hhvg9Ap0zAMjyAhEUAF5mrlWH3Y0DPUGlT429l10ilHCIJyBRN6ufgbm7EPaiXRY2MWo5nC0oe5bpolPX0FWkkYTFWUpkGTfGitFQX-KRET1qgu-5-UA8IIShtshX6ZdUugsRlhBuCb-YZQYuwm"/>
<!-- Dramatic Atmospheric Gradient Overlays -->
<div class="absolute inset-0 hero-gradient-overlay"></div>
<div class="absolute inset-0 hero-bottom-fade"></div>
</div>
<div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
<div class="max-w-2xl lg:max-w-3xl">
<!-- Red Pill Badge -->
<div class="inline-flex items-center gap-2 px-4 py-1.5 mb-6 rounded-md bg-red-600 text-white font-bold text-xs tracking-wider uppercase shadow-md shadow-red-600/30">
<span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
<span>Easy | Economic | Excellent</span>
</div>
<!-- Main Headline -->
<h1 class="font-condensed text-5xl sm:text-6xl md:text-7xl lg:text-[5rem] font-bold text-slate-900 dark:text-white uppercase leading-[0.95] tracking-tight mb-5 text-shadow-lg">
          Mesin <span class="text-red-600">Roasting</span> Kopi Berkualitas untuk <span class="text-red-600">Indonesia</span>
</h1>
<!-- Subtitle with Red Accent Bar -->
<div class="flex items-center gap-3 mb-4">
<span class="w-1.5 h-6 bg-red-600 rounded-full inline-block"></span>
<p class="text-lg md:text-xl font-semibold text-slate-700 dark:text-slate-200 tracking-wide">
            Coffee Roasting Machine Manufacturer
          </p>
</div>
<!-- Paragraph Description -->
<p class="text-slate-600 dark:text-slate-300 text-sm md:text-base leading-relaxed mb-8 max-w-xl text-shadow-sm font-normal">
          Produsen mesin roasting kopi berkualitas tinggi sejak 2015. Dipercaya oleh customer dari seluruh Indonesia hingga mancanegara: Malaysia, Brunei, Timor Leste, Thailand, Vietnam, Jepang, dan lainnya.
        </p>
<!-- CTA Buttons -->
<div class="flex flex-wrap items-center gap-4">
<a class="inline-flex items-center gap-2.5 px-6 py-3.5 rounded-lg bg-red-600 hover:bg-red-700 text-white font-semibold text-sm transition-all shadow-lg shadow-red-600/40 hover:shadow-red-600/60 transform hover:-translate-y-0.5" href="#products">
<i class="ti ti-book text-base"></i>
<span>Lihat Katalog</span>
</a>
<a class="inline-flex items-center gap-2.5 px-6 py-3.5 rounded-lg border border-gray-400 dark:border-slate-500/80 bg-white/60 dark:bg-slate-900/60 hover:bg-gray-100 dark:hover:bg-slate-800/90 text-slate-900 dark:text-white font-medium text-sm transition-all backdrop-blur-sm hover:border-slate-400 dark:hover:border-slate-300 transform hover:-translate-y-0.5" href="https://wa.me/6281234567890" target="_blank">
<i class="ti ti-phone text-sm text-red-500"></i>
<span>Hubungi Kami</span>
</a>
</div>
<!-- Subtle Decorative Divider Line -->
<div class="w-full max-w-xl mt-12 pt-4 border-t border-gray-300/60 dark:border-slate-700/60 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 font-mono">
<span><i class="ti ti-settings text-red-500 mr-1.5"></i> Precision Gas &amp; Airflow Control</span>
<span><i class="ti ti-device-laptop text-red-500 mr-1.5"></i> Artisan / Cropster Connect</span>
</div>
</div>
</div>
</section>
<!-- END: HeroSection -->

<!-- BEGIN: ProductShowcaseCarousel -->
<section class="py-20 bg-gray-100 dark:bg-brand-navy border-t border-b border-gray-200 dark:border-slate-800/80 relative" id="products">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="flex flex-col md:flex-row md:items-end justify-between mb-10 pb-4">
<div>
<div class="inline-flex items-center gap-2 px-3 py-1 rounded bg-gray-200 dark:bg-slate-800 text-red-600 dark:text-red-400 font-bold text-xs uppercase tracking-wider mb-2 border border-gray-300 dark:border-slate-700">
<i class="ti ti-award"></i> Seri Mesin Roasting Unggulan
          </div>
<h2 class="font-condensed text-3xl md:text-4xl lg:text-5xl font-bold uppercase text-slate-900 dark:text-white tracking-tight">
            Katalog Unit <span class="text-red-500">Eiko Roaster</span>
</h2>
<p class="text-slate-600 dark:text-slate-400 text-sm md:text-base mt-1 max-w-2xl">
            Solusi sangrai dari micro-roastery, coffee shop specialty, hingga fasilitas industri berskala tonase.
          </p>
</div>
<!-- Navigation Buttons -->
<div class="flex items-center gap-3 mt-6 md:mt-0">
<button aria-label="Previous Slide" class="w-12 h-12 rounded-full border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800/90 text-slate-700 dark:text-white hover:bg-red-600 hover:text-white dark:hover:bg-red-600 dark:hover:text-white hover:border-red-600 transition-all flex items-center justify-center shadow-lg active:scale-95" id="carouselPrevBtn">
<i class="ti ti-chevron-left text-base"></i>
</button>
<button aria-label="Next Slide" class="w-12 h-12 rounded-full border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800/90 text-slate-700 dark:text-white hover:bg-red-600 hover:text-white dark:hover:bg-red-600 dark:hover:text-white hover:border-red-600 transition-all flex items-center justify-center shadow-lg active:scale-95" id="carouselNextBtn">
<i class="ti ti-chevron-right text-base"></i>
</button>
</div>
</div>
<!-- Carousel Cards Container -->
<div class="carousel-container flex gap-6 overflow-x-auto snap-x snap-mandatory scroll-smooth pb-6 pt-2" id="roasterCarousel">
<!-- Card 1 -->
<div class="snap-start shrink-0 w-[280px] sm:w-[320px] rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 hover:border-red-600/70 transition-all duration-300 flex flex-col group overflow-hidden shadow-xl">
<div class="relative h-56 bg-gray-100 dark:bg-slate-800 overflow-hidden flex items-center justify-center p-4">
<img alt="Eiko Nano Sample Roaster" class="w-full h-full object-cover rounded-md group-hover:scale-105 transition-transform duration-500 filter brightness-90" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDp6JpYYvagPY2hvlvlvtxh308ht2z4B1L69Vfx55olTMVRjazO8XRDt7S7kvR--AkSP7klA5L_4psf-neLyoXRfcxOrD6PJZLSR8Hhr-im9aH_9BFjNPex0Y-__RWV9D4cJZfjEUJ0FYWVVT34tYZyBLNsLjIkA9g_KOvC4zUSKkAmojlwVAtFjRr2VxZQvpPImOqIBIj0ty2SnJ4-CB-E5iml47n33P1AHQuTNRZVb4TibrNx_d5P"/>
<span class="absolute top-3 right-3 bg-red-600 text-white text-[11px] font-bold px-2.5 py-1 rounded shadow">100g - 250g</span>
</div>
<div class="p-5 flex-1 flex flex-col justify-between bg-white dark:bg-slate-900">
<div>
<p class="text-xs font-semibold text-red-600 dark:text-red-500 uppercase tracking-wider">Sample &amp; QC Roaster</p>
<h3 class="font-condensed text-2xl font-bold text-slate-900 dark:text-white uppercase mt-1">Eiko Nano 250</h3>
<p class="text-xs text-slate-500 dark:text-slate-400 mt-2 line-clamp-2">Ideal untuk sampling green bean lab QC dan profiling kopi kompetisi specialty.</p>
<ul class="mt-4 space-y-1.5 text-xs text-slate-600 dark:text-slate-300">
<li class="flex items-center gap-2"><i class="ti ti-check text-red-500 text-[10px]"></i> Digital Dual Thermocouple</li>
<li class="flex items-center gap-2"><i class="ti ti-check text-red-500 text-[10px]"></i> Electric / Micro Gas Burner</li>
<li class="flex items-center gap-2"><i class="ti ti-check text-red-500 text-[10px]"></i> USB Logging Port Support</li>
</ul>
</div>
<div class="mt-6 pt-4 border-t border-gray-200 dark:border-slate-800 flex items-center justify-between">
<span class="text-xs text-slate-500 dark:text-slate-400">Tersedia Siap Kirim</span>
<a class="text-xs font-bold text-slate-900 dark:text-white hover:text-red-600 dark:hover:text-red-400 flex items-center gap-1 group-hover:underline" href="#detail-nano">Detail Unit <i class="ti ti-arrow-right text-[10px]"></i></a>
</div>
</div>
</div>
<!-- Card 2 -->
<div class="snap-start shrink-0 w-[280px] sm:w-[320px] rounded-xl bg-white dark:bg-slate-900 border-2 border-red-600/80 transition-all duration-300 flex flex-col group overflow-hidden shadow-2xl relative">
<div class="absolute top-2 left-2 z-10 bg-red-600 text-white text-[10px] font-black uppercase tracking-widest px-2.5 py-0.5 rounded shadow">Best Seller Cafe</div>
<div class="relative h-56 bg-gray-100 dark:bg-slate-800 overflow-hidden flex items-center justify-center p-4">
<img alt="Eiko Artisan Pro 1.2 kg Roaster" class="w-full h-full object-cover rounded-md group-hover:scale-105 transition-transform duration-500 filter brightness-90" src="https://lh3.googleusercontent.com/aida-public/AB6AXuB13MhKQPPZLdubUd9eSBALgotWbs7VKM4pro4al7Vjto6DzWouuFZTdeohZGZ-QqhdtSuiNrLdjaRMqpod5fCOYBLSn_lo3NVioHsmGpJzC-2Bs4kFY9gnUQqgbKDk-2inxEvBNcDXufrzBeT7nGAi5mbF1B9mr6pMxdGUtgA1avi9LlhaFsWU9jONmdQhJ9mmgBXOEyPCjfNzAldMBETDHPeT4xfSMnmC7cHeSZIZ5nWlvS7IxW9l"/>
<span class="absolute top-3 right-3 bg-gray-900/80 dark:bg-slate-950/80 text-white text-[11px] font-bold px-2.5 py-1 rounded border border-gray-300 dark:border-slate-700 shadow">1.0kg - 1.2kg</span>
</div>
<div class="p-5 flex-1 flex flex-col justify-between bg-white dark:bg-slate-900">
<div>
<p class="text-xs font-semibold text-red-600 dark:text-red-500 uppercase tracking-wider">Specialty Coffee Shop</p>
<h3 class="font-condensed text-2xl font-bold text-slate-900 dark:text-white uppercase mt-1">Eiko Pro Artisan 1.2</h3>
<p class="text-xs text-slate-500 dark:text-slate-400 mt-2 line-clamp-2">Pilihan utama kedai kopi kekinian dengan presisi profil roast tinggi dan drum ganda.</p>
<ul class="mt-4 space-y-1.5 text-xs text-slate-600 dark:text-slate-300">
<li class="flex items-center gap-2"><i class="ti ti-check text-red-500 text-[10px]"></i> Double Wall Drum Carbon Steel</li>
<li class="flex items-center gap-2"><i class="ti ti-check text-red-500 text-[10px]"></i> Artisan &amp; Cropster Ready</li>
<li class="flex items-center gap-2"><i class="ti ti-check text-red-500 text-[10px]"></i> Adjustable Drum &amp; Airflow Speed</li>
</ul>
</div>
<div class="mt-6 pt-4 border-t border-gray-200 dark:border-slate-800 flex items-center justify-between">
<span class="text-xs text-green-600 dark:text-green-400 font-medium">Garansi 2 Tahun</span>
<a class="text-xs font-bold text-slate-900 dark:text-white hover:text-red-600 dark:hover:text-red-400 flex items-center gap-1 group-hover:underline" href="#detail-pro1">Detail Unit <i class="ti ti-arrow-right text-[10px]"></i></a>
</div>
</div>
</div>
<!-- Card 3 -->
<div class="snap-start shrink-0 w-[280px] sm:w-[320px] rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 hover:border-red-600/70 transition-all duration-300 flex flex-col group overflow-hidden shadow-xl">
<div class="relative h-56 bg-gray-100 dark:bg-slate-800 overflow-hidden flex items-center justify-center p-4">
<img alt="Eiko Commercial 3-5kg Roaster" class="w-full h-full object-cover rounded-md group-hover:scale-105 transition-transform duration-500 filter brightness-90" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAGMSP1-FUcZhS3Rf6vat8zHk0NtbuCnDqkbYzuB898sAyujc6eF27DXuWzPijg3ZkA6fP6zp8WL_wnhyJ_la_fV9Szz07JOtqW43oBPFOj9oC94OiHUjUgV0-R3ONVZn_qROMBEatVJpeCoHsgf9cvulYc8g7jVx-HwJBkApxxJqq0mBJxw3cfIWRrCR7_oVZc0gDFMelwZP0o2daomSTBJwc-ScrYROp5jaGUyechISDVpY6_vypC"/>
<span class="absolute top-3 right-3 bg-red-600 text-white text-[11px] font-bold px-2.5 py-1 rounded shadow">3.0kg - 5.0kg</span>
</div>
<div class="p-5 flex-1 flex flex-col justify-between bg-white dark:bg-slate-900">
<div>
<p class="text-xs font-semibold text-red-600 dark:text-red-500 uppercase tracking-wider">Medium Scale Roastery</p>
<h3 class="font-condensed text-2xl font-bold text-slate-900 dark:text-white uppercase mt-1">Eiko Commercial 5</h3>
<p class="text-xs text-slate-500 dark:text-slate-400 mt-2 line-clamp-2">Kapasitas optimal untuk roasting beans komersial kemasan harian tanpa kompromi konsistensi.</p>
<ul class="mt-4 space-y-1.5 text-xs text-slate-600 dark:text-slate-300">
<li class="flex items-center gap-2"><i class="ti ti-check text-red-500 text-[10px]"></i> Heavy Duty Cast Iron Drum</li>
<li class="flex items-center gap-2"><i class="ti ti-check text-red-500 text-[10px]"></i> Independent Agitator &amp; Cooling Fan</li>
<li class="flex items-center gap-2"><i class="ti ti-check text-red-500 text-[10px]"></i> High Efficiency Premix Burner</li>
</ul>
</div>
<div class="mt-6 pt-4 border-t border-gray-200 dark:border-slate-800 flex items-center justify-between">
<span class="text-xs text-slate-500 dark:text-slate-400">Efisiensi Bahan Bakar</span>
<a class="text-xs font-bold text-slate-900 dark:text-white hover:text-red-600 dark:hover:text-red-400 flex items-center gap-1 group-hover:underline" href="#detail-com5">Detail Unit <i class="ti ti-arrow-right text-[10px]"></i></a>
</div>
</div>
</div>
<!-- Card 4 -->
<div class="snap-start shrink-0 w-[280px] sm:w-[320px] rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 hover:border-red-600/70 transition-all duration-300 flex flex-col group overflow-hidden shadow-xl">
<div class="relative h-56 bg-gray-100 dark:bg-slate-800 overflow-hidden flex items-center justify-center p-4">
<img alt="Eiko Industrial 12kg" class="w-full h-full object-cover rounded-md group-hover:scale-105 transition-transform duration-500 filter brightness-90" src="https://lh3.googleusercontent.com/aida-public/AB6AXuABxt-GfHCA1H1ZnpnWSZuERdbyLQlY6OPC2_7PlnphGzsI4Y_lOK6GSSClFfhLwaOMC9Z2-NGV6io5DxM27C8d-LtzNFkyUhPK0BsEo3QCen6A4MXazNfRwgJ9NQSwjNHx6qej2fV9kY4ajvVQXJncjhtb3WlcnMl3EgCo-fHA9tFuMFl25JCKf20K0ndXJjwZbnGInMn_AZrhTyKdyoYSPSnRwJYEUtiTHHiGrNzpki1I_h9NGlx6"/>
<span class="absolute top-3 right-3 bg-red-600 text-white text-[11px] font-bold px-2.5 py-1 rounded shadow">10kg - 15kg</span>
</div>
<div class="p-5 flex-1 flex flex-col justify-between bg-white dark:bg-slate-900">
<div>
<p class="text-xs font-semibold text-red-600 dark:text-red-500 uppercase tracking-wider">Industrial Heavy Roaster</p>
<h3 class="font-condensed text-2xl font-bold text-slate-900 dark:text-white uppercase mt-1">Eiko Master 12</h3>
<p class="text-xs text-slate-500 dark:text-slate-400 mt-2 line-clamp-2">Dirancang khusus untuk roastery pabrikan dengan siklus roasting non-stop 12 jam sehari.</p>
<ul class="mt-4 space-y-1.5 text-xs text-slate-600 dark:text-slate-300">
<li class="flex items-center gap-2"><i class="ti ti-check text-red-500 text-[10px]"></i> Siemens Touchscreen PLC Panel</li>
<li class="flex items-center gap-2"><i class="ti ti-check text-red-500 text-[10px]"></i> Pneumatic Bean Loader &amp; Dumper</li>
<li class="flex items-center gap-2"><i class="ti ti-check text-red-500 text-[10px]"></i> Cyclone Chaff Collector Terpisah</li>
</ul>
</div>
<div class="mt-6 pt-4 border-t border-gray-200 dark:border-slate-800 flex items-center justify-between">
<span class="text-xs text-slate-500 dark:text-slate-400">Support On-Site Tech</span>
<a class="text-xs font-bold text-slate-900 dark:text-white hover:text-red-600 dark:hover:text-red-400 flex items-center gap-1 group-hover:underline" href="#detail-master12">Detail Unit <i class="ti ti-arrow-right text-[10px]"></i></a>
</div>
</div>
</div>
<!-- Card 5 -->
<div class="snap-start shrink-0 w-[280px] sm:w-[320px] rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 hover:border-red-600/70 transition-all duration-300 flex flex-col group overflow-hidden shadow-xl">
<div class="relative h-56 bg-gray-100 dark:bg-slate-800 overflow-hidden flex items-center justify-center p-4">
<img alt="Eiko Industrial Plant 30kg+" class="w-full h-full object-cover rounded-md group-hover:scale-105 transition-transform duration-500 filter brightness-90" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCj_OYNuyPpikqzE-HkHWmR_Y974xZi6htPZPAcVd8zIBlCQLdUaWvz5Q2kSnX9rpAT1StM40ftzULtAKqjX65vK3IDoFkqdzwLFU-uMXMpd5DpJPpr4mRB14OHZzYlJHB7iAxf23f_TnQ4E4HvlijJhXVmYA-xIW2pgnMuo4U7u-xJvPFoemop0vsqDowkQmsf6CJnf4roNHutzk1JmnLrBpEYeuAb2l9TX_kZTNSXhiXZ-5rOpIp0"/>
<span class="absolute top-3 right-3 bg-red-600 text-white text-[11px] font-bold px-2.5 py-1 rounded shadow">30kg - 60kg</span>
</div>
<div class="p-5 flex-1 flex flex-col justify-between bg-white dark:bg-slate-900">
<div>
<p class="text-xs font-semibold text-red-600 dark:text-red-500 uppercase tracking-wider">Turnkey Plant Solution</p>
<h3 class="font-condensed text-2xl font-bold text-slate-900 dark:text-white uppercase mt-1">Eiko MegaPlant 30</h3>
<p class="text-xs text-slate-500 dark:text-slate-400 mt-2 line-clamp-2">Solusi terintegrasi lengkap dengan destoner, afterburner ramah lingkungan, dan conveyor.</p>
<ul class="mt-4 space-y-1.5 text-xs text-slate-600 dark:text-slate-300">
<li class="flex items-center gap-2"><i class="ti ti-check text-red-500 text-[10px]"></i> Fully Automated SCADA System</li>
<li class="flex items-center gap-2"><i class="ti ti-check text-red-500 text-[10px]"></i> Smokeless Eco Afterburner</li>
<li class="flex items-center gap-2"><i class="ti ti-check text-red-500 text-[10px]"></i> Integrated Destoner Machine</li>
</ul>
</div>
<div class="mt-6 pt-4 border-t border-gray-200 dark:border-slate-800 flex items-center justify-between">
<span class="text-xs text-slate-500 dark:text-slate-400">Custom Engineering</span>
<a class="text-xs font-bold text-slate-900 dark:text-white hover:text-red-600 dark:hover:text-red-400 flex items-center gap-1 group-hover:underline" href="#detail-megaplant">Detail Unit <i class="ti ti-arrow-right text-[10px]"></i></a>
</div>
</div>
</div>
</div>
<!-- Indicator bullets -->
<div class="flex justify-center items-center gap-2 mt-4">
<span class="w-8 h-1.5 bg-red-600 rounded-full"></span>
<span class="w-2.5 h-1.5 bg-gray-300 dark:bg-slate-700 rounded-full"></span>
<span class="w-2.5 h-1.5 bg-gray-300 dark:bg-slate-700 rounded-full"></span>
<span class="w-2.5 h-1.5 bg-gray-300 dark:bg-slate-700 rounded-full"></span>
</div>
</div>
</section>
<!-- END: ProductShowcaseCarousel -->

<!-- BEGIN: WhyChooseEikoSection -->
<section class="py-20 bg-white dark:bg-brand-dark relative overflow-hidden" id="about">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="text-center max-w-3xl mx-auto mb-16">
<span class="text-red-600 dark:text-red-500 font-bold uppercase tracking-widest text-xs">Keunggulan Utama</span>
<h2 class="font-condensed text-4xl sm:text-5xl font-bold uppercase text-slate-900 dark:text-white tracking-tight mt-2">
          Mengapa Memilih <span class="text-red-600">Eiko Roaster?</span>
</h2>
<p class="text-slate-600 dark:text-slate-400 text-sm sm:text-base mt-3">
          Prinsip dasar rancang bangun mesin kami: Kemudahan Operasional, Efisiensi Energi Tinggi, dan Kualitas Roast Sempurna.
        </p>
</div>
<!-- 3 Pillars Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
<!-- Pillar 1 -->
<div class="p-8 rounded-2xl bg-gray-50 dark:bg-slate-900/90 border border-gray-200 dark:border-slate-800 relative hover:border-red-600/60 transition-all duration-300">
<div class="w-14 h-14 rounded-xl bg-red-600/10 border border-red-600/30 text-red-600 dark:text-red-500 flex items-center justify-center text-2xl mb-6">
<i class="ti ti-adjustments-horizontal"></i>
</div>
<span class="text-xs font-mono font-bold text-red-600 dark:text-red-500 uppercase tracking-widest">Pilar 01</span>
<h3 class="font-condensed text-2xl font-bold uppercase text-slate-900 dark:text-white mt-1 mb-3">Easy Operation &amp; Care</h3>
<p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">Rancangan modular memudahkan pembersihan drum, cyclone, dan sirkulasi udara tanpa memerlukan teknisi bongkar khusus. Antarmuka kontrol intuitif bagi roaster pemula maupun master roaster.</p>
<div class="mt-6 pt-4 border-t border-gray-200 dark:border-slate-800 text-xs text-slate-600 dark:text-slate-300 flex items-center gap-2">
<i class="ti ti-circle-check text-red-500"></i>
<span>Quick-clean cyclone tray &amp; bean sight glass</span>
</div>
</div>
<!-- Pillar 2 -->
<div class="p-8 rounded-2xl bg-gray-50 dark:bg-slate-900/90 border border-gray-200 dark:border-slate-800 relative hover:border-red-600/60 transition-all duration-300">
<div class="w-14 h-14 rounded-xl bg-red-600/10 border border-red-600/30 text-red-600 dark:text-red-500 flex items-center justify-center text-2xl mb-6">
<i class="ti ti-leaf"></i>
</div>
<span class="text-xs font-mono font-bold text-red-600 dark:text-red-500 uppercase tracking-widest">Pilar 02</span>
<h3 class="font-condensed text-2xl font-bold uppercase text-slate-900 dark:text-white mt-1 mb-3">Economic &amp; Energy Efficient</h3>
<p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">Sistem burner premix inframerah atau gas burner mikro bertekanan presisi menghemat konsumsi gas LPG hingga 35%. Insulasi drum termal tingkat tinggi menahan panas konduksi dan konveksi stabil.</p>
<div class="mt-6 pt-4 border-t border-gray-200 dark:border-slate-800 text-xs text-slate-600 dark:text-slate-300 flex items-center gap-2">
<i class="ti ti-circle-check text-red-500"></i>
<span>Insulasi ceramic fiber 1200°C</span>
</div>
</div>
<!-- Pillar 3 -->
<div class="p-8 rounded-2xl bg-gray-50 dark:bg-slate-900/90 border border-gray-200 dark:border-slate-800 relative hover:border-red-600/60 transition-all duration-300">
<div class="w-14 h-14 rounded-xl bg-red-600/10 border border-red-600/30 text-red-600 dark:text-red-500 flex items-center justify-center text-2xl mb-6">
<i class="ti ti-chart-line"></i>
</div>
<span class="text-xs font-mono font-bold text-red-600 dark:text-red-500 uppercase tracking-widest">Pilar 03</span>
<h3 class="font-condensed text-2xl font-bold uppercase text-slate-900 dark:text-white mt-1 mb-3">Excellent Roast Profile</h3>
<p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">Respon Rate-of-Rise (RoR) yang sangat cepat dan sensitif. Mendukung koneksi Artisan/Cropster secara native untuk reproduksi profile batch-to-batch yang konsisten dari light roast hingga dark roast.</p>
<div class="mt-6 pt-4 border-t border-gray-200 dark:border-slate-800 text-xs text-slate-600 dark:text-slate-300 flex items-center gap-2">
<i class="ti ti-circle-check text-red-500"></i>
<span>Dual thermocouple ungrounded probe</span>
</div>
</div>
</div>
</div>
</section>
<!-- END: WhyChooseEikoSection -->

<!-- BEGIN: ServicesAndSupportSection -->
<section class="py-20 bg-gray-100 dark:bg-brand-navy border-t border-gray-200 dark:border-slate-800" id="services">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
<!-- Left details -->
<div class="lg:col-span-5">
<span class="text-red-600 dark:text-red-500 font-bold uppercase tracking-widest text-xs">Layanan Purna Jual</span>
<h2 class="font-condensed text-4xl sm:text-5xl font-bold uppercase text-slate-900 dark:text-white tracking-tight mt-2 mb-6">
            Dukungan Teknis &amp; <br/><span class="text-red-600">Garansi Pabrikan</span>
</h2>
<p class="text-slate-600 dark:text-slate-300 text-sm sm:text-base leading-relaxed mb-6">
            Kami memahami mesin roasting adalah jantung bisnis roastery Anda. Karena itu, EIKO menjamin dukungan suku cadang lokal 100% dan tim teknisi berpengalaman yang siap membantu di seluruh penjuru Indonesia.
          </p>
<div class="space-y-4 text-sm text-slate-700 dark:text-slate-200">
<div class="flex items-start gap-3">
<div class="w-7 h-7 rounded-full bg-red-600/20 text-red-600 dark:text-red-500 flex items-center justify-center shrink-0 mt-0.5">
<i class="ti ti-tool text-xs"></i>
</div>
<div>
<strong class="text-slate-900 dark:text-white block font-medium">Commissioning &amp; Training Onsite</strong>
<span class="text-slate-500 dark:text-slate-400 text-xs">Instalasi langsung di lokasi roastery Anda lengkap dengan pelatihan kalibrasi dan roasting profiling.</span>
</div>
</div>
<div class="flex items-start gap-3">
<div class="w-7 h-7 rounded-full bg-red-600/20 text-red-600 dark:text-red-500 flex items-center justify-center shrink-0 mt-0.5">
<i class="ti ti-box-multiple text-xs"></i>
</div>
<div>
<strong class="text-slate-900 dark:text-white block font-medium">Ketersediaan Sparepart Lokal 100%</strong>
<span class="text-slate-500 dark:text-slate-400 text-xs">Tidak perlu menunggu import berbulan-bulan. Motor penggerak, burner, sensor thermocouple selalu ready stock.</span>
</div>
</div>
<div class="flex items-start gap-3">
<div class="w-7 h-7 rounded-full bg-red-600/20 text-red-600 dark:text-red-500 flex items-center justify-center shrink-0 mt-0.5">
<i class="ti ti-shield-check text-xs"></i>
</div>
<div>
<strong class="text-slate-900 dark:text-white block font-medium">Garansi Servis &amp; Konsultasi Seumur Hidup</strong>
<span class="text-slate-500 dark:text-slate-400 text-xs">Garansi suku cadang 1-2 tahun serta asistensi teknis roasting via video call &amp; WhatsApp grup teknis.</span>
</div>
</div>
</div>
<div class="mt-8">
<a class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-6 py-3 rounded-lg shadow-lg shadow-red-600/30 transition-all" href="https://wa.me/6281234567890?text=Halo%20Eiko,%20saya%20ingin%20konsultasi%20mesin%20roasting">
<i class="ti ti-brand-whatsapp text-lg"></i>
<span>Konsultasi Kebutuhan Roastery</span>
</a>
</div>
</div>
<!-- Right Visual Showcase -->
<div class="lg:col-span-7">
<div class="grid grid-cols-2 gap-4">
<div class="rounded-xl overflow-hidden border border-gray-300 dark:border-slate-700 h-64 shadow-xl">
<img alt="Pabrik Perakitan Mesin Roasting" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBuYJZXXqyHj-rzk0cKZ4-qksuS48PMxsJHQFRi_CnGpqqUWA19kJM-KJYR1cPVlIIx3Y4SG0_K83P5BfSZ0dhXP6yuIq5OQbKo_9OjPkgpiw-_9dQDioh9UHScT3KsyA1wBh9-Ah5s6l9sJjCzYR8zsP43dFN8nUfceHYQO-kH7UFq_uv1eRESzVUHgLXEDzmO2bByffHbWGrOhYSjEAj6WAJuwfC04OUSiEqTGoTXt3_LhSPS7d_Q"/>
</div>
<div class="rounded-xl overflow-hidden border border-gray-300 dark:border-slate-700 h-64 shadow-xl">
<img alt="Coffee Bean Cupping and Quality Inspection" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuD16CqxUv0bh_6H8twvXO53lEuWYT4SZIjO4IB6o2gly8Ft4gRFr9b1lFBG4-4JhMY_LSd1970C28mCBowqqD8RKyeawThcz98a6I90rfCkPZxTuynMkMY7S_C6Pm3rishDRVhVQS2jptwpCHU5xKUStc1RkgTAJs3l3d2_HUe2EnNfWHIbtC3S6KalqSEoxzVl-wb82mcCgDYetr15tsy2jW_aIhwQd7-em3KZqQAqXGH7i1ZwDWPt"/>
</div>
<div class="rounded-xl overflow-hidden border border-gray-300 dark:border-slate-700 h-52 col-span-2 relative shadow-xl">
<img alt="Workshop Bengkel Mesin Kopi Eiko" class="w-full h-full object-cover filter brightness-75 dark:brightness-75" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBChILsb0Gg95MmEsQVVrUp38xFeJJdpp6xf3YPGF0rafTz_0ANTpSCaYcwxv6GmhFTTEMbqz6AIMtC0cxlJGmJgJ9aE7STJXR4J-MbbN0qXpGdJ_7jJsivHfpwspvTjEwms97u5bVZExS_XZGOgB3t4mGX916kKjL2P__2xFFETlEZhA35h3PLLFB9A6BpzpdASmcsRYaTBTCGhyzhKzrwVVC0qKlcQRVYkWzVRKp65lCCaEnh8hY8"/>
<div class="absolute inset-0 bg-gradient-to-t from-gray-900/90 dark:from-slate-950 via-gray-900/40 dark:via-slate-950/40 to-transparent flex items-end p-6">
<div>
<p class="text-white font-condensed uppercase text-xl font-bold tracking-wide">Workshop &amp; Engineering Lab EIKO</p>
<p class="text-xs text-gray-200 dark:text-slate-300">Setiap mesin melewati uji bakar dan sensor temperatur 48 jam sebelum proses pengiriman.</p>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- END: ServicesAndSupportSection -->

<!-- BEGIN: ExportNetworkSection -->
<section class="py-16 bg-gray-50 dark:bg-slate-950 border-b border-gray-200 dark:border-slate-800/80">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="text-center mb-8">
<p class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400 font-semibold">
          Terpercaya Sejak 2015 di Seluruh Indonesia &amp; Jangkauan Ekspor
        </p>
</div>
<!-- Countries and regions pill tags -->
<div class="flex flex-wrap justify-center items-center gap-3 md:gap-5 text-xs sm:text-sm font-medium text-slate-700 dark:text-slate-300">
<span class="px-4 py-2 rounded-lg bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 flex items-center gap-2 hover:border-red-500/50 transition-colors">
<span class="text-base">🇮🇩</span> Seluruh Indonesia (38 Provinsi)
        </span>
<span class="px-4 py-2 rounded-lg bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 flex items-center gap-2 hover:border-red-500/50 transition-colors">
<span class="text-base">🇲🇾</span> Malaysia
        </span>
<span class="px-4 py-2 rounded-lg bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 flex items-center gap-2 hover:border-red-500/50 transition-colors">
<span class="text-base">🇧🇳</span> Brunei Darussalam
        </span>
<span class="px-4 py-2 rounded-lg bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 flex items-center gap-2 hover:border-red-500/50 transition-colors">
<span class="text-base">🇹🇱</span> Timor Leste
        </span>
<span class="px-4 py-2 rounded-lg bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 flex items-center gap-2 hover:border-red-500/50 transition-colors">
<span class="text-base">🇹🇭</span> Thailand
        </span>
<span class="px-4 py-2 rounded-lg bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 flex items-center gap-2 hover:border-red-500/50 transition-colors">
<span class="text-base">🇻🇳</span> Vietnam
        </span>
<span class="px-4 py-2 rounded-lg bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 flex items-center gap-2 hover:border-red-500/50 transition-colors">
<span class="text-base">🇯🇵</span> Jepang
        </span>
</div>
</div>
</section>
<!-- END: ExportNetworkSection -->

<!-- BEGIN: ContactAndInquiryFooter -->
<footer class="bg-gray-100 dark:bg-brand-dark pt-16 pb-12 border-t border-gray-200 dark:border-slate-800" id="contact">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<!-- Top Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 pb-12 border-b border-gray-200 dark:border-slate-800">
<!-- Col 1: Brand Info -->
<div>
<div class="flex items-center gap-2 group mb-4">
<div class="w-8 h-8 rounded bg-red-600 flex items-center justify-center text-white font-black text-base shadow">
<i class="ti ti-flame"></i>
</div>
<span class="font-condensed text-2xl tracking-wider text-slate-900 dark:text-white font-bold uppercase">
              EIKO <span class="text-slate-500 dark:text-slate-400 font-normal">ROASTER</span>
</span>
</div>
<p class="text-slate-600 dark:text-slate-400 text-xs leading-relaxed mb-6">
            Manufaktur mesin sangrai kopi Indonesia dengan standar durabilitas industri presisi tinggi. Mengantarkan cita rasa kopi nusantara ke panggung internasional.
          </p>
<div class="flex items-center gap-3 text-slate-500 dark:text-slate-400">
<a class="w-8 h-8 rounded-full bg-gray-200 dark:bg-slate-800 hover:bg-red-600 hover:text-white transition-colors flex items-center justify-center text-xs" href="#"><i class="ti ti-brand-instagram"></i></a>
<a class="w-8 h-8 rounded-full bg-gray-200 dark:bg-slate-800 hover:bg-red-600 hover:text-white transition-colors flex items-center justify-center text-xs" href="#"><i class="ti ti-brand-youtube"></i></a>
<a class="w-8 h-8 rounded-full bg-gray-200 dark:bg-slate-800 hover:bg-red-600 hover:text-white transition-colors flex items-center justify-center text-xs" href="#"><i class="ti ti-brand-facebook"></i></a>
<a class="w-8 h-8 rounded-full bg-gray-200 dark:bg-slate-800 hover:bg-red-600 hover:text-white transition-colors flex items-center justify-center text-xs" href="#"><i class="ti ti-brand-tiktok"></i></a>
</div>
</div>
<!-- Col 2: Quick Links -->
<div>
<h4 class="font-condensed text-lg uppercase text-slate-900 dark:text-white font-bold tracking-wider mb-4 border-l-2 border-red-600 pl-2.5">Kategori Mesin</h4>
<ul class="space-y-2 text-xs text-slate-600 dark:text-slate-400">
<li><a class="hover:text-red-600 dark:hover:text-red-400 transition-colors" href="#products">Sample Micro Roaster (100g - 250g)</a></li>
<li><a class="hover:text-red-600 dark:hover:text-red-400 transition-colors" href="#products">Shop Specialty Roaster (1kg - 2.5kg)</a></li>
<li><a class="hover:text-red-600 dark:hover:text-red-400 transition-colors" href="#products">Commercial Roaster (3kg - 5kg)</a></li>
<li><a class="hover:text-red-600 dark:hover:text-red-400 transition-colors" href="#products">Industrial Plant Roaster (10kg - 60kg)</a></li>
<li><a class="hover:text-red-600 dark:hover:text-red-400 transition-colors" href="#products">Accessories: Destoner &amp; Afterburner</a></li>
</ul>
</div>
<!-- Col 3: Layanan & Informasi -->
<div>
<h4 class="font-condensed text-lg uppercase text-slate-900 dark:text-white font-bold tracking-wider mb-4 border-l-2 border-red-600 pl-2.5">Dukungan</h4>
<ul class="space-y-2 text-xs text-slate-600 dark:text-slate-400">
<li><a class="hover:text-red-600 dark:hover:text-red-400 transition-colors" href="#services">Katalog &amp; Price List Resmi (PDF)</a></li>
<li><a class="hover:text-red-600 dark:hover:text-red-400 transition-colors" href="#services">Panduan Software Artisan / Cropster</a></li>
<li><a class="hover:text-red-600 dark:hover:text-red-400 transition-colors" href="#services">Pemesanan Suku Cadang Orisinil</a></li>
<li><a class="hover:text-red-600 dark:hover:text-red-400 transition-colors" href="#services">Jadwal Training Roasting Onsite</a></li>
<li><a class="hover:text-red-600 dark:hover:text-red-400 transition-colors" href="#services">Klaim Garansi &amp; Maintenance</a></li>
</ul>
</div>
<!-- Col 4: Hubungi Kami -->
<div>
<h4 class="font-condensed text-lg uppercase text-slate-900 dark:text-white font-bold tracking-wider mb-4 border-l-2 border-red-600 pl-2.5">Workshop &amp; Showroom</h4>
<div class="text-xs text-slate-600 dark:text-slate-400 space-y-3">
<p class="flex items-start gap-2.5">
<i class="ti ti-map-pin text-red-500 mt-1 shrink-0"></i>
<span>Kawasan Industri Manufaktur Kopi, Jawa Barat / Jawa Timur, Indonesia.</span>
</p>
<p class="flex items-center gap-2.5">
<i class="ti ti-phone text-red-500 shrink-0"></i>
<span>+62 812-3456-7890 / (021) 789-0123</span>
</p>
<p class="flex items-center gap-2.5">
<i class="ti ti-mail text-red-500 shrink-0"></i>
<span>sales@eikocoffeeroaster.com</span>
</p>
<p class="flex items-center gap-2.5">
<i class="ti ti-clock text-red-500 shrink-0"></i>
<span>Senin - Sabtu: 08.30 - 17.00 WIB</span>
</p>
</div>
</div>
</div>
<!-- Bottom Credits -->
<div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 dark:text-slate-500 gap-4">
<p>© 2025 EIKO COFFEE ROASTER. All Rights Reserved. Coffee Roasting Machine Manufacturer.</p>
<div class="flex items-center gap-4">
<a class="hover:text-slate-700 dark:hover:text-slate-400" href="#">Privacy Policy</a>
<span>•</span>
<a class="hover:text-slate-700 dark:hover:text-slate-400" href="#">Terms of Service</a>
<span>•</span>
<a class="hover:text-slate-700 dark:hover:text-slate-400" href="#">Indonesia</a>
</div>
</div>
</div>
</footer>
<!-- END: ContactAndInquiryFooter -->

<!-- BEGIN: InteractiveScripts -->
<script data-purpose="carousel-and-nav-events">
    document.addEventListener('DOMContentLoaded', () => {
      // Theme Toggle Logic
      const themeToggleBtn = document.getElementById('themeToggle');
      const htmlElement = document.documentElement;

      // Check local storage or system preference
      if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        htmlElement.classList.add('dark');
      } else {
        htmlElement.classList.remove('dark');
      }

      themeToggleBtn.addEventListener('click', () => {
        if (htmlElement.classList.contains('dark')) {
          htmlElement.classList.remove('dark');
          localStorage.theme = 'light';
        } else {
          htmlElement.classList.add('dark');
          localStorage.theme = 'dark';
        }
      });

      // Carousel Navigation
      const carousel = document.getElementById('roasterCarousel');
      const prevBtn = document.getElementById('carouselPrevBtn');
      const nextBtn = document.getElementById('carouselNextBtn');

      if (carousel && prevBtn && nextBtn) {
        prevBtn.addEventListener('click', () => {
          carousel.scrollBy({ left: -340, behavior: 'smooth' });
        });

        nextBtn.addEventListener('click', () => {
          carousel.scrollBy({ left: 340, behavior: 'smooth' });
        });
      }

      // Smooth scroll for anchor tags
      document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
          const target = document.querySelector(this.getAttribute('href'));
          if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
        });
      });
    });
</script>
<!-- END: InteractiveScripts -->
</body>
</html>
