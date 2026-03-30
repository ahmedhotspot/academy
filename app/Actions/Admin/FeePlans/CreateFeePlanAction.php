<?php

namespace App\Actions\Admin\FeePlans;

use App\Actions\BaseAction;
use App\Models\FeePlan;

class CreateFeePlanAction extends BaseAction
{
    public function handle(array $data): FeePlan
    {
        return FeePlan::query()->create([
            'name'                  => $data['name'],
            'payment_cycle'         => $data['payment_cycle'],
            'amount'                => $data['amount'],
            'has_sisters_discount'  => $data['has_sisters_discount'] ?? false,
            'status'                => $data['status'] ?? 'active',
        ]);
    }
}

