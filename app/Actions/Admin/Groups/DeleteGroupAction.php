<?php

namespace App\Actions\Admin\Groups;

use App\Actions\BaseAction;
use App\Models\Group;

class DeleteGroupAction extends BaseAction
{
    public function handle(array $data): bool
    {
        /** @var Group $group */
        $group = $data['group'];

        return (bool) $group->delete();
    }
}

