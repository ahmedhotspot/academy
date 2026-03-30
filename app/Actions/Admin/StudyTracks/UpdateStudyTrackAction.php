<?php

namespace App\Actions\Admin\StudyTracks;

use App\Actions\BaseAction;
use App\Models\StudyTrack;

class UpdateStudyTrackAction extends BaseAction
{
    public function handle(array $data): StudyTrack
    {
        /** @var StudyTrack $studyTrack */
        $studyTrack = $data['studyTrack'];
        unset($data['studyTrack']);

        $studyTrack->update($data);

        return $studyTrack;
    }
}

