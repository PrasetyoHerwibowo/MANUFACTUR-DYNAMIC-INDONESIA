<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ContactMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Halaman "Tentang Kami".
     */
    public function about(): View
    {
        $categories = Category::query()
            ->active()
            ->ordered()
            ->withCount(['machines' => fn ($query) => $query->where('is_active', true)])
            ->get();

        return view('public.about', compact('categories'));
    }

    /**
     * Halaman "Kontak".
     */
    public function contact(): View
    {
        return view('public.contact');
    }

    /**
     * Simpan pesan dari formulir kontak.
     */
    public function sendContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'company' => ['nullable', 'string', 'max:150'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        ContactMessage::create($validated);

        return back()->with(
            'success',
            'Terima kasih! Pesan Anda sudah kami terima dan akan segera dijawab oleh tim kami.'
        );
    }
}
