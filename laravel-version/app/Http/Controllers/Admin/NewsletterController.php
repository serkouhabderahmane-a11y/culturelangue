<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function index()
    {
        $subscribers = Newsletter::where('is_subscribed', true)->latest()->paginate(20);
        return view('admin.newsletter.index', compact('subscribers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:newsletters',
        ]);

        Newsletter::create($validated + ['is_subscribed' => true]);

        return redirect()->back()->with('success', 'Inscription à la newsletter réussie.');
    }

    public function destroy(Newsletter $newsletter)
    {
        $newsletter->update(['is_subscribed' => false]);
        return redirect()->route('admin.newsletter.index')
            ->with('success', 'Désabonnement réussi.');
    }
}
