<?php

namespace Database\Seeders;

use App\Models\CompanyProfile;
use Illuminate\Database\Seeder;

/**
 * Mengisi data profil PT Manufactur Dynamic Indonesia.
 */
class CompanyProfileSeeder extends Seeder
{
    public function run(): void
    {
        CompanyProfile::query()->updateOrCreate(
            ['id' => 1],
            [
                'name' => 'PT Manufactur Dynamic Indonesia',
                'tagline' => 'Spesialis Perancangan & Produksi Mesin Pengolahan Kopi dan Kakao',
                'about' => "PT Manufactur Dynamic Indonesia adalah perusahaan manufaktur yang bergerak di bidang perancangan, pembuatan, dan pemasangan mesin pengolahan kopi serta kakao.\n\nKami melayani perkebunan, koperasi, pabrik pengolahan, dan UMKM dengan mesin yang disesuaikan terhadap kapasitas produksi serta kondisi lahan pelanggan. Seluruh mesin diproduksi dengan material food grade dan melalui tahap uji fungsi sebelum dikirim.",
                'vision' => 'Menjadi pionir manufaktur rekayasa mesin pengolahan kopi dan kakao terdepan di Indonesia yang berstandar industri presisi, higienis Food Grade SUS 304/316, efisiensi energi optimal, serta berdaya saing global.',
                'mission' => "Merancang dan memproduksi lini mesin pengolahan kopi serta kakao berstandar industri presisi, material Food Grade SUS 304/316, dan efisiensi konsumsi daya optimal.\nMendukung peningkatan kapasitas produksi dan nilai tambah komoditas unggulan bagi perkebunan, koperasi, agroindustri, dan UMKM di seluruh Indonesia.\nMenjaga keandalan mutu melalui kendali kualitas (QC) bertingkat, standar fabrikasi ketat, serta uji fungsi operasional presisi sebelum proses pengiriman.\nMemberikan pendampingan purna jual terpadu mencakup pelatihan operator, jaminan suku cadang original, dan garansi servis mekanik serta elektrikal.",
                'address' => 'Jl. Industri Raya No. 88, Karangploso',
                'city' => 'Kabupaten Malang, Jawa Timur',
                'phone' => '(0341) 555-8899',
                'whatsapp' => '6281234567890',
                'email' => 'info@manufacturdynamic.co.id',
                'website' => 'www.manufacturdynamic.co.id',
                'founded_year' => '2010',
                'employees' => '75+ karyawan',
                'export_countries' => 'Nasional & Asia Tenggara',
                'facebook' => 'https://facebook.com/manufacturdynamic',
                'instagram' => 'https://instagram.com/manufacturdynamic',
                'linkedin' => 'https://linkedin.com/company/manufacturdynamic',
                'youtube' => 'https://youtube.com/@manufacturdynamic',
            ]
        );
    }
}
