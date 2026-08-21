<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'students.view', 'students.create', 'students.update', 'students.delete', 'students.export',
            'teachers.view', 'teachers.create', 'teachers.update', 'teachers.delete',
            'programs.view', 'programs.create', 'programs.update', 'programs.delete',
            'lessons.view', 'lessons.create', 'lessons.update', 'lessons.delete',
            'attendance.view', 'attendance.mark',
            'tests.view', 'tests.create',
            'grades.view', 'grades.enter',
            'bookings.view', 'bookings.confirm', 'bookings.cancel',
            'payments.view', 'payments.refund',
            'reports.view', 'reports.export',
            'settings.view', 'settings.update',
            'cms.view', 'cms.update',
            'notifications.view',
            'support.view', 'support.reply',
            'calendar.view', 'calendar.create',
            'enrollments.view', 'enrollments.create', 'enrollments.update',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $admin = Role::findOrCreate('admin', 'web');
        $admin->syncPermissions(Permission::all());

        $teacher = Role::findOrCreate('teacher', 'web');
        $teacher->syncPermissions([
            'students.view', 'students.update',
            'teachers.view', 'teachers.update',
            'programs.view',
            'lessons.view', 'lessons.update',
            'attendance.view', 'attendance.mark',
            'tests.view', 'tests.create',
            'grades.view', 'grades.enter',
            'notifications.view',
            'calendar.view',
        ]);

        $student = Role::findOrCreate('student', 'web');
        $student->syncPermissions([
            'students.view', 'students.update',
            'programs.view',
            'lessons.view',
            'attendance.view',
            'tests.view',
            'grades.view',
            'bookings.view',
            'payments.view',
            'notifications.view',
            'support.view', 'support.reply',
            'calendar.view',
        ]);
    }
}
