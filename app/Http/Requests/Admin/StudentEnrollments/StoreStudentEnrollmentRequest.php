<?php

namespace App\Http\Requests\Admin\StudentEnrollments;

use App\Http\Requests\Admin\AdminRequest;
use Illuminate\Validation\Rule;

class StoreStudentEnrollmentRequest extends AdminRequest
{
    public function rules(): array
    {
        return [
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['required', 'integer', 'distinct', 'exists:students,id'],
            'group_id' => ['required', 'integer', 'exists:groups,id'],
            'status' => ['required', Rule::in(['active', 'transferred', 'suspended'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'student_ids' => 'الطلاب',
            'student_ids.*' => 'الطالب',
            'group_id' => 'الحلقة',
            'status' => 'حالة التسجيل',
        ];
    }
}

