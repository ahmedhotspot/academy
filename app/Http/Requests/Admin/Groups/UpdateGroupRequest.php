<?php

namespace App\Http\Requests\Admin\Groups;

use App\Http\Requests\Admin\AdminRequest;
use Illuminate\Validation\Rule;

class UpdateGroupRequest extends AdminRequest
{
    public function rules(): array
    {
        return [
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'teacher_id' => ['required', 'integer', 'exists:users,id'],
            'study_level_id' => ['required', 'integer', 'exists:study_levels,id'],
            'study_track_id' => ['required', 'integer', 'exists:study_tracks,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['individual', 'group'])],
            'schedule_type' => ['required', Rule::in(['daily', 'weekly'])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'branch_id' => 'الفرع',
            'teacher_id' => 'المعلم',
            'study_level_id' => 'المستوى',
            'study_track_id' => 'المسار',
            'name' => 'اسم الحلقة',
            'type' => 'نوع الحلقة',
            'schedule_type' => 'نظام الحلقة',
            'status' => 'الحالة',
        ];
    }
}

