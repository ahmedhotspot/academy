<?php

namespace App\Actions\Admin\Assessments;

use App\Actions\BaseAction;
use App\Models\Assessment;

class DeleteAssessmentAction extends BaseAction
{
    public function handle(array $data): bool
    {
        /** @var Assessment $assessment */
        $assessment = $data['assessment'];

        return (bool) $assessment->delete();
    }
}

