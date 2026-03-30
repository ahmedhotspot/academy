<?php

namespace App\Actions\Admin\StudyLevels;

use App\Actions\BaseAction;
use App\Models\StudyLevel;

class UpdateStudyLevelAction extends BaseAction
{
    public function handle(array $data): StudyLevel
    {
        /** @var StudyLevel $studyLevel */
        $studyLevel = $data['studyLevel'];
        unset($data['studyLevel']);

        $studyLevel->update($data);

        return $studyLevel;
    }
}

