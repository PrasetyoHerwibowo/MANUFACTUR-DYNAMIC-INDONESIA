<?php

namespace App\Http\Requests\Admin;

use App\Models\Customer;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validasi data pelanggan untuk form tambah & ubah.
 *
 * Base class ini menangani hal yang berlaku untuk keduanya:
 *
 * 1. Sanitasi (memotong spasi tepi, membersihkan nomor telepon) yang
 *    dijalankan di {@see self::prepareForValidation()} sehingga Aturan
 *    validasi selalu memeriksa nilai yang sudah bersih.
 * 2. Unik komposit "nama + nomor telepon" yang mencegah pelanggan ganda
 *    tercatat pada nama dan nomor telepon yang sama.
 * 3. Unik email, dengan pengecualian record sendiri saat menyunting.
 *
 * Subclass hanya perlu menyatakan pelanggan mana yang sedang di-edit
 * melalui {@see self::customerId()}.
 */
abstract class CustomerRequest extends FormRequest
{
    /**
     * Batas panjang setiap kolom, mengikuti skema tabel "customers".
     *
     * Dipakai sekaligus sebagai aturan validasi server dan, melalui
     * atribut maxlength pada form Blade, sebagai batas sisi klien —
     * sehingga keduanya tidak pernah berbeda.
     *
     * @var array<string, int>
     */
    public const LENGTHS = [
        'name' => 255,
        'email' => 255,
        'phone' => 30,
        'company' => 255,
        'address' => 1000,
        'city' => 100,
        'province' => 100,
        'postal_code' => 10,
        'notes' => 1000,
    ];

    /**
     * Kolom yang isinya dirapikan sebelum divalidasi.
     *
     * @var list<string>
     */
    private const TRIMMED_FIELDS = [
        'name',
        'email',
        'phone',
        'company',
        'address',
        'city',
        'province',
        'postal_code',
        'notes',
    ];

    /**
     * Kolom yang menyimpan nomor telepon.
     */
    private const PHONE = 'phone';

    /**
     * Kolom yang menyimpan nama pelanggan.
     */
    private const NAME = 'name';

    /**
     * Awalan nomor telepon Indonesia dalam format internasional.
     *
     * Setelah karakter non-digit dibuang, "+62 812-3456-7890" menjadi
     * "6281234567890" — bentuk yang BERBEDA dari "081234567890". Tanpa
     * langkah tambahan ini, admin yang mengetik nomor yang sama dengan dua
     * gaya penulisan lolos dari pemeriksaan pelanggan ganda, padahal
     * tujuannya justru mencegah itu. Karena itu awalan "62" dipetakan
     * kembali ke "0" sesuai kebiasaan penulisan di Indonesia.
     *
     * Nomor yang tidak diawali "62" dibiarkan apa adanya, sehingga nomor
     * dari negara lain tidak ikut dirusak.
     */
    private const ID_COUNTRY_CODE = '62';

    private const ID_TRUNK_PREFIX = '0';

    /**
     * Otorisasi (hanya admin) dijaga middleware 'admin' pada route,
     * sehingga FormRequest ini tidak perlu memeriksa ulang peran pengguna.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Sanitasi input SEBELUM validasi dijalankan.
     *
     * Penting: validasi tidak boleh melihat nilai mentah. Jika "+62 812-3456-7890"
     * baru dibersihkan setelah validasi, aturan max:30 dan pengecekan
     * keunikan akan menghitung karakter "+", spasi, dan tanda hubung yang
     * sebenarnya tidak disimpan.
     */
    protected function prepareForValidation(): void
    {
        $normalized = [];

        foreach (self::TRIMMED_FIELDS as $field) {
            $normalized[$field] = $this->sanitize($field, $this->input($field));
        }

        $this->merge($normalized);
    }

    /**
     * Aturan validasi.
     *
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:'.self::LENGTHS['name'],
                $this->nameAndPhoneMustBeUnique(),
            ],
            'email' => [
                'required',
                'string',
                $this->emailRule(),
                'max:'.self::LENGTHS['email'],
                Rule::unique('customers', 'email')->ignore($this->customerId()),
            ],
            self::PHONE => [
                'nullable',
                'string',
                'max:'.self::LENGTHS['phone'],
            ],
            'company' => ['nullable', 'string', 'max:'.self::LENGTHS['company']],
            'address' => ['nullable', 'string', 'max:'.self::LENGTHS['address']],
            'city' => ['nullable', 'string', 'max:'.self::LENGTHS['city']],
            'province' => ['nullable', 'string', 'max:'.self::LENGTHS['province']],
            'postal_code' => ['nullable', 'string', 'max:'.self::LENGTHS['postal_code']],
            'notes' => ['nullable', 'string', 'max:'.self::LENGTHS['notes']],
            'is_active' => ['required', 'boolean'],
        ];
    }

    /**
     * Nama kolom yang tampil pada pesan validasi.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama pelanggan',
            'email' => 'email',
            'phone' => 'nomor telepon',
            'company' => 'perusahaan',
            'address' => 'alamat',
            'city' => 'kota/kabupaten',
            'province' => 'provinsi',
            'postal_code' => 'kode pos',
            'notes' => 'catatan internal',
            'is_active' => 'status akun',
        ];
    }

    /**
     * Pesan validasi berbahasa Indonesia.
     *
     * Ditulis eksplisit (bukan mengandalkan berkas translate) karena aturan
     * ini dipakai langsung oleh form admin.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama pelanggan wajib diisi.',
            'name.max' => 'Nama pelanggan maksimal '.self::LENGTHS['name'].' karakter.',
            'name.string' => 'Nama pelanggan harus berupa teks.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid. Contoh: nama@perusahaan.com',
            'email.max' => 'Email maksimal '.self::LENGTHS['email'].' karakter.',
            'email.unique' => 'Email ini sudah terdaftar pada pelanggan lain. Gunakan email lain atau buka data pelanggan yang sudah ada untuk memperbarui.',
            'phone.max' => 'Nomor telepon maksimal '.self::LENGTHS['phone'].' digit.',
            'phone.string' => 'Nomor telepon harus berupa teks.',
            'company.max' => 'Perusahaan maksimal '.self::LENGTHS['company'].' karakter.',
            'address.max' => 'Alamat maksimal '.self::LENGTHS['address'].' karakter.',
            'city.max' => 'Kota/kabupaten maksimal '.self::LENGTHS['city'].' karakter.',
            'province.max' => 'Provinsi maksimal '.self::LENGTHS['province'].' karakter.',
            'postal_code.max' => 'Kode pos maksimal '.self::LENGTHS['postal_code'].' karakter.',
            'notes.max' => 'Catatan internal maksimal '.self::LENGTHS['notes'].' karakter.',
            'is_active.required' => 'Status akun pelanggan wajib dipilih.',
            'is_active.boolean' => 'Status akun pelanggan tidak valid.',
        ];
    }

    /**
     * Aturan untuk kolom email.
     *
     * "email:dns" memeriksa record MX/A domain sehingga salah ketik pada
     * domain tertangkap lebih awal. Pengecekan itu butuh koneksi internet,
     * jadi dapat dimatikan lewat VALIDATE_EMAIL_DNS=false pada instalasi
     * lokal atau jaringan tertutup. Bila dimatikan, sintaks RFC tetap
     * divalidasi — hanya pencarian MX/A yang dilewati.
     */
    private function emailRule(): string
    {
        return config('validation.verify_email_dns') ? 'email:dns' : 'email';
    }

    /**
     * ID pelanggan yang sedang disunting, atau null saat menambah baru.
     *
     * Nilai ini dipakai untuk mengabaikan record sendiri pada aturan
     * keunikan email maupun keunikan nama + nomor telepon.
     */
    abstract protected function customerId(): ?int;

    /**
     * Aturan yang menolak pelanggan ganda pada nama + nomor telepon sama.
     *
     * Tiga hal yang perlu diperhatikan:
     *
     * - Nomor telepon kosong TIDAK ikut diperiksa. Tanpa hal ini, semua
     *   pelanggan tanpa nomor telepon dan bernama sama akan saling menabrak.
     * - Saat menyunting tanpa mengubah nama maupun nomor telepon,
     *   pemeriksaan dilewati. Ini mengikuti pola yang sudah dipakai
     *   MachineController, sehingga pelanggan ganda warisan lama tetap
     *   dapat diperbaiki (mis. hanya memperbarui alamat) tanpa terkunci.
     * - Perbandingan nama memakai LOWER() secara sadar, bukan mengandalkan
     *   collation kolom. Kollation bawaan MySQL (utf8mb4_*) memang
     *   mengabaikan huruf besar-kecil, sedangkan SQLite tidak — tanpa
     *   LOWER()Aturan ini akan menangkap "BUDI SANTOSO" di produksi tetapi
     *   lolos di test. Nilai yang sudah dinormalisasi (tanpa spasi tepi)
     *   tetap dibandingkan apa adanya, hanya huruf besarnya yang diabaikan.
     */
    private function nameAndPhoneMustBeUnique(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            $name = $this->input(self::NAME);
            $phone = $this->input(self::PHONE);

            if (! is_string($name) || $name === '' || ! is_string($phone) || $phone === '') {
                return;
            }

            $customerId = $this->customerId();
            $customer = $customerId === null ? null : Customer::query()->find($customerId);

            // Bandekan tanpa melihat huruf besar-kecil, konsisten dengan
            // query di bawah. Kalau tidak, menyunting "Budi Santoso"
            // menjadi "BUDI SANTOSO" akan salah dianggap sebagai duplikat
            // dengan dirinya sendiri.
            if ($customer !== null
                && mb_strtolower(trim((string) $customer->name)) === mb_strtolower($name)
                && (string) $customer->phone === $phone) {
                return;
            }

            $exists = Customer::query()
                ->whereRaw('LOWER('.$this->qualifyNameColumn().') = ?', [mb_strtolower($name)])
                ->where(self::PHONE, $phone)
                ->when($customerId !== null, fn (Builder $query) => $query->whereKeyNot($customerId))
                ->exists();

            if ($exists) {
                $fail('Pelanggan dengan nama dan nomor telepon tersebut sudah terdaftar. '
                    .'Gunakan email yang berbeda bila ini memang pelanggan yang berbeda.');
            }
        };
    }

    /**
     * Nama kolom "name" yang sudah diberi prefiks tabel.
     */
    private function qualifyNameColumn(): string
    {
        return (new Customer)->getTable().'.'.self::NAME;
    }

    /**
     * Rapikan satu nilai input sebelum divalidasi.
     *
     * - Teks: spasi tepi dibuang, teks kosong menjadi null agar aturan
     *   "nullable" tidak menyimpan string kosong.
     * - Nomor telepon: seluruh karakter selain digit dibuang, lalu awalan
     *   negara "62" dipetakan ke "0". Dengan begitu "+62 812-3456-7890",
     *   "62 812 3456 7890", "0812-3456-7890", dan "(0812) 3456 7890"
     *   semuanya menjadi "081234567890" — satu bentuk yang dapat
     *   dibandingkan, sehingga mendeteksi pelanggan ganda benar-benar bekerja.
     */
    private function sanitize(string $field, mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        if ($field === self::PHONE) {
            return $this->normalizePhone($value);
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }

    /**
     * Ubah nomor telepon menjadi bentuk kanonik (hanya digit).
     */
    private function normalizePhone(string $value): ?string
    {
        $digits = preg_replace('/\D+/', '', $value) ?? '';

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, self::ID_COUNTRY_CODE)) {
            $digits = self::ID_TRUNK_PREFIX.substr($digits, strlen(self::ID_COUNTRY_CODE));
        }

        return $digits;
    }
}
