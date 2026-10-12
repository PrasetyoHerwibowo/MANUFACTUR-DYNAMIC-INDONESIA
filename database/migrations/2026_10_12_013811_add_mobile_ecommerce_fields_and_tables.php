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
        // 1. Tambah field e-commerce manufaktur pada tabel orders
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'production_status')) {
                $table->string('production_status', 50)->default('verifikasi_dp')->after('status');
            }
            if (!Schema::hasColumn('orders', 'dp_amount')) {
                $table->decimal('dp_amount', 15, 2)->default(0)->after('total_amount');
            }
            if (!Schema::hasColumn('orders', 'pelunasan_amount')) {
                $table->decimal('pelunasan_amount', 15, 2)->default(0)->after('dp_amount');
            }
            if (!Schema::hasColumn('orders', 'dp_proof')) {
                $table->string('dp_proof')->nullable()->after('pelunasan_amount');
            }
            if (!Schema::hasColumn('orders', 'pelunasan_proof')) {
                $table->string('pelunasan_proof')->nullable()->after('dp_proof');
            }
            if (!Schema::hasColumn('orders', 'delivery_address')) {
                $table->text('delivery_address')->nullable()->after('pelunasan_proof');
            }
            if (!Schema::hasColumn('orders', 'custom_notes')) {
                $table->text('custom_notes')->nullable()->after('delivery_address');
            }
        });

        // 2. Buat tabel service_bookings (Booking Maintenance / Jadwal Teknisi)
        if (!Schema::hasTable('service_bookings')) {
            Schema::create('service_bookings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
                $table->string('booking_code', 50)->unique();
                $table->string('company_name')->nullable();
                $table->string('serial_number', 100);
                $table->string('machine_model', 150);
                $table->string('service_type', 50)->default('maintenance_rutin'); // maintenance_rutin, kalibrasi, troubleshooting, emergency
                $table->date('scheduled_date');
                $table->string('scheduled_time', 50)->default('10:00 WIB');
                $table->text('address');
                $table->text('issue_description')->nullable();
                $table->string('status', 50)->default('dijadwalkan'); // dijadwalkan, teknisi_otw, dalam_pengerjaan, selesai
                $table->string('technician_name')->nullable();
                $table->string('technician_phone')->nullable();
                $table->timestamps();
            });
        }

        // 3. Buat tabel warranty_claims (Klaim Garansi & Upload Foto Kerusakan)
        if (!Schema::hasTable('warranty_claims')) {
            Schema::create('warranty_claims', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
                $table->string('claim_code', 50)->unique();
                $table->string('serial_number', 100);
                $table->string('purchase_date', 50)->nullable();
                $table->text('damage_description');
                $table->string('photo_path')->nullable();
                $table->string('status', 50)->default('dalam_peninjauan'); // dalam_peninjauan, disetujui, ditolak, sparepart_dikirim, selesai
                $table->text('response_notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warranty_claims');
        Schema::dropIfExists('service_bookings');
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'production_status',
                'dp_amount',
                'pelunasan_amount',
                'dp_proof',
                'pelunasan_proof',
                'delivery_address',
                'custom_notes',
            ]);
        });
    }
};
