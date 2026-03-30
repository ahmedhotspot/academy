<?php

namespace App\Http\Requests\Admin\Students;

use App\Http\Requests\Admin\AdminRequest;
use Illuminate\Validation\Rule;

class StoreStudentRequest extends AdminRequest
{
    public function rules(): array
    {
        return [
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'guardian_mode' => ['nullable', Rule::in(['none', 'existing', 'new'])],
            'guardian_id' => ['nullable', 'integer', 'required_if:guardian_mode,existing', 'exists:guardians,id'],
            'guardian_full_name' => ['nullable', 'string', 'max:255', 'required_if:guardian_mode,new'],
            'guardian_phone' => ['nullable', 'string', 'max:20', 'required_if:guardian_mode,new'],
            'guardian_whatsapp' => ['nullable', 'string', 'max:20'],
            'full_name' => ['required', 'string', 'max:255'],
            'age' => ['required', 'integer', 'min:5', 'max:100'],
            'nationality' => ['required', 'string', 'max:100'],
            'identity_number' => ['nullable', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'branch_id' => 'الفرع',
            'guardian_mode' => 'طريقة اختيار ولي الأمر',
            'guardian_id' => 'ولي الأمر',
            'guardian_full_name' => 'اسم ولي الأمر الجديد',
            'guardian_phone' => 'هاتف ولي الأمر الجديد',
            'guardian_whatsapp' => 'واتساب ولي الأمر الجديد',
            'full_name' => 'الاسم الكامل',
            'age' => 'العمر',
            'nationality' => 'الجنسية',
            'identity_number' => 'رقم الهوية أو الجواز',
            'phone' => 'رقم الهاتف',
            'whatsapp' => 'رقم الواتساب',
            'status' => 'الحالة',
        ];
    }
}

