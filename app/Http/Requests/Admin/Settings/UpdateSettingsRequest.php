<?php

namespace App\Http\Requests\Admin\Settings;

use App\Http\Requests\Admin\AdminRequest;

class UpdateSettingsRequest extends AdminRequest
{
    public function rules(): array
    {
        return [
            'institution_name'    => ['required', 'string', 'max:255'],
            'institution_address' => ['nullable', 'string', 'max:500'],
            'institution_phone'   => ['nullable', 'string', 'max:50'],
            'institution_email'   => ['nullable', 'email', 'max:255'],
            'institution_logo'    => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'institution_name'    => 'اسم المؤسسة',
            'institution_address' => 'العنوان',
            'institution_phone'   => 'الهاتف',
            'institution_email'   => 'البريد الإلكتروني',
            'institution_logo'    => 'الشعار',
        ];
    }
}

