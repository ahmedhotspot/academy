<?php

namespace App\Actions\Admin\Guardians;

use App\Actions\BaseAction;
use App\Models\Guardian;

class DeleteGuardianAction extends BaseAction
{
    public function handle(array $data): bool
    {
        /** @var Guardian $guardian */
        $guardian = $data['guardian'];

        if ($guardian->students()->exists()) {
            return false;
        }

        return (bool) $guardian->delete();
    }
}

