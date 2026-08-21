<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $user->load(['bookings.service', 'payments']);

        return view('student.dashboard', compact('user'));
    }

    public function programs()
    {
        $user = auth()->user();
        $enrollments = $user->bookings()->with('service')->get();
        return view('student.programs', compact('enrollments'));
    }

    public function payments()
    {
        $user = auth()->user();
        $payments = $user->payments()->with('booking')->latest()->get();
        return view('student.payments', compact('payments'));
    }

    public function profile()
    {
        $user = auth()->user()->load('studentProfile');
        return view('student.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
        ]);

        $user = auth()->user();
        $user->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone' => $validated['phone'] ?? null,
        ]);

        if ($user->studentProfile) {
            $user->studentProfile->update($validated);
        }

        return redirect()->route('student.profile')
            ->with('success', 'Profil mis à jour avec succès.');
    }

    public function calendar()
    {
        $sessions = auth()->user()->studentSessions()
            ->with('teacher')
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->get();

        return view('student.calendar', compact('sessions'));
    }

    public function levelTests()
    {
        return view('student.level-tests');
    }

    public function support()
    {
        return view('student.support');
    }
}
