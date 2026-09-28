<?php

namespace Tests\Feature\Admin;

use App\Models\Customer;
use App\Models\User;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\EmailValidation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Matriks duplikasi pelanggan.
 *
 * Satu test untuk setiap kombinasi nama/email/telepon yang sama dan berbeda,
 * sehingga dapat dipastikan pelanggan benar-benar tidak dapat didaftarkan
 * ganda dengan data yang sama.
 */
class CustomerDuplicateMatrixTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    /** Pelanggan yang selalu ada sebagai pembanding. */
    private const EXISTING = [
        'name' => 'Budi Santoso',
        'email' => 'budi@contoh.test',
        'phone' => '081234567890',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->app->bind(EmailValidator::class, fn () => new class extends EmailValidator
        {
            public function isValid(string $email, EmailValidation $emailValidation): bool
            {
                return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
            }
        });

        Customer::query()->create(self::EXISTING + [
            'customer_code' => 'CUS-EXISTING',
            'is_active' => true,
        ]);
    }

    /**
     * Kombinasi duplikasi: [label, name, email, phone, bolehDisimpan, fieldYangDitolak].
     *
     * @return array<string, array{0:string,1:string,2:string,3:string,4:bool,5:string}>
     */
    public static function duplicateCombinations(): array
    {
        return [
            'semua field sama' => [
                'Semua field sama persis', 'Budi Santoso', 'budi@contoh.test', '081234567890',
                false, 'name',
            ],
            'nama + telepon sama, email beda' => [
                'Nama + telepon sama, email berbeda', 'Budi Santoso', 'lain@contoh.test', '081234567890',
                false, 'name',
            ],
            'nama + telepon sama, email beda, format internasional' => [
                'Nama + telepon sama, telepon format +62', 'Budi Santoso', 'lain@contoh.test', '+62 812-3456-7890',
                false, 'name',
            ],
            'nama + telepon sama, email beda, format bercakung' => [
                'Nama + telepon sama, telepon format (0812)', 'Budi Santoso', 'lain@contoh.test', '(0812) 3456 7890',
                false, 'name',
            ],
            'email sama, nama + telepon beda' => [
                'Email sama, nama & telepon berbeda', 'Sari Wulandari', 'budi@contoh.test', '081298765432',
                false, 'email',
            ],
            'email sama, nama sama, telepon beda' => [
                'Email sama, nama sama, telepon berbeda', 'Budi Santoso', 'budi@contoh.test', '081298765432',
                false, 'email',
            ],
            'nama sama, telepon beda' => [
                'Nama sama, telepon berbeda', 'Budi Santoso', 'lain@contoh.test', '081298765432',
                true, '',
            ],
            'telepon sama, nama beda' => [
                'Telepon sama, nama berbeda', 'Sari Wulandari', 'lain@contoh.test', '081234567890',
                true, '',
            ],
            'semua field beda' => [
                'Nama, telepon, dan email berbeda', 'Sari Wulandari', 'sari@contoh.test', '081298765432',
                true, '',
            ],
            'nama beda huruf besar-kecil' => [
                'Nama beda kapitalisasi, telepon sama', 'BUDI SANTOSO', 'lain@contoh.test', '081234567890',
                false, 'name',
            ],
            'nama dengan spasi berlebih' => [
                'Nama dengan spasi tepi, telepon sama', '  Budi Santoso  ', 'lain@contoh.test', '081234567890',
                false, 'name',
            ],
        ];
    }

    #[DataProvider('duplicateCombinations')]
    public function test_duplicate_matrix(
        string $label,
        string $name,
        string $email,
        string $phone,
        bool $shouldSucceed,
        string $rejectedField,
    ): void {
        $response = $this->actingAs($this->admin)
            ->from(route('admin.customers.create'))
            ->post(route('admin.customers.store'), [
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'is_active' => '1',
            ]);

        if ($shouldSucceed) {
            $response->assertSessionHasNoErrors();
            $this->assertSame(2, Customer::query()->count(), "[{$label}] seharusnya tersimpan.");

            return;
        }

        $response->assertSessionHasErrors($rejectedField);
        $this->assertSame(1, Customer::query()->count(), "[{$label}] tidak boleh menambah pelanggan.");
    }

    public function test_adding_the_exact_same_customer_twice_in_a_row_is_blocked(): void
    {
        $data = [
            'name' => 'Budi Santoso',
            'email' => 'budi@contoh.test',
            'phone' => '081234567890',
            'is_active' => '1',
        ];

        // Percobaan pertama: data identik dengan pelanggan yang sudah ada.
        $this->actingAs($this->admin)
            ->from(route('admin.customers.create'))
            ->post(route('admin.customers.store'), $data)
            ->assertSessionHasErrors(['name', 'email']);

        $this->assertDatabaseCount('customers', 1);

        // Percobaan kedua: klik lagi tanpa memperbaiki apa pun.
        $this->actingAs($this->admin)
            ->from(route('admin.customers.create'))
            ->post(route('admin.customers.store'), $data)
            ->assertSessionHasErrors(['name', 'email']);

        $this->assertSame(1, Customer::query()->count(), 'Data yang sama tidak boleh masuk dua kali.');
    }

    public function test_twenty_consecutive_duplicate_attempts_never_create_a_row(): void
    {
        for ($attempt = 0; $attempt < 20; $attempt++) {
            $this->actingAs($this->admin)
                ->from(route('admin.customers.create'))
                ->post(route('admin.customers.store'), [
                    'name' => 'Budi Santoso',
                    'email' => 'budi@contoh.test',
                    'phone' => '081234567890',
                    'is_active' => '1',
                ])
                ->assertSessionHasErrors('name');
        }

        $this->assertDatabaseCount('customers', 1);
    }

    public function test_database_has_no_duplicate_name_and_phone_pair(): void
    {
        $this->actingAs($this->admin)
            ->from(route('admin.customers.create'))
            ->post(route('admin.customers.store'), [
                'name' => 'Budi Santoso',
                'email' => 'lain@contoh.test',
                'phone' => '081234567890',
                'is_active' => '1',
            ])
            ->assertSessionHasErrors('name');

        $duplicates = Customer::query()
            ->whereNotNull('phone')
            ->selectRaw('name, phone, count(*) as total')
            ->groupBy('name', 'phone')
            ->havingRaw('count(*) > 1')
            ->get();

        $this->assertCount(0, $duplicates, 'Tidak boleh ada pasangan nama + telepon yang ganda.');
    }

    public function test_case_only_edit_is_not_mistaken_for_a_duplicate_of_itself(): void
    {
        $customer = Customer::query()->firstOrFail();

        // Hanya kapitalisasi nama yang diubah, nomor telepon tetap.
        // Perbandingan keunikan tidak boleh menganggap ini bentrok dengan
        // record yang sedang disunting.
        $this->actingAs($this->admin)
            ->from(route('admin.customers.edit', $customer))
            ->put(route('admin.customers.update', $customer), [
                'name' => 'BUDI SANTOSO',
                'email' => $customer->email,
                'phone' => '081234567890',
                'is_active' => '1',
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame('BUDI SANTOSO', $customer->fresh()->name);
        $this->assertSame(1, Customer::query()->count());
    }

    public function test_legacy_duplicate_can_be_recased_without_being_locked_out(): void
    {
        // Duplikat warisan: nama sama, nomor sama, email berbeda.
        $other = Customer::query()->create([
            'customer_code' => 'CUS-DUP',
            'name' => 'budi santoso',
            'email' => 'duplikat@contoh.test',
            'phone' => '081234567890',
            'is_active' => true,
        ]);

        // Hanya kapitalisasi nama yang diubah, nomor telepon tetap.
        // Aturan "lewati bila tidak berubah" berlaku karena nilai yang
        // dibandingkan sama saja secara makna — admin tidak sedang membuat
        // duplikat baru, dan mengunci form di sini hanya menghambat
        // perbaikan data lama.
        $this->actingAs($this->admin)
            ->from(route('admin.customers.edit', $other))
            ->put(route('admin.customers.update', $other), [
                'name' => 'Budi Santoso',
                'email' => 'duplikat@contoh.test',
                'phone' => '081234567890',
                'is_active' => '1',
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame('Budi Santoso', $other->fresh()->name);
    }

    public function test_changing_a_legacy_duplicate_to_collide_with_another_is_blocked(): void
    {
        $existing = Customer::query()->firstOrFail();

        $other = Customer::query()->create([
            'customer_code' => 'CUS-DUP',
            'name' => 'Sari Wulandari',
            'email' => 'duplikat@contoh.test',
            'phone' => '081298765432',
            'is_active' => true,
        ]);

        // Mengubah nama + nomor menjadi milik pelanggan lain harus ditolak,
        // walau nilai baris ini sendiri berubah.
        $this->actingAs($this->admin)
            ->from(route('admin.customers.edit', $other))
            ->put(route('admin.customers.update', $other), [
                'name' => $existing->name,
                'email' => 'duplikat@contoh.test',
                'phone' => $existing->phone,
                'is_active' => '1',
            ])
            ->assertSessionHasErrors('name');

        $this->assertSame('Sari Wulandari', $other->fresh()->name);
    }
}
