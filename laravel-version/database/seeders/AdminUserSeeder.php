<?php

namespace Database\Seeders;

use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@cultulangues.ca'],
            [
                'first_name' => 'Admin',
                'last_name' => 'Cultulangues',
                'password' => Hash::make('password'),
                'phone' => '+1 514 555 0100',
                'is_active' => true,
            ],
        );
        $admin->assignRole('admin');

        $teacher = User::updateOrCreate(
            ['email' => 'teacher@cultulangues.ca'],
            [
                'first_name' => 'Marie',
                'last_name' => 'Dubois',
                'password' => Hash::make('password'),
                'phone' => '+1 514 555 0101',
                'is_active' => true,
            ],
        );
        $teacher->assignRole('teacher');
        TeacherProfile::updateOrCreate(
            ['user_id' => $teacher->id],
            [
                'employee_number' => 'TCH-' . Str::upper(Str::random(5)),
                'department' => 'French',
                'hourly_rate_solo' => 55.00,
                'hourly_rate_group' => 45.00,
                'contract_hours_month' => 80,
                'hire_date' => now()->subYears(3),
            ],
        );

        $student = User::updateOrCreate(
            ['email' => 'student@cultulangues.ca'],
            [
                'first_name' => 'Jean',
                'last_name' => 'Tremblay',
                'password' => Hash::make('password'),
                'phone' => '+1 514 555 0102',
                'is_active' => true,
            ],
        );
        $student->assignRole('student');
        StudentProfile::updateOrCreate(
            ['user_id' => $student->id],
            [
                'student_number' => 'STU-' . now()->year . '-' . str_pad((string) (1000 + $student->id), 4, '0', STR_PAD_LEFT),
                'native_language' => 'en',
                'current_level' => 'A1',
                'target_level' => 'B1',
                'goal' => 'General French',
                'enrollment_date' => now(),
                'preferred_language' => 'fr',
            ],
        );
    }
}
