<?php

namespace App\Actions\Admin\GroupSchedules;

use App\Actions\BaseAction;
use App\Models\GroupSchedule;

class UpdateGroupScheduleAction extends BaseAction
{
    public function handle(array $data): GroupSchedule
    {
        /** @var GroupSchedule $groupSchedule */
        $groupSchedule = $data['groupSchedule'];
        unset($data['groupSchedule']);

        $groupSchedule->update($data);

        return $groupSchedule;
    }
}

