<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Traits\ApiResponse;
use App\Models\Booking;
use App\Models\ContactMessage;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Invoice;
use App\Models\Lesson;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\StudentProfile;
use App\Models\SupportTicket;
use App\Models\TeacherPayroll;
use App\Models\TeachingSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    use ApiResponse;

    public function dashboard()
    {
        $stats = [
            'students_count' => User::role('student')->count(),
            'teachers_count' => User::role('teacher')->count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'total_bookings' => Booking::count(),
            'active_enrollments' => Enrollment::where('status', 'active')->count(),
            'revenue' => Payment::where('status', 'completed')->sum('amount'),
            'pending_payments' => Payment::where('status', 'pending')->sum('amount'),
            'unread_messages' => ContactMessage::where('status', 'new')->count(),
            'open_tickets' => SupportTicket::where('status', 'open')->count(),
        ];

        $recentBookings = Booking::with(['user', 'service'])
            ->latest()
            ->take(8)
            ->get();

        $recentUsers = User::with('roles')
            ->latest()
            ->take(8)
            ->get();

        $monthlyRevenue = Payment::where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths(6))
            ->get()
            ->groupBy(fn ($p) => $p->created_at->format('Y-m'))
            ->map(fn ($group) => (float) $group->sum('amount'));

        return $this->success([
            'stats' => $stats,
            'recent_bookings' => $recentBookings,
            'recent_users' => $recentUsers,
            'monthly_revenue' => $monthlyRevenue,
        ]);
    }

    public function users(Request $request)
    {
        $query = User::with(['roles', 'studentProfile', 'teacherProfile']);

        if ($request->has('role') && in_array($request->input('role'), ['admin', 'teacher', 'student'])) {
            $query->role($request->input('role'));
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(fn ($q) => $q
                ->where('first_name', 'ilike', "%{$search}%")
                ->orWhere('last_name', 'ilike', "%{$search}%")
                ->orWhere('email', 'ilike', "%{$search}%"));
        }

        $users = $query->orderByDesc('created_at')->paginate($request->integer('per_page', 15));

        return $this->paginated($users);
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'password' => ['required', Password::defaults()],
            'role' => 'required|in:admin,teacher,student',
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        $user->assignRole($validated['role']);

        if ($validated['role'] === 'student') {
            StudentProfile::create([
                'user_id' => $user->id,
                'student_number' => 'STU-' . now()->year . '-' . str_pad((string) (1000 + $user->id), 4, '0', STR_PAD_LEFT),
                'enrollment_date' => now(),
            ]);
        }

        return $this->success($user->load('roles'), 'User created successfully.', 201);
    }

    public function updateUser(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:30',
            'password' => ['nullable', Password::defaults()],
            'is_active' => 'sometimes|boolean',
            'role' => 'sometimes|in:admin,teacher,student',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        if (isset($validated['role'])) {
            $user->syncRoles([$validated['role']]);
        }

        return $this->success($user->load(['roles', 'studentProfile', 'teacherProfile']), 'User updated successfully.');
    }

    public function deleteUser(int $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return $this->error('You cannot delete your own account.', 422);
        }

        $user->delete();

        return $this->success(null, 'User deleted successfully.');
    }

    public function bookings(Request $request)
    {
        $query = Booking::with(['user', 'service', 'program']);

        if ($request->has('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(fn ($q) => $q
                ->where('first_name', 'ilike', "%{$search}%")
                ->orWhere('last_name', 'ilike', "%{$search}%")
                ->orWhere('email', 'ilike', "%{$search}%")
                ->orWhere('booking_ref', 'ilike', "%{$search}%"));
        }

        $bookings = $query->orderByDesc('created_at')->paginate($request->integer('per_page', 15));

        return $this->paginated($bookings);
    }

    public function updateBookingStatus(Request $request, int $id)
    {
        $booking = Booking::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed',
        ]);

        $booking->update(['status' => $validated['status']]);

        if ($validated['status'] === 'confirmed') {
            $this->materializeConfirmation($booking);
        }

        return $this->success($booking->load(['service', 'program', 'user']), 'Booking status updated successfully.');
    }

    protected function materializeConfirmation(Booking $booking): void
    {
        $amount = (float) ($booking->total_amount ?? $this->priceToNumber($booking->service?->price));

        if ($booking->user_id) {
            if ($booking->service_id) {
                $alreadyEnrolled = Enrollment::where('student_id', $booking->user_id)
                    ->where('service_id', $booking->service_id)
                    ->exists();

                if (! $alreadyEnrolled) {
                    Enrollment::create([
                        'student_id' => $booking->user_id,
                        'service_id' => $booking->service_id,
                        'booking_id' => $booking->id,
                        'start_date' => $booking->preferred_date?->toDateString() ?? now()->toDateString(),
                        'status' => 'active',
                        'progress' => 0,
                        'enrolled_at' => now(),
                    ]);
                }
            }

            $start = $booking->preferred_date
                ? $booking->preferred_date
                : now()->addDay()->setTime(18, 0);

            TeachingSession::firstOrCreate(
                ['booking_id' => $booking->id],
                [
                    'teacher_id' => $this->defaultTeacherId($booking->service_id),
                    'student_id' => $booking->user_id,
                    'start_time' => $start,
                    'end_time' => $start->copy()->addHour(),
                    'status' => 'scheduled',
                ],
            );

            Notification::create([
                'user_id' => $booking->user_id,
                'title' => 'Réservation confirmée',
                'message' => 'Votre réservation ' . ($booking->booking_ref ?? $booking->id) . ' a été confirmée. Une session a été planifiée pour vous.',
                'type' => 'booking',
                'is_read' => false,
                'link' => '/student/dashboard',
            ]);

            Invoice::firstOrCreate(
                ['booking_id' => $booking->id],
                [
                    'invoice_number' => 'INV-' . now()->format('Ymd') . '-' . str_pad((string) $booking->id, 4, '0', STR_PAD_LEFT),
                    'student_id' => $booking->user_id,
                    'service_id' => $booking->service_id,
                    'total_amount' => $amount,
                    'currency' => $booking->currency ?? 'CAD',
                    'status' => 'pending',
                    'due_date' => now()->addDays(30)->toDateString(),
                ],
            );
        }
    }

    protected function defaultTeacherId(?int $serviceId): ?int
    {
        $lessonTeacher = Lesson::where('service_id', $serviceId)
            ->whereNotNull('teacher_id')
            ->value('teacher_id');

        if ($lessonTeacher) {
            return (int) $lessonTeacher;
        }

        return User::role('teacher')->value('id');
    }

    protected function priceToNumber(?string $price): float
    {
        if (! $price) {
            return 0.0;
        }

        preg_match('/\d+(?:[.,]\d+)?/', $price, $matches);

        return $matches ? (float) str_replace(',', '.', $matches[0]) : 0.0;
    }

    public function payments(Request $request)
    {
        $query = Payment::with(['booking', 'user', 'service']);

        if ($request->has('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        $payments = $query->orderByDesc('created_at')->paginate($request->integer('per_page', 15));

        return $this->paginated($payments);
    }

    public function refundPayment(Request $request, int $id)
    {
        $payment = Payment::findOrFail($id);

        if ($payment->status === 'refunded') {
            return $this->error('Payment already refunded.', 422);
        }

        $validated = $request->validate([
            'refund_reason' => 'nullable|string|max:2000',
        ]);

        $payment->update([
            'status' => 'refunded',
            'refund_reason' => $validated['refund_reason'] ?? null,
        ]);

        return $this->success($payment, 'Payment refunded successfully.');
    }

    public function invoices(Request $request)
    {
        $invoices = Invoice::with(['student', 'booking'])
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 15));

        return $this->paginated($invoices);
    }

    public function services(Request $request)
    {
        $services = Service::with(['category', 'benefits'])
            ->orderBy('order')
            ->paginate($request->integer('per_page', 20));

        return $this->paginated($services);
    }

    public function storeService(Request $request)
    {
        $validated = $request->validate([
            'service_category_id' => 'required|integer|exists:service_categories,id',
            'slug' => 'required|string|max:255|unique:services,slug',
            'name_fr' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'short_description_fr' => 'nullable|string',
            'short_description_en' => 'nullable|string',
            'description_fr' => 'nullable|string',
            'description_en' => 'nullable|string',
            'duration' => 'nullable|string|max:100',
            'price' => 'nullable|string|max:50',
            'image' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean',
            'order' => 'sometimes|integer',
        ]);

        $service = Service::create($validated);

        return $this->success($service, 'Service created successfully.', 201);
    }

    public function updateService(Request $request, int $id)
    {
        $service = Service::findOrFail($id);

        $validated = $request->validate([
            'service_category_id' => 'sometimes|integer|exists:service_categories,id',
            'slug' => 'sometimes|string|max:255|unique:services,slug,' . $service->id,
            'name_fr' => 'sometimes|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'short_description_fr' => 'nullable|string',
            'short_description_en' => 'nullable|string',
            'description_fr' => 'nullable|string',
            'description_en' => 'nullable|string',
            'duration' => 'nullable|string|max:100',
            'price' => 'nullable|string|max:50',
            'image' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean',
            'order' => 'sometimes|integer',
        ]);

        $service->update($validated);

        return $this->success($service, 'Service updated successfully.');
    }

    public function deleteService(int $id)
    {
        $service = Service::findOrFail($id);
        $service->delete();

        return $this->success(null, 'Service deleted successfully.');
    }

    public function categories()
    {
        return $this->success(ServiceCategory::withCount('services')->orderBy('order')->get());
    }

    public function lessons(Request $request)
    {
        $query = Lesson::with(['service', 'teacher']);

        if ($request->has('from')) {
            $query->where('date', '>=', $request->date('from'));
        }

        $lessons = $query->orderBy('date')->orderBy('start_time')->paginate($request->integer('per_page', 20));

        return $this->paginated($lessons);
    }

    public function storeLesson(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|integer|exists:services,id',
            'teacher_id' => 'nullable|integer|exists:users,id',
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'room' => 'nullable|string|max:100',
            'lesson_type' => 'sometimes|in:class,practice,exam-prep,workshop',
            'status' => 'sometimes|in:scheduled,in-progress,cancelled,completed',
            'notes' => 'nullable|string|max:2000',
        ]);

        $lesson = Lesson::create($validated + ['status' => $validated['status'] ?? 'scheduled']);

        return $this->success($lesson->load(['service', 'teacher']), 'Lesson created successfully.', 201);
    }

    public function updateLesson(Request $request, int $id)
    {
        $lesson = Lesson::findOrFail($id);

        $validated = $request->validate([
            'service_id' => 'sometimes|integer|exists:services,id',
            'teacher_id' => 'nullable|integer|exists:users,id',
            'title' => 'sometimes|string|max:255',
            'date' => 'sometimes|date',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i',
            'room' => 'nullable|string|max:100',
            'lesson_type' => 'sometimes|in:class,practice,exam-prep,workshop',
            'status' => 'sometimes|in:scheduled,in-progress,cancelled,completed',
            'notes' => 'nullable|string|max:2000',
        ]);

        $lesson->update($validated);

        return $this->success($lesson->load(['service', 'teacher']), 'Lesson updated successfully.');
    }

    public function deleteLesson(int $id)
    {
        $lesson = Lesson::findOrFail($id);
        $lesson->delete();

        return $this->success(null, 'Lesson deleted successfully.');
    }

    public function sessions(Request $request)
    {
        $query = TeachingSession::with(['booking', 'teacher:id,first_name,last_name', 'student:id,first_name,last_name']);

        if ($request->has('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        $sessions = $query->orderByDesc('start_time')->paginate($request->integer('per_page', 20));

        return $this->paginated($sessions);
    }

    public function storeSession(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'nullable|integer|exists:bookings,id',
            'teacher_id' => 'required|integer|exists:users,id',
            'student_id' => 'required|integer|exists:users,id',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after:start_time',
            'status' => 'sometimes|in:scheduled,in-progress,completed,cancelled',
            'notes' => 'nullable|string|max:2000',
            'meeting_link' => 'nullable|url|max:500',
        ]);

        $session = TeachingSession::create($validated + ['status' => $validated['status'] ?? 'scheduled']);

        return $this->success($session->load(['booking', 'teacher', 'student']), 'Session created successfully.', 201);
    }

    public function updateSession(Request $request, int $id)
    {
        $session = TeachingSession::findOrFail($id);

        $validated = $request->validate([
            'booking_id' => 'nullable|integer|exists:bookings,id',
            'teacher_id' => 'sometimes|integer|exists:users,id',
            'student_id' => 'sometimes|integer|exists:users,id',
            'start_time' => 'sometimes|date',
            'end_time' => 'nullable|date|after:start_time',
            'status' => 'sometimes|in:scheduled,in-progress,completed,cancelled',
            'notes' => 'nullable|string|max:2000',
            'meeting_link' => 'nullable|url|max:500',
        ]);

        $session->update($validated);

        return $this->success($session->load(['booking', 'teacher', 'student']), 'Session updated successfully.');
    }

    public function deleteSession(int $id)
    {
        $session = TeachingSession::findOrFail($id);
        $session->delete();

        return $this->success(null, 'Session deleted successfully.');
    }

    public function updatePaymentStatus(Request $request, int $id)
    {
        $payment = Payment::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,completed,refunded',
            'payment_method' => 'nullable|string|max:100',
            'transaction_id' => 'nullable|string|max:255',
        ]);

        $data = [
            'status' => $validated['status'],
            'payment_method' => $validated['payment_method'] ?? $payment->payment_method,
            'transaction_id' => $validated['transaction_id'] ?? $payment->transaction_id,
            'paid_at' => $validated['status'] === 'completed' ? now() : ($validated['status'] === 'refunded' ? $payment->paid_at : null),
        ];

        $payment->update($data);

        return $this->success($payment, 'Payment status updated successfully.');
    }

    public function analytics()
    {
        $studentsPerMonth = User::role('student')
            ->where('created_at', '>=', now()->subMonths(6))
            ->get()
            ->groupBy(fn ($u) => $u->created_at->format('Y-m'))
            ->map(fn ($g) => $g->count());

        $bookingsByStatus = Booking::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $topServices = Service::withCount('enrollments')
            ->orderByDesc('enrollments_count')
            ->take(5)
            ->get();

        $avgGrade = round(Grade::avg('score') ?? 0, 1);

        return $this->success([
            'students_per_month' => $studentsPerMonth,
            'bookings_by_status' => $bookingsByStatus,
            'top_services' => $topServices,
            'average_grade' => $avgGrade,
        ]);
    }

    public function calendar()
    {
        $lessons = Lesson::with(['service', 'teacher'])
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->take(60)
            ->get();

        return $this->success($lessons);
    }

    public function supportTickets(Request $request)
    {
        $tickets = SupportTicket::with(['student', 'messages.sender'])
            ->when($request->input('status') && $request->input('status') !== 'all', fn ($q) => $q->where('status', $request->input('status')))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return $this->paginated($tickets);
    }

    public function replyToTicket(Request $request, int $id)
    {
        $ticket = SupportTicket::findOrFail($id);

        $validated = $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $message = $ticket->messages()->create([
            'sender_id' => auth()->id(),
            'body' => $validated['message'],
        ]);

        $ticket->update(['status' => 'replied']);

        return $this->success($message, 'Reply sent.', 201);
    }

    public function closeTicket(int $id)
    {
        $ticket = SupportTicket::findOrFail($id);
        $ticket->update(['status' => 'closed']);

        return $this->success($ticket, 'Ticket closed.');
    }

    public function contactMessages(Request $request)
    {
        $messages = ContactMessage::orderByDesc('created_at')->paginate($request->integer('per_page', 15));

        return $this->paginated($messages);
    }

    public function updateContactMessage(Request $request, int $id)
    {
        $message = ContactMessage::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:new,read,replied,archived',
        ]);

        $message->update($validated);

        return $this->success($message, 'Message updated.');
    }

    public function payrolls(Request $request)
    {
        $payrolls = TeacherPayroll::with('teacher')
            ->orderByDesc('period_start')
            ->paginate($request->integer('per_page', 15));

        return $this->paginated($payrolls);
    }
}
