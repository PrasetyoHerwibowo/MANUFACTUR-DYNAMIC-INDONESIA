<?php

use App\Mail\ContactMessageReplyMail;
use App\Models\ContactMessage;
use App\Models\ContactMessageReply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ReplyFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    private function message(): ContactMessage
    {
        return ContactMessage::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'phone' => '081234567890',
            'company' => 'PT Kopi Nusantara',
            'subject' => 'Permintaan mesin roasting',
            'message' => "Halo, kami butuh mesin roasting.\nMohon informasi harga.\nTerima kasih.",
        ]);
    }

    public function test_halaman_detail_menyediakan_editor_balasan(): void
    {
        $response = $this->actingAs($this->admin())
            ->get(route('admin.messages.show', $this->message()));

        $response->assertOk();
        $response->assertSee('Tulis Balasan', false);
        $response->assertSee('Re: Permintaan mesin roasting');
        $response->assertSee('Budi Santoso');
    }

    public function test_draf_tersimpan_tanpa_mengirim_email(): void
    {
        Mail::fake();

        $message = $this->message();

        $this->actingAs($this->admin())
            ->post(route('admin.messages.replies.store', $message), [
                'subject' => 'Re: Permintaan mesin roasting',
                'body' => 'Halo Pak Budi, terima kasih sudah menghubungi kami.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('contact_message_replies', [
            'contact_message_id' => $message->id,
            'status' => ContactMessageReply::STATUS_DRAFT,
        ]);

        Mail::assertNothingSent();
    }

    public function test_balasan_terkirim_lewat_mail(): void
    {
        Mail::fake();

        $message = $this->message();

        $this->actingAs($this->admin())
            ->post(route('admin.messages.replies.send', $message), [
                'subject' => 'Re: Permintaan mesin roasting',
                'body' => 'Halo Pak Budi, kami sedang menyiapkan penawaran.',
            ])
            ->assertRedirect();

        Mail::assertSent(ContactMessageReplyMail::class, function ($mail) {
            return $mail->hasTo('budi@example.com')
                && $mail->reply->isSent();
        });

        $this->assertDatabaseHas('contact_message_replies', [
            'contact_message_id' => $message->id,
            'status' => ContactMessageReply::STATUS_SENT,
        ]);
    }

    public function test_balasan_gagal_tetap_tersimpan(): void
    {
        Mail::shouldReceive('to->send')->andThrow(new RuntimeException('SMTP tidak terhubung'));

        $message = $this->message();

        $this->actingAs($this->admin())
            ->post(route('admin.messages.replies.send', $message), [
                'subject' => 'Re: Uji gagal',
                'body' => 'Isi balasan uji.',
            ]);

        $this->assertDatabaseHas('contact_message_replies', [
            'contact_message_id' => $message->id,
            'status' => ContactMessageReply::STATUS_FAILED,
        ]);
    }

    public function test_draf_terakhir_dipakai_sebagai_isi_editor(): void
    {
        $message = $this->message();
        $message->replies()->create([
            'subject' => 'Subjek draf',
            'body' => 'Isi draf yang belum terkirim.',
            'status' => ContactMessageReply::STATUS_DRAFT,
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.messages.show', $message))
            ->assertSee('Isi draf yang belum terkirim.')
            ->assertDontSee('Re: Permintaan mesin roasting');
    }

    public function test_balasan_wajib_diisi(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.messages.replies.store', $this->message()), [
                'subject' => 'Tanpa isi',
                'body' => '',
            ])
            ->assertSessionHasErrors('body');
    }
}
