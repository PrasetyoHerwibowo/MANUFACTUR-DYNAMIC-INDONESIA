<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jadikan kolom phone pada tabel customers nullable dan lepas batasan unik tunggal.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE customers ALTER COLUMN phone DROP NOT NULL');
        DB::statement('ALTER TABLE customers DROP CONSTRAINT IF EXISTS customers_phone_unique');
        DB::statement("UPDATE customers SET phone = NULL WHERE phone = '-'");
    }

    public function down(): void
    {
        DB::statement("UPDATE customers SET phone = '-' WHERE phone IS NULL");
        DB::statement('ALTER TABLE customers ALTER COLUMN phone SET NOT NULL');
        DB::statement('ALTER TABLE customers ADD CONSTRAINT customers_phone_unique UNIQUE (phone)');
    }
};
