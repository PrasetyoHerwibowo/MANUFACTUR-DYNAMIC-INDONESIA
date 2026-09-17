<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Machine;
use Illuminate\Database\Seeder;

/**
 * Contoh data jenis mesin (kategori) dan model mesin beserta fungsi mesin.
 */
class ProductSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->data() as $group) {
            $category = Category::query()->updateOrCreate(
                ['slug' => $group['slug']],
                [
                    'name' => $group['name'],
                    'tagline' => $group['tagline'],
                    'description' => $group['description'],
                    'sort_order' => $group['sort_order'],
                    'is_active' => true,
                ]
            );

            foreach ($group['machines'] as $machine) {
                Machine::query()->updateOrCreate(
                    ['slug' => $machine['slug']],
                    $machine + ['category_id' => $category->id, 'is_active' => true]
                );
            }
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function data(): array
    {
        return [
            [
                'slug' => 'mesin-pengolahan-kopi',
                'name' => 'Mesin Pengolahan Kopi',
                'tagline' => 'Dari cherry hingga green bean siap sangrai',
                'description' => 'Rangkaian mesin untuk mengolah buah kopi menjadi biji kopi kering siap jual, mulai dari pengupasan, fermentasi, pencucian, hingga pengeringan.',
                'sort_order' => 1,
                'machines' => [
                    [
                        'name' => 'Mesin Pulper Kopi Basah',
                        'slug' => 'mesin-pulper-kopi-basah',
                        'model_code' => 'MDI-PL200',
                        'function' => 'Memisahkan biji kopi dari kulit buah (pulp) segera setelah panen sehingga proses fermentasi dapat berjalan seragam.',
                        'short_description' => 'Pengupas buah kopi basah dengan kapasitas hingga 800 kg per jam dan tingkat kerusakan biji rendah.',
                        'description' => "Mesin pulper kopi basah MDI-PL200 dirancang untuk pengolahan pascapanen skala koperasi maupun pabrik.\nSilinder pengupas berbahan stainless steel dengan jarak yang dapat disetel sehingga cocok untuk berbagai ukuran biji kopi robusta dan arabika.\nSaluran air pencuci terintegrasi membantu memisahkan biji superior dari biji cacat sejak tahap awal.",
                        'capacity' => '800 kg/jam',
                        'power' => '5,5 kW, 3 phase',
                        'dimension' => '180 x 90 x 140 cm',
                        'weight' => '320 kg',
                        'material' => 'Stainless steel 304',
                        'specifications' => "Rangka: Besi UNP 80 dilapisi cat epoxy\nSilinder pengupas: Stainless steel 304\nSistem transmisi: Pulley dan V-belt\nKontrol: Panel on/off dengan pengaman overload",
                        'is_featured' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Mesin Huller Kopi Kering',
                        'slug' => 'mesin-huller-kopi-kering',
                        'model_code' => 'MDI-HL150',
                        'function' => 'Mengupas kulit tanduk biji kopi kering sebelum proses sortasi dan pengemasan.',
                        'short_description' => 'Pengupas kulit tanduk kopi kering dengan hasil kupasan bersih dan persentase biji pecah di bawah 3%.',
                        'description' => "Huller MDI-HL150 menggunakan sistem gesek berputar dengan pengaturan jarak rol yang presisi.\nDilengkapi blower peniup untuk memisahkan kulit tanduk dari biji kopi secara otomatis.\nCocok dipasangkan langsung setelah mesin pengering pada lini pengolahan kopi kering.",
                        'capacity' => '600 kg/jam',
                        'power' => '4 kW, 3 phase',
                        'dimension' => '150 x 80 x 130 cm',
                        'weight' => '240 kg',
                        'material' => 'Stainless steel 304',
                        'specifications' => "Rol pengupas: Karet berkekuatan tinggi\nBlower: Sentrifugal 1,5 kW\nBiji pecah: kurang dari 3%",
                        'sort_order' => 2,
                    ],
                ],
            ],
            [
                'slug' => 'mesin-sangrai-dan-penepung',
                'name' => 'Mesin Sangrai & Penepung',
                'tagline' => 'Penentu cita rasa dan kehalusan produk',
                'description' => 'Mesin roaster dan grinder untuk menghasilkan kopi sangrai serta bubuk kopi/kakao dengan tingkat kematangan dan kehalusan yang konsisten.',
                'sort_order' => 2,
                'machines' => [
                    [
                        'name' => 'Mesin Sangrai Kopi (Roaster)',
                        'slug' => 'mesin-sangrai-kopi-roaster',
                        'model_code' => 'MDI-RS50',
                        'function' => 'Menyangrai biji kopi hijau (green bean) pada suhu dan durasi terkendali untuk membentuk profil cita rasa akhir.',
                        'short_description' => 'Roaster drum 50 kg per batch dengan pengaturan suhu digital dan pendingin (cooling tray) terintegrasi.',
                        'description' => "Roaster MDI-RS50 memakai sistem drum berputar dengan pemanasan merata sehingga hasil sangrai konsisten.\nDilengkapi termometer digital, pengatur gas, serta kipas pembuang asap.\nCooling tray berkapasitas sama mempercepat pendinginan biji agar profil rasa tidak berlanjut matang.",
                        'capacity' => '50 kg/batch',
                        'power' => '2,2 kW, 1 phase',
                        'dimension' => '160 x 110 x 190 cm',
                        'weight' => '450 kg',
                        'material' => 'Besi dan stainless steel',
                        'specifications' => "Bahan bakar: LPG\nDrum: Stainless steel 304\nPendingin: Cooling tray 50 kg dengan agitator",
                        'is_featured' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Mesin Penepung Kopi & Kakao',
                        'slug' => 'mesin-penepung-kopi-kakao',
                        'model_code' => 'MDI-GR30',
                        'function' => 'Menghaluskan biji kopi sangrai atau bungkil kakao menjadi bubuk dengan tingkat kehalusan yang dapat diatur.',
                        'short_description' => 'Grinder disk mill kapasitas 150 kg per jam dengan pengatur tingkat kehalusan bubuk.',
                        'description' => "Mesin penepung MDI-GR30 menggunakan mata disk mill berbahan hardened steel yang tahan lama.\nTingkat kehalusan dapat disetel sesuai kebutuhan penyajian maupun industri.\nSeluruh bagian yang bersentuhan dengan produk mudah dibongkar untuk pembersihan.",
                        'capacity' => '150 kg/jam',
                        'power' => '7,5 kW, 3 phase',
                        'dimension' => '90 x 70 x 130 cm',
                        'weight' => '180 kg',
                        'material' => 'Stainless steel 304',
                        'specifications' => "Mata pisau: Hardened steel\nHopper: 20 liter\nKehalusan: 40 - 100 mesh",
                        'sort_order' => 2,
                    ],
                ],
            ],
            [
                'slug' => 'mesin-pengolahan-kakao',
                'name' => 'Mesin Pengolahan Kakao',
                'tagline' => 'Fermentasi dan pengeringan biji kakao',
                'description' => 'Perlengkapan untuk fermentasi serta pengeringan biji kakao agar mutu dan aroma khas kakao tetap terjaga.',
                'sort_order' => 3,
                'machines' => [
                    [
                        'name' => 'Box Fermentasi Kakao',
                        'slug' => 'box-fermentasi-kakao',
                        'model_code' => 'MDI-FM500',
                        'function' => 'Wadah fermentasi biji kakao dengan pengaturan aliran udara dan pembalikan berkala untuk menurunkan rasa pahit dan mengembangkan aroma.',
                        'short_description' => 'Kotak fermentasi kayu 500 kg dengan sekat bertingkat dan lubang drainase.',
                        'description' => "Box fermentasi MDI-FM500 dibuat dari kayu keras pilihan yang tidak bereaksi dengan biji kakao.\nTerdiri dari beberapa sekat bertingkat sehingga proses pembalikan lebih mudah dilakukan.\nLubang drainase di dasar kotak menjaga kelembapan tetap ideal selama lima sampai enam hari fermentasi.",
                        'capacity' => '500 kg/batch',
                        'power' => 'Tanpa listrik (manual)',
                        'dimension' => '120 x 90 x 100 cm',
                        'weight' => '120 kg',
                        'material' => 'Kayu keras (kruing)',
                        'specifications' => "Jumlah sekat: 3 tingkat\nPembalikan: Manual\nDrainase: Lubang bawah dengan kasa",
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Mesin Pengering Kakao',
                        'slug' => 'mesin-pengering-kakao',
                        'model_code' => 'MDI-DR1000',
                        'function' => 'Menurunkan kadar air biji kakao hingga 7-8 persen secara terkendali sebelum sortasi dan pengemasan.',
                        'short_description' => 'Dryer tipe rak dengan pemanas tidak langsung, kapasitas 1 ton per proses.',
                        'description' => "Mesin pengering MDI-DR1000 memakai sistem pemanasan tidak langsung sehingga biji kakao tidak terkontaminasi asap.\nSuhu dijaga pada kisaran 55-60 derajat Celsius agar aroma kakao tidak rusak.\nRak pengering dapat dilepas sehingga memudahkan proses bongkar muat.",
                        'capacity' => '1.000 kg/proses',
                        'power' => '3 kW, 1 phase',
                        'dimension' => '240 x 120 x 180 cm',
                        'weight' => '380 kg',
                        'material' => 'Stainless steel 304',
                        'specifications' => "Sumber panas: Tungku biomassa atau LPG\nSuhu kerja: 55 - 60 derajat Celsius\nJumlah rak: 12 rak",
                        'is_featured' => true,
                        'sort_order' => 2,
                    ],
                ],
            ],
            [
                'slug' => 'mesin-pengemasan',
                'name' => 'Mesin Pengemasan',
                'tagline' => 'Melindungi mutu produk akhir',
                'description' => 'Mesin pengemasan untuk mengisi dan menutup kemasan produk kopi maupun kakao siap jual.',
                'sort_order' => 4,
                'machines' => [
                    [
                        'name' => 'Mesin Pengemas Vakum',
                        'slug' => 'mesin-pengemas-vakum',
                        'model_code' => 'MDI-VP20',
                        'function' => 'Menghisap udara dari dalam kemasan lalu menyegelnya sehingga umur simpan produk menjadi lebih panjang.',
                        'short_description' => 'Vacuum sealer tipe meja dengan kapasitas hingga 20 kemasan per menit.',
                        'description' => "Pengemas vakum MDI-VP20 cocok untuk bubuk kopi, biji sangrai, dan produk kakao olahan.\nDilengkapi pompa vakum kering yang minim perawatan serta pengatur waktu hisap dan segel.\nBodi stainless steel memudahkan pembersihan harian.",
                        'capacity' => '20 kemasan/menit',
                        'power' => '1,5 kW, 1 phase',
                        'dimension' => '80 x 60 x 90 cm',
                        'weight' => '110 kg',
                        'material' => 'Stainless steel 304',
                        'specifications' => "Pompa: Vacuum pump kering 20 m3/jam\nPanjang segel: 400 mm\nKontrol: Panel digital waktu hisap dan segel",
                        'is_featured' => true,
                        'sort_order' => 1,
                    ],
                ],
            ],
        ];
    }
}
