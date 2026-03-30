<?php

namespace App\Actions\Admin\Students;

use App\Actions\BaseAction;
use App\Models\Student;

class DeleteStudentAction extends BaseAction
{
    public function handle(array $data): bool
    {
        /** @var Student $student */
        $student = $data['student'];

        return (bool) $student->delete();
    }
}

