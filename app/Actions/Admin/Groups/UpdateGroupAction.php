<?php

namespace App\Actions\Admin\Groups;

use App\Actions\BaseAction;
use App\Models\Group;

class UpdateGroupAction extends BaseAction
{
    public function handle(array $data): Group
    {
        /** @var Group $group */
        $group = $data['group'];
        unset($data['group']);

        $group->update($data);

        return $group;
    }
}

