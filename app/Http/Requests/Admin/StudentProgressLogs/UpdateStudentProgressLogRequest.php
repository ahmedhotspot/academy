<?php

namespace App\Http\Requests\Admin\StudentProgressLogs;

use App\Http\Requests\Admin\AdminRequest;
use App\Models\StudentProgressLog;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

class UpdateStudentProgressLogRequest extends AdminRequest
{
    private function teacherRule(): Exists
    {
        $user = auth()->user();

        return Rule::exists('users', 'id')->where(function ($query) use ($user) {
            $query->whereIn('id', function ($subQuery) {
                $subQuery->select('model_id')
                    ->from('model_has_roles')
                    ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                    ->where('model_has_roles.model_type', User::class)
                    ->where('roles.name', 'المعلم');
            });

            if ($user && ! $user->isSuperAdmin() && $user->branch_id) {
                $query->where('branch_id', $user->branch_id);
            }
        });
    }

    public function rules(): array
    {
        return [
            'student_id'          => ['required', 'integer', 'exists:students,id'],
            'group_id'            => ['required', 'integer', 'exists:groups,id'],
            'teacher_id'          => ['required', 'integer', $this->teacherRule()],
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
