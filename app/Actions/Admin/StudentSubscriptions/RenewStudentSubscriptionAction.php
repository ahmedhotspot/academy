<?php

namespace App\Actions\Admin\StudentSubscriptions;

use App\Actions\BaseAction;
use App\Models\StudentSubscription;
use Carbon\Carbon;

class RenewStudentSubscriptionAction extends BaseAction
{
    public function handle(array $data): StudentSubscription
    {
        /** @var StudentSubscription $old */
        $old = $data['subscription'];
        $old->load('feePlan');

        // تاريخ بداية التجديد = اليوم التالي لتاريخ الاستحقاق الحالي
        $newStartDate = $old->due_date
            ? $old->due_date->copy()->addDay()
            : Carbon::today();

        // حساب تاريخ الاستحقاق الجديد بناءً على خطة الرسوم
        $newDueDate = $old->feePlan
            ? StudentSubscription::calculateDueDate($old->feePlan->payment_cycle, $newStartDate)
            : null;

        $subscription = StudentSubscription::query()->create([
            'branch_id'          => $old->branch_id,
            'student_id'         => $old->student_id,
            'fee_plan_id'        => $old->fee_plan_id,
            'amount'             => $old->amount,
            'discount_amount'    => $old->discount_amount,
            'final_amount'       => $old->final_amount,
            'paid_amount'        => 0,
            'remaining_amount'   => $old->final_amount,
            'status'             => 'نشط',
            'start_date'         => $newStartDate,
            'due_date'           => $newDueDate,
            'remaining_due_date' => $newDueDate,
        ]);

        // أوقف الاشتراك القديم حتى لا يظهر كمتأخر أو منتهي
        $old->update(['status' => 'موقوف']);

        return $subscription;
    }
}

