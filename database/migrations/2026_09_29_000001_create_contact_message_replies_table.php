<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Riwayat balasan admin terhadap pesan dari formulir kontak.
     *
     * Balasan ditulis di panel admin lalu dikirim lewat Gmail SMTP. Kolom
     * "status" membedakan draf yang belum dikirim dari balasan yang sudah
     * terkirim, sehingga admin bisa melanjutkan draf yang tertunda.
     */
    public function up(): void
    {
        Schema::create('contact_message_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_message_id')->constrained('contact_messages')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('subject', 200);
            $table->text('body');
            $table->string('status', 20)->default('draft');
            $table->timestamp('sent_at')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();

            $table->index(['contact_message_id', 'created_at']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_message_replies');
    }
};
