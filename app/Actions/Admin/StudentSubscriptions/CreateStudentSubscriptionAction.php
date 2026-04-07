<?php

namespace App\Actions\Admin\StudentSubscriptions;

use App\Actions\BaseAction;
use App\Models\FeePlan;
use App\Models\Student;
use App\Models\StudentSubscription;
use Carbon\Carbon;

class CreateStudentSubscriptionAction extends BaseAction
{
    public function handle(array $data): StudentSubscription
    {
        $discountAmount = $data['discount_amount'] ?? 0;
        $finalAmount    = $data['amount'] - $discountAmount;
        $student        = Student::query()->find($data['student_id']);

        // حساب تاريخ الاستحقاق تلقائياً إن لم يُحدد
        $startDate = isset($data['start_date'])
            ? Carbon::parse($data['start_date'])
            : now();

        $dueDate = ! empty($data['due_date'])
            ? Carbon::parse($data['due_date'])
            : $this->resolveDueDate($data['fee_plan_id'], $startDate);

        $remainingDueDate = ! empty($data['remaining_due_date'])
            ? Carbon::parse($data['remaining_due_date'])
            : $dueDate;

        return StudentSubscription::query()->create([
            'branch_id'          => $student?->branch_id ?? auth()->user()?->branch_id,
            'student_id'         => $data['student_id'],
            'fee_plan_id'        => $data['fee_plan_id'],
            'amount'             => $data['amount'],
            'discount_amount'    => $discountAmount,
            'final_amount'       => $finalAmount,
            'paid_amount'        => $data['paid_amount'] ?? 0,
            'remaining_amount'   => $finalAmount - ($data['paid_amount'] ?? 0),
            'status'             => $data['status'] ?? 'نشط',
            'start_date'         => $startDate,
            'due_date'           => $dueDate,
            'remaining_due_date' => $remainingDueDate,
        ]);
    }

    private function resolveDueDate(int $feePlanId, Carbon $startDate): ?Carbon
    {
        $feePlan = FeePlan::query()->find($feePlanId);
        if (! $feePlan) return null;
        return StudentSubscription::calculateDueDate($feePlan->payment_cycle, $startDate);
    }
}
