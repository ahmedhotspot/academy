<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'branches.view',
            'branches.create',
            'branches.update',
            'branches.delete',
            'students.view',
            'students.create',
            'students.update',
            'students.delete',
            'guardians.view',
            'guardians.create',
            'guardians.update',
            'guardians.delete',
            'study-levels.view',
            'study-levels.create',
            'study-levels.update',
            'study-levels.delete',
            'study-tracks.view',
            'study-tracks.create',
            'study-tracks.update',
            'study-tracks.delete',
            'groups.view',
            'groups.create',
            'groups.update',
            'groups.delete',
            'group-schedules.view',
            'group-schedules.create',
            'group-schedules.update',
            'group-schedules.delete',
            'student-enrollments.view',
            'student-enrollments.create',
            'student-enrollments.update',
            'student-enrollments.delete',
            'teacher-attendances.view',
            'teacher-attendances.create',
            'teacher-attendances.update',
            'teacher-attendances.delete',
            'student-attendances.view',
            'student-attendances.create',
            'student-attendances.update',
            'student-attendances.delete',
            'student-progress-logs.view',
            'student-progress-logs.create',
            'student-progress-logs.update',
            'student-progress-logs.delete',
            'assessments.view',
            'assessments.create',
            'assessments.update',
            'assessments.delete',
            'fee-plans.view',
            'fee-plans.create',
            'fee-plans.update',
            'fee-plans.delete',
            'student-subscriptions.view',
            'student-subscriptions.create',
            'student-subscriptions.update',
            'student-subscriptions.delete',
            'payments.view',
            'payments.create',
            'payments.update',
            'payments.delete',
            'teacher-payrolls.view',
            'teacher-payrolls.create',
            'teacher-payrolls.update',
            'expenses.view',
            'expenses.create',
            'expenses.update',
            'expenses.delete',
            'teachers.view',
            'teachers.create',
            'teachers.update',
            'teachers.delete',
            'attendance.manage',
            'progress.manage',
            'assessments.manage',
            'fees.view',
            'payments.create',
            'receipts.view',
            'receipts.create',
            'payroll.view',
            'expenses.view',
            'reports.view',
            'settings.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $superAdminRole = Role::findOrCreate('المشرف العام', 'web');
        $secretaryRole = Role::findOrCreate('السكرتيرة', 'web');
        $teacherRole = Role::findOrCreate('المعلم', 'web');

        $superAdminRole->syncPermissions(Permission::all());

        $secretaryRole->syncPermissions(
            Permission::query()
                ->whereNotIn('name', ['users.view', 'users.create', 'users.update', 'users.delete'])
                ->pluck('name')
                ->all()
        );

        $teacherRole->syncPermissions([
            'students.view',
            'study-levels.view',
            'study-tracks.view',
            'groups.view',
            'groups.create',
            'groups.update',
            'group-schedules.view',
            'group-schedules.create',
            'group-schedules.update',
            'student-enrollments.view',
            'student-enrollments.create',
            'student-enrollments.update',
            'teacher-attendances.view',
            'teacher-attendances.create',
            'teacher-attendances.update',
            'student-attendances.view',
            'student-attendances.create',
            'student-attendances.update',
            'student-progress-logs.view',
            'student-progress-logs.create',
            'student-progress-logs.update',
            'assessments.view',
            'assessments.create',
            'assessments.update',
            'attendance.manage',
            'progress.manage',
            'assessments.manage',
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}

