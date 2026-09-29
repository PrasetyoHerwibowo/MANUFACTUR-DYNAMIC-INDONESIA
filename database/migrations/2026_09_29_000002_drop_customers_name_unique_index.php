<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Melepas batasan unik pada kolom customers.name.
 *
 * Aturan yang berlaku adalah keunikan KOMPOSIT nama + nomor telepon, bukan
 * nama saja. Dua orang berbeda memang boleh sama-sama bernama "Budi Santoso";
 * yang tidak boleh tercatat dua kali adalah pasangan nama + nomor telepon
 * yang sama persis. Batasan unik pada kolom name membuat aturan tersebut
 * mustahil diterapkan — dan membuat data ganda warisan lama tidak bisa
 * diperbaiki sama sekali.
 *
 * Migrasi ini diperlukan untuk basis data yang sudah menjalankan
 * 2026_09_28_000001_drop_catalog_and_unused_columns, yang sempat menambahkan
 * unique('name') sebelum aturan keunikan digeser ke bentuk komposit.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('customers') || ! Schema::hasIndex('customers', ['name'])) {
            return;
        }

        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique(['name']);
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('customers') || Schema::hasIndex('customers', ['name'])) {
            return;
        }

        Schema::table('customers', function (Blueprint $table) {
            $table->unique('name');
        });
    }
};
