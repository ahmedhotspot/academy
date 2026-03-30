<?php

namespace App\Actions\Admin\TeacherAttendances;

use App\Actions\BaseAction;
use App\Models\TeacherAttendance;

class CreateTeacherAttendanceAction extends BaseAction
{
    public function handle(array $data): TeacherAttendance
    {
        return TeacherAttendance::query()->create([
            'teacher_id'      => $data['teacher_id'],
            'attendance_date' => $data['attendance_date'],
            'status'          => $data['status'],
            'notes'           => $data['notes'] ?? null,
        ]);
    }
}

