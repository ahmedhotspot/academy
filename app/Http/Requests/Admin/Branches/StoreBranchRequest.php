<?php

namespace App\Http\Requests\Admin\Branches;

use App\Http\Requests\Admin\AdminRequest;
use Illuminate\Validation\Rule;

class StoreBranchRequest extends AdminRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:branches,name'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'اسم الفرع',
            'status' => 'الحالة',
        ];
    }
}

