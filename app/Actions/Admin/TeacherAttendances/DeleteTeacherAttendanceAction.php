<?php

namespace App\Actions\Admin\TeacherAttendances;

use App\Actions\BaseAction;
use App\Models\TeacherAttendance;

class DeleteTeacherAttendanceAction extends BaseAction
{
    public function handle(array $data): bool
    {
        /** @var TeacherAttendance $teacherAttendance */
        $teacherAttendance = $data['teacherAttendance'];

        return (bool) $teacherAttendance->delete();
    }
}

