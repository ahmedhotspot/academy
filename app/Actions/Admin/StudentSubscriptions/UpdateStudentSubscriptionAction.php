<?php

namespace App\Actions\Admin\StudentSubscriptions;

use App\Actions\BaseAction;
use App\Models\FeePlan;
use App\Models\StudentSubscription;
use Carbon\Carbon;

class UpdateStudentSubscriptionAction extends BaseAction
{
    public function handle(array $data): StudentSubscription
    {
        /** @var StudentSubscription $subscription */
        $subscription = $data['subscription'];

        $discountAmount  = $data['discount_amount'] ?? 0;
        $finalAmount     = $data['amount'] - $discountAmount;
        $paidAmount      = $data['paid_amount'] ?? 0;
        $remainingAmount = max(0, $finalAmount - $paidAmount);

        $startDate = isset($data['start_date'])
            ? Carbon::parse($data['start_date'])
            : $subscription->start_date ?? now();

        $dueDate = ! empty($data['due_date'])
            ? Carbon::parse($data['due_date'])
            : $this->resolveDueDate($data['fee_plan_id'], $startDate);

        $remainingDueDate = ! empty($data['remaining_due_date'])
            ? Carbon::parse($data['remaining_due_date'])
            : $dueDate;

        $subscription->update([
            'student_id'         => $data['student_id'],
            'fee_plan_id'        => $data['fee_plan_id'],
            'amount'             => $data['amount'],
            'discount_amount'    => $discountAmount,
            'final_amount'       => $finalAmount,
            'paid_amount'        => $paidAmount,
            'remaining_amount'   => $remainingAmount,
            'status'             => $data['status'] ?? StudentSubscription::resolveFinancialStatus(
                (float) $remainingAmount,
                $dueDate
            ),
            'start_date'         => $startDate,
            'due_date'           => $dueDate,
            'remaining_due_date' => $remainingDueDate,
        ]);

        return $subscription->fresh();
    }

    private function resolveDueDate(int $feePlanId, Carbon $startDate): ?Carbon
    {
        $feePlan = FeePlan::query()->find($feePlanId);
        if (! $feePlan) return null;
        return StudentSubscription::calculateDueDate($feePlan->payment_cycle, $startDate);
    }
}

