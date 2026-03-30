<?php

namespace App\Actions\Admin\GroupSchedules;

use App\Actions\BaseAction;
use App\Models\GroupSchedule;

class DeleteGroupScheduleAction extends BaseAction
{
    public function handle(array $data): bool
    {
        /** @var GroupSchedule $groupSchedule */
        $groupSchedule = $data['groupSchedule'];

        return (bool) $groupSchedule->delete();
    }
}

