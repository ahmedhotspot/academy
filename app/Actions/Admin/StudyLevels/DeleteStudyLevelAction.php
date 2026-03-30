<?php

namespace App\Actions\Admin\StudyLevels;

use App\Actions\BaseAction;
use App\Models\StudyLevel;

class DeleteStudyLevelAction extends BaseAction
{
    public function handle(array $data): bool
    {
        /** @var StudyLevel $studyLevel */
        $studyLevel = $data['studyLevel'];

        return (bool) $studyLevel->delete();
    }
}

