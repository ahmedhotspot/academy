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
        // Core seeders: required in all environments.
        $this->call([
            RolePermissionSeeder::class,
            BranchesSeeder::class,
            UsersSeeder::class,
            SettingsSeeder::class,
        ]);

        // Demo seeders: run only in local/testing or when SEED_DEMO_DATA=true.
//        $seedDemoData = app()->environment(['local', 'testing']) || (bool) config('app.seed_demo_data', false);
//
//        if (! $seedDemoData) {
//            return;
//        }
//
//        $this->call([
//            StudyCatalogSeeder::class,
//            TeachersSeeder::class,
//            GuardiansSeeder::class,
//            StudentsSeeder::class,
//            GroupsSeeder::class,
//            GroupSchedulesSeeder::class,
//            StudentEnrollmentsSeeder::class,
//            TeacherAttendancesSeeder::class,
//            StudentAttendancesSeeder::class,
//            StudentProgressLogsSeeder::class,
//            AssessmentsSeeder::class,
//            FeePlansSeeder::class,
//            StudentSubscriptionsSeeder::class,
//            PaymentsSeeder::class,
//            TeacherPayrollsSeeder::class,
//            ExpensesSeeder::class,
//        ]);
    }
}
