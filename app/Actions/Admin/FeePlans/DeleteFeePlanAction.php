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

        return (bool) $feePlan->delete();
    }
}

