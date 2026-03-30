<?php

namespace App\Http\Requests\Admin\Assessments;

use App\Http\Requests\Admin\AdminRequest;
use App\Models\Assessment;
use Illuminate\Validation\Rule;

class StoreAssessmentRequest extends AdminRequest
{
    public function rules(): array
    {
        return [
            'student_id'          => ['required', 'integer', 'exists:students,id'],
            'group_id'            => ['nullable', 'integer', 'exists:groups,id'],
            'teacher_id'          => ['required', 'integer', 'exists:users,id'],
            'assessment_date'     => ['required', 'date'],
            'type'                => ['required', Rule::in(Assessment::TYPES)],
            'memorization_result' => ['nullable', 'numeric', 'min:0', 'max:' . Assessment::MAX_SCORE],
            'tajweed_result'      => ['nullable', 'numeric', 'min:0', 'max:' . Assessment::MAX_SCORE],
            'tadabbur_result'     => ['nullable', 'numeric', 'min:0', 'max:' . Assessment::MAX_SCORE],
            'notes'               => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'student_id'          => 'الطالب',
            'group_id'            => 'الحلقة',
            'teacher_id'          => 'المعلم',
            'assessment_date'     => 'تاريخ الاختبار',
            'type'                => 'نوع الاختبار',
            'memorization_result' => 'نتيجة الحفظ',
            'tajweed_result'      => 'نتيجة التجويد',
            'tadabbur_result'     => 'نتيجة التدبر',
            'notes'               => 'الملاحظات',
        ];
    }
}

