<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $categories = ServiceCategory::where('is_active', true)
            ->with(['activeServices'])
            ->orderBy('order')
            ->get();

        return view('booking', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'service_id' => 'nullable|exists:services,id',
            'program_id' => 'nullable|exists:programs,id',
            'preferred_date' => 'nullable|date',
            'preferred_time' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:2000',
        ]);

        $booking = Booking::create($validated + ['status' => 'pending']);

        return redirect()->route('booking')
            ->with('success', 'Votre réservation a été envoyée avec succès. Nous vous contacterons sous peu.');
    }
}
