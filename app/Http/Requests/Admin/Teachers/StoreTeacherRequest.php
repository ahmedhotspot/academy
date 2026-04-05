<?php

namespace App\Http\Requests\Admin\Teachers;

use App\Enums\UserStatusEnum;
use App\Http\Requests\Admin\AdminRequest;
use Illuminate\Validation\Rule;

class StoreTeacherRequest extends AdminRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'username' => ['nullable', 'string', 'max:50', 'unique:users,username'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'status' => ['required', Rule::in(array_keys(UserStatusEnum::options()))],
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'الاسم',
            'phone' => 'رقم الجوال',
            'email' => 'البريد الإلكتروني',
            'username' => 'اسم المستخدم',
            'password' => 'كلمة المرور',
            'password_confirmation' => 'تأكيد كلمة المرور',
            'status' => 'الحالة',
            'branch_id' => 'الفرع',
        ];
    }
}

