<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Traits\ApiResponse;
use App\Models\AttendanceRecord;
use App\Models\CalendarEvent;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Lesson;
use App\Models\Notification;
use App\Models\TeacherAvailability;
use App\Models\TeacherSpecialty;
use App\Models\TeachingSession;
use App\Models\Test;
use App\Models\User;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    use ApiResponse;

    public function dashboard()
    {
        $teacher = auth()->user();

        $todayLessons = Lesson::where('teacher_id', $teacher->id)
            ->where('date', now()->toDateString())
            ->orderBy('start_time')
            ->get();

        $upcomingLessons = Lesson::where('teacher_id', $teacher->id)
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('start_time')
            ->take(10)
            ->get();

        $activeStudents = Enrollment::whereIn('service_id', Lesson::where('teacher_id', $teacher->id)->pluck('service_id'))
            ->where('status', 'active')
            ->distinct('student_id')
            ->count('student_id');

        $stats = [
            'lessons_today' => $todayLessons->count(),
            'upcoming_lessons' => $upcomingLessons->count(),
            'active_students' => $activeStudents,
            'unread_notifications' => Notification::where('user_id', $teacher->id)->where('is_read', false)->count(),
            'attendance_to_mark' => AttendanceRecord::whereIn('lesson_id', Lesson::where('teacher_id', $teacher->id)->pluck('id'))
                ->whereNull('marked_at')
                ->count(),
        ];

        return $this->success([
            'stats' => $stats,
            'today_lessons' => $todayLessons,
            'upcoming_lessons' => $upcomingLessons,
        ]);
    }

    public function lessons(Request $request)
    {
        $query = Lesson::where('teacher_id', auth()->id())
            ->with(['service']);

        if ($request->has('from')) {
            $query->where('date', '>=', $request->date('from'));
        }

        $lessons = $query->orderBy('date')->orderBy('start_time')->paginate($request->integer('per_page', 20));

        return $this->paginated($lessons);
    }

    public function lesson(Request $request, int $id)
    {
        $lesson = Lesson::where('teacher_id', auth()->id())
            ->with(['service', 'attendanceRecords.student'])
            ->findOrFail($id);

        return $this->success($lesson);
    }

    public function students(Request $request)
    {
        $serviceIds = Lesson::where('teacher_id', auth()->id())->pluck('service_id');

        $students = User::role('student')
            ->whereIn('id', Enrollment::whereIn('service_id', $serviceIds)->pluck('student_id'))
            ->with(['studentProfile', 'enrollments' => fn ($q) => $q->whereIn('service_id', $serviceIds)->with('service')])
            ->get();

        return $this->success($students);
    }

    public function markAttendance(Request $request, int $lessonId)
    {
        $lesson = Lesson::where('teacher_id', auth()->id())->findOrFail($lessonId);

        $validated = $request->validate([
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|integer|exists:users,id',
            'attendance.*.status' => 'required|string|in:present,late,absent,excused',
            'attendance.*.notes' => 'nullable|string|max:1000',
        ]);

        foreach ($validated['attendance'] as $record) {
            AttendanceRecord::updateOrCreate(
                [
                    'lesson_id' => $lesson->id,
                    'student_id' => $record['student_id'],
                ],
                [
                    'status' => $record['status'],
                    'notes' => $record['notes'] ?? null,
                    'marked_by' => auth()->id(),
                    'marked_at' => now(),
                ],
            );
        }

        return $this->success(null, 'Attendance marked successfully.');
    }

    public function grades(Request $request)
    {
        $lessonIds = Lesson::where('teacher_id', auth()->id())->pluck('id');

        $grades = Grade::whereIn('lesson_id', $lessonIds)
            ->with(['student', 'lesson.service'])
            ->latest()
            ->paginate($request->integer('per_page', 20));

        return $this->paginated($grades);
    }

    public function enterGrade(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|integer|exists:users,id',
            'lesson_id' => 'required|integer|exists:lessons,id',
            'score' => 'required|numeric|min:0|max:100',
            'letter_grade' => 'nullable|string|max:10',
            'notes' => 'nullable|string|max:2000',
        ]);

        $lesson = Lesson::where('teacher_id', auth()->id())->findOrFail($validated['lesson_id']);

        $grade = Grade::updateOrCreate(
            [
                'student_id' => $validated['student_id'],
                'lesson_id' => $lesson->id,
            ],
            [
                'score' => $validated['score'],
                'letter_grade' => $validated['letter_grade'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'teacher_id' => auth()->id(),
            ],
        );

        return $this->success($grade, 'Grade recorded successfully.', 201);
    }

    public function tests(Request $request)
    {
        $serviceIds = Lesson::where('teacher_id', auth()->id())->pluck('service_id');

        $tests = Test::whereIn('service_id', $serviceIds)
            ->with(['service'])
            ->paginate($request->integer('per_page', 20));

        return $this->paginated($tests);
    }

    public function createTest(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|integer|exists:services,id',
            'name' => 'required|string|max:255',
            'test_type' => 'nullable|string|max:50',
            'total_score' => 'nullable|integer|min:0',
            'duration' => 'nullable|integer|min:1',
        ]);

        $serviceIds = Lesson::where('teacher_id', auth()->id())->pluck('service_id');
        abort_unless(in_array((int) $validated['service_id'], $serviceIds->map(fn ($id) => (int) $id)->all()), 403, 'You can only create tests for your own courses.');

        $test = Test::create($validated + ['created_by' => auth()->id()]);

        return $this->success($test, 'Test created successfully.', 201);
    }

    public function sessions(Request $request)
    {
        $sessions = TeachingSession::where('teacher_id', auth()->id())
            ->with(['booking', 'students'])
            ->orderByDesc('start_time')
            ->paginate($request->integer('per_page', 20));

        return $this->paginated($sessions);
    }

    public function calendar()
    {
        $events = CalendarEvent::where('teacher_id', auth()->id())
            ->orderBy('event_date')
            ->get();

        return $this->success($events);
    }

    public function availability()
    {
        $availability = TeacherAvailability::where('teacher_id', auth()->id())
            ->orderBy('day_of_week')
            ->get();

        return $this->success($availability);
    }

    public function updateAvailability(Request $request)
    {
        $validated = $request->validate([
            'slots' => 'required|array',
            'slots.*.day_of_week' => 'required|integer|min:0|max:6',
            'slots.*.start_time' => 'required|string',
            'slots.*.end_time' => 'required|string|after:slots.*.start_time',
        ]);

        TeacherAvailability::where('teacher_id', auth()->id())->delete();

        foreach ($validated['slots'] as $slot) {
            TeacherAvailability::create($slot + ['teacher_id' => auth()->id()]);
        }

        return $this->success(null, 'Availability updated successfully.');
    }

    public function specialties()
    {
        return $this->success(TeacherSpecialty::where('teacher_id', auth()->id())->get());
    }
}
