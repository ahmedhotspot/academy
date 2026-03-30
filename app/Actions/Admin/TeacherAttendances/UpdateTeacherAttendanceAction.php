<?php

namespace App\Actions\Admin\TeacherAttendances;

use App\Actions\BaseAction;
use App\Models\TeacherAttendance;

class UpdateTeacherAttendanceAction extends BaseAction
{
    public function handle(array $data): TeacherAttendance
    {
        /** @var TeacherAttendance $teacherAttendance */
        $teacherAttendance = $data['teacherAttendance'];

        $teacherAttendance->update([
            'teacher_id'      => $data['teacher_id'],
            'attendance_date' => $data['attendance_date'],
            'status'          => $data['status'],
            'notes'           => $data['notes'] ?? null,
        ]);

        return $teacherAttendance->fresh();
    }
}

