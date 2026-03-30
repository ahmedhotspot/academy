<?php

namespace App\Actions\Admin\FeePlans;

use App\Actions\BaseAction;
use App\Models\FeePlan;

class UpdateFeePlanAction extends BaseAction
{
    public function handle(array $data): FeePlan
    {
        /** @var FeePlan $feePlan */
        $feePlan = $data['feePlan'];

        $feePlan->update([
            'name'                  => $data['name'],
            'payment_cycle'         => $data['payment_cycle'],
            'amount'                => $data['amount'],
            'has_sisters_discount'  => $data['has_sisters_discount'] ?? false,
            'status'                => $data['status'] ?? 'active',
        ]);

        return $feePlan->fresh();
    }
}

