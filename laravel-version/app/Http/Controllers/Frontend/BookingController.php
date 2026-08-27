<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $serviceMap = Service::where('is_active', true)
            ->pluck('id', 'slug')
            ->toArray();

        return view('booking', compact('serviceMap'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'contact_method' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:2000',
            'full_name' => 'nullable|string|max:255',
            'course' => 'nullable|string|max:255',
            'service_id' => 'nullable|exists:services,id',
            'program_id' => 'nullable|exists:programs,id',
            'preferred_date' => 'nullable|date',
            'preferred_slot' => 'nullable|string|max:50',
            'placement_score' => 'nullable|numeric',
            'placement_level' => 'nullable|string|max:10',
            'oral_test_date' => 'nullable|date',
            'oral_test_slot' => 'nullable|string|max:50',
            'oral_test_status' => 'nullable|string|max:50',
        ]);

        // Resolve the service from a course/slug if service_id wasn't posted directly.
        $serviceId = $validated['service_id'] ?? null;
        if (!$serviceId && !empty($validated['course'])) {
            $service = Service::where('slug', $validated['course'])
                ->where('is_active', true)
                ->first();
            if ($service) {
                $serviceId = $service->id;
            }
        }

        // If only a single full name was captured, derive last_name from it.
        $firstName = $validated['first_name'];
        $lastName = $validated['last_name'] ?? null;
        if (!$lastName && !empty($validated['full_name'])) {
            $parts = preg_split('/\s+/', trim($validated['full_name']));
            if (count($parts) > 1) {
                $lastName = array_pop($parts);
                $firstName = implode(' ', $parts);
            } else {
                $lastName = trim($validated['full_name']);
            }
        }
        $lastName = $lastName ?: $firstName;

        $booking = Booking::create([
            'booking_ref' => 'BK-' . strtoupper(substr(uniqid(), -6)),
            'service_id' => $serviceId,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'contact_method' => $validated['contact_method'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
            'preferred_date' => $validated['preferred_date'] ?? null,
            'preferred_slot' => $validated['preferred_slot'] ?? null,
            'placement_score' => $validated['placement_score'] ?? null,
            'placement_level' => $validated['placement_level'] ?? null,
            'oral_test_date' => $validated['oral_test_date'] ?? null,
            'oral_test_slot' => $validated['oral_test_slot'] ?? null,
            'oral_test_status' => $validated['oral_test_status'] ?? null,
            'source' => 'website',
            'ip_address' => $request->ip(),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'booking_ref' => $booking->booking_ref], 201);
        }

        return redirect()->route('booking')
            ->with('success', 'Votre réservation a été envoyée avec succès. Nous vous contacterons sous peu.');
    }
}
