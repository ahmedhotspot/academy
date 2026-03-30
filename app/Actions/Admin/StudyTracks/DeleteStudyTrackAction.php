<?php

namespace App\Actions\Admin\StudyTracks;

use App\Actions\BaseAction;
use App\Models\StudyTrack;

class DeleteStudyTrackAction extends BaseAction
{
    public function handle(array $data): bool
    {
        /** @var StudyTrack $studyTrack */
        $studyTrack = $data['studyTrack'];

        return (bool) $studyTrack->delete();
    }
}

