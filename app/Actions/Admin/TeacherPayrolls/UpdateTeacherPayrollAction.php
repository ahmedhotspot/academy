<?php

namespace App\Actions\Admin\TeacherPayrolls;

use App\Actions\BaseAction;
use App\Models\TeacherPayroll;

class UpdateTeacherPayrollAction extends BaseAction
{
    public function handle(array $data): TeacherPayroll
    {
        /** @var TeacherPayroll $payroll */
        $payroll = $data['payroll'];

        // حساب المبلغ النهائي
        $finalAmount = $data['base_salary'] - ($data['deduction_amount'] ?? 0) - ($data['penalty_amount'] ?? 0) + ($data['bonus_amount'] ?? 0);

        $payroll->update([
            'base_salary'      => $data['base_salary'],
            'deduction_amount' => $data['deduction_amount'] ?? 0,
            'penalty_amount'   => $data['penalty_amount'] ?? 0,
            'bonus_amount'     => $data['bonus_amount'] ?? 0,
            'final_amount'     => $finalAmount,
            'notes'            => $data['notes'] ?? null,
        ]);

        return $payroll->fresh();
    }
}

