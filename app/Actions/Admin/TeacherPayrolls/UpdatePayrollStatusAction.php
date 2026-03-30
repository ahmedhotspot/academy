<?php

namespace App\Actions\Admin\TeacherPayrolls;

use App\Actions\BaseAction;
use App\Models\TeacherPayroll;

class UpdatePayrollStatusAction extends BaseAction
{
    public function handle(array $data): TeacherPayroll
    {
        /** @var TeacherPayroll $payroll */
        $payroll = $data['payroll'];

        $payroll->update([
            'status' => $data['status'],
        ]);

        return $payroll->fresh();
    }
}

