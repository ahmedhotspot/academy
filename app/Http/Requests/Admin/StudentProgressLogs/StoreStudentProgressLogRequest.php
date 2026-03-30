<?php

namespace App\Http\Requests\Admin\StudentProgressLogs;

use App\Http\Requests\Admin\AdminRequest;
use App\Models\StudentProgressLog;
use Illuminate\Validation\Rule;

class StoreStudentProgressLogRequest extends AdminRequest
{
    public function rules(): array
    {
        return [
            'student_id'          => ['required', 'integer', 'exists:students,id'],
            'group_id'            => ['required', 'integer', 'exists:groups,id'],
            'teacher_id'          => ['required', 'integer', 'exists:users,id'],
            'progress_date'       => ['required', 'date'],
            'memorization_amount' => ['required', 'string', 'max:500'],
            'revision_amount'     => ['required', 'string', 'max:500'],
            'tajweed_evaluation'  => ['required', Rule::in(StudentProgressLog::EVALUATION_LEVELS)],
            'tadabbur_evaluation' => ['required', Rule::in(StudentProgressLog::EVALUATION_LEVELS)],
            'repeated_mistakes'   => ['nullable', 'string', 'max:2000'],
            'mastery_level'       => ['required', Rule::in(StudentProgressLog::EVALUATION_LEVELS)],
            'commitment_status'   => ['required', Rule::in(StudentProgressLog::COMMITMENT_STATUSES)],
            'notes'               => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'student_id'          => 'الطالب',
            'group_id'            => 'الحلقة',
            'teacher_id'          => 'المعلم',
            'progress_date'       => 'تاريخ المتابعة',
            'memorization_amount' => 'مقدار الحفظ',
            'revision_amount'     => 'مقدار المراجعة',
            'tajweed_evaluation'  => 'تقييم التجويد',
            'tadabbur_evaluation' => 'تقييم التدبر',
            'repeated_mistakes'   => 'الأخطاء المتكررة',
            'mastery_level'       => 'مستوى الإتقان',
            'commitment_status'   => 'حالة الالتزام',
            'notes'               => 'الملاحظات',
        ];
    }
}

