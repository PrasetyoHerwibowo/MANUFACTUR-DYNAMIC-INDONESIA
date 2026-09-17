@extends('layouts.admin')

@section('title', 'Pengaturan Akun')
@section('page_title', 'Pengaturan Akun')
@section('page_subtitle', 'Kelola data dan password akun administrator')

@section('content')
    <div class="grid max-w-5xl gap-6 lg:grid-cols-2">
        {{-- Data akun --}}
        <form method="POST" action="{{ route('admin.account.update') }}" enctype="multipart/form-data"
              class="space-y-5 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-7">
            @csrf
            @method('PUT')

            <h2 class="text-base font-extrabold text-stone-900">Data akun</h2>

            <x-admin.file name="avatar" label="Foto profil" :current="$user->avatar ? $user->avatarUrl() : null"
                          hint="Opsional. Maksimal 4 MB." />

            <x-admin.input name="name" label="Nama lengkap" :value="$user->name" required />
            <x-admin.input name="email" label="Email login" type="email" :value="$user->email" required />
            <x-admin.input name="phone" label="Nomor telepon" :value="$user->phone" />

            <p class="rounded-xl bg-stone-50 px-4 py-3 text-xs text-stone-500">
                Role akun ini: <span class="font-bold text-brand-700">Admin</span> (satu-satunya role pada sistem).
            </p>

            <button type="submit"
                    class="rounded-xl bg-brand-700 px-6 py-2.5 text-sm font-bold text-white transition hover:bg-brand-800">
                Simpan Data Akun
            </button>
        </form>

        {{-- Ganti password --}}
        <form method="POST" action="{{ route('admin.account.password') }}"
              class="h-fit space-y-5 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-7">
            @csrf
            @method('PUT')

            <h2 class="text-base font-extrabold text-stone-900">Ganti password</h2>

            <div>
                <label for="current_password" class="mb-1.5 block text-sm font-semibold text-stone-700">Password saat ini</label>
                <input type="password" name="current_password" id="current_password" required autocomplete="current-password"
                       class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
                @error('current_password') <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="mb-1.5 block text-sm font-semibold text-stone-700">Password baru</label>
                <input type="password" name="password" id="password" required autocomplete="new-password"
                       class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
                <p class="mt-1.5 text-xs text-stone-500">Minimal 6 karakter.</p>
                @error('password') <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="mb-1.5 block text-sm font-semibold text-stone-700">Ulangi password baru</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                       class="w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
            </div>

            <button type="submit"
                    class="rounded-xl bg-stone-900 px-6 py-2.5 text-sm font-bold text-white transition hover:bg-stone-700">
                Perbarui Password
            </button>
        </form>
    </div>
@endsection
