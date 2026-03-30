<?php

namespace App\Actions\Admin\StudentProgressLogs;

use App\Actions\BaseAction;
use App\Models\StudentProgressLog;

class CreateStudentProgressLogAction extends BaseAction
{
    public function handle(array $data): StudentProgressLog
    {
        return StudentProgressLog::query()->create([
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

