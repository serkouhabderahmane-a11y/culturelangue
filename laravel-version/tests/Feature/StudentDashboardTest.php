<?php

namespace Tests\Feature;

use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Notification;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\StudentSkillProgress;
use App\Models\TimeSlot;
use Carbon\Carbon;

class StudentDashboardTest extends BaseApiTestCase
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

    public function test_dashboard_returns_expected_structure(): void
    {
        $student = $this->createUser('student');
        $token = $this->apiLogin($student);

        $this->withToken($token)
            ->getJson('/api/v1/student/dashboard')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'user' => ['full_name', 'initials', 'current_level', 'target_level'],
                    'stats' => [
                        'active_programs', 'upcoming_sessions', 'global_progress',
                        'current_level', 'target_level', 'unread_notifications',
                    ],
                    'enrollments',
                    'upcoming_slots',
                    'next_session',
                    'progress_by_skill',
                    'notifications',
                ],
            ]);
    }

    public function test_dashboard_is_null_safe_without_data(): void
    {
        $student = $this->createUser('student');
        $token = $this->apiLogin($student);

        $response = $this->withToken($token)
            ->getJson('/api/v1/student/dashboard')
            ->assertStatus(200);

        $response->assertJsonPath('data.stats.active_programs', 0)
            ->assertJsonPath('data.stats.upcoming_sessions', 0)
            ->assertJsonPath('data.stats.global_progress', 0)
            ->assertJsonPath('data.next_session', null)
            ->assertJsonCount(0, 'data.notifications')
            ->assertJsonCount(4, 'data.progress_by_skill')
            ->assertJsonPath('data.progress_by_skill.0.skill', 'listening')
            ->assertJsonPath('data.progress_by_skill.0.percentage', 0);
    }

    public function test_dashboard_reflects_student_data(): void
    {
        $student = $this->createUser('student');
        $student->studentProfile->update(['current_level' => 'B2', 'target_level' => 'C1']);
        $teacher = $this->createUser('teacher');
        $token = $this->apiLogin($student);

        $service = $this->createService();

        Enrollment::create([
            'student_id' => $student->id,
            'service_id' => $service->id,
            'start_date' => now()->subDays(10)->toDateString(),
            'end_date' => now()->addDays(20)->toDateString(),
            'progress' => 50,
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        $slot = TimeSlot::create([
            'service_id' => $service->id,
            'slot_date' => Carbon::tomorrow()->toDateString(),
            'slot_time' => '14:00:00',
            'is_available' => false,
            'booked_by' => $student->id,
        ]);

        foreach ([['listening', 80], ['reading', 60], ['speaking', 40], ['writing', 20]] as [$skill, $pct]) {
            StudentSkillProgress::create([
                'student_id' => $student->id,
                'skill' => $skill,
                'percentage' => $pct,
            ]);
        }

        Notification::create([
            'user_id' => $student->id,
            'title' => 'Bienvenue',
            'message' => 'Votre compte est prêt.',
            'type' => 'system',
        ]);

        Grade::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'score' => 88,
            'letter_grade' => 'A-',
        ]);

        $response = $this->withToken($token)
            ->getJson('/api/v1/student/dashboard')
            ->assertStatus(200);

        $response->assertJsonPath('data.user.full_name', $student->full_name)
            ->assertJsonPath('data.user.current_level', 'B2')
            ->assertJsonPath('data.stats.active_programs', 1)
            ->assertJsonPath('data.stats.upcoming_sessions', 1)
            ->assertJsonPath('data.stats.global_progress', 50)
            ->assertJsonPath('data.stats.current_level', 'B2')
            ->assertJsonPath('data.stats.target_level', 'C1')
            ->assertJsonPath('data.stats.unread_notifications', 1)
            ->assertJsonPath('data.next_session.id', $slot->id)
            ->assertJsonPath('data.next_session.service.slug', 'francais-express')
            ->assertJsonCount(4, 'data.progress_by_skill')
            ->assertJsonPath('data.progress_by_skill.0.percentage', 80)
            ->assertJsonCount(1, 'data.notifications')
            ->assertJsonPath('data.notifications.0.title', 'Bienvenue');
    }

    public function test_global_progress_falls_back_to_enrollment_progress(): void
    {
        $student = $this->createUser('student');
        $token = $this->apiLogin($student);

        $service = $this->createService();

        Enrollment::create([
            'student_id' => $student->id,
            'service_id' => $service->id,
            'start_date' => now()->subDays(5)->toDateString(),
            'end_date' => now()->addDays(30)->toDateString(),
            'progress' => 66,
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        $this->withToken($token)
            ->getJson('/api/v1/student/dashboard')
            ->assertJsonPath('data.stats.global_progress', 66);
    }
}
