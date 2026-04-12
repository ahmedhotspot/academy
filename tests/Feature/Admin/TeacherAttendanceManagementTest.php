<?php

use App\Actions\Admin\TeacherAttendances\CreateTeacherAttendanceAction;
use App\Models\Branch;
use App\Models\TeacherAttendance;
use App\Models\User;

it('does not create duplicate teacher attendance for the same teacher and date in daily sheet mode', function () {
    $branch = Branch::factory()->create();
    $teacher = User::factory()->create(['branch_id' => $branch->id]);

    TeacherAttendance::query()->create([
        'branch_id' => $branch->id,
        'teacher_id' => $teacher->id,
        'attendance_date' => '2026-04-12',
        'status' => 'حاضر',
        'notes' => 'initial',
    ]);

    $result = app(CreateTeacherAttendanceAction::class)->handle([
        'attendance_date' => '2026-04-12',
        'entries' => [
            [
                'teacher_id' => $teacher->id,
                'status' => 'متأخر',
                'notes' => 'updated',
            ],
        ],
    ]);

    expect($result['processed'])->toBe(1)
        ->and($result['created'])->toBe(0)
        ->and($result['updated'])->toBe(1);

    $records = TeacherAttendance::query()
        ->where('teacher_id', $teacher->id)
        ->where('attendance_date', '2026-04-12')
        ->get();

    expect($records)->toHaveCount(1)
        ->and($records->first()->status)->toBe('متأخر')
        ->and($records->first()->notes)->toBe('updated');
});

it('does not create duplicate teacher attendance for the same teacher and date in single mode', function () {
    $branch = Branch::factory()->create();
    $teacher = User::factory()->create(['branch_id' => $branch->id]);

    $action = app(CreateTeacherAttendanceAction::class);

    $first = $action->handle([
        'teacher_id' => $teacher->id,
        'attendance_date' => '2026-04-12',
        'status' => 'حاضر',
        'notes' => 'first',
    ]);

    $second = $action->handle([
        'teacher_id' => $teacher->id,
        'attendance_date' => '2026-04-12',
        'status' => 'غائب',
        'notes' => 'second',
    ]);

    expect($first['created'])->toBe(1)
        ->and($first['updated'])->toBe(0)
        ->and($second['created'])->toBe(0)
        ->and($second['updated'])->toBe(1);

    $records = TeacherAttendance::query()
        ->where('teacher_id', $teacher->id)
        ->where('attendance_date', '2026-04-12')
        ->get();

    expect($records)->toHaveCount(1)
        ->and($records->first()->status)->toBe('غائب')
        ->and($records->first()->notes)->toBe('second');
});

