<?php

namespace App\Http\Requests\Admin\TeacherPayrolls;

use App\Http\Requests\Admin\AdminRequest;
use Illuminate\Validation\Rule;

class StoreTeacherPayrollRequest extends AdminRequest
{
    public function rules(): array
    {
        return [
            'teacher_id'           => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('status', 'active')
                        ->whereNotNull('branch_id');

                    $user = auth()->user();
                    if ($user && ! $user->isSuperAdmin() && $user->branch_id) {
                        $query->where('branch_id', (int) $user->branch_id);
                    }
                }),
                Rule::unique('teacher_payrolls', 'teacher_id')
                    ->where(fn ($query) => $query
                        ->where('month', $this->integer('month'))
                        ->where('year', $this->integer('year'))
                    ),
            ],
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

    public function messages(): array
    {
        return [
            'teacher_id.unique' => 'تم حساب مستحق لهذا المعلم في نفس الشهر والسنة مسبقاً.',
            'teacher_id.exists' => 'المعلم المحدد غير متاح أو غير مرتبط بفرع.',
        ];
    }
}

