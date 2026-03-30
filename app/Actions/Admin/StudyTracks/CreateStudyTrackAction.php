<?php

namespace App\Actions\Admin\StudyTracks;

use App\Actions\BaseAction;
use App\Models\StudyTrack;

class CreateStudyTrackAction extends BaseAction
{
    public function handle(array $data): StudyTrack
    {
        return StudyTrack::query()->create($data);
    }
}

