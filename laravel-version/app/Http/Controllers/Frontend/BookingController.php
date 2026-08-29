<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Program;
use App\Models\Service;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Stripe\Stripe;
use Stripe\StripeClient;

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
            'payment_intent_id' => 'nullable|string|max:255',
        ]);

        // Resolve the concrete service and the authoritative charge amount.
        // This also maps solo package bookings (course='solo' + package='15h') to the
        // correct DB service (solo-15h) so the price and service association are right.
        [$service, $chargeAmount] = $this->resolveServiceAndAmount(
            $validated['service_id'] ?? null,
            $validated['course'] ?? null,
            $validated['package'] ?? null
        );

        // When Stripe is configured and the program is payable online, the booking may
        // only be finalized once the PaymentIntent has succeeded (verified server-side).
        // Programs without a payable amount (e.g. ateliers "à venir") proceed unpaid.
        $paid = false;
        $payable = $chargeAmount !== null && $chargeAmount > 0;
        if ($this->stripeConfigured() && $payable) {
            $intent = $this->verifyPaymentIntent($request, $validated['payment_intent_id'] ?? null);
            if (!$intent) {
                throw ValidationException::withMessages([
                    'payment_intent_id' => 'Le paiement n\'a pas été complété. Veuillez réessayer.',
                ]);
            }
            $paid = true;
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
                'payment_status' => $paid ? 'paid' : 'unpaid',
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

            $amount = $chargeAmount ?? $this->priceToNumber($service->price);
            $currency = strtoupper(config('services.stripe.currency', 'cad'));
            Payment::create([
                'booking_id' => $booking->id,
                'user_id' => $user->id,
                'student_id' => $user->id,
                'service_id' => $service->id,
                'amount' => $amount ?: null,
                'currency' => $currency,
                'transaction_id' => $paid ? $intent->id : null,
                'status' => $paid ? 'paid' : 'pending',
                'paid_at' => $paid ? now() : null,
                'metadata' => $paid ? json_encode([
                    'source' => 'stripe',
                    'amount' => $amount,
                    'currency' => $currency,
                    'booking_ref' => $booking->booking_ref,
                ]) : null,
            ]);

            Notification::create([
                'user_id' => $user->id,
                'title' => $paid ? 'Réservation confirmée' : 'Demande de réservation reçue',
                'message' => $paid
                    ? 'Votre réservation ' . $booking->booking_ref . ' a bien été confirmée et votre paiement reçu. Nous vous contacterons sous 24 h pour la planification de votre test oral.'
                    : 'Votre demande de réservation ' . $booking->booking_ref . ' a bien été enregistrée. Nous vous contacterons sous 24 h pour la confirmation.',
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
     * Create a Stripe PaymentIntent for the selected program. The amount is
     * computed server-side so the client can never influence the charge.
     */
    public function paymentIntent(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'course' => 'required|string|max:255',
            'package' => 'nullable|string|max:50',
            'program' => 'nullable|string|max:255',
            'group' => 'nullable|string|max:255',
            'service_id' => 'nullable|exists:services,id',
        ]);

        if (!$this->stripeConfigured()) {
            return response()->json([
                'success' => false,
                'requires_payment' => false,
                'message' => 'Le paiement en ligne n\'est pas configuré pour le moment.',
            ], 200);
        }

        try {
            [$service, $amount] = $this->resolveServiceAndAmount(
                $validated['service_id'] ?? null,
                $validated['course'] ?? null,
                $validated['package'] ?? null
            );

            if ($amount === null || $amount <= 0) {
                return response()->json([
                    'success' => true,
                    'requires_payment' => false,
                    'amount' => 0,
                    'currency' => strtolower(config('services.stripe.currency', 'cad')),
                ], 200);
            }

            $stripe = $this->stripe();
            $cents = (int) round($amount * 100);

            $intent = $stripe->paymentIntents->create([
                'amount' => $cents,
                'currency' => strtolower(config('services.stripe.currency', 'cad')),
                'automatic_payment_methods' => ['enabled' => true],
                'receipt_email' => $validated['email'],
                'metadata' => [
                    'service_id' => (string) $service->id,
                    'service_slug' => $service->slug,
                    'email' => $validated['email'],
                    'source' => 'website_booking',
                ],
            ]);

            // Remember the intent id for this session so store() can verify it later.
            $sessionIntents = $request->session()->get('booking_payment_intents', []);
            $sessionIntents[] = $intent->id;
            $request->session()->put('booking_payment_intents', array_slice($sessionIntents, -20));

            return response()->json([
                'success' => true,
                'requires_payment' => true,
                'client_secret' => $intent->client_secret,
                'payment_intent_id' => $intent->id,
                'amount' => $amount,
                'currency' => strtolower(config('services.stripe.currency', 'cad')),
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'requires_payment' => false,
                'message' => 'Impossible de préparer le paiement pour le moment. Veuillez réessayer.',
            ], 422);
        }
    }

    /**
     * Resolve the concrete Service and the authoritative amount (CAD) to charge.
     * Returns [Service, ?float]. Amount is null when the program is not payable online.
     */
    protected function resolveServiceAndAmount(?int $serviceId, ?string $course, ?string $package): array
    {
        $service = null;

        if ($serviceId) {
            $service = Service::where('id', $serviceId)->where('is_active', true)->first();
        }

        // For solo bookings the wizard sends course='solo' + package='15h'. Map to the
        // concrete DB service (e.g. solo-15h) so the price + booking service_id are correct.
        if (!$service && $course === 'solo' && $package) {
            $slug = 'solo-' . strtolower(trim($package));
            $service = Service::where('slug', $slug)->where('is_active', true)->first();
        }

        if (!$service && $course) {
            $service = Service::where('slug', $course)->where('is_active', true)->first();
        }

        if (!$service) {
            throw ValidationException::withMessages([
                'course' => 'Le programme sélectionné n\'existe pas ou n\'est plus disponible.',
            ]);
        }

        $amount = $this->computeAmount($service, $package);

        return [$service, $amount];
    }

    /**
     * Determine the charge amount for a service. Prefers a matching Program price
     * (when a package is supplied), otherwise uses the service's base price.
     */
    protected function computeAmount(Service $service, ?string $package): ?float
    {
        // Prefer a matching active Program row for option-specific pricing.
        if ($package) {
            $programs = Program::where('service_id', $service->id)->where('is_active', true)->get();
            $needle = strtolower((string) $package);
            foreach ($programs as $program) {
                $haystack = strtolower(($program->name_fr ?? '') . ' ' . ($program->name_en ?? ''));
                if (str_contains($haystack, $needle) || str_contains($needle, $haystack)) {
                    $price = $this->priceToNumber($program->price);
                    if ($price > 0) {
                        return $price;
                    }
                }
            }
        }

        // Fall back to the service's base price.
        $price = $this->priceToNumber($service->price);

        return $price > 0 ? $price : null;
    }

    protected function stripe(): StripeClient
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        return new StripeClient(config('services.stripe.secret'));
    }

    protected function stripeConfigured(): bool
    {
        return (bool) config('services.stripe.secret');
    }

    /**
     * Verify a Stripe PaymentIntent succeeded before finalizing the booking.
     * Returns the intent on success, or null when not succeeded / not configured.
     */
    protected function verifyPaymentIntent(Request $request, ?string $paymentIntentId): ?\Stripe\PaymentIntent
    {
        if (!$paymentIntentId || !$this->stripeConfigured()) {
            return null;
        }

        // Only accept intents that were created for this session.
        $sessionIntents = $request->session()->get('booking_payment_intents', []);
        if (!in_array($paymentIntentId, $sessionIntents, true)) {
            return null;
        }

        $intent = $this->stripe()->paymentIntents->retrieve($paymentIntentId);

        if ($intent->status !== 'succeeded') {
            return null;
        }

        return $intent;
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
