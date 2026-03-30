<?php

namespace App\Http\Requests\Admin\Payments;

use App\Http\Requests\Admin\AdminRequest;

class UpdatePaymentRequest extends AdminRequest
{
    public function rules(): array
    {
        return [
            'payment_date' => ['required', 'date'],
            'amount'       => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'notes'        => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'payment_date' => 'تاريخ الدفع',
            'amount'       => 'المبلغ',
            'notes'        => 'الملاحظات',
        ];
    }
}

