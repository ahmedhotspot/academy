<?php

namespace App\Actions\Admin\Guardians;

use App\Actions\BaseAction;
use App\Models\Guardian;

class CreateGuardianAction extends BaseAction
{
    public function handle(array $data): Guardian
    {
        return Guardian::query()->create($data);
    }
}

