<?php

namespace App\Actions\Admin\TeacherAttendances;

use App\Actions\BaseAction;
use App\Models\TeacherAttendance;
use App\Models\User;

class CreateTeacherAttendanceAction extends BaseAction
{
    public function handle(array $data): array
    {
        if (! empty($data['entries']) && is_array($data['entries'])) {
            foreach ($data['entries'] as $entry) {
                $teacher = User::query()->findOrFail($entry['teacher_id']);

                TeacherAttendance::query()->create([
                    'branch_id'       => $teacher->branch_id,
                    'teacher_id'      => $entry['teacher_id'],
                    'attendance_date' => $data['attendance_date'],
                    'status'          => $entry['status'],
                    'notes'           => $entry['notes'] ?? null,
                ]);
            }

            return [
                'processed' => count($data['entries']),
                'created' => count($data['entries']),
                'updated' => 0,
            ];
        }

        $teacher = User::query()->findOrFail($data['teacher_id']);

        TeacherAttendance::query()->create([
            'branch_id'       => $teacher->branch_id,
            'teacher_id'      => $data['teacher_id'],
            'attendance_date' => $data['attendance_date'],
            'status'          => $data['status'],
            'notes'           => $data['notes'] ?? null,
        ]);

        return [
            'processed' => 1,
            'created' => 1,
            'updated' => 0,
        ];
    }
}

