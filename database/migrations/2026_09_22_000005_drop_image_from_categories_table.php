<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Foto jenis mesin sudah tidak dipakai lagi (form admin maupun tampilan
     * publik tidak lagi menampilkan foto jenis mesin), jadi kolom "image"
     * dihapus dari tabel categories.
     */
    public function up(): void
    {
        if (Schema::hasColumn('categories', 'image')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('categories', 'image')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->string('image')->nullable()->after('description');
            });
        }
    }
};
