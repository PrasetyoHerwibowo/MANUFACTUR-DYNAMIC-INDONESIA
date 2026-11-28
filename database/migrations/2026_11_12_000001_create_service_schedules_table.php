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
        if (!Schema::hasTable('service_schedules')) {
            Schema::create('service_schedules', function (Blueprint $table) {
                $table->id();
                $table->date('date')->unique();
                $table->boolean('is_available')->default(true);
                $table->unsignedSmallInteger('technician_standby_count')->default(2);
                $table->unsignedSmallInteger('max_quota')->default(3);
                $table->string('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_schedules');
    }
};

