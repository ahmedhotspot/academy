<?php

namespace App\Http\Requests\Admin\TeacherAttendances;

use App\Http\Requests\Admin\AdminRequest;
use App\Models\TeacherAttendance;
use Illuminate\Validation\Rule;

class StoreTeacherAttendanceRequest extends AdminRequest
{
    public function rules(): array
    {
        if ($this->has('entries')) {
            return [
                'attendance_date' => ['required', 'date'],
                'entries' => ['required', 'array', 'min:1'],
                'entries.*.teacher_id' => ['required', 'integer', 'exists:users,id', 'distinct'],
                'entries.*.status' => ['required', Rule::in(TeacherAttendance::STATUSES)],
                'entries.*.notes' => ['nullable', 'string', 'max:1000'],
            ];
        }

        return [
            'teacher_id' => ['required', 'integer', 'exists:users,id'],
            'attendance_date' => ['required', 'date'],
            'status' => ['required', Rule::in(TeacherAttendance::STATUSES)],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'teacher_id' => 'المعلم',
            'attendance_date' => 'تاريخ الحضور',
            'status' => 'الحالة',
            'notes' => 'الملاحظات',
            'entries' => 'كشف الحضور',
            'entries.*.teacher_id' => 'المعلم',
            'entries.*.status' => 'الحالة',
            'entries.*.notes' => 'الملاحظات',
        ];
    }
}

