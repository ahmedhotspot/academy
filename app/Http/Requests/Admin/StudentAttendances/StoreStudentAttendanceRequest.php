<?php

namespace App\Http\Requests\Admin\StudentAttendances;

use App\Http\Requests\Admin\AdminRequest;
use App\Models\StudentAttendance;
use Illuminate\Validation\Rule;

class StoreStudentAttendanceRequest extends AdminRequest
{
    public function rules(): array
    {
        return [
            'attendance_date' => ['required', 'date'],
            'entries' => ['required', 'array', 'min:1'],
            'entries.*.student_id' => ['required', 'integer', 'exists:students,id', 'distinct'],
            'entries.*.status' => ['required', Rule::in(StudentAttendance::STATUSES)],
            'entries.*.notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'attendance_date' => 'تاريخ الحضور',
            'entries' => 'كشف الحضور',
            'entries.*.student_id' => 'الطالب',
            'entries.*.status' => 'الحالة',
            'entries.*.notes' => 'الملاحظات',
        ];
    }
}

