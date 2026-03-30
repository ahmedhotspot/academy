<?php

namespace App\Http\Requests\Admin\FeePlans;

use App\Http\Requests\Admin\AdminRequest;
use App\Models\FeePlan;
use Illuminate\Validation\Rule;

class StoreFeePlanRequest extends AdminRequest
{
    public function rules(): array
    {
        return [
            'name'                  => ['required', 'string', 'max:255', 'unique:fee_plans,name'],
            'payment_cycle'         => ['required', Rule::in(FeePlan::PAYMENT_CYCLES)],
            'amount'                => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'has_sisters_discount'  => ['nullable', 'boolean'],
            'status'                => ['required', Rule::in(FeePlan::STATUSES)],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'                  => 'اسم خطة الرسوم',
            'payment_cycle'         => 'دورة الدفع',
            'amount'                => 'المبلغ',
            'has_sisters_discount'  => 'خصم الأخوات',
            'status'                => 'الحالة',
        ];
    }
}

