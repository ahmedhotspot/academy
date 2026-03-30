<?php

namespace App\Actions\Admin\StudentEnrollments;

use App\Actions\BaseAction;
use App\Models\StudentEnrollment;

class CreateStudentEnrollmentAction extends BaseAction
{
    public function handle(array $data): StudentEnrollment
    {
        StudentEnrollment::query()
            ->where('student_id', $data['student_id'])
            ->where('status', 'active')
            ->update(['status' => 'transferred']);

        if ($data['status'] === 'transferred') {
            $data['status'] = 'active';
        }

        return StudentEnrollment::query()->create($data);
    }
}

