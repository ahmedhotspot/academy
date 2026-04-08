<?php

namespace App\Actions\Admin\TeacherPayrolls;

use App\Actions\BaseAction;
use App\Models\TeacherAttendance;
use App\Models\TeacherPayroll;

class CreateTeacherPayrollAction extends BaseAction
{
    public function handle(array $data): TeacherPayroll
    {
        $existingPayroll = TeacherPayroll::query()
            ->where('teacher_id', $data['teacher_id'])
            ->where('month', $data['month'])
            ->where('year', $data['year'])
            ->first();

        if ($existingPayroll) {
            return $existingPayroll;
        }

        // حساب الاستقطاع من الغياب
        $deductionAmount = $this->calculateDeductionFromAbsences(
            $data['teacher_id'],
            $data['month'],
            $data['year'],
            $data['deduction_per_absence'] ?? 0
        );

        // حساب المبلغ النهائي
        $finalAmount = $data['base_salary'] - $deductionAmount - ($data['penalty_amount'] ?? 0) + ($data['bonus_amount'] ?? 0);

        return TeacherPayroll::query()->create([
            'teacher_id'      => $data['teacher_id'],
            'month'           => $data['month'],
            'year'            => $data['year'],
            'base_salary'     => $data['base_salary'],
            'deduction_amount' => $deductionAmount,
            'penalty_amount'  => $data['penalty_amount'] ?? 0,
            'bonus_amount'    => $data['bonus_amount'] ?? 0,
            'final_amount'    => $finalAmount,
            'status'          => 'غير مصروف',
            'notes'           => $data['notes'] ?? null,
        ]);
    }

    /**
     * حساب الاستقطاع من الغياب
     */
    private function calculateDeductionFromAbsences(int $teacherId, int $month, int $year, float $deductionPerAbsence = 0): float
    {
        if ($deductionPerAbsence <= 0) {
            return 0;
        }

        $absenceCount = TeacherAttendance::query()
            ->where('teacher_id', $teacherId)
            ->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)
            ->where('status', 'غائب')
            ->count();

        return $absenceCount * $deductionPerAbsence;
    }
}

