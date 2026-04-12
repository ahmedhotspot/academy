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
            $created = 0;
            $updated = 0;

            foreach ($data['entries'] as $entry) {
                $teacher = User::query()->findOrFail($entry['teacher_id']);

                $attendance = TeacherAttendance::query()->updateOrCreate(
                    [
                        'teacher_id' => $entry['teacher_id'],
                        'attendance_date' => $data['attendance_date'],
                    ],
                    [
                        'branch_id' => $teacher->branch_id,
                        'status' => $entry['status'],
                        'notes' => $entry['notes'] ?? null,
                    ]
                );

                if ($attendance->wasRecentlyCreated) {
                    $created++;
                } else {
                    $updated++;
                }
            }

            return [
                'processed' => count($data['entries']),
                'created' => $created,
                'updated' => $updated,
            ];
        }

        $teacher = User::query()->findOrFail($data['teacher_id']);

        $attendance = TeacherAttendance::query()->updateOrCreate(
            [
                'teacher_id' => $data['teacher_id'],
                'attendance_date' => $data['attendance_date'],
            ],
            [
                'branch_id' => $teacher->branch_id,
                'status' => $data['status'],
                'notes' => $data['notes'] ?? null,
            ]
        );

        return [
            'processed' => 1,
            'created' => $attendance->wasRecentlyCreated ? 1 : 0,
            'updated' => $attendance->wasRecentlyCreated ? 0 : 1,
        ];
    }
}

