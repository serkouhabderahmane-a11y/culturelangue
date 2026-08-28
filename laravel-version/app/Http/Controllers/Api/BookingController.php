<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Traits\ApiResponse;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = Booking::query();

        if ($request->user()) {
            $query->where('user_id', $request->user()->id);
        }

        $bookings = $query->with(['service', 'program'])->latest()->paginate($request->integer('per_page', 10));

        return $this->paginated($bookings);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'program_id' => 'nullable|exists:programs,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:30',
            'preferred_date' => 'nullable|date',
            'preferred_time' => 'nullable|string|max:50',
            'contact_method' => 'nullable|string|in:email,phone',
            'preferred_slot' => 'nullable|string|max:255',
            'currency' => 'nullable|string|max:3',
            'notes' => 'nullable|string|max:2000',
        ]);

        $user = $request->user();
        $token = null;
        $accountCreated = false;

        // Anonymous booking → find or create the student account and authenticate them
        // so the browser can land directly on their student dashboard.
        if (! $user) {
            $user = $this->ensureStudentAccount($validated);
            $accountCreated = $user->wasRecentlyCreated;
            $token = $user->createToken('api')->plainTextToken;
        }

        $booking = new Booking($validated);
        $booking->user_id = $user->id;
        $booking->booking_ref = 'BK-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5));
        $booking->status = 'pending';
        $booking->payment_status = 'unpaid';
        $booking->source = $request->input('source', 'website');
        $booking->ip_address = $request->ip();
        $booking->save();

        $amount = $this->priceToNumber($booking->service?->price);

        if ($booking->total_amount === null) {
            $booking->update(['total_amount' => $amount ?: null]);
        }

        Payment::create([
            'booking_id' => $booking->id,
            'user_id' => $user->id,
            'student_id' => $user->id,
            'service_id' => $booking->service_id,
            'amount' => $amount,
            'currency' => $booking->currency ?? 'CAD',
            'payment_method' => null,
            'status' => 'pending',
        ]);

        $data = [
            'booking' => $booking->load(['service', 'program']),
            'account_created' => $accountCreated,
        ];

        if ($token) {
            $data['token'] = $token;
            $data['user'] = $this->userPayload($user);
        }

        Notification::create([
            'user_id' => $user->id,
            'title' => 'Demande de réservation reçue',
            'message' => 'Votre demande de réservation ' . $booking->booking_ref . ' a bien été enregistrée. Nous vous contacterons sous 24 h pour la confirmation.',
            'type' => 'booking',
            'is_read' => false,
            'link' => '/student/dashboard',
        ]);

        return $this->success($data, 'Booking created successfully.', 201);
    }

    protected function ensureStudentAccount(array $data): User
    {
        $user = User::where('email', $data['email'])->first();

        if ($user) {
            return $user;
        }

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make(Str::random(32)),
            'is_active' => true,
        ]);

        $user->assignRole('student');

        $this->createStudentProfile($user);

        return $user;
    }

    protected function createStudentProfile(User $user): StudentProfile
    {
        $attempt = 0;

        do {
            try {
                return StudentProfile::create([
                    'user_id' => $user->id,
                    'student_number' => $this->generateStudentNumber(),
                    'enrollment_date' => now(),
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                $attempt++;
                if ($attempt >= 5) {
                    throw $e;
                }
            }
        } while (true);
    }

    protected function generateStudentNumber(): string
    {
        $year = now()->year;
        $latest = StudentProfile::where('student_number', 'like', "STU-$year-%")
            ->orderByDesc('student_number')
            ->value('student_number');

        $next = 1001;

        if ($latest) {
            $suffix = (int) substr($latest, strrpos($latest, '-') + 1);
            $next = max(1001, $suffix + 1);
        }

        return 'STU-' . $year . '-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    protected function priceToNumber(?string $price): float
    {
        if (! $price) {
            return 0.0;
        }

        preg_match('/\d+(?:[.,]\d+)?/', $price, $matches);

        return $matches ? (float) str_replace(',', '.', $matches[0]) : 0.0;
    }

    protected function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'full_name' => trim($user->first_name . ' ' . $user->last_name),
            'initials' => $user->initials,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->roles->pluck('name')->first(),
            'roles' => $user->roles->pluck('name'),
            'is_active' => $user->is_active,
            'student_profile' => $user->studentProfile,
        ];
    }
}
