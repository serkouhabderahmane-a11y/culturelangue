<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ContactInfo;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $contactInfos = ContactInfo::where('is_active', true)->orderBy('order')->get();
        return view('contact', compact('contactInfos'));
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'consent' => 'nullable',
        ]);

        ContactMessage::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'consent' => isset($validated['consent']),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('contact')
            ->with('success', 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.');
    }
}
