<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Service;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

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
            'email' => 'required|email|max:255',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'full_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'contact_method' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:2000',
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
            $service = Service::where('slug', $validated['course'])->where('is_active', true)->first();
            if ($service) {
                $serviceId = $service->id;
            }
        }

        // A booking must reference a real, active service.
        $service = Service::where('id', $serviceId)->where('is_active', true)->first();
        if (!$service) {
            throw ValidationException::withMessages([
                'course' => 'Le programme sélectionné n\'existe pas ou n\'est plus disponible.',
            ]);
        }

        // Derive first/last name from a single full name if provided.
        $firstName = $validated['first_name'] ?? '';
        $lastName = $validated['last_name'] ?? null;
        if (!$lastName && !empty($validated['full_name'])) {
            $parts = preg_split('/\s+/', trim($validated['full_name']));
            if (count($parts) > 1) {
                $lastName = array_pop($parts);
                $firstName = implode(' ', $parts);
            } else {
                $lastName = trim($validated['full_name']);
                $firstName = $lastName;
            }
        }
        $lastName = $lastName ? $lastName : $firstName;

        try {
            DB::beginTransaction();

            // The booking must always belong to a known student.
            // If a user is authenticated, reuse their identity. Otherwise auto-provision a
            // student account from the email and log them in so they land on their dashboard.
            $user = $this->resolveOrCreateStudent(
                $request,
                $firstName,
                $lastName,
                $validated['email'],
                $validated['phone'] ?? null
            );

            // Prevent duplicate pending bookings for the same student + service.
            $existing = Booking::where('user_id', $user->id)
                ->where('service_id', $service->id)
                ->where('status', 'pending')
                ->latest()
                ->first();
            if ($existing) {
                DB::rollBack();
                return $this->respond($request, $existing, 'Votre réservation a déjà été enregistrée. Nous vous contacterons sous peu.', 200);
            }

            $booking = new Booking([
                'service_id' => $service->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'contact_method' => $validated['contact_method'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'preferred_date' => $validated['preferred_date'] ?? null,
                'preferred_slot' => $validated['preferred_slot'] ?? null,
                'placement_score' => $validated['placement_score'] ?? null,
                'placement_level' => $validated['placement_level'] ?? null,
                'oral_test_date' => $validated['oral_test_date'] ?? null,
                'oral_test_slot' => $validated['oral_test_slot'] ?? null,
                'oral_test_status' => $validated['oral_test_status'] ?? null,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'source' => 'website',
                'ip_address' => $request->ip(),
                'booking_ref' => 'BK-' . strtoupper(substr(uniqid(), -6)),
            ]);
            $booking->user_id = $user->id;
            $booking->save();

            if (!empty($validated['program_id'])) {
                $booking->program_id = $validated['program_id'];
                $booking->save();
            }

            $amount = $this->priceToNumber($service->price);
            Payment::create([
                'booking_id' => $booking->id,
                'user_id' => $user->id,
                'student_id' => $user->id,
                'service_id' => $service->id,
                'amount' => $amount ?: null,
                'currency' => 'CAD',
                'status' => 'pending',
            ]);

            Notification::create([
                'user_id' => $user->id,
                'title' => 'Demande de réservation reçue',
                'message' => 'Votre demande de réservation ' . $booking->booking_ref . ' a bien été enregistrée. Nous vous contacterons sous 24 h pour la confirmation.',
                'type' => 'booking',
                'is_read' => false,
                'link' => '/student/dashboard',
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible d\'enregistrer votre réservation pour le moment. Veuillez réessayer.',
                ], 500);
            }

            return redirect()->route('booking')
                ->withInput()
                ->withErrors(['booking' => 'Impossible d\'enregistrer votre réservation pour le moment. Veuillez réessayer.']);
        }

        return $this->respond(
            $request,
            $booking,
            'Votre réservation a été enregistrée avec succès. Vous pouvez la retrouver dans vos programmes.'
        );
    }

    /**
     * Return a JSON payload (with redirect URL) for the fetch-based frontend, or
     * redirect to the student's "My Courses" page for regular form submissions.
     */
    protected function respond(Request $request, Booking $booking, string $message, int $jsonStatus = 201)
    {
        $redirect = route('student.programs');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'booking_ref' => $booking->booking_ref,
                'redirect' => $redirect,
            ], $jsonStatus);
        }

        return redirect()->to($redirect)->with('success', $message);
    }

    /**
     * Return the authenticated user, or auto-provision a student account for a guest
     * and log them in via the existing session so they can reach their dashboard.
     */
    protected function resolveOrCreateStudent(Request $request, string $firstName, string $lastName, string $email, ?string $phone): User
    {
        if (Auth::check()) {
            return Auth::user();
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'first_name' => $firstName ?: ($lastName ?: 'Étudiant'),
                'last_name' => $lastName ?: 'Inscrit',
                'email' => $email,
                'phone' => $phone,
                'password' => Hash::make(Str::random(32)),
                'is_active' => true,
            ]);

            $user->assignRole('student');

            $this->createStudentProfile($user);
        }

        // Log the (possibly newly created) student in via the session so they can
        // access the session-protected dashboard without logging in again.
        Auth::login($user);
        $request->session()->regenerate();

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
        if (!$price) {
            return 0.0;
        }

        preg_match('/\d+(?:[.,]\d+)?/', $price, $matches);

        return $matches ? (float) str_replace(',', '.', $matches[0]) : 0.0;
    }
}
