<?php

namespace App\Http\Requests\Admin\Guardians;

use App\Http\Requests\Admin\AdminRequest;
use Illuminate\Validation\Rule;

class UpdateGuardianRequest extends AdminRequest
{
    public function rules(): array
    {
        return [
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'whatsapp' => ['required', 'string', 'max:20'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'portal_password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ];
    }

    public function attributes(): array
    {
        return [
            'branch_id' => 'الفرع',
            'full_name' => 'الاسم الكامل',
            'phone' => 'رقم الهاتف',
            'whatsapp' => 'رقم الواتساب',
            'status' => 'الحالة',
            'portal_password' => 'كلمة المرور',
            'portal_password_confirmation' => 'تأكيد كلمة المرور',
        ];
    }
}

