<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('role');
            }
            if (!Schema::hasColumn('users', 'activation_code')) {
                $table->string('activation_code', 20)->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('users', 'activation_code_expires_at')) {
                $table->timestamp('activation_code_expires_at')->nullable()->after('activation_code');
            }
            if (!Schema::hasColumn('users', 'auth_provider')) {
                $table->string('auth_provider', 50)->default('email')->after('activation_code_expires_at');
            }
            if (!Schema::hasColumn('users', 'google_id')) {
                $table->string('google_id', 100)->nullable()->after('auth_provider');
            }
        });

        // Ensure all existing users are active
        DB::table('users')->update(['is_active' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach (['is_active', 'activation_code', 'activation_code_expires_at', 'auth_provider', 'google_id'] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
