<?php

namespace Tests\Feature;

use App\Models\Grade;
use App\Models\Lesson;
use App\Models\Notification;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\SupportTicket;

class StudentPortalTest extends BaseApiTestCase
{
    protected function createService(): Service
    {
        $category = ServiceCategory::create([
            'slug' => 'parcours-linguistique',
            'name_fr' => 'Parcours linguistique',
            'name_en' => 'Linguistic Pathway',
            'order' => 1,
            'is_active' => true,
        ]);

        return Service::create([
            'service_category_id' => $category->id,
            'slug' => 'francais-express',
            'name_fr' => 'Français Express',
            'name_en' => 'Français Express',
            'order' => 1,
            'is_active' => true,
        ]);
    }

    public function test_student_dashboard(): void
    {
        $student = $this->createUser('student');
        $token = $this->apiLogin($student);

        $this->withToken($token)
            ->getJson('/api/v1/student/dashboard')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['stats', 'enrollments', 'upcoming_slots'],
            ]);
    }

    public function test_student_grades(): void
    {
        $student = $this->createUser('student');
        $teacher = $this->createUser('teacher');
        $token = $this->apiLogin($student);

        $service = $this->createService();
        $lesson = Lesson::create([
            'service_id' => $service->id,
            'teacher_id' => $teacher->id,
            'date' => '2026-08-10',
            'start_time' => '18:00:00',
            'end_time' => '19:00:00',
        ]);

        Grade::create([
            'student_id' => $student->id,
            'lesson_id' => $lesson->id,
            'teacher_id' => $teacher->id,
            'score' => 85,
            'letter_grade' => 'B+',
        ]);

        $this->withToken($token)
            ->getJson('/api/v1/student/grades')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.score', '85.00');
    }

    public function test_student_notifications(): void
    {
        $student = $this->createUser('student');
        $token = $this->apiLogin($student);

        Notification::create([
            'user_id' => $student->id,
            'title' => 'Welcome',
            'message' => 'Welcome to Cultulangues!',
            'type' => 'system',
        ]);

        $this->withToken($token)
            ->getJson('/api/v1/student/notifications')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');

        $this->withToken($token)
            ->postJson('/api/v1/student/notifications/read-all')
            ->assertStatus(200);

        $this->assertDatabaseHas('notifications', ['user_id' => $student->id, 'is_read' => true]);
    }

    public function test_student_can_open_support_ticket(): void
    {
        $student = $this->createUser('student');
        $token = $this->apiLogin($student);

        $this->withToken($token)->postJson('/api/v1/student/tickets', [
            'subject' => 'Placement test',
            'category' => 'tests',
            'message' => 'When can I take the placement test?',
        ])->assertStatus(201);

        $this->assertDatabaseCount('support_tickets', 1);
        $this->assertDatabaseCount('support_messages', 1);
    }

    public function test_student_cannot_view_others_tickets(): void
    {
        $student = $this->createUser('student');
        $other = $this->createUser('student');

        $ticket = SupportTicket::create([
            'student_id' => $other->id,
            'subject' => 'Private',
            'status' => 'open',
        ]);

        $token = $this->apiLogin($student);

        $this->withToken($token)
            ->getJson('/api/v1/student/tickets')
            ->assertStatus(200)
            ->assertJsonCount(0, 'data');

        $this->assertFalse($ticket->student_id === $student->id);
    }
}
