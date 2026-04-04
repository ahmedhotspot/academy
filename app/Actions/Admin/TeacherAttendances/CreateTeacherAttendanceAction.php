<?php

namespace App\Actions\Admin\TeacherAttendances;

use App\Actions\BaseAction;
use App\Models\TeacherAttendance;

class CreateTeacherAttendanceAction extends BaseAction
{
    public function handle(array $data): array
    {
        if (! empty($data['entries']) && is_array($data['entries'])) {
            foreach ($data['entries'] as $entry) {
                TeacherAttendance::query()->create([
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

        TeacherAttendance::query()->create([
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

