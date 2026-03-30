<?php

namespace App\Http\Requests\Admin\StudentSubscriptions;

use App\Http\Requests\Admin\AdminRequest;
use App\Models\StudentSubscription;
use Illuminate\Validation\Rule;

class StoreStudentSubscriptionRequest extends AdminRequest
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
}

