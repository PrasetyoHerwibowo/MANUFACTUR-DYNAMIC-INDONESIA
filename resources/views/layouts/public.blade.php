<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('meta_description', $company->name.' — '.$company->tagline)">
    <title>@yield('title', $company->name)</title>

    <link rel="icon" href="{{ $company->logoUrl() ?? asset('uploads/placeholder.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.31.0/dist/tabler-icons.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Terapkan tema tersimpan sebelum halaman digambar agar tidak berkedip. --}}
    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="min-h-screen bg-white font-sans text-stone-700 antialiased transition-colors duration-300 dark:bg-gray-900 dark:text-gray-300">

    @include('public.partials.navbar')

    @include('public.partials.flash')

    <main>
        @yield('content')
    </main>

    @include('public.partials.footer')

    <script>
        (function () {
            const toggle = document.querySelector('[data-theme-toggle]');
            const knob = document.querySelector('[data-theme-knob]');
            const icon = document.querySelector('[data-theme-icon]');

            function paintTheme() {
                const isDark = document.documentElement.classList.contains('dark');

                if (knob) {
                    knob.style.transform = isDark ? 'translateX(1.5rem)' : 'translateX(0)';
                }

                if (icon) {
                    icon.className = isDark ? 'ti ti-sun text-[11px] text-gray-600' : 'ti ti-moon text-[11px] text-gray-600';
                }
            }

            toggle?.addEventListener('click', () => {
                const isDark = document.documentElement.classList.toggle('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                paintTheme();
            });

            paintTheme();
        })();
    </script>

</body>
</html>
