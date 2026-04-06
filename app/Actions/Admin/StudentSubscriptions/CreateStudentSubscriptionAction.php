<?php

namespace App\Actions\Admin\StudentSubscriptions;

use App\Actions\BaseAction;
use App\Models\Student;
use App\Models\StudentSubscription;

class CreateStudentSubscriptionAction extends BaseAction
{
    public function handle(array $data): StudentSubscription
    {
        $discountAmount = $data['discount_amount'] ?? 0;
        $finalAmount = $data['amount'] - $discountAmount;
        $student = Student::query()->find($data['student_id']);

        return StudentSubscription::query()->create([
            'branch_id'        => $student?->branch_id ?? auth()->user()?->branch_id,
            'student_id'       => $data['student_id'],
            'fee_plan_id'      => $data['fee_plan_id'],
            'amount'           => $data['amount'],
            'discount_amount'  => $discountAmount,
            'final_amount'     => $finalAmount,
            'paid_amount'      => $data['paid_amount'] ?? 0,
            'remaining_amount' => $finalAmount - ($data['paid_amount'] ?? 0),
            'status'           => $data['status'] ?? 'نشط',
        ]);
    }
}
