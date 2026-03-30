<?php

namespace App\Http\Requests\Admin\StudentSubscriptions;

use App\Http\Requests\Admin\AdminRequest;
use App\Models\StudentSubscription;
use Illuminate\Validation\Rule;

class UpdateStudentSubscriptionRequest extends AdminRequest
{
    public function rules(): array
    {
        return [
            'student_id'       => ['required', 'integer', 'exists:students,id'],
            'fee_plan_id'      => ['required', 'integer', 'exists:fee_plans,id'],
            'amount'           => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'discount_amount'  => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'paid_amount'      => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'status'           => ['required', Rule::in(StudentSubscription::STATUSES)],
        ];
    }

    public function attributes(): array
    {
        return [
            'student_id'       => 'الطالب',
            'fee_plan_id'      => 'خطة الرسوم',
            'amount'           => 'المبلغ الأساسي',
            'discount_amount'  => 'مبلغ الخصم',
            'paid_amount'      => 'المبلغ المدفوع',
            'status'           => 'الحالة',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $subscription = $this->route('studentSubscription');

            if (! $subscription) {
                return;
            }

            $amount = (float) $this->input('amount', 0);
            $discountAmount = (float) $this->input('discount_amount', 0);
            $paidAmount = (float) $this->input('paid_amount', 0);
            $remainingAmount = max(0, $amount - $discountAmount - $paidAmount);

            $currentStatus = (string) $subscription->status;
            $newStatus = (string) $this->input('status', $currentStatus);

            if ($newStatus !== $currentStatus && $remainingAmount > 0) {
                $validator->errors()->add(
                    'status',
                    'لا يمكن تغيير الحالة قبل سداد المبلغ المتبقي بالكامل.'
                );
            }
        });
    }
}

