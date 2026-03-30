<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            BranchesSeeder::class,
            UsersSeeder::class,
            StudyLevelsSeeder::class,
            StudyTracksSeeder::class,
            TeachersSeeder::class,
            GuardiansSeeder::class,
            StudentsSeeder::class,
            GroupsSeeder::class,
            GroupSchedulesSeeder::class,
            StudentEnrollmentsSeeder::class,
            StudentAttendancesSeeder::class,
            TeacherAttendancesSeeder::class,
            StudentProgressLogsSeeder::class,
            AssessmentsSeeder::class,
            FeePlansSeeder::class,
            StudentSubscriptionsSeeder::class,
            PaymentsSeeder::class,
            TeacherPayrollsSeeder::class,
            ExpensesSeeder::class,
            SettingsSeeder::class,
        ]);
    }
}
