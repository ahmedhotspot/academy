<?php

namespace App\Actions\Admin\GroupSchedules;

use App\Actions\BaseAction;
use App\Models\GroupSchedule;

class CreateGroupScheduleAction extends BaseAction
{
    public function handle(array $data): GroupSchedule
    {
        return GroupSchedule::query()->create($data);
    }
}

