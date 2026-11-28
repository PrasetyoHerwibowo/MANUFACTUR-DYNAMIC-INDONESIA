<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('categories', 'series')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->string('series', 20)->nullable()->default('both')->after('description');
            });
        }

        if (! Schema::hasColumn('machines', 'series')) {
            Schema::table('machines', function (Blueprint $table) {
                $table->string('series', 20)->nullable()->after('category_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('categories', 'series')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('series');
            });
        }

        if (Schema::hasColumn('machines', 'series')) {
            Schema::table('machines', function (Blueprint $table) {
                $table->dropColumn('series');
            });
        }
    }
};
