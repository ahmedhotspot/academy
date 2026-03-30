<?php

namespace App\Actions\Admin\Branches;

use App\Actions\BaseAction;
use App\Models\Branch;

class CreateBranchAction extends BaseAction
{
    public function handle(array $data): Branch
    {
        return Branch::query()->create($data);
    }
}

