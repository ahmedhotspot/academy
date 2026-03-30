<?php

namespace App\Actions\Admin\Groups;

use App\Actions\BaseAction;
use App\Models\Group;

class CreateGroupAction extends BaseAction
{
    public function handle(array $data): Group
    {
        return Group::query()->create($data);
    }
}

