<?php

namespace Tests\Feature;

use App\Models\AttendanceRecord;
use App\Models\Grade;
use App\Models\Lesson;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Test;
use App\Models\TeacherAvailability;

class TeacherPortalTest extends BaseApiTestCase
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

    public function test_teacher_dashboard(): void
    {
        $teacher = $this->createUser('teacher');
        $token = $this->apiLogin($teacher);

        $service = $this->createService();
        Lesson::create([
            'service_id' => $service->id,
            'teacher_id' => $teacher->id,
            'date' => now()->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
        ]);

        $this->withToken($token)
            ->getJson('/api/v1/teacher/dashboard')
            ->assertStatus(200)
            ->assertJsonPath('data.stats.lessons_today', 1)
            ->assertJsonStructure([
                'data' => ['stats', 'today_lessons', 'upcoming_lessons'],
            ]);
    }

    public function test_teacher_can_mark_attendance(): void
    {
        $teacher = $this->createUser('teacher');
        $student = $this->createUser('student');
        $token = $this->apiLogin($teacher);

        $service = $this->createService();
        $lesson = Lesson::create([
            'service_id' => $service->id,
            'teacher_id' => $teacher->id,
            'date' => '2026-08-10',
            'start_time' => '18:00:00',
            'end_time' => '19:00:00',
        ]);

        $this->withToken($token)->postJson("/api/v1/teacher/lessons/{$lesson->id}/attendance", [
            'attendance' => [
                ['student_id' => $student->id, 'status' => 'present'],
            ],
        ])->assertStatus(200);

        $this->assertDatabaseHas('attendance_records', [
            'lesson_id' => $lesson->id,
            'student_id' => $student->id,
            'status' => 'present',
        ]);
    }

    public function test_teacher_cannot_mark_attendance_for_other_teachers_lesson(): void
    {
        $teacher = $this->createUser('teacher');
        $otherTeacher = $this->createUser('teacher');
        $student = $this->createUser('student');
        $token = $this->apiLogin($teacher);

        $service = $this->createService();
        $lesson = Lesson::create([
            'service_id' => $service->id,
            'teacher_id' => $otherTeacher->id,
            'date' => '2026-08-10',
            'start_time' => '18:00:00',
            'end_time' => '19:00:00',
        ]);

        $this->withToken($token)->postJson("/api/v1/teacher/lessons/{$lesson->id}/attendance", [
            'attendance' => [
                ['student_id' => $student->id, 'status' => 'present'],
            ],
        ])->assertStatus(404);
    }

    public function test_teacher_can_enter_grade_for_own_lesson(): void
    {
        $teacher = $this->createUser('teacher');
        $student = $this->createUser('student');
        $token = $this->apiLogin($teacher);

        $service = $this->createService();
        $lesson = Lesson::create([
            'service_id' => $service->id,
            'teacher_id' => $teacher->id,
            'date' => '2026-08-10',
            'start_time' => '18:00:00',
            'end_time' => '19:00:00',
        ]);

        $this->withToken($token)->postJson('/api/v1/teacher/grades', [
            'student_id' => $student->id,
            'lesson_id' => $lesson->id,
            'score' => 90,
            'letter_grade' => 'A',
        ])->assertStatus(201);

        $this->assertDatabaseHas('grades', [
            'student_id' => $student->id,
            'lesson_id' => $lesson->id,
            'score' => '90.00',
        ]);
    }

    public function test_teacher_can_create_test(): void
    {
        $teacher = $this->createUser('teacher');
        $token = $this->apiLogin($teacher);

        $service = $this->createService();
        Lesson::create([
            'service_id' => $service->id,
            'teacher_id' => $teacher->id,
            'date' => '2026-08-10',
            'start_time' => '18:00:00',
            'end_time' => '19:00:00',
        ]);

        $this->withToken($token)->postJson('/api/v1/teacher/tests', [
            'service_id' => $service->id,
            'name' => 'Midterm',
            'test_type' => 'course',
            'total_score' => 100,
            'duration' => 60,
        ])->assertStatus(201);

        $this->assertDatabaseHas('tests', ['name' => 'Midterm']);
    }

    public function test_teacher_can_update_availability(): void
    {
        $teacher = $this->createUser('teacher');
        $token = $this->apiLogin($teacher);

        $this->withToken($token)->putJson('/api/v1/teacher/availability', [
            'slots' => [
                ['day_of_week' => 1, 'start_time' => '09:00', 'end_time' => '12:00'],
                ['day_of_week' => 3, 'start_time' => '13:00', 'end_time' => '16:00'],
            ],
        ])->assertStatus(200);

        $this->assertDatabaseCount('teacher_availabilities', 2);
    }

    public function test_student_cannot_access_teacher_endpoints(): void
    {
        $student = $this->createUser('student');
        $token = $this->apiLogin($student);

        $this->withToken($token)
            ->getJson('/api/v1/teacher/dashboard')
            ->assertStatus(403);
    }
}
