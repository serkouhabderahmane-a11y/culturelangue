<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\StudentProfile;
use App\Models\User;

class BookingTest extends BaseApiTestCase
{
    public function test_public_user_can_create_booking(): void
    {
        $category = ServiceCategory::create([
            'slug' => 'parcours-linguistique',
            'name_fr' => 'Parcours linguistique',
            'name_en' => 'Linguistic Pathway',
            'order' => 1,
            'is_active' => true,
        ]);

        $service = Service::create([
            'service_category_id' => $category->id,
            'slug' => 'francais-express',
            'name_fr' => 'Français Express',
            'name_en' => 'Français Express',
            'order' => 1,
            'is_active' => true,
        ]);

        $this->postJson('/api/v1/bookings', [
            'service_id' => $service->id,
            'first_name' => 'Pierre',
            'last_name' => 'Lafleur',
            'email' => 'pierre@example.com',
            'phone' => '5145551234',
            'preferred_date' => '2026-09-01',
            'preferred_time' => '18:00',
            'notes' => 'Beginner level',
        ])->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.booking.status', 'pending')
            ->assertJsonPath('data.booking.payment_status', 'unpaid');

        $this->assertNotNull($booking = Booking::first());
        $this->assertStringStartsWith('BK-', $booking->booking_ref);
        $this->assertEquals('18:00', $booking->preferred_time);
    }

    public function test_public_booking_flows_into_admin_listing_and_status_management(): void
    {
        $category = ServiceCategory::create([
            'slug' => 'parcours-linguistique',
            'name_fr' => 'Parcours linguistique',
            'name_en' => 'Linguistic Pathway',
            'order' => 1,
            'is_active' => true,
        ]);

        $service = Service::create([
            'service_category_id' => $category->id,
            'slug' => 'francais-express',
            'name_fr' => 'Français Express',
            'name_en' => 'Français Express',
            'order' => 1,
            'is_active' => true,
        ]);

        // 1. Public visitor submits the booking form (as seen from booking.html)
        $this->postJson('/api/v1/bookings', [
            'service_id' => $service->id,
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'email' => 'maria@example.com',
            'phone' => '+1 514 555 0101',
            'preferred_date' => '2026-09-05',
            'preferred_time' => '18:00',
            'preferred_slot' => '18:00',
            'contact_method' => 'email',
            'currency' => 'CAD',
            'notes' => 'Débutante',
            'source' => 'website',
        ])->assertStatus(201)
            ->assertJsonPath('success', true);

        $booking = Booking::first();
        $this->assertNotNull($booking);
        $this->assertEquals('pending', $booking->status);
        $this->assertEquals('website', $booking->source);
        $this->assertEquals('18:00', $booking->preferred_time);

        // 2. Admin retrieves the real booking in the admin listing
        $admin = $this->createUser('admin');
        $token = $this->apiLogin($admin);

        $this->withToken($token)
            ->getJson('/api/v1/admin/bookings?search=' . $booking->booking_ref)
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.id', $booking->id)
            ->assertJsonPath('data.0.first_name', 'Maria');

        // 3. Admin changes the booking status
        $this->withToken($token)
            ->putJson("/api/v1/admin/bookings/{$booking->id}/status", ['status' => 'confirmed'])
            ->assertStatus(200)
            ->assertJsonPath('data.status', 'confirmed');

        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'confirmed']);

        // 4. The dashboard reflects the booking
        $this->withToken($token)
            ->getJson('/api/v1/admin/dashboard')
            ->assertStatus(200)
            ->assertJsonPath('data.stats.total_bookings', 1)
            ->assertJsonPath('data.recent_bookings.0.booking_ref', $booking->booking_ref);
    }

    public function test_booking_validation_returns_field_errors(): void
    {
        $this->postJson('/api/v1/bookings', [
            'service_id' => 99999,
            'first_name' => '',
            'last_name' => '',
            'email' => 'pas-un-courriel',
        ])->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'service_id',
                    'first_name',
                    'last_name',
                    'email',
                ],
            ]);
    }

    public function test_booking_requires_service(): void
    {
        $this->postJson('/api/v1/bookings', [
            'first_name' => 'Pierre',
            'last_name' => 'Lafleur',
            'email' => 'pierre@example.com',
        ])->assertStatus(422);
    }

    public function test_booking_links_to_authenticated_student(): void
    {
        $student = $this->createUser('student');
        $token = $this->apiLogin($student);

        $category = ServiceCategory::create([
            'slug' => 'solo',
            'name_fr' => 'Solo',
            'name_en' => 'Solo',
            'order' => 1,
            'is_active' => true,
        ]);

        $service = Service::create([
            'service_category_id' => $category->id,
            'slug' => 'solo-5h',
            'name_fr' => 'Solo 5h',
            'name_en' => 'Solo 5h',
            'order' => 1,
            'is_active' => true,
        ]);

        $this->withToken($token)->postJson('/api/v1/bookings', [
            'service_id' => $service->id,
            'first_name' => 'Student',
            'last_name' => 'Test',
            'email' => 'student@test.ca',
        ])->assertStatus(201);

        $booking = Booking::first();
        $this->assertEquals($student->id, $booking->user_id);
    }

    public function test_admin_can_confirm_booking(): void
    {
        $admin = $this->createUser('admin');
        $token = $this->apiLogin($admin);

        $student = $this->createUser('student');
        $booking = Booking::create([
            'user_id' => $student->id,
            'first_name' => 'Student',
            'last_name' => 'Test',
            'email' => 'student@test.ca',
            'status' => 'pending',
        ]);

        $this->withToken($token)->putJson("/api/v1/admin/bookings/{$booking->id}/status", [
            'status' => 'confirmed',
        ])->assertStatus(200);

        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'confirmed']);
    }

    public function test_booking_list_is_visible_to_student(): void
    {
        $student = $this->createUser('student');
        $token = $this->apiLogin($student);

        Booking::create([
            'user_id' => $student->id,
            'first_name' => 'Student',
            'last_name' => 'Test',
            'email' => 'student@test.ca',
            'status' => 'pending',
        ]);

        $this->withToken($token)
            ->getJson('/api/v1/student/bookings')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_anonymous_booking_creates_student_account_and_returns_token(): void
    {
        $category = ServiceCategory::create([
            'slug' => 'parcours-linguistique',
            'name_fr' => 'Parcours linguistique',
            'name_en' => 'Linguistic Pathway',
            'order' => 1,
            'is_active' => true,
        ]);

        $service = Service::create([
            'service_category_id' => $category->id,
            'slug' => 'francais-express',
            'name_fr' => 'Français Express',
            'name_en' => 'Français Express',
            'order' => 1,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/v1/bookings', [
            'service_id' => $service->id,
            'first_name' => 'Test Booking',
            'last_name' => 'User',
            'email' => 'e2e-account@example.com',
            'phone' => '+1 555 0100 0100',
        ])->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.account_created', true)
            ->assertJsonPath('data.user.full_name', 'Test Booking User')
            ->assertJsonPath('data.user.email', 'e2e-account@example.com')
            ->assertJsonPath('data.user.role', 'student')
            ->assertJsonStructure(['data' => ['token', 'user' => ['id', 'role', 'initials']]]);

        $user = User::where('email', 'e2e-account@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('student'));
        $this->assertNotNull(StudentProfile::where('user_id', $user->id)->first());

        $booking = Booking::where('email', 'e2e-account@example.com')->first();
        $this->assertNotNull($booking);
        $this->assertEquals($user->id, $booking->user_id);

        // The returned token is valid and resolves to the same student
        $this->withToken($response->json('data.token'))
            ->getJson('/api/v1/me')
            ->assertStatus(200)
            ->assertJsonPath('data.user.email', 'e2e-account@example.com');
    }

    public function test_booking_does_not_duplicate_existing_account(): void
    {
        $existing = $this->createUser('student');
        $existing->forceFill(['email' => 'repeat@example.com'])->save();

        $category = ServiceCategory::create([
            'slug' => 'solo',
            'name_fr' => 'Solo',
            'name_en' => 'Solo',
            'order' => 1,
            'is_active' => true,
        ]);

        $service = Service::create([
            'service_category_id' => $category->id,
            'slug' => 'solo-5h',
            'name_fr' => 'Solo 5h',
            'name_en' => 'Solo 5h',
            'order' => 1,
            'is_active' => true,
        ]);

        $this->postJson('/api/v1/bookings', [
            'service_id' => $service->id,
            'first_name' => 'Repeat',
            'last_name' => 'User',
            'email' => 'repeat@example.com',
        ])->assertStatus(201)
            ->assertJsonPath('data.account_created', false);

        $this->assertDatabaseCount('users', 1);
    }

    public function test_student_dashboard_includes_pending_booking(): void
    {
        $student = $this->createUser('student');
        $token = $this->apiLogin($student);

        $booking = Booking::create([
            'user_id' => $student->id,
            'first_name' => $student->first_name,
            'last_name' => $student->last_name,
            'email' => $student->email,
            'booking_ref' => 'BK-20260101-AB12C',
            'status' => 'pending',
        ]);

        $this->withToken($token)
            ->getJson('/api/v1/student/dashboard')
            ->assertStatus(200)
            ->assertJsonPath('data.user.full_name', $student->full_name)
            ->assertJsonPath('data.stats.pending_bookings', 1)
            ->assertJsonPath('data.bookings.0.booking_ref', $booking->booking_ref)
            ->assertJsonPath('data.bookings.0.status', 'pending');
    }
}
