<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hapus fitur yang sudah tidak dipakai lagi:
     *
     * - Katalog produk (tabel catalogs & catalog_images).
     * - Status akun pelanggan (kolom customers.is_active).
     * - Nama item pesanan (kolom order_items.name): nama diambil dari model mesin.
     */
    public function up(): void
    {
        Schema::dropIfExists('catalog_images');
        Schema::dropIfExists('catalogs');

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'name']);
            $table->dropColumn('is_active');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

    /**
     * Kembalikan struktur tabel seperti sebelum penghapusan fitur.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('name')->default('')->after('machine_id');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('notes');
            $table->index(['is_active', 'name']);
        });

        Schema::create('catalogs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('pdf_file')->nullable();
            $table->string('year', 10)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('catalog_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalog_id')->constrained('catalogs')->cascadeOnDelete();
            $table->string('path');
            $table->string('caption')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }
};
