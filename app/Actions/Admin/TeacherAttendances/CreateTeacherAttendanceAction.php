<?php

namespace App\Actions\Admin\TeacherAttendances;

use App\Actions\BaseAction;
use App\Models\TeacherAttendance;
use Illuminate\Support\Facades\DB;

class CreateTeacherAttendanceAction extends BaseAction
{
    public function handle(array $data): array
    {
        return DB::transaction(function () use ($data) {
            if (! empty($data['entries']) && is_array($data['entries'])) {
                $created = 0;
                $updated = 0;

                foreach ($data['entries'] as $entry) {
                    $attendance = TeacherAttendance::query()->firstOrNew([
                        'teacher_id' => $entry['teacher_id'],
                        'attendance_date' => $data['attendance_date'],
                    ]);

                    $wasExisting = $attendance->exists;

                    $attendance->fill([
                        'status' => $entry['status'],
                        'notes' => $entry['notes'] ?? null,
                    ]);

                    $attendance->save();

                    $wasExisting ? $updated++ : $created++;
                }

                return [
                    'processed' => $created + $updated,
                    'created' => $created,
                    'updated' => $updated,
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
        });
    }
}

