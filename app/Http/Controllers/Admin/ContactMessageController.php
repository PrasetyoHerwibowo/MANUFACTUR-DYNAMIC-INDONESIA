<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ContactMessageReplyMail;
use App\Models\ContactMessage;
use App\Models\ContactMessageReply;
use App\Support\ContactReply;
use App\Support\Sanitize;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ContactMessageController extends Controller
{
    /** Aturan validasi isi balasan. */
    private const RULES = [
        'subject' => ['required', 'string', 'max:'.ContactReply::SUBJECT_MAX],
        'body' => ['required', 'string', 'max:'.ContactReply::BODY_MAX],
    ];

    /** Pesan kesalahan validasi. */
    private const MESSAGES = [
        'subject.required' => 'Subjek balasan wajib diisi.',
        'subject.max' => 'Subjek balasan maksimal '.ContactReply::SUBJECT_MAX.' karakter.',
        'body.required' => 'Isi balasan wajib diisi.',
        'body.max' => 'Isi balasan maksimal '.ContactReply::BODY_MAX.' karakter.',
    ];

    /**
     * Daftar pesan dari formulir kontak.
     */
    public function index(): View
    {
        $messages = ContactMessage::query()->latest()->paginate(15);

        return view('admin.messages.index', compact('messages'));
    }

    /**
     * Detail pesan (sekaligus menandai sudah dibaca) beserta editor balasan.
     */
    public function show(ContactMessage $message): View
    {
        if (! $message->is_read) {
            $message->update(['is_read' => true]);
        }

        $message->load('replies.user');

        // Draf paling baru dipakai sebagai isi awal editor, agar admin dapat
        // melanjutkan tulisan yang belum terkirim.
        $draft = $message->replies
            ->sortByDesc('id')
            ->first(fn (ContactMessageReply $reply) => ! $reply->isSent());

        return view('admin.messages.show', [
            'message' => $message,
            'replies' => $message->replies->sortByDesc('id')->values(),
            'replySubject' => $draft->subject ?? ContactReply::subject((string) $message->subject),
            'replyBody' => $draft->body ?? ContactReply::body($message),
            'bodyMax' => ContactReply::BODY_MAX,
            'statusLabels' => ContactMessageReply::statusLabels(),
        ]);
    }

    /**
     * Simpan balasan sebagai draf tanpa mengirimnya.
     */
    public function storeReply(Request $request, ContactMessage $message): RedirectResponse
    {
        $validated = $this->validated($request);

        $message->replies()->create([
            'user_id' => Auth::id(),
            'subject' => $validated['subject'],
            'body' => $validated['body'],
            'status' => ContactMessageReply::STATUS_DRAFT,
        ]);

        return back()->with('success', 'Balasan tersimpan sebagai draf. Belum dikirim.');
    }

    /**
     * Kirim balasan lewat Gmail SMTP dan catat di riwayat.
     *
     * Balasan yang gagal kirim tetap disimpan dengan status "failed" beserta
     * pesan errornya agar admin bisa memperbaikinya tanpa menulis ulang.
     */
    public function sendReply(Request $request, ContactMessage $message): RedirectResponse
    {
        $validated = $this->validated($request);

        $reply = $message->replies()->create([
            'user_id' => Auth::id(),
            'subject' => $validated['subject'],
            'body' => $validated['body'],
            'status' => ContactMessageReply::STATUS_DRAFT,
        ]);

        try {
            // Sengaja dikirim sinkron: admin perlu tahu sekarang apakah email
            // benar-benar keluar sebelum meninggalkan halaman.
            Mail::to($message->email)->send(new ContactMessageReplyMail($reply));

            $reply->forceFill([
                'status' => ContactMessageReply::STATUS_SENT,
                'sent_at' => now(),
                'error' => null,
            ])->save();

            return back()->with('success', 'Balasan berhasil dikirim ke '.$message->email.'.');
        } catch (Throwable $e) {
            report($e);

            $reply->forceFill([
                'status' => ContactMessageReply::STATUS_FAILED,
                'error' => $e->getMessage(),
            ])->save();

            return back()->with(
                'error',
                'Balasan gagal dikirim: '.$e->getMessage().' Isi balasan tetap tersimpan, '
                .'silakan periksa konfigurasi surel lalu coba lagi.'
            );
        }
    }

    /**
     * Hapus satu balasan dari riwayat.
     */
    public function destroyReply(ContactMessage $message, ContactMessageReply $reply): RedirectResponse
    {
        abort_unless($reply->contact_message_id === $message->id, 404);

        $reply->delete();

        return back()->with('success', 'Balasan berhasil dihapus.');
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $message->delete();

        return redirect()
            ->route('admin.messages.index')
            ->with('success', 'Pesan berhasil dihapus.');
    }

    /**
     * Validasi input balasan.
     *
     * @return array{subject: string, body: string}
     */
    private function validated(Request $request): array
    {
        $request->merge([
            'subject' => Sanitize::text($request->input('subject')),
        ]);

        $validated = $request->validate(self::RULES, self::MESSAGES);

        return [
            'subject' => $validated['subject'],
            'body' => (string) $request->input('body'),
        ];
    }
}
