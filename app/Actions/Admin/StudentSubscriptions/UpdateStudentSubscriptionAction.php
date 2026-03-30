<?php

namespace App\Actions\Admin\StudentSubscriptions;

use App\Actions\BaseAction;
use App\Models\StudentSubscription;

class UpdateStudentSubscriptionAction extends BaseAction
{
    public function handle(array $data): StudentSubscription
    {
        /** @var StudentSubscription $subscription */
        $subscription = $data['subscription'];

        $discountAmount = $data['discount_amount'] ?? 0;
        $finalAmount = $data['amount'] - $discountAmount;
        $paidAmount = $data['paid_amount'] ?? 0;
        $remainingAmount = max(0, $finalAmount - $paidAmount);

        $subscription->update([
            'student_id'       => $data['student_id'],
            'fee_plan_id'      => $data['fee_plan_id'],
            'amount'           => $data['amount'],
            'discount_amount'  => $discountAmount,
            'final_amount'     => $finalAmount,
            'paid_amount'      => $paidAmount,
            'remaining_amount' => $remainingAmount,
            'status'           => $data['status'] ?? 'نشط',
        ]);

        return $subscription->fresh();
    }
}

