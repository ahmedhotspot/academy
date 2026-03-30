<?php

namespace App\Http\Requests\Admin\TeacherPayrolls;

use App\Http\Requests\Admin\AdminRequest;

class UpdateTeacherPayrollRequest extends AdminRequest
{
    public function rules(): array
    {
        return [
            'base_salary'      => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'deduction_amount' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'penalty_amount'   => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'bonus_amount'     => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'notes'            => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'base_salary'      => 'الراتب الأساسي',
            'deduction_amount' => 'الاستقطاع',
            'penalty_amount'   => 'الجزاء',
            'bonus_amount'     => 'المكافأة',
            'notes'            => 'الملاحظات',
        ];
    }
}

