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
</head>
<body class="flex min-h-screen items-center justify-center bg-gradient-to-br from-brand-950 via-brand-900 to-brand-700 px-4 py-10 font-sans text-stone-700 antialiased">
    <div class="w-full max-w-md">
        <div class="mb-7 text-center">
            @if ($company->logoUrl())
                <img src="{{ $company->logoUrl() }}" alt="Logo {{ $company->name }}" class="mx-auto h-16 w-16 rounded-2xl bg-white/10 object-contain p-2">
            @else
                <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-white/10 text-xl font-extrabold text-white">MD</span>
            @endif

            <h1 class="mt-4 text-lg font-extrabold text-white">{{ $company->name }}</h1>
            <p class="mt-1 text-xs font-semibold uppercase tracking-[0.2em] text-brand-200">Panel Admin</p>
        </div>

        <div class="rounded-3xl border border-white/10 bg-white p-7 shadow-2xl sm:p-8">
            <h2 class="text-xl font-extrabold text-stone-900">Masuk ke akun admin</h2>
            <p class="mt-1.5 text-sm text-stone-500">Gunakan email dan password administrator.</p>

            @include('public.partials.flash')

            @if ($errors->any())
                <div class="mt-5">
                    <x-alert type="error">
                        <ul class="list-inside list-disc space-y-1 text-xs">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-alert>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.attempt') }}" class="mt-6 space-y-5">
                @csrf

                <div>
                    <label for="email" class="mb-1.5 block text-sm font-semibold text-stone-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                           placeholder="admin@example.com"
                           class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
                </div>

                <div>
                    <label for="password" class="mb-1.5 block text-sm font-semibold text-stone-700">Password</label>
                    <input type="password" name="password" id="password" required
                           placeholder="••••••••"
                           class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
                </div>

                <label for="remember" class="flex cursor-pointer items-center gap-2.5 text-sm text-stone-600">
                    <input type="checkbox" name="remember" id="remember" value="1"
                           class="size-4 rounded border-stone-300 text-brand-600 focus:ring-brand-300">
                    Ingat saya di perangkat ini
                </label>

                <button type="submit"
                        class="w-full rounded-xl bg-brand-700 px-6 py-3 text-sm font-bold text-white transition hover:bg-brand-800">
                    Masuk
                </button>
            </form>

            <p class="mt-6 text-center text-xs text-stone-500">
                Hanya administrator yang dapat mengakses panel ini.
            </p>
        </div>

        <p class="mt-6 text-center text-xs text-brand-100/70">
            &copy; {{ now()->year }} {{ $company->name }}
        </p>
    </div>
</body>
</html>
