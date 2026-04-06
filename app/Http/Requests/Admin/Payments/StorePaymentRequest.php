<?php

namespace App\Http\Requests\Admin\Payments;

use App\Http\Requests\Admin\AdminRequest;
use App\Models\StudentSubscription;
use Illuminate\Validation\Validator;

class StorePaymentRequest extends AdminRequest
{
    public function rules(): array
    {
        return [
            'student_id'              => ['required', 'integer', 'exists:students,id'],
            'student_subscription_id' => ['required', 'integer', 'exists:student_subscriptions,id'],
            'payment_date'            => ['required', 'date'],
            'amount'                  => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'notes'                   => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'student_id'              => 'الطالب',
            'student_subscription_id' => 'الاشتراك',
            'payment_date'            => 'تاريخ الدفع',
            'amount'                  => 'المبلغ',
            'notes'                   => 'الملاحظات',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $subscriptionId = (int) $this->input('student_subscription_id');
            $studentId = (int) $this->input('student_id');
            $amount = (float) $this->input('amount');

            $subscription = StudentSubscription::query()->find($subscriptionId);

            if (! $subscription) {
                return;
            }

            if ((int) $subscription->student_id !== $studentId) {
                $validator->errors()->add('student_subscription_id', 'الاشتراك المختار لا يتبع الطالب المحدد.');

                return;
            }

            $remaining = (float) $subscription->remaining_amount;

            if ($remaining <= 0) {
                $validator->errors()->add('student_subscription_id', 'هذا الاشتراك مكتمل السداد ولا يمكن إضافة دفعة جديدة.');

                return;
            }

            if ($amount > $remaining) {
                $validator->errors()->add('amount', 'المبلغ المدخل أكبر من المتبقي على الاشتراك.');
            }
        });
    }
}

