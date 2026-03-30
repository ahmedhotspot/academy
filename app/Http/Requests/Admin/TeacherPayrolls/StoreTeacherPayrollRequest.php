<?php

namespace App\Http\Requests\Admin\TeacherPayrolls;

use App\Http\Requests\Admin\AdminRequest;

class StoreTeacherPayrollRequest extends AdminRequest
{
    public function rules(): array
    {
        return [
            'teacher_id'           => ['required', 'integer', 'exists:users,id'],
            'month'                => ['required', 'integer', 'min:1', 'max:12'],
            'year'                 => ['required', 'integer', 'min:2000', 'max:2100'],
            'base_salary'          => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'deduction_per_absence' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'deduction_amount'     => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'penalty_amount'       => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'bonus_amount'         => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'notes'                => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'teacher_id'           => 'المعلم',
            'month'                => 'الشهر',
            'year'                 => 'السنة',
            'base_salary'          => 'الراتب الأساسي',
            'deduction_per_absence' => 'الاستقطاع لكل غياب',
            'deduction_amount'     => 'الاستقطاع',
            'penalty_amount'       => 'الجزاء',
            'bonus_amount'         => 'المكافأة',
            'notes'                => 'الملاحظات',
        ];
    }
}

