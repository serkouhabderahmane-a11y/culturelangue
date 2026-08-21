<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\TeachingSession;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function dashboard()
    {
        $teacher = auth()->user()->load('teacherProfile');
        $upcomingSessions = TeachingSession::where('teacher_id', auth()->id())
            ->with('student')
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->take(10)
            ->get();

        $totalStudents = TeachingSession::where('teacher_id', auth()->id())
            ->distinct('student_id')
            ->count('student_id');

        return view('teacher.dashboard', compact('teacher', 'upcomingSessions', 'totalStudents'));
    }

    public function students()
    {
        $studentIds = TeachingSession::where('teacher_id', auth()->id())
            ->distinct('student_id')
            ->pluck('student_id');

        $students = \App\Models\User::whereIn('id', $studentIds)->get();

        return view('teacher.students', compact('students'));
    }

    public function schedule()
    {
        $sessions = TeachingSession::where('teacher_id', auth()->id())
            ->with('student')
            ->orderBy('start_time')
            ->get();

        return view('teacher.schedule', compact('sessions'));
    }

    public function sessionDetails($id)
    {
        $session = TeachingSession::with(['student', 'booking'])
            ->where('teacher_id', auth()->id())
            ->findOrFail($id);

        return view('teacher.session-details', compact('session'));
    }

    public function profile()
    {
        $teacher = auth()->user()->load('teacherProfile');
        return view('teacher.profile', compact('teacher'));
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'bio_fr' => 'nullable|string',
            'bio_en' => 'nullable|string',
            'hourly_rate' => 'nullable|numeric|min:0',
        ]);

        $user = auth()->user();
        $user->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone' => $validated['phone'] ?? null,
        ]);

        if ($user->teacherProfile) {
            $user->teacherProfile->update($validated);
        }

        return redirect()->route('teacher.profile')
            ->with('success', 'Profil mis à jour avec succès.');
    }
}
