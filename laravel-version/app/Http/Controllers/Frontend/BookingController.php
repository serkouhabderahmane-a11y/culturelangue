<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\BookingConfirmation;
use App\Models\Booking;
use App\Models\CalendarProgram;
use App\Models\CalendarSession;
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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Stripe\Checkout\Session as CheckoutSession;
use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\Webhook;

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
     * Create a Stripe Checkout Session (hosted, redirect-based) for the selected
     * booking. The amount is resolved server-side and never trusted from the client.
     *
     * A Booking row is created as "pending/unpaid" BEFORE redirecting to Stripe so
     * the payment can be reconciled server-side via the webhook. If the same
     * student + service + session already has an open (unpaid) booking, it is reused
     * instead of creating a duplicate.
     */
    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'full_name' => 'nullable|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'course' => 'nullable|string|max:255',
            'package' => 'nullable|string|max:50',
            'program' => 'nullable|string|max:255',
            'service_id' => 'nullable|exists:services,id',
            'calendar_session_id' => 'nullable|integer',
            'calendar_program_id' => 'nullable|integer',
            'session_label' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'slot' => 'nullable|string|max:50',
        ]);

        try {
            // 1. Resolve the concrete service + authoritative amount (server-side).
            [$service, $chargeAmount] = $this->resolveServiceAndAmount(
                $validated['service_id'] ?? null,
                $validated['course'] ?? null,
                $validated['package'] ?? null
            );

            // Derive full name.
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

            // 2. Resolve (or auto-provision + login) the student.
            $user = $this->resolveOrCreateStudent(
                $request,
                $firstName,
                $lastName,
                $validated['email'],
                $validated['phone'] ?? null
            );

            $sessionId = isset($validated['calendar_session_id']) ? (int) $validated['calendar_session_id'] : null;
            $calendarSession = null;
            $sessionLabel = $validated['session_label'] ?? null;
            $sessionDate = null;

            if ($sessionId) {
                $calendarSession = CalendarSession::where('id', $sessionId)
                    ->where('is_active', true)
                    ->first();
                if ($calendarSession) {
                    $sessionLabel = $sessionLabel ?: $this->sessionLabel($calendarSession);
                    $sessionDate = $calendarSession->start_date;
                }
            }

            // 3. Deduplicate: reuse an existing open (unpaid) booking for the same
            //    student + service + session instead of creating a duplicate.
            $existing = Booking::where('user_id', $user->id)
                ->where('service_id', $service->id)
                ->whereIn('status', ['pending'])
                ->where('payment_status', 'unpaid')
                ->when($sessionId, fn ($q) => $q->where('calendar_session_id', $sessionId))
                ->when(!$sessionId, fn ($q) => $q->whereNull('calendar_session_id'))
                ->latest()
                ->first();

            if ($existing) {
                $booking = $existing;
            } else {
                $currency = strtoupper(config('services.stripe.currency', 'cad'));
                $booking = new Booking([
                    'service_id' => $service->id,
                    'program_id' => ($validated['program'] ?? null) ? (Program::where('service_id', $service->id)->where('slug', $validated['program'])->value('id') ?? null) : null,
                    'calendar_program_id' => $validated['calendar_program_id'] ?? null,
                    'calendar_session_id' => $sessionId,
                    'session_label' => $sessionLabel,
                    'session_date' => $sessionDate ?: ($validated['date'] ?? null),
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $validated['email'],
                    'phone' => $validated['phone'] ?? null,
                    'preferred_slot' => $validated['slot'] ?? null,
                    'status' => 'pending',
                    'payment_status' => 'unpaid',
                    'total_amount' => $chargeAmount !== null ? $chargeAmount : $this->priceToNumber($service->price),
                    'currency' => $currency,
                    'source' => 'website',
                    'ip_address' => $request->ip(),
                    'booking_ref' => 'BK-' . strtoupper(substr(uniqid(), -6)),
                ]);
                $booking->user_id = $user->id;
                $booking->save();
            }

            // 4. Not payable online -> booking recorded directly, no payment needed.
            if ($chargeAmount === null || $chargeAmount <= 0) {
                $this->markBookingPaid($booking, null, 0.0, strtoupper(config('services.stripe.currency', 'cad')));
                return response()->json([
                    'success' => true,
                    'requires_payment' => false,
                    'message' => 'Votre inscription a bien été enregistrée.',
                    'booking_id' => $booking->id,
                    'booking_ref' => $booking->booking_ref,
                    'redirect' => route('student.programs'),
                ], 200);
            }

            if (!$this->stripeConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le paiement en ligne n\'est pas configuré pour le moment. Veuillez réessayer plus tard.',
                ], 422);
            }

            // 5. Build the hosted Checkout Session.
            $currency = strtolower(config('services.stripe.currency', 'cad'));
            $cents = (int) round($chargeAmount * 100);

            $successBase = config('services.stripe.success_url') ?: $this->appUrl() . '/paiement/succes';
            $cancelBase = config('services.stripe.cancel_url') ?: $this->appUrl() . '/paiement/annule';

            $stripe = $this->stripe();
            $session = $stripe->checkout->sessions->create([
                'mode' => 'payment',
                'customer_email' => $validated['email'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => $currency,
                        'unit_amount' => $cents,
                        'product_data' => [
                            'name' => $service->name_fr ?? $service->name ?? 'Programme',
                            'description' => $sessionLabel ?: ($service->slug ?? 'Cours de langues'),
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'metadata' => [
                    'booking_id' => (string) $booking->id,
                    'booking_ref' => (string) $booking->booking_ref,
                    'service_id' => (string) $service->id,
                    'program_id' => $validated['program'] ?? '',
                    'session_id' => (string) ($sessionId ?: ''),
                    'student_id' => (string) $user->id,
                ],
                'client_reference_id' => (string) $booking->id,
                'success_url' => $successBase . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $cancelBase . '?booking_ref=' . $booking->booking_ref,
            ]);

            // Persist the checkout session id so it can be reconciled by the webhook.
            $booking->update(['preferred_date' => $booking->preferred_date]);

            return response()->json([
                'success' => true,
                'requires_payment' => true,
                'checkout_url' => $session->url,
                'session_id' => $session->id,
                'booking_id' => $booking->id,
                'booking_ref' => $booking->booking_ref,
                'amount' => $chargeAmount,
                'currency' => strtolower($currency),
            ], 200);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Booking checkout failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Impossible de préparer le paiement pour le moment. Veuillez réessayer.',
            ], 422);
        }
    }

    /**
     * Landing page after Stripe Checkout. The session is verified server-side with
     * Stripe; the booking/payment are confirmed (best-effort, in case the webhook
     * has not fired yet) and a confirmation screen with a dashboard link is shown.
     */
    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');
        $booking = null;
        $payment = null;
        $error = null;

        try {
            if ($sessionId && $this->stripeConfigured()) {
                $session = $this->stripe()->checkout->sessions->retrieve($sessionId);

                $bookingId = $session->client_reference_id
                    ? (int) $session->client_reference_id
                    : (int) ($session->metadata['booking_id'] ?? 0);

                if ($bookingId) {
                    $booking = Booking::find($bookingId);

                    $isPaid = in_array($session->payment_status, ['paid', 'no_payment_required'], true);
                    if ($isPaid && $booking && $booking->payment_status !== 'paid') {
                        [$payment, $rawAmount] = $this->confirmBookingFromSession($session, $booking);
                        $this->sendConfirmation($booking, $payment);
                    } elseif ($booking) {
                        $payment = $booking->payments()->where('status', 'paid')->latest()->first();
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('Booking success page error', ['error' => $e->getMessage()]);
            $error = 'Merci de vérifier votre paiement. Votre confirmation vous sera également envoyée par courriel.';
        }

        if (!$booking) {
            return view('booking.payment-result', [
                'paid' => false,
                'booking' => null,
                'payment' => null,
                'message' => 'La session de paiement est introuvable ou déjà consommée.',
            ]);
        }

        return view('booking.payment-result', [
            'paid' => $booking->payment_status === 'paid',
            'booking' => $booking,
            'payment' => $payment,
            'message' => $error,
        ]);
    }

    /**
     * Landing page when the user abandons Checkout. The booking stays unpaid so the
     * user can retry (a new Checkout session reuses the same open booking).
     */
    public function cancel(Request $request)
    {
        $ref = $request->query('booking_ref');

        return view('booking.payment-result', [
            'paid' => false,
            'cancel' => true,
            'booking' => $ref ? Booking::where('booking_ref', $ref)->first() : null,
            'payment' => null,
            'message' => 'Votre paiement a été annulé. Aucun montant n\'a été débité.',
        ]);
    }

    /**
     * Provide a CSRF token + session cookie so the static payment page can safely
     * POST to the protected booking/checkout endpoint.
     */
    public function token(Request $request)
    {
        return response()->json([
            'token' => csrf_token(),
        ]);
    }

    /**
     * Stripe webhook. Verifies the signature with STRIPE_WEBHOOK_SECRET, then
     * confirms the booking + payment on checkout.session.completed.
     */
    public function webhook(Request $request)
    {
        $webhookSecret = config('services.stripe.webhook_secret');

        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        if (!$webhookSecret) {
            Log::warning('Stripe webhook secret is not configured.');

            return response()->json(['error' => 'Webhook secret not configured.'], 500);
        }

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload.'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature.'], 400);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                /** @var CheckoutSession $session */
                $session = $event->data->object;
                $this->handleCheckoutCompleted($session);
                break;

            case 'checkout.session.expired':
                $session = $event->data->object;
                $bookingId = $session->client_reference_id
                    ? (int) $session->client_reference_id
                    : (int) ($session->metadata['booking_id'] ?? 0);
                if ($bookingId) {
                    Booking::where('id', $bookingId)
                        ->where('payment_status', 'unpaid')
                        ->where('status', 'pending')
                        ->update(['status' => 'cancelled']);
                }
                break;

            default:
                // Ignore unhandled event types.
                break;
        }

        return response()->json(['received' => true], 200);
    }

    protected function handleCheckoutCompleted(CheckoutSession $session): void
    {
        $bookingId = $session->client_reference_id
            ? (int) $session->client_reference_id
            : (int) ($session->metadata['booking_id'] ?? 0);

        $booking = $bookingId ? Booking::find($bookingId) : null;
        if (!$booking) {
            Log::warning('Stripe webhook: booking not found', ['session' => $session->id]);

            return;
        }

        if (!in_array($session->payment_status, ['paid', 'no_payment_required'], true)) {
            return;
        }

        [$payment] = $this->confirmBookingFromSession($session, $booking);
        $this->sendConfirmation($booking, $payment);
    }

    /**
     * Mark a booking as paid and upsert its Payment record from a Stripe Checkout
     * session. Returns [Payment, rawCents].
     */
    protected function confirmBookingFromSession(CheckoutSession $session, Booking $booking): array
    {
        $rawCents = $session->amount_total ?? 0;
        $actualCents = (int) $booking->total_amount * 100;
        $snapshotAmount = $rawCents > 0 ? $rawCents / 100 : $actualCents / 100;

        $paymentIntentId = $session->payment_intent ?? null;

        $payment = Payment::updateOrCreate(
            ['booking_id' => $booking->id, 'stripe_checkout_session_id' => $session->id],
            [
                'user_id' => $booking->user_id,
                'student_id' => $booking->user_id,
                'service_id' => $booking->service_id,
                'amount' => $snapshotAmount,
                'currency' => strtoupper($session->currency ?: config('services.stripe.currency', 'cad')),
                'payment_method' => 'stripe_checkout',
                'transaction_id' => $paymentIntentId,
                'status' => 'paid',
                'paid_at' => now(),
                'metadata' => json_encode([
                    'source' => 'stripe_checkout',
                    'checkout_session_id' => $session->id,
                    'amount_total' => $session->amount_total,
                    'currency' => $session->currency,
                    'booking_ref' => $booking->booking_ref,
                ]),
            ]
        );

        $booking->update([
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        return [$payment, $rawCents];
    }

    /**
     * Create the in-app notification + best-effort confirmation email for a paid
     * booking. Email failures never break the confirmation flow.
     */
    protected function sendConfirmation(Booking $booking, ?Payment $payment = null): void
    {
        Notification::updateOrCreate(
            [
                'user_id' => $booking->user_id,
                'link' => '/student/dashboard',
                'title' => 'Réservation confirmée — ' . $booking->booking_ref,
            ],
            [
                'message' => 'Votre réservation ' . $booking->booking_ref . ' a bien été confirmée et votre paiement reçu. Nous vous contacterons sous 24 h pour la planification de votre test oral.',
                'type' => 'booking',
                'is_read' => false,
            ]
        );

        if ($booking->email && config('mail.default')) {
            try {
                Mail::to($booking->email)->send(new BookingConfirmation($booking, $payment));
            } catch (\Throwable $e) {
                Log::warning('Confirmation email could not be sent', [
                    'booking_ref' => $booking->booking_ref,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    protected function markBookingPaid(Booking $booking, ?string $checkoutSessionId, float $amount, string $currency): ?Payment
    {
        $payment = Payment::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'user_id' => $booking->user_id,
                'student_id' => $booking->user_id,
                'service_id' => $booking->service_id,
                'amount' => $amount ?: $booking->total_amount,
                'currency' => $currency,
                'payment_method' => 'free',
                'transaction_id' => null,
                'status' => 'paid',
                'paid_at' => now(),
                'metadata' => json_encode(['source' => 'no_payment_required']),
            ]
        );

        $booking->update([
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $this->sendConfirmation($booking, $payment);

        return $payment;
    }

    protected function sessionLabel(CalendarSession $session): string
    {
        if ($session->title) {
            return $session->title;
        }

        $program = $session->calendarProgram;
        $label = $program ? ($program->title ?: $program->name ?? '') : '';
        $days = trim((string) $session->days_text);

        return trim(implode(' — ', array_filter([$label, $days])) ?: 'Session');
    }

    protected function appUrl(): string
    {
        $url = config('app.url', url('/'));
        $url = rtrim($url, '/');

        return $url;
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
