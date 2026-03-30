<?php

namespace App\Actions\Admin\StudentEnrollments;

use App\Actions\BaseAction;
use App\Models\StudentEnrollment;

class DeleteStudentEnrollmentAction extends BaseAction
{
    public function handle(array $data): bool
    {
        /** @var StudentEnrollment $studentEnrollment */
        $studentEnrollment = $data['studentEnrollment'];

        return (bool) $studentEnrollment->delete();
    }
}

