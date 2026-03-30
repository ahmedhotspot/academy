<?php

namespace App\Http\Requests\Admin\StudyTracks;

use App\Http\Requests\Admin\AdminRequest;
use Illuminate\Validation\Rule;

class UpdateStudyTrackRequest extends AdminRequest
{
    public function rules(): array
    {
        $studyTrackId = (int) $this->route('studyTrack')->id;

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('study_tracks', 'name')->ignore($studyTrackId)],
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

