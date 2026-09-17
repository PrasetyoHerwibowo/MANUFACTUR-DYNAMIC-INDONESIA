<?php

namespace Database\Seeders;

use App\Models\Catalog;
use Illuminate\Database\Seeder;

/**
 * Contoh data katalog produk (foto katalog ditambahkan melalui panel admin).
 */
class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $catalogs = [
            [
                'slug' => 'katalog-mesin-pengolahan-kopi',
                'title' => 'Katalog Mesin Pengolahan Kopi',
                'year' => '2026',
                'description' => 'Berisi rangkaian mesin pengolahan kopi dari pengupasan buah, fermentasi, pencucian, pengeringan, sangrai, hingga pengemasan beserta spesifikasi teknisnya.',
                'sort_order' => 1,
            ],
            [
                'slug' => 'katalog-mesin-pengolahan-kakao',
                'title' => 'Katalog Mesin Pengolahan Kakao',
                'year' => '2026',
                'description' => 'Daftar mesin untuk fermentasi, pengeringan, penepungan, dan pengemasan biji kakao dengan kapasitas mulai skala UMKM hingga pabrik.',
                'sort_order' => 2,
            ],
        ];

        foreach ($catalogs as $catalog) {
            Catalog::query()->updateOrCreate(
                ['slug' => $catalog['slug']],
                $catalog + ['is_active' => true]
            );
        }
    }
}
