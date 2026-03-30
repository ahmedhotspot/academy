<?php

namespace Database\Seeders;

use App\Models\TeacherPayroll;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TeacherPayrollsSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = User::query()->role('المعلم')->where('status', 'active')->get();

        if ($teachers->isEmpty()) {
            $this->command->warn('لا يوجد معلمون لإنشاء مستحقات الرواتب.');
            return;
        }

        $months = [
            Carbon::now()->subMonths(2),
            Carbon::now()->subMonth(),
            Carbon::now(),
        ];

        $count = 0;

        foreach ($teachers as $teacher) {
            foreach ($months as $monthDate) {
                $baseSalary = random_int(2800, 4500);
                $deduction = random_int(0, 250);
                $penalty = random_int(0, 200);
                $bonus = random_int(0, 400);
                $finalAmount = max(0, $baseSalary - $deduction - $penalty + $bonus);

                TeacherPayroll::query()->updateOrCreate(
                    [
                        'teacher_id' => $teacher->id,
                        'month' => $monthDate->month,
                        'year' => $monthDate->year,
                    ],
                    [
                        'base_salary' => $baseSalary,
                        'deduction_amount' => $deduction,
                        'penalty_amount' => $penalty,
                        'bonus_amount' => $bonus,
                        'final_amount' => $finalAmount,
                        'status' => $monthDate->isCurrentMonth() ? 'غير مصروف' : 'مصروف',
                        'notes' => null,
                    ]
                );

                $count++;
            }
        }

        $this->command->info("تم إنشاء {$count} سجل مستحقات معلمين بنجاح.");
    }
}

