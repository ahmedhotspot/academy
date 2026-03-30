<?php

namespace App\Http\Requests\Admin\TeacherAttendances;

use App\Http\Requests\Admin\AdminRequest;
use Illuminate\Validation\Rule;

class StoreTeacherAttendanceRequest extends AdminRequest
{
    public function rules(): array
    {
        return [
            'teacher_id' => ['required', 'integer', 'exists:users,id'],
            'attendance_date' => ['required', 'date'],
            'status' => ['required', Rule::in(['حاضر', 'غائب', 'متأخر', 'بعذر'])],
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
        ];
    }
}

