<?php

namespace App\Http\Requests\Admin\Users;

use App\Enums\UserStatusEnum;
use App\Http\Requests\Admin\AdminRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends AdminRequest
{
    public function rules(): array
    {
        $userId = (int) $this->route('user')->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'username' => ['nullable', 'string', 'max:50', Rule::unique('users', 'username')->ignore($userId)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'status' => ['required', Rule::in(array_keys(UserStatusEnum::options()))],
            'role' => ['required', Rule::in(['المشرف العام', 'السكرتيرة', 'المعلم'])],
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
            'role' => 'الدور',
        ];
    }
}

