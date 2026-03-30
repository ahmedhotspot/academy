<?php

namespace App\Actions\Admin\Assessments;

use App\Actions\BaseAction;
use App\Models\Assessment;

class UpdateAssessmentAction extends BaseAction
{
    public function handle(array $data): Assessment
    {
        /** @var Assessment $assessment */
        $assessment = $data['assessment'];

        $assessment->update([
            'student_id'          => $data['student_id'],
            'group_id'            => $data['group_id'] ?? null,
            'teacher_id'          => $data['teacher_id'],
            'assessment_date'     => $data['assessment_date'],
            'type'                => $data['type'],
            'memorization_result' => $data['memorization_result'] ?? null,
            'tajweed_result'      => $data['tajweed_result'] ?? null,
            'tadabbur_result'     => $data['tadabbur_result'] ?? null,
            'notes'               => $data['notes'] ?? null,
        ]);

        return $assessment->fresh();
    }
}

