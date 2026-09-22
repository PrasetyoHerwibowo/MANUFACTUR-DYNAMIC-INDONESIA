<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pesanan mesin dari pelanggan.
     *
     * Kolom "payment_deadline" adalah batas pembayaran yang dipakai pesanan.
     * Bila pelanggan melewatinya, pembayaran menjadi gagal dan pelanggan
     * harus mengulang pembayaran dari awal (kode pembayaran baru).
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->string('order_code', 40)->unique();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('status', 30)->default('menunggu_pembayaran');
            $table->timestamp('payment_deadline')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'payment_deadline']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
