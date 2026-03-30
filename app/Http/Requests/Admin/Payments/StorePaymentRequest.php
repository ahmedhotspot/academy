<?php

namespace App\Http\Requests\Admin\Payments;

use App\Http\Requests\Admin\AdminRequest;

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
}

