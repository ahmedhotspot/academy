<?php

namespace App\Actions\Admin\StudentAttendances;

use App\Actions\BaseAction;
use App\Models\Student;
use App\Models\StudentAttendance;

class CreateStudentAttendanceAction extends BaseAction
{
    public function handle(array $data): array
    {
        $created = 0;
        $updated = 0;

        foreach ($data['entries'] as $entry) {
            $student = Student::query()->findOrFail($entry['student_id']);

            $attendance = StudentAttendance::query()->updateOrCreate(
                [
                    'student_id' => $entry['student_id'],
                    'attendance_date' => $data['attendance_date'],
                ],
                [
                    'branch_id' => $student->branch_id,
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
}

