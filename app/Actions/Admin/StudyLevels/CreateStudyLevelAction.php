<?php

namespace App\Actions\Admin\StudyLevels;

use App\Actions\BaseAction;
use App\Models\StudyLevel;

class CreateStudyLevelAction extends BaseAction
{
    public function handle(array $data): StudyLevel
    {
        return StudyLevel::query()->create($data);
    }
}

