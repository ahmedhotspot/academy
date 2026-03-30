<?php

namespace App\Http\Requests\Admin\StudyLevels;

use App\Http\Requests\Admin\AdminRequest;
use Illuminate\Validation\Rule;

class UpdateStudyLevelRequest extends AdminRequest
{
    public function rules(): array
    {
        $studyLevelId = (int) $this->route('studyLevel')->id;

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('study_levels', 'name')->ignore($studyLevelId)],
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

