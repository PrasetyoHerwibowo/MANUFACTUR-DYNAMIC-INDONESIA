<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Machine;
use App\Models\Order;
use App\Models\Payment;
use App\Support\Reference;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

/**
 * Contoh data pelanggan, pesanan, dan pembayaran untuk menguji modul
 * "Pesanan & Pembayaran" serta "Pelanggan" pada panel admin.
 */
class CustomerOrderSeeder extends Seeder
{
    public function run(): void
    {
        /** @var Collection<int, Machine> $machines */
        $machines = Machine::query()->get(['id', 'name', 'slug']);

        foreach ($this->customers() as $data) {
            $customer = Customer::query()->firstOrCreate(
                ['email' => $data['email']],
                [
                    'customer_code' => Reference::make(Reference::CUSTOMER, Customer::class, 'customer_code'),
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'company' => $data['company'] ?? null,
                    'address' => $data['address'],
                    'city' => $data['city'],
                    'province' => $data['province'],
                    'postal_code' => $data['postal_code'],
                    'notes' => $data['notes'] ?? null,
                    'is_active' => $data['is_active'] ?? true,
                ],
            );

            // Lewati pelanggan yang sudah punya pesanan agar seeder aman diulang.
            if ($customer->orders()->exists() || $machines->isEmpty()) {
                continue;
            }

            foreach ($data['orders'] as $scenario) {
                $this->createOrder($customer, $scenario, $machines);
            }

            $customer->forceFill([
                'last_order_at' => $customer->orders()->latest('created_at')->value('created_at'),
            ])->save();
        }

        if ($machines->isEmpty()) {
            $this->command?->warn('CustomerOrderSeeder dilewati: data mesin belum tersedia.');
        }
    }

    /**
     * Buat satu pesanan beserta item dan riwayat pembayarannya.
     *
     * @param  array<string, mixed>  $scenario
     * @param  Collection<int, Machine>  $machines
     */
    private function createOrder(Customer $customer, array $scenario, Collection $machines): void
    {
        $createdAt = CarbonImmutable::now()->subDays((int) $scenario['days_ago'])->setTime(9, 30);

        $order = Order::query()->create([
            'customer_id' => $customer->id,
            'order_code' => Reference::make(Reference::ORDER, Order::class, 'order_code'),
            'status' => Order::STATUS_MENUNGGU_PEMBAYARAN,
            'notes' => $scenario['notes'] ?? null,
        ]);

        $total = 0.0;

        foreach ($scenario['items'] as $item) {
            $machine = isset($item['slug']) ? $machines->firstWhere('slug', $item['slug']) : null;
            $subtotal = (float) $item['unit_price'] * (int) $item['quantity'];
            $total += $subtotal;

            $order->items()->create([
                'machine_id' => $machine?->id,
                'name' => $item['name'] ?? $machine?->name ?? 'Item pesanan',
                'unit_price' => $item['unit_price'],
                'quantity' => $item['quantity'],
                'subtotal' => $subtotal,
            ]);
        }

        $order->forceFill([
            'total_amount' => $total,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ])->save();

        $this->seedPayments($order, $scenario, $createdAt);
    }

    /**
     * Riwayat pembayaran sesuai skenario: menunggu, lunas, gagal, atau kedaluwarsa.
     *
     * @param  array<string, mixed>  $scenario
     */
    private function seedPayments(Order $order, array $scenario, CarbonImmutable $createdAt): void
    {
        $method = $scenario['method'];
        $status = $scenario['payment'];

        // Pembayaran pertama selalu dibuat pada waktu pesanan dibuat.
        $firstDeadline = $createdAt->addHours(Order::PAYMENT_WINDOW_HOURS);

        if ($status === 'kedaluwarsa') {
            // Lewat batas waktu: otomatis berstatus gagal saat halaman admin dibuka.
            $payment = $order->createPayment($method, CarbonImmutable::now()->subHours(3), null, 'Menunggu konfirmasi transfer.');
            $payment->forceFill(['created_at' => $createdAt, 'updated_at' => $createdAt])->save();

            return;
        }

        if ($status === 'gagal') {
            $failed = $order->createPayment($method, $firstDeadline);
            $failed->forceFill(['created_at' => $createdAt, 'updated_at' => $createdAt])->save();
            $failed->markAsFailed('Bukti transfer tidak diterima sebelum batas waktu');

            $replacement = $order->createPayment(
                $method,
                CarbonImmutable::now()->addHours(Order::PAYMENT_WINDOW_HOURS),
                null,
                'Kode pembayaran pengganti untuk pelanggan.',
            );
            $replacement->forceFill([
                'created_at' => $createdAt->addHours(6),
                'updated_at' => $createdAt->addHours(6),
            ])->save();

            return;
        }

        $payment = $order->createPayment($method, $status === 'lunas' ? $firstDeadline : CarbonImmutable::now()->addHours(Order::PAYMENT_WINDOW_HOURS));
        $payment->forceFill(['created_at' => $createdAt, 'updated_at' => $createdAt])->save();

        if ($status === 'lunas') {
            $payment->forceFill([
                'status' => Payment::STATUS_LUNAS,
                'paid_at' => $createdAt->addHours(5),
                'notes' => 'Transfer diterima dan diverifikasi admin.',
            ])->save();

            $order->syncStatusFromPayments();
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function customers(): array
    {
        return [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@kopinusantara.co.id',
                'phone' => '0812-3456-7890',
                'company' => 'CV Kopi Nusantara',
                'address' => 'Jl. Merdeka No. 45, Kel. Citarum',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'postal_code' => '40115',
                'notes' => 'Pembayaran selalu melalui transfer bank perusahaan.',
                'orders' => [
                    [
                        'days_ago' => 1,
                        'method' => Payment::METHOD_TRANSFER_BANK,
                        'payment' => 'menunggu',
                        'notes' => 'Pengiriman ke gudang Bandung, biaya kirim ditanggung pembeli.',
                        'items' => [
                            ['slug' => 'mesin-pulper-kopi-basah', 'unit_price' => 28500000, 'quantity' => 1],
                            ['name' => 'Instalasi & Pelatihan Operator', 'unit_price' => 2500000, 'quantity' => 1],
                        ],
                    ],
                    [
                        'days_ago' => 8,
                        'method' => Payment::METHOD_VIRTUAL_ACCOUNT,
                        'payment' => 'lunas',
                        'notes' => 'Sudah dikirim melalui ekspedisi darat.',
                        'items' => [
                            ['slug' => 'mesin-huller-kopi-kering', 'unit_price' => 17500000, 'quantity' => 2],
                        ],
                    ],
                    [
                        'days_ago' => 15,
                        'method' => Payment::METHOD_QRIS,
                        'payment' => 'gagal',
                        'notes' => 'Pelanggan meminta kode pembayaran baru.',
                        'items' => [
                            ['name' => 'Suku Cadang Silinder Pulper', 'unit_price' => 1250000, 'quantity' => 3],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Siti Rahmawati',
                'email' => 'siti.rahmawati@tanimakmur.id',
                'phone' => '0857-1122-3344',
                'company' => 'Koperasi Tani Makmur',
                'address' => 'Jl. Raya Ungaran KM 12, Kel. Bandungan',
                'city' => 'Semarang',
                'province' => 'Jawa Tengah',
                'postal_code' => '50211',
                'notes' => 'Pembelian secara kolektif untuk anggota koperasi.',
                'orders' => [
                    [
                        'days_ago' => 6,
                        'method' => Payment::METHOD_TRANSFER_BANK,
                        'payment' => 'lunas',
                        'notes' => 'Pengiriman dilakukan dua tahap bersama unit pengering.',
                        'items' => [
                            ['slug' => 'mesin-pengering-kakao', 'unit_price' => 62500000, 'quantity' => 1],
                        ],
                    ],
                    [
                        'days_ago' => 3,
                        'method' => Payment::METHOD_TUNAI,
                        'payment' => 'kedaluwarsa',
                        'notes' => 'Pelanggan belum mengonfirmasi pembayaran tunai.',
                        'items' => [
                            ['name' => 'Biaya Servis Rutin Mesin Huller', 'unit_price' => 850000, 'quantity' => 2],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Andi Pratama',
                'email' => 'andi.pratama@kakaonusantara.com',
                'phone' => '0813-5566-7788',
                'company' => 'PT Kakao Nusantara',
                'address' => 'Jl. Perintis Kemerdekaan No. 88, Tamalanrea',
                'city' => 'Makassar',
                'province' => 'Sulawesi Selatan',
                'postal_code' => '90245',
                'orders' => [
                    [
                        'days_ago' => 2,
                        'method' => Payment::METHOD_VIRTUAL_ACCOUNT,
                        'payment' => 'menunggu',
                        'notes' => 'Proyek pengembangan pabrik kakao tahap pertama.',
                        'items' => [
                            ['slug' => 'mesin-pulper-kopi-basah', 'unit_price' => 28500000, 'quantity' => 2],
                            ['name' => 'Konveyor Pengumpan', 'unit_price' => 7500000, 'quantity' => 1],
                        ],
                    ],
                    [
                        'days_ago' => 22,
                        'method' => Payment::METHOD_TRANSFER_BANK,
                        'payment' => 'lunas',
                        'notes' => 'Sudah lunas dan dikirim melalui jasa ekspedisi laut.',
                        'items' => [
                            ['slug' => 'mesin-pengemas-vakum', 'unit_price' => 18900000, 'quantity' => 1],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi@roasterydewi.co.id',
                'phone' => '0878-9900-1122',
                'company' => 'UMKM Roastery Dewi',
                'address' => 'Jl. Kaliurang KM 5, Sleman',
                'city' => 'Yogyakarta',
                'province' => 'DIY',
                'postal_code' => '55281',
                'notes' => 'Calon pelanggan, baru menanyakan harga mesin sangrai.',
                'is_active' => false,
                'orders' => [],
            ],
        ];
    }
}

