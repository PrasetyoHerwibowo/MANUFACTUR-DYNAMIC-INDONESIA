@extends('layouts.admin')

@section('title', 'Profil Perusahaan')
@section('page_title', 'Profil Perusahaan')
@section('page_subtitle', 'Data ini tampil pada seluruh halaman website')

@section('content')
    <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" class="max-w-4xl space-y-6">
        @csrf
        @method('PUT')

        {{-- Identitas --}}
        <div class="space-y-5 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-7">
            <h2 class="text-base font-extrabold text-stone-900">Identitas perusahaan</h2>

            <x-admin.input name="name" label="Nama perusahaan" :value="$profile->name" required />

            <x-admin.input name="tagline" label="Tagline" :value="$profile->tagline"
                           hint="Kalimat singkat yang muncul di bawah nama perusahaan pada beranda." />

            <x-admin.textarea name="about" label="Tentang perusahaan" :value="$profile->about" :rows="6"
                              hint="Satu baris kosong memisahkan paragraf." />

            <div class="grid gap-5 sm:grid-cols-2">
                <x-admin.textarea name="vision" label="Visi" :value="$profile->vision" :rows="3" />
                <x-admin.textarea name="mission" label="Misi" :value="$profile->mission" :rows="3"
                                  hint="Satu baris satu poin misi." />
            </div>

            <div class="grid gap-5 sm:grid-cols-3">
                <x-admin.input name="founded_year" label="Tahun berdiri" :value="$profile->founded_year" placeholder="2010" />
                <x-admin.input name="employees" label="Jumlah karyawan" :value="$profile->employees" placeholder="50+ karyawan" />
                <x-admin.input name="export_countries" label="Wilayah pemasaran" :value="$profile->export_countries" placeholder="Nasional & Asia Tenggara" />
            </div>
        </div>

        {{-- Kontak --}}
        <div class="space-y-5 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-7">
            <h2 class="text-base font-extrabold text-stone-900">Kontak &amp; lokasi</h2>

            <x-admin.input name="address" label="Alamat" :value="$profile->address" />
            <x-admin.input name="city" label="Kota / provinsi" :value="$profile->city" />

            <div class="grid gap-5 sm:grid-cols-2">
                <x-admin.input name="phone" label="Telepon" :value="$profile->phone" placeholder="(0341) 123456" />
                <x-admin.input name="whatsapp" label="WhatsApp" :value="$profile->whatsapp" placeholder="628123456789"
                               hint="Gunakan format internasional tanpa tanda +." />
                <x-admin.input name="email" label="Email" type="email" :value="$profile->email" />
                <x-admin.input name="website" label="Website" :value="$profile->website" placeholder="www.manufacturdynamic.co.id" />
            </div>

            <x-admin.textarea name="map_embed" label="Kode embed peta (Google Maps)" :value="$profile->map_embed" :rows="3"
                              hint="Tempel kode &lt;iframe&gt; dari Google Maps. Kosongkan bila tidak diperlukan." />
        </div>

        {{-- Media sosial & foto --}}
        <div class="space-y-5 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-7">
            <h2 class="text-base font-extrabold text-stone-900">Media sosial &amp; foto</h2>

            <div class="grid gap-5 sm:grid-cols-2">
                <x-admin.input name="facebook" label="Facebook" :value="$profile->facebook" placeholder="https://facebook.com/..." />
                <x-admin.input name="instagram" label="Instagram" :value="$profile->instagram" placeholder="https://instagram.com/..." />
                <x-admin.input name="linkedin" label="LinkedIn" :value="$profile->linkedin" placeholder="https://linkedin.com/company/..." />
                <x-admin.input name="youtube" label="YouTube" :value="$profile->youtube" placeholder="https://youtube.com/@..." />
            </div>

            <div class="grid gap-5 sm:grid-cols-3">
                <x-admin.file name="logo" label="Logo perusahaan" :current="$profile->logoUrl()"
                              hint="PNG transparan disarankan." />
                <x-admin.file name="hero_image" label="Foto banner beranda" :current="$profile->heroUrl()" />
                <x-admin.file name="about_image" label="Foto tentang kami" :current="$profile->aboutUrl()" />
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <button type="submit"
                    class="rounded-xl bg-brand-700 px-6 py-2.5 text-sm font-bold text-white transition hover:bg-brand-800">
                Simpan Profil
            </button>

            <a href="{{ route('home') }}" target="_blank"
               class="rounded-xl border border-stone-300 px-5 py-2.5 text-sm font-bold text-stone-600 transition hover:bg-white">
                Lihat Website
            </a>
        </div>
    </form>
@endsection
