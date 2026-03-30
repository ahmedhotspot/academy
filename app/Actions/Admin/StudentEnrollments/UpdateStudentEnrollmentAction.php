<?php

namespace App\Actions\Admin\StudentEnrollments;

use App\Actions\BaseAction;
use App\Models\StudentEnrollment;

class UpdateStudentEnrollmentAction extends BaseAction
{
    public function handle(array $data): StudentEnrollment
    {
        /** @var StudentEnrollment $studentEnrollment */
        $studentEnrollment = $data['studentEnrollment'];
        unset($data['studentEnrollment']);

        $targetGroupId = (int) $data['group_id'];

        if ($targetGroupId !== (int) $studentEnrollment->group_id) {
            $studentEnrollment->update(['status' => 'transferred']);

            return StudentEnrollment::query()->create([
                'student_id' => $studentEnrollment->student_id,
                'group_id' => $targetGroupId,
                'status' => $data['status'] === 'transferred' ? 'active' : $data['status'],
            ]);
        }

        $studentEnrollment->update($data);

        return $studentEnrollment;
    }
}

