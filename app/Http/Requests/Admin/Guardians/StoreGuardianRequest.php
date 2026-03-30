<?php

namespace App\Http\Requests\Admin\Guardians;

use App\Http\Requests\Admin\AdminRequest;
use Illuminate\Validation\Rule;

class StoreGuardianRequest extends AdminRequest
{
    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'full_name' => 'الاسم الكامل',
            'phone' => 'رقم الهاتف',
            'whatsapp' => 'رقم الواتساب',
            'status' => 'الحالة',
        ];
    }
}

