<?php

namespace Tests\Feature\Admin;

use App\Http\Requests\Admin\CustomerRequest;
use App\Models\Customer;
use App\Models\User;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\EmailValidation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Validasi form pelanggan: sanitasi telepon, unik komposit nama + telepon,
 * keunikan email, dan pemotongan panjang kolom.
 */
class CustomerFormValidationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        // Pengecekan email:dns melakukan pencarian DNS sungguhan. Diuji dengan
        // validator tiruan supaya suite tidak bergantung pada jaringan.
        $this->app->bind(EmailValidator::class, fn () => new class extends EmailValidator
        {
            public function isValid(string $email, EmailValidation $emailValidation): bool
            {
                return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
            }
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Budi Santoso',
            'email' => 'budi@contoh.test',
            'phone' => '081234567890',
            'company' => 'CV Kopi Nusantara',
            'address' => 'Jl. Merdeka 1',
            'city' => 'Kota Bandung',
            'province' => 'Jawa Barat',
            'postal_code' => '40123',
            'notes' => null,
            'is_active' => '1',
        ], $overrides);
    }

    private function customer(array $attributes = []): Customer
    {
        return Customer::query()->create(array_merge([
            'customer_code' => 'CUS-'.uniqid(),
            'name' => 'Budi Santoso',
            'email' => 'budi@contoh.test',
            'phone' => '081234567890',
            'is_active' => true,
        ], $attributes));
    }

    // -----------------------------------------------------------------
    // Sanitasi nomor telepon
    // -----------------------------------------------------------------

    public function test_phone_is_stripped_of_non_digit_characters(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.customers.store'), $this->payload([
                'phone' => '+62 812-3456-7890',
            ]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('customers', [
            'phone' => '081234567890',
        ]);
    }

    public function test_international_and_local_phone_formats_collapse_to_one_value(): void
    {
        $formats = [
            '081234567890',
            '0812-3456-7890',
            '(0812) 3456 7890',
            '0812 3456 7890',
            '+62 812-3456-7890',
            '62 812 3456 7890',
            '+6281234567890',
        ];

        // Nama dibuat berbeda per iterasi agar tidak bentrok dengan aturan
        // keunikan nama + telepon — yang justru diuji oleh test lain.
        // Suffiksnya huruf, bukan angka, karena nama hanya boleh berisi
        // huruf dan spasi (lihat NAME_PATTERN di CustomerRequest).
        $suffixes = ['sari', 'wulan', 'dari', 'medina', 'ratna', 'ayu', 'loka'];

        foreach ($formats as $index => $input) {
            $this->actingAs($this->admin)
                ->post(route('admin.customers.store'), $this->payload([
                    'name' => 'Sari Wulandari '.$suffixes[$index],
                    'email' => 'sari-'.$suffixes[$index].'@contoh.test',
                    'phone' => $input,
                ]))
                ->assertSessionHasNoErrors();
        }

        $this->assertSame(
            ['081234567890'],
            Customer::query()->pluck('phone')->unique()->values()->all(),
            'Semua gaya penulisan harus menjadi satu bentuk kanonik yang sama.',
        );
    }

    public function test_foreign_phone_numbers_are_not_rewritten(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.customers.store'), $this->payload([
                'name' => 'Budi Santoso',
                'email' => 'budi@contoh.test',
                'phone' => '+1 202 555 0143',
            ]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('customers', ['phone' => '12025550143']);
    }

    public function test_blank_phone_becomes_null_instead_of_empty_string(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.customers.store'), $this->payload([
                'name' => 'Tanpa Telepon',
                'email' => 'tanpatelepon@contoh.test',
                'phone' => '   ',
            ]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('customers', [
            'email' => 'tanpatelepon@contoh.test',
            'phone' => null,
        ]);
    }

    // -----------------------------------------------------------------
    // Unik komposit: nama + nomor telepon
    // -----------------------------------------------------------------

    public function test_duplicate_name_and_phone_is_rejected_even_when_email_differs(): void
    {
        $this->customer();

        $this->actingAs($this->admin)
            ->from(route('admin.customers.create'))
            ->post(route('admin.customers.store'), $this->payload([
                'email' => 'beda-email@contoh.test',
            ]))
            ->assertRedirect(route('admin.customers.create'))
            ->assertSessionHasErrors('name');

        $this->assertSame(
            1,
            Customer::query()->where('name', 'Budi Santoso')->count(),
            'Pelanggan ganda tidak boleh ikut tersimpan.',
        );
    }

    public function test_duplicate_is_detected_across_phone_formats(): void
    {
        $this->customer(['phone' => '081234567890']);

        $this->actingAs($this->admin)
            ->from(route('admin.customers.create'))
            ->post(route('admin.customers.store'), $this->payload([
                'email' => 'lain@contoh.test',
                'phone' => '+62 812 3456 7890',
            ]))
            ->assertSessionHasErrors('name');

        $this->assertSame(1, Customer::query()->count());
    }

    public function test_same_name_with_different_phone_is_allowed(): void
    {
        $this->customer(['phone' => '081234567890']);

        $this->actingAs($this->admin)
            ->from(route('admin.customers.create'))
            ->post(route('admin.customers.store'), $this->payload([
                'email' => 'budi.kantor@contoh.test',
                'phone' => '081298765432',
            ]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('customers', ['email' => 'budi.kantor@contoh.test']);
    }

    public function test_same_name_without_phone_is_not_treated_as_duplicate(): void
    {
        $this->customer(['phone' => null]);

        $this->actingAs($this->admin)
            ->from(route('admin.customers.create'))
            ->post(route('admin.customers.store'), $this->payload([
                'email' => 'kedua@contoh.test',
                'phone' => null,
            ]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('customers', ['email' => 'kedua@contoh.test']);
    }

    public function test_error_message_is_in_indonesian(): void
    {
        $this->customer();

        $this->actingAs($this->admin)
            ->from(route('admin.customers.create'))
            ->post(route('admin.customers.store'), $this->payload([
                'email' => 'beda@contoh.test',
            ]))
            ->assertSessionHasErrors([
                'name' => 'Pelanggan dengan nama dan nomor telepon tersebut sudah terdaftar. Gunakan email yang berbeda bila ini memang pelanggan yang berbeda.',
            ]);
    }

    // -----------------------------------------------------------------
    // Unik email
    // -----------------------------------------------------------------

    public function test_duplicate_email_is_rejected(): void
    {
        $this->customer();

        $this->actingAs($this->admin)
            ->from(route('admin.customers.create'))
            ->post(route('admin.customers.store'), $this->payload([
                'name' => 'Nama Lain',
                'phone' => '081200000000',
            ]))
            ->assertSessionHasErrors('email');

        $this->assertDatabaseMissing('customers', ['name' => 'Nama Lain']);
    }

    // -----------------------------------------------------------------
    // Menyunting: record sendiri harus diabaikan
    // -----------------------------------------------------------------

    public function test_update_ignores_its_own_record(): void
    {
        $customer = $this->customer();

        // Nama + nomor telepon sengaja dibiarkan sama dengan yang tersimpan,
        // sehingga aturan keunikan hanya bisa lolos bila record sendiri
        // benar-benar dikecualikan.
        $this->actingAs($this->admin)
            ->put(route('admin.customers.update', $customer), $this->payload([
                'city' => 'Kota Bekasi',
            ]))
            ->assertSessionHasNoErrors();

        $this->assertSame('Kota Bekasi', $customer->fresh()->city);
    }

    public function test_update_rejects_another_customers_email(): void
    {
        $customer = $this->customer();
        $other = $this->customer([
            'name' => 'Sari Wulandari',
            'email' => 'sari@contoh.test',
            'phone' => '081298765432',
        ]);

        $this->actingAs($this->admin)
            ->from(route('admin.customers.edit', $other))
            ->put(route('admin.customers.update', $other), $this->payload([
                'name' => 'Sari Wulandari',
                'phone' => '081298765432',
                'email' => $customer->email,
            ]))
            ->assertSessionHasErrors('email');
    }

    public function test_update_rejects_another_customers_name_and_phone(): void
    {
        $this->customer();

        $other = $this->customer([
            'name' => 'Sari Wulandari',
            'email' => 'sari@contoh.test',
            'phone' => '081298765432',
        ]);

        $this->actingAs($this->admin)
            ->from(route('admin.customers.edit', $other))
            ->put(route('admin.customers.update', $other), $this->payload([
                'name' => 'Budi Santoso',
                'phone' => '081234567890',
            ]))
            ->assertSessionHasErrors('name');
    }

    public function test_legacy_duplicate_can_still_be_edited_when_name_and_phone_unchanged(): void
    {
        $first = $this->customer();
        $second = $this->customer([
            'name' => 'Budi Santoso',
            'email' => 'duplikat@contoh.test',
            'phone' => '081234567890',
        ]);

        // Hanya alamat yang diubah → pemeriksaan keunikan harus dilewati,
        // jika tidak pelanggan ganda warisan lama tidak akan pernah bisa diperbaiki.
        $this->actingAs($this->admin)
            ->put(route('admin.customers.update', $second), $this->payload([
                'email' => 'duplikat@contoh.test',
                'address' => 'Alamat Baru 99',
            ]))
            ->assertSessionHasNoErrors();

        $this->assertSame('Alamat Baru 99', $second->fresh()->address);
        $this->assertNotNull($first->fresh());
    }

    // -----------------------------------------------------------------
    // Pemotongan & aturan dasar
    // -----------------------------------------------------------------

    public function test_all_strings_are_trimmed_before_saving(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.customers.store'), $this->payload([
                'name' => '  Budi Santoso  ',
                'email' => '  budi@contoh.test  ',
                'company' => '  CV Kopi Nusantara  ',
                'address' => '  Jl. Merdeka 1  ',
                'city' => '  Kota Bandung  ',
                'province' => '  Jawa Barat  ',
                'postal_code' => '  40123  ',
                'notes' => '  catatan  ',
            ]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('customers', [
            'name' => 'Budi Santoso',
            'email' => 'budi@contoh.test',
            'company' => 'CV Kopi Nusantara',
            'address' => 'Jl. Merdeka 1',
            'city' => 'Kota Bandung',
            'province' => 'Jawa Barat',
            'postal_code' => '40123',
            'notes' => 'catatan',
        ]);
    }

    public function test_max_length_is_enforced_per_column(): void
    {
        $this->actingAs($this->admin)
            ->from(route('admin.customers.create'))
            ->post(route('admin.customers.store'), $this->payload([
                'name' => str_repeat('a', 256),
                'company' => str_repeat('b', 256),
                'city' => str_repeat('c', 101),
                'province' => str_repeat('d', 101),
                'postal_code' => str_repeat('e', 11),
                'address' => str_repeat('f', 1001),
                'notes' => str_repeat('g', 1001),
                'phone' => str_repeat('1', 31),
            ]))
            ->assertSessionHasErrors([
                'name', 'company', 'city', 'province', 'postal_code', 'address', 'notes', 'phone',
            ]);
    }

    public function test_required_fields_are_enforced(): void
    {
        $this->actingAs($this->admin)
            ->from(route('admin.customers.create'))
            ->post(route('admin.customers.store'), $this->payload([
                'name' => '',
                'email' => '',
            ]))
            ->assertSessionHasErrors(['name', 'email']);
    }

    public function test_customer_code_is_generated_automatically(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.customers.store'), $this->payload())
            ->assertSessionHasNoErrors();

        $customer = Customer::query()->firstOrFail();

        $this->assertNotEmpty($customer->customer_code);
    }

    // -----------------------------------------------------------------
    // Otorisasi
    // -----------------------------------------------------------------

    public function test_guest_cannot_reach_the_form(): void
    {
        $this->post(route('admin.customers.store'), $this->payload())
            ->assertRedirect(route('admin.login'));

        $this->assertDatabaseCount('customers', 0);
    }

    public function test_form_carries_maxlength_matching_the_request_limits(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.customers.create'));

        $response->assertOk();

        $html = $response->getContent();

        foreach (CustomerRequest::LENGTHS as $field => $limit) {
            $this->assertStringContainsString(
                'maxlength="'.$limit.'"',
                $html,
                "Field {$field} harus punya maxlength={$limit}.",
            );
        }
    }
}
