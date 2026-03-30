<?php

namespace App\Actions\Admin\Guardians;

use App\Actions\BaseAction;
use App\Models\Guardian;

class UpdateGuardianAction extends BaseAction
{
    public function handle(array $data): Guardian
    {
        /** @var Guardian $guardian */
        $guardian = $data['guardian'];
        unset($data['guardian']);

        $guardian->update($data);

        return $guardian;
    }
}

