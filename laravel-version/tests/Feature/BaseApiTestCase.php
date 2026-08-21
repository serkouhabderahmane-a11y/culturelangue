<?php

namespace Tests\Feature;

use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

abstract class BaseApiTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    protected function createUser(string $role): User
    {
        static $counter = 0;
        $counter++;

        $email = strtolower($role) . '.' . $counter . '@test.ca';

        $user = User::create([
            'first_name' => ucfirst($role),
            'last_name' => 'Test',
            'email' => $email,
            'password' => Hash::make('password'),
            'phone' => '+1 514 555 0000',
            'is_active' => true,
        ]);
        $user->assignRole($role);

        if ($role === 'student') {
            StudentProfile::create([
                'user_id' => $user->id,
                'student_number' => 'STU-' . now()->year . '-' . str_pad((string) (1000 + $user->id), 4, '0', STR_PAD_LEFT),
                'enrollment_date' => now(),
            ]);
        }

        if ($role === 'teacher') {
            TeacherProfile::create([
                'user_id' => $user->id,
                'employee_number' => 'TCH-' . strtoupper(substr(md5((string) $user->id), 0, 5)),
                'department' => 'French',
                'hourly_rate_solo' => 55.00,
                'hourly_rate_group' => 45.00,
            ]);
        }

        return $user;
    }

    protected function apiLogin(User $user): string
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        return $response->json('data.token');
    }
}
