<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\ContactMessage;
use App\Models\Payment;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;

class AdminPanelTest extends BaseApiTestCase
{
    public function test_admin_dashboard_stats(): void
    {
        $admin = $this->createUser('admin');
        $token = $this->apiLogin($admin);

        $this->createUser('student');
        $this->createUser('teacher');

        $this->withToken($token)
            ->getJson('/api/v1/admin/dashboard')
            ->assertStatus(200)
            ->assertJsonPath('data.stats.students_count', 1)
            ->assertJsonPath('data.stats.teachers_count', 1);
    }

    public function test_admin_can_create_student(): void
    {
        $admin = $this->createUser('admin');
        $token = $this->apiLogin($admin);

        $this->withToken($token)->postJson('/api/v1/admin/users', [
            'first_name' => 'New',
            'last_name' => 'Student',
            'email' => 'newstudent@test.ca',
            'password' => 'password123',
            'role' => 'student',
        ])->assertStatus(201);

        $this->assertDatabaseHas('users', ['email' => 'newstudent@test.ca']);
        $this->assertDatabaseHas('student_profiles', ['user_id' => User::where('email', 'newstudent@test.ca')->first()->id]);
    }

    public function test_admin_can_update_user(): void
    {
        $admin = $this->createUser('admin');
        $token = $this->apiLogin($admin);
        $student = $this->createUser('student');

        $this->withToken($token)->putJson("/api/v1/admin/users/{$student->id}", [
            'first_name' => 'Renamed',
        ])->assertStatus(200);

        $this->assertDatabaseHas('users', ['id' => $student->id, 'first_name' => 'Renamed']);
    }

    public function test_admin_can_delete_user(): void
    {
        $admin = $this->createUser('admin');
        $token = $this->apiLogin($admin);
        $student = $this->createUser('student');

        $this->withToken($token)
            ->deleteJson("/api/v1/admin/users/{$student->id}")
            ->assertStatus(200);

        $this->assertSoftDeleted('users', ['id' => $student->id]);
    }

    public function test_admin_cannot_delete_self(): void
    {
        $admin = $this->createUser('admin');
        $token = $this->apiLogin($admin);

        $this->withToken($token)
            ->deleteJson("/api/v1/admin/users/{$admin->id}")
            ->assertStatus(422);
    }

    public function test_admin_can_manage_services(): void
    {
        $admin = $this->createUser('admin');
        $token = $this->apiLogin($admin);

        $category = ServiceCategory::create([
            'slug' => 'ateliers',
            'name_fr' => 'Ateliers',
            'name_en' => 'Workshops',
            'order' => 1,
            'is_active' => true,
        ]);

        $this->withToken($token)->postJson('/api/v1/admin/services', [
            'service_category_id' => $category->id,
            'slug' => 'atelier-culture',
            'name_fr' => 'Atelier Culture',
            'name_en' => 'Culture Workshop',
            'price' => '20 $',
        ])->assertStatus(201);

        $service = Service::where('slug', 'atelier-culture')->first();
        $this->assertNotNull($service);

        $this->withToken($token)->putJson("/api/v1/admin/services/{$service->id}", [
            'name_fr' => 'Atelier Culture Québec',
        ])->assertStatus(200);

        $this->withToken($token)
            ->deleteJson("/api/v1/admin/services/{$service->id}")
            ->assertStatus(200);

        $this->assertSoftDeleted('services', ['id' => $service->id]);
    }

    public function test_admin_can_refund_payment(): void
    {
        $admin = $this->createUser('admin');
        $token = $this->apiLogin($admin);
        $student = $this->createUser('student');

        $booking = Booking::create([
            'user_id' => $student->id,
            'first_name' => 'Student',
            'last_name' => 'Test',
            'email' => 'student@test.ca',
            'status' => 'confirmed',
        ]);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'user_id' => $student->id,
            'amount' => 600,
            'status' => 'completed',
        ]);

        $this->withToken($token)->postJson("/api/v1/admin/payments/{$payment->id}/refund", [
            'refund_reason' => 'Student request',
        ])->assertStatus(200);

        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'status' => 'refunded']);
    }

    public function test_admin_support_ticket_workflow(): void
    {
        $admin = $this->createUser('admin');
        $token = $this->apiLogin($admin);
        $student = $this->createUser('student');

        $ticket = \App\Models\SupportTicket::create([
            'student_id' => $student->id,
            'subject' => 'Help',
            'status' => 'open',
        ]);

        $this->withToken($token)->postJson("/api/v1/admin/support-tickets/{$ticket->id}/reply", [
            'message' => 'How can I help?',
        ])->assertStatus(201);

        $this->withToken($token)
            ->postJson("/api/v1/admin/support-tickets/{$ticket->id}/close")
            ->assertStatus(200);

        $this->assertDatabaseHas('support_tickets', ['id' => $ticket->id, 'status' => 'closed']);
    }

    public function test_admin_can_manage_contact_messages(): void
    {
        $admin = $this->createUser('admin');
        $token = $this->apiLogin($admin);

        $message = ContactMessage::create([
            'first_name' => 'Alice',
            'email' => 'alice@example.com',
            'message' => 'Hello',
            'status' => 'new',
        ]);

        $this->withToken($token)->putJson("/api/v1/admin/contact-messages/{$message->id}", [
            'status' => 'read',
        ])->assertStatus(200);

        $this->assertDatabaseHas('contact_messages', ['id' => $message->id, 'status' => 'read']);
    }

    public function test_non_admin_cannot_access_admin_endpoints(): void
    {
        $student = $this->createUser('student');
        $token = $this->apiLogin($student);

        $this->withToken($token)
            ->getJson('/api/v1/admin/users')
            ->assertStatus(403);
    }
}
