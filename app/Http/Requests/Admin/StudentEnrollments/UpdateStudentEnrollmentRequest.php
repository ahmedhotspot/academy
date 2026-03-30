<?php

namespace App\Http\Requests\Admin\StudentEnrollments;

use App\Http\Requests\Admin\AdminRequest;
use Illuminate\Validation\Rule;

class UpdateStudentEnrollmentRequest extends AdminRequest
{
    public function rules(): array
    {
        return [
            'group_id' => ['required', 'integer', 'exists:groups,id'],
            'status' => ['required', Rule::in(['active', 'transferred', 'suspended'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'group_id' => 'الحلقة',
            'status' => 'حالة التسجيل',
        ];
    }
}

