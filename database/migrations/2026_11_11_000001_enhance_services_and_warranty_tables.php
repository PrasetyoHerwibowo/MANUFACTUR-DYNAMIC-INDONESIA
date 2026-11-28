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
        // 1. Tambah serial number dan masa garansi pada tabel orders
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'serial_number')) {
                $table->string('serial_number', 100)->nullable()->after('order_code');
            }
            if (!Schema::hasColumn('orders', 'warranty_period_months')) {
                $table->integer('warranty_period_months')->default(24)->after('serial_number');
            }
            if (!Schema::hasColumn('orders', 'warranty_expiry_date')) {
                $table->timestamp('warranty_expiry_date')->nullable()->after('warranty_period_months');
            }
        });

        // 2. Tambah relasi order_id dan catatan admin pada tabel service_bookings
        Schema::table('service_bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('service_bookings', 'order_id')) {
                $table->foreignId('order_id')->nullable()->after('customer_id')->constrained('orders')->nullOnDelete();
            }
            if (!Schema::hasColumn('service_bookings', 'admin_notes')) {
                $table->text('admin_notes')->nullable()->after('technician_phone');
            }
        });

        // 3. Tambah fitur verifikasi admin, model mesin, jenis file, dan penjadwalan kunjungan perbaikan pada warranty_claims
        Schema::table('warranty_claims', function (Blueprint $table) {
            if (!Schema::hasColumn('warranty_claims', 'order_id')) {
                $table->foreignId('order_id')->nullable()->after('customer_id')->constrained('orders')->nullOnDelete();
            }
            if (!Schema::hasColumn('warranty_claims', 'machine_model')) {
                $table->string('machine_model', 150)->nullable()->after('serial_number');
            }
            if (!Schema::hasColumn('warranty_claims', 'file_type')) {
                $table->string('file_type', 50)->nullable()->after('photo_path');
            }
            if (!Schema::hasColumn('warranty_claims', 'verification_status')) {
                $table->string('verification_status', 50)->default('menunggu_verifikasi')->after('status');
            }
            if (!Schema::hasColumn('warranty_claims', 'warranty_status')) {
                $table->string('warranty_status', 50)->default('dalam_garansi')->after('verification_status');
            }
            if (!Schema::hasColumn('warranty_claims', 'scheduled_visit_date')) {
                $table->date('scheduled_visit_date')->nullable()->after('warranty_status');
            }
            if (!Schema::hasColumn('warranty_claims', 'scheduled_visit_time')) {
                $table->string('scheduled_visit_time', 50)->nullable()->after('scheduled_visit_date');
            }
            if (!Schema::hasColumn('warranty_claims', 'visit_address')) {
                $table->text('visit_address')->nullable()->after('scheduled_visit_time');
            }
            if (!Schema::hasColumn('warranty_claims', 'technician_name')) {
                $table->string('technician_name', 150)->nullable()->after('visit_address');
            }
            if (!Schema::hasColumn('warranty_claims', 'technician_phone')) {
                $table->string('technician_phone', 50)->nullable()->after('technician_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warranty_claims', function (Blueprint $table) {
            $table->dropColumn([
                'order_id',
                'machine_model',
                'file_type',
                'verification_status',
                'warranty_status',
                'scheduled_visit_date',
                'scheduled_visit_time',
                'visit_address',
                'technician_name',
                'technician_phone',
            ]);
        });

        Schema::table('service_bookings', function (Blueprint $table) {
            $table->dropColumn(['order_id', 'admin_notes']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['serial_number', 'warranty_period_months', 'warranty_expiry_date']);
        });
    }
};
