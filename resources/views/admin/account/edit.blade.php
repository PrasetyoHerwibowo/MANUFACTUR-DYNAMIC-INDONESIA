@extends('layouts.admin')

@section('title', 'Pengaturan Akun')
@section('page_title', 'Pengaturan Akun')
@section('page_subtitle', 'Kelola data dan password akun administrator')

@section('content')
    <div class="grid max-w-5xl gap-6 lg:grid-cols-2">
        {{-- Data akun --}}
        <form method="POST" action="{{ route('admin.account.update') }}" enctype="multipart/form-data"
              class="space-y-5 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-7 dark:border-slate-700 dark:bg-slate-900">
            @csrf
            @method('PUT')

            <h2 class="text-base font-extrabold text-stone-900 dark:text-white">Data akun</h2>

            <x-admin.file name="avatar" label="Foto profil" :current="$user->avatar ? $user->avatarUrl() : null"
                          hint="Opsional. Maksimal 4 MB." />

            <x-admin.input name="name" label="Nama lengkap" :value="$user->name" required />
            <x-admin.input name="email" label="Email login" type="email" :value="$user->email" required />
            <x-admin.input name="phone" label="Nomor telepon" :value="$user->phone" />

            <p class="rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-xs text-stone-600 transition-colors dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                Role akun ini: <span class="font-bold text-coffee-600 dark:text-coffee-400">Admin</span> (satu-satunya role pada sistem).
            </p>

            <button type="submit"
                    class="rounded-xl bg-brand-700 px-6 py-2.5 text-sm font-bold text-white transition hover:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                Simpan Data Akun
            </button>
        </form>

        {{-- Ganti password --}}
        <form method="POST" action="{{ route('admin.account.password') }}"
              class="h-fit space-y-5 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-7 dark:border-slate-700 dark:bg-slate-900">
            @csrf
            @method('PUT')

            <h2 class="text-base font-extrabold text-stone-900 dark:text-white">Ganti password</h2>

            <div>
                <label for="current_password" class="mb-1.5 block text-sm font-semibold text-stone-700 dark:text-slate-200">Password saat ini</label>
                <input type="password" name="current_password" id="current_password" required autocomplete="current-password"
                       placeholder="Masukkan password saat ini"
                       class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 outline-none transition placeholder:text-stone-400 focus:border-coffee-500 focus:ring-2 focus:ring-coffee-200 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-400">
                @error('current_password') <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="mb-1.5 block text-sm font-semibold text-stone-700 dark:text-slate-200">Password baru</label>
                <input type="password" name="password" id="password" required autocomplete="new-password"
                       placeholder="Masukkan password baru"
                       class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 outline-none transition placeholder:text-stone-400 focus:border-coffee-500 focus:ring-2 focus:ring-coffee-200 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-400">
                <p class="mt-1.5 text-xs text-stone-500 dark:text-slate-400">Minimal 6 karakter.</p>
                @error('password') <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="mb-1.5 block text-sm font-semibold text-stone-700 dark:text-slate-200">Ulangi password baru</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                       placeholder="Ulangi password baru"
                       class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-900 outline-none transition placeholder:text-stone-400 focus:border-coffee-500 focus:ring-2 focus:ring-coffee-200 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-400">
            </div>

            <button type="submit"
                    class="rounded-xl bg-stone-900 px-6 py-2.5 text-sm font-bold text-white transition hover:bg-stone-800 dark:border dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                Perbarui Password
            </button>
        </form>
    </div>
@endsection