<?php

namespace App\Http\Requests\Admin\StudentSubscriptions;

use App\Http\Requests\Admin\AdminRequest;
use App\Models\FeePlan;
use App\Models\StudentSubscription;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreStudentSubscriptionRequest extends AdminRequest
{
    public function rules(): array
    {
        return [
            'student_id'          => ['required', 'integer', 'exists:students,id'],
            'fee_plan_id'         => ['required', 'integer', 'exists:fee_plans,id'],
            'amount'              => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'discount_amount'     => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'paid_amount'         => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'status'              => ['required', Rule::in(StudentSubscription::STATUSES)],
            'start_date'          => ['required', 'date'],
            'due_date'            => ['nullable', 'date', 'after_or_equal:start_date'],
            'remaining_due_date'  => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }

    public function attributes(): array
    {
        return [
            'student_id'         => 'الطالب',
            'fee_plan_id'        => 'خطة الرسوم',
            'amount'             => 'المبلغ الأساسي',
            'discount_amount'    => 'مبلغ الخصم',
            'paid_amount'        => 'المبلغ المدفوع',
            'status'             => 'الحالة',
            'start_date'         => 'تاريخ البداية',
            'due_date'           => 'تاريخ الاستحقاق',
            'remaining_due_date' => 'تاريخ استحقاق الباقي',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $feePlanId = (int) $this->input('fee_plan_id');
            $amount = (float) $this->input('amount');

            $feePlan = FeePlan::query()->find($feePlanId);
            if (! $feePlan) {
                return;
            }

            if (abs((float) $feePlan->amount - $amount) > 0.0001) {
                $validator->errors()->add('amount', 'المبلغ الأساسي يجب أن يطابق مبلغ خطة الرسوم المختارة.');
            }
        });
    }
}
