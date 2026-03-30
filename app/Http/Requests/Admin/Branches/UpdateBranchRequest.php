<?php

namespace App\Http\Requests\Admin\Branches;

use App\Http\Requests\Admin\AdminRequest;
use Illuminate\Validation\Rule;

class UpdateBranchRequest extends AdminRequest
{
    public function rules(): array
    {
        $branchId = (int) $this->route('branch')->id;

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('branches', 'name')->ignore($branchId)],
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

