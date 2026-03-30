<?php

namespace App\Http\Requests\Admin\StudyTracks;

use App\Http\Requests\Admin\AdminRequest;
use Illuminate\Validation\Rule;

class StoreStudyTrackRequest extends AdminRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:study_tracks,name'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'اسم المسار',
            'status' => 'الحالة',
        ];
    }
}

