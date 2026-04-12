<?php

namespace App\Actions\Admin\FeePlans;

use App\Actions\BaseAction;
use App\Models\FeePlan;

class DeleteFeePlanAction extends BaseAction
{
    public function handle(array $data): bool
    {
        /** @var FeePlan $feePlan */
        $feePlan = $data['feePlan'];

        if ($feePlan->studentSubscriptions()->exists()) {
            return false;
        }

        return (bool) $feePlan->delete();
    }
}

