<?php

namespace App\Actions\Admin\StudentProgressLogs;

use App\Actions\BaseAction;
use App\Models\Group;
use App\Models\Student;
use App\Models\StudentProgressLog;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class CreateStudentProgressLogAction extends BaseAction
{
    public function handle(array $data): StudentProgressLog
    {
        $group = Group::query()->select(['id', 'branch_id'])->findOrFail($data['group_id']);
        $student = Student::query()->select(['id', 'branch_id'])->findOrFail($data['student_id']);
        $teacher = User::query()->select(['id', 'branch_id'])->findOrFail($data['teacher_id']);

        $branchId = $group->branch_id ?? $student->branch_id ?? $teacher->branch_id;

        if (! $branchId) {
            throw ValidationException::withMessages([
                'group_id' => 'تعذر تحديد الفرع الخاص بسجل المتابعة.',
            ]);
        }

        if ((int) $student->branch_id !== (int) $branchId) {
            throw ValidationException::withMessages([
                'student_id' => 'الطالب المختار لا ينتمي إلى نفس فرع الحلقة.',
            ]);
        }

        if ($teacher->branch_id && (int) $teacher->branch_id !== (int) $branchId) {
            throw ValidationException::withMessages([
                'teacher_id' => 'المعلم المختار لا ينتمي إلى نفس فرع الحلقة.',
            ]);
        }

        return StudentProgressLog::query()->create([
            'branch_id'           => $branchId,
            'student_id'          => $data['student_id'],
            'group_id'            => $data['group_id'],
            'teacher_id'          => $data['teacher_id'],
            'progress_date'       => $data['progress_date'],
            'memorization_amount' => $data['memorization_amount'],
            'revision_amount'     => $data['revision_amount'],
            'tajweed_evaluation'  => $data['tajweed_evaluation'],
            'tadabbur_evaluation' => $data['tadabbur_evaluation'],
            'repeated_mistakes'   => $data['repeated_mistakes'] ?? null,
            'mastery_level'       => $data['mastery_level'],
            'commitment_status'   => $data['commitment_status'],
            'notes'               => $data['notes'] ?? null,
        ]);
    }
}
