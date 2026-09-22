<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pembayaran tiap pesanan.
     *
     * Satu pesanan dapat memiliki beberapa pembayaran (percobaan ulang).
     * Kolom "expires_at" adalah batas pembayaran: pembayaran yang masih
     * "menunggu_pembayaran" dan sudah melewati batas ini otomatis menjadi
     * "gagal" dan pelanggan harus mengulang pembayaran dari awal.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('payment_code', 40)->unique();
            $table->unsignedTinyInteger('attempt')->default(1);
            $table->string('method', 30);
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('status', 30)->default('menunggu_pembayaran');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->string('failure_reason')->nullable();
            $table->string('proof')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
