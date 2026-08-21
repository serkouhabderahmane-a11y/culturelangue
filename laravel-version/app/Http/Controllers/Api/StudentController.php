<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Traits\ApiResponse;
use App\Models\AttendanceRecord;
use App\Models\Booking;
use App\Models\CalendarEvent;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\StudentSkillProgress;
use App\Models\SupportTicket;
use App\Models\TestResult;
use App\Models\TimeSlot;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    use ApiResponse;

    public function dashboard()
    {
        $user = auth()->user();
        $profile = $user->studentProfile;

        $enrollments = Enrollment::where('student_id', $user->id)
            ->with(['service'])
            ->latest()
            ->get();

        $activeEnrollments = $enrollments->where('status', 'active');

        $upcomingSlots = TimeSlot::where('booked_by', $user->id)
            ->where('is_available', false)
            ->where('slot_date', '>=', now()->toDateString())
            ->with('service')
            ->orderBy('slot_date')
            ->orderBy('slot_time')
            ->take(10)
            ->get();

        $skillProgress = StudentSkillProgress::where('student_id', $user->id)
            ->get()
            ->keyBy('skill');

        $skills = ['listening', 'reading', 'speaking', 'writing'];
        $progressBySkill = collect($skills)->map(function (string $skill) use ($skillProgress) {
            return [
                'skill' => $skill,
                'percentage' => round((float) ($skillProgress[$skill]->percentage ?? 0), 1),
            ];
        })->values();

        $started = $progressBySkill->pluck('percentage')->filter(fn ($value) => $value > 0);
        $globalProgress = $started->count() > 0
            ? round($started->avg(), 1)
            : round((float) ($activeEnrollments->avg('progress') ?? 0), 1);

        $nextSession = $upcomingSlots->first();

        $notifications = Notification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $bookings = Booking::where('user_id', $user->id)
            ->with(['service'])
            ->latest()
            ->take(10)
            ->get();

        $stats = [
            'active_programs' => $activeEnrollments->count(),
            'upcoming_sessions' => $upcomingSlots->count(),
            'global_progress' => $globalProgress,
            'current_level' => $profile?->current_level,
            'target_level' => $profile?->target_level,
            'enrollments_count' => $enrollments->count(),
            'upcoming_slots' => $upcomingSlots->count(),
            'unread_notifications' => Notification::where('user_id', $user->id)->where('is_read', false)->count(),
            'pending_bookings' => $bookings->where('status', 'pending')->count(),
            'pending_payments' => (float) Payment::where('user_id', $user->id)->where('status', 'pending')->sum('amount'),
            'average_grade' => round(Grade::where('student_id', $user->id)->avg('score') ?? 0, 1),
        ];

        return $this->success([
            'user' => [
                'full_name' => $user->full_name,
                'initials' => $user->initials,
                'current_level' => $profile?->current_level,
                'target_level' => $profile?->target_level,
            ],
            'stats' => $stats,
            'enrollments' => $enrollments,
            'upcoming_slots' => $upcomingSlots,
            'next_session' => $nextSession,
            'progress_by_skill' => $progressBySkill,
            'notifications' => $notifications,
            'bookings' => $bookings,
        ]);
    }

    public function enrollments()
    {
        $enrollments = Enrollment::where('student_id', auth()->id())
            ->with(['service'])
            ->latest()
            ->get();

        return $this->success($enrollments);
    }

    public function schedule()
    {
        $slots = TimeSlot::where('booked_by', auth()->id())
            ->where('is_available', false)
            ->with('service')
            ->orderBy('slot_date')
            ->orderBy('slot_time')
            ->paginate();

        return $this->paginated($slots);
    }

    public function attendance()
    {
        $records = AttendanceRecord::where('student_id', auth()->id())
            ->with(['lesson.service'])
            ->latest()
            ->get();

        $stats = [
            'present' => $records->where('status', 'present')->count(),
            'late' => $records->where('status', 'late')->count(),
            'absent' => $records->where('status', 'absent')->count(),
        ];

        return $this->success(['records' => $records, 'stats' => $stats]);
    }

    public function tests()
    {
        $results = TestResult::where('student_id', auth()->id())
            ->with(['test.service'])
            ->latest()
            ->get();

        return $this->success($results);
    }

    public function grades()
    {
        $grades = Grade::where('student_id', auth()->id())
            ->with(['test', 'lesson.service'])
            ->latest()
            ->get();

        return $this->success($grades);
    }

    public function payments()
    {
        $payments = Payment::where('user_id', auth()->id())
            ->with(['booking', 'service'])
            ->latest()
            ->paginate();

        return $this->paginated($payments);
    }

    public function bookings()
    {
        $user = auth()->user();
        $bookings = Booking::where(fn ($q) => $q->where('user_id', $user->id)->orWhere('email', $user->email))
            ->with(['service', 'program'])
            ->latest()
            ->get();

        return $this->success($bookings);
    }

    public function calendar()
    {
        $events = CalendarEvent::where(fn ($q) => $q->whereNull('teacher_id')->orWhere('teacher_id', auth()->id()))
            ->orderBy('event_date')
            ->get();

        return $this->success($events);
    }

    public function notifications(Request $request)
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 15));

        return $this->paginated($notifications);
    }

    public function markNotificationRead(Request $request, int $id)
    {
        $notification = Notification::where('user_id', auth()->id())->findOrFail($id);
        $notification->update(['is_read' => true, 'read_at' => now()]);

        return $this->success($notification);
    }

    public function markAllNotificationsRead()
    {
        Notification::where('user_id', auth()->id())->where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return $this->success(null, 'All notifications marked as read');
    }

    public function tickets()
    {
        $tickets = SupportTicket::where('student_id', auth()->id())
            ->with('messages')
            ->latest()
            ->get();

        return $this->success($tickets);
    }

    public function storeTicket(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'message' => 'required|string|max:5000',
        ]);

        $ticket = SupportTicket::create([
            'student_id' => auth()->id(),
            'subject' => $validated['subject'],
            'category' => $validated['category'] ?? 'general',
            'status' => 'open',
        ]);

        $ticket->messages()->create([
            'sender_id' => auth()->id(),
            'body' => $validated['message'],
        ]);

        return $this->success($ticket->load('messages'), 'Ticket created successfully.', 201);
    }

    public function replyToTicket(Request $request, int $id)
    {
        $ticket = SupportTicket::where('student_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $message = $ticket->messages()->create([
            'sender_id' => auth()->id(),
            'body' => $validated['message'],
        ]);

        $ticket->update(['status' => 'open']);

        return $this->success($message, 'Reply sent.', 201);
    }
}
