<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Enrollment;
use App\Models\Invoice;
use App\Models\Lesson;
use App\Models\Payment;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\TeachingSession;
use Database\Seeders\LessonSeeder;
use Database\Seeders\ServiceCategorySeeder;
use Database\Seeders\ServiceSeeder;

class LessonManagementTest extends BaseApiTestCase
{
    protected function tokenRequest(string $method, string $uri, string $token, array $data = [])
    {
        $this->app['auth']->forgetGuards();

        return match (strtoupper($method)) {
            'POST' => $this->withToken($token)->postJson($uri, $data),
            'PUT' => $this->withToken($token)->putJson($uri, $data),
            'DELETE' => $this->withToken($token)->deleteJson($uri, $data),
            default => $this->withToken($token)->getJson($uri),
        };
    }

    protected function createService(array $overrides = []): Service
    {
        $category = ServiceCategory::create([
            'slug' => 'cat-' . substr(md5((string) random_int(0, PHP_INT_MAX)), 0, 6),
            'name_fr' => 'Test Catégorie',
            'name_en' => 'Test Category',
            'description_fr' => 'Catégorie de test',
            'order' => 1,
            'is_active' => true,
        ]);

        return Service::create(array_merge([
            'service_category_id' => $category->id,
            'slug' => 'svc-' . substr(md5((string) random_int(0, PHP_INT_MAX)), 0, 8),
            'name_fr' => 'Français Test',
            'name_en' => 'French Test',
            'price' => '600 $',
            'is_active' => true,
            'order' => 1,
        ], $overrides));
    }

    protected function createLesson(Service $service, array $overrides = []): Lesson
    {
        return Lesson::create(array_merge([
            'service_id' => $service->id,
            'teacher_id' => $this->createUser('teacher')->id,
            'title' => 'Test Lesson',
            'date' => now()->addDays(2)->toDateString(),
            'start_time' => '17:00',
            'end_time' => '20:00',
            'room' => 'A-101',
            'lesson_type' => 'class',
            'status' => 'scheduled',
        ], $overrides));
    }

    public function test_lesson_seeder_creates_default_lessons_in_database(): void
    {
        $this->seed(ServiceCategorySeeder::class);
        $this->seed(ServiceSeeder::class);
        $this->createUser('teacher');
        $this->seed(LessonSeeder::class);

        $this->assertGreaterThanOrEqual(8, Lesson::count());

        $lesson = Lesson::first();
        $this->assertNotNull($lesson->service);
        $this->assertNotNull($lesson->teacher);
        $this->assertEquals('scheduled', $lesson->status);
    }

    public function test_public_lessons_endpoint_returns_lessons_from_database(): void
    {
        $service = $this->createService();
        $this->createLesson($service, ['title' => 'Leçon publique']);

        $response = $this->getJson('/api/v1/lessons');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Leçon publique')
            ->assertJsonPath('data.0.service.name_fr', 'Français Test')
            ->assertJsonPath('data.0.teacher.first_name', 'Teacher');
    }

    public function test_public_lessons_endpoint_hides_cancelled_and_past_lessons(): void
    {
        $service = $this->createService();
        $this->createLesson($service, ['title' => 'Annulée', 'status' => 'cancelled']);
        $this->createLesson($service, ['title' => 'Passée', 'date' => now()->subDays(5)->toDateString()]);

        $this->getJson('/api/v1/lessons')
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_admin_can_create_update_and_delete_lesson(): void
    {
        $admin = $this->createUser('admin');
        $token = $this->apiLogin($admin);
        $service = $this->createService();

        $create = $this->withToken($token)->postJson('/api/v1/admin/lessons', [
            'service_id' => $service->id,
            'title' => 'Nouvelle leçon',
            'date' => now()->addDays(5)->toDateString(),
            'start_time' => '18:00',
            'end_time' => '21:00',
            'room' => 'B-200',
            'lesson_type' => 'class',
        ]);

        $create->assertCreated();
        $lessonId = $create->json('data.id');
        $this->assertDatabaseHas('lessons', ['id' => $lessonId, 'title' => 'Nouvelle leçon', 'status' => 'scheduled']);

        $update = $this->withToken($token)->putJson("/api/v1/admin/lessons/{$lessonId}", [
            'title' => 'Leçon modifiée',
            'room' => 'C-300',
        ]);

        $update->assertOk();
        $this->assertDatabaseHas('lessons', ['id' => $lessonId, 'title' => 'Leçon modifiée', 'room' => 'C-300']);

        $delete = $this->withToken($token)->deleteJson("/api/v1/admin/lessons/{$lessonId}");

        $delete->assertOk();
        $this->assertDatabaseMissing('lessons', ['id' => $lessonId]);
    }

    public function test_admin_can_create_update_and_delete_session(): void
    {
        $admin = $this->createUser('admin');
        $token = $this->apiLogin($admin);
        $teacher = $this->createUser('teacher');
        $student = $this->createUser('student');

        $start = now()->addDays(2)->setTime(18, 0);

        $create = $this->withToken($token)->postJson('/api/v1/admin/sessions', [
            'teacher_id' => $teacher->id,
            'student_id' => $student->id,
            'start_time' => $start->toDateTimeString(),
            'end_time' => $start->copy()->addHour()->toDateTimeString(),
        ]);

        $create->assertCreated();
        $sessionId = $create->json('data.id');
        $this->assertDatabaseHas('teaching_sessions', ['id' => $sessionId, 'status' => 'scheduled']);

        $update = $this->withToken($token)->putJson("/api/v1/admin/sessions/{$sessionId}", [
            'status' => 'in-progress',
            'meeting_link' => 'https://meet.example.com/cultulangues',
        ]);

        $update->assertOk();
        $this->assertDatabaseHas('teaching_sessions', ['id' => $sessionId, 'status' => 'in-progress']);

        $delete = $this->withToken($token)->deleteJson("/api/v1/admin/sessions/{$sessionId}");

        $delete->assertOk();
        $this->assertDatabaseMissing('teaching_sessions', ['id' => $sessionId]);
    }

    public function test_booking_confirmation_creates_payment_enrollment_session_and_invoice(): void
    {
        $admin = $this->createUser('admin');
        $adminToken = $this->apiLogin($admin);
        $student = $this->createUser('student');
        $studentToken = $this->apiLogin($student);
        $service = $this->createService(['price' => '600 $']);

        $booking = $this->tokenRequest('POST', '/api/v1/bookings', $studentToken, [
            'service_id' => $service->id,
            'first_name' => 'Jean',
            'last_name' => 'Tremblay',
            'email' => $student->email,
            'phone' => '+1 514 555 0100',
            'preferred_date' => now()->addDays(3)->toDateString(),
            'preferred_time' => '18:00',
        ]);

        $booking->assertCreated();
        $bookingId = $booking->json('data.booking.id');
        $this->assertDatabaseHas('bookings', ['id' => $bookingId, 'status' => 'pending']);
        $this->assertDatabaseHas('payments', ['booking_id' => $bookingId, 'status' => 'pending', 'amount' => 600.00]);

        $confirm = $this->tokenRequest('PUT', "/api/v1/admin/bookings/{$bookingId}/status", $adminToken, [
            'status' => 'confirmed',
        ]);

        $confirm->assertOk()
            ->assertJsonPath('data.status', 'confirmed');

        $this->assertDatabaseHas('enrollments', [
            'student_id' => $student->id,
            'service_id' => $service->id,
            'status' => 'active',
        ]);
        $this->assertDatabaseHas('teaching_sessions', [
            'booking_id' => $bookingId,
            'student_id' => $student->id,
            'status' => 'scheduled',
        ]);
        $this->assertDatabaseHas('invoices', [
            'booking_id' => $bookingId,
            'student_id' => $student->id,
            'total_amount' => 600.00,
            'status' => 'pending',
        ]);

        $paymentId = Payment::where('booking_id', $bookingId)->value('id');

        $markPaid = $this->tokenRequest('PUT', "/api/v1/admin/payments/{$paymentId}/status", $adminToken, [
            'status' => 'completed',
            'payment_method' => 'card',
        ]);

        $markPaid->assertOk();
        $this->assertDatabaseHas('payments', ['id' => $paymentId, 'status' => 'completed']);
        $this->assertNotNull(Payment::find($paymentId)->paid_at);

        $payments = $this->tokenRequest('GET', '/api/v1/student/payments', $studentToken);
        $payments->assertOk();
        $this->assertTrue(collect($payments->json('data'))->contains('id', $paymentId));
    }

    public function test_student_bookings_are_isolated_from_other_students(): void
    {
        $admin = $this->createUser('admin');
        $adminToken = $this->apiLogin($admin);
        $service = $this->createService();

        $studentA = $this->createUser('student');
        $tokenA = $this->apiLogin($studentA);

        $booking = $this->tokenRequest('POST', '/api/v1/bookings', $tokenA, [
            'service_id' => $service->id,
            'first_name' => 'Alice',
            'last_name' => 'A',
            'email' => $studentA->email,
            'phone' => '+1 514 555 0100',
        ]);

        $booking->assertCreated();
        $bookingId = $booking->json('data.booking.id');

        $this->tokenRequest('PUT', "/api/v1/admin/bookings/{$bookingId}/status", $adminToken, [
            'status' => 'confirmed',
        ])->assertOk();

        $studentB = $this->createUser('student');
        $tokenB = $this->apiLogin($studentB);

        $listB = $this->tokenRequest('GET', '/api/v1/student/bookings', $tokenB);
        $listB->assertOk();
        $this->assertFalse(collect($listB->json('data'))->pluck('id')->contains($bookingId));

        $listA = $this->tokenRequest('GET', '/api/v1/student/bookings', $tokenA);
        $listA->assertOk();
        $this->assertTrue(collect($listA->json('data'))->pluck('id')->contains($bookingId));
    }

    public function test_non_admin_cannot_manage_lessons_or_sessions(): void
    {
        $student = $this->createUser('student');
        $studentToken = $this->apiLogin($student);

        $this->tokenRequest('GET', '/api/v1/admin/lessons', $studentToken)->assertStatus(403);
        $this->tokenRequest('POST', '/api/v1/admin/lessons', $studentToken, [])->assertStatus(403);
        $this->tokenRequest('GET', '/api/v1/admin/sessions', $studentToken)->assertStatus(403);
        $this->tokenRequest('POST', '/api/v1/admin/sessions', $studentToken, [])->assertStatus(403);

        $teacher = $this->createUser('teacher');
        $teacherToken = $this->apiLogin($teacher);

        $this->tokenRequest('DELETE', '/api/v1/admin/lessons/1', $teacherToken)->assertStatus(403);
    }

    public function test_admin_endpoints_require_authentication(): void
    {
        $this->getJson('/api/v1/admin/lessons')->assertStatus(401);
        $this->getJson('/api/v1/admin/sessions')->assertStatus(401);
        $this->postJson('/api/v1/admin/lessons', [])->assertStatus(401);
    }

    public function test_teacher_can_view_own_assigned_lessons(): void
    {
        $service = $this->createService();
        $teacher = $this->createUser('teacher');
        $token = $this->apiLogin($teacher);

        Lesson::create([
            'service_id' => $service->id,
            'teacher_id' => $teacher->id,
            'title' => 'Leçon du prof',
            'date' => now()->addDays(1)->toDateString(),
            'start_time' => '09:00',
            'end_time' => '12:00',
            'status' => 'scheduled',
        ]);

        $this->withToken($token)
            ->getJson('/api/v1/teacher/lessons')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }
}
