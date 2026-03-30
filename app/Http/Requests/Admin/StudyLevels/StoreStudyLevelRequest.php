<?php

namespace App\Http\Requests\Admin\StudyLevels;

use App\Http\Requests\Admin\AdminRequest;
use Illuminate\Validation\Rule;

class StoreStudyLevelRequest extends AdminRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:study_levels,name'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'اسم المستوى',
            'status' => 'الحالة',
        ];
    }
}

