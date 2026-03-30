<?php

namespace App\Http\Requests\Admin\Expenses;

use App\Http\Requests\Admin\AdminRequest;

class StoreExpenseRequest extends AdminRequest
{
    public function rules(): array
    {
        return [
            'branch_id'    => ['nullable', 'integer', 'exists:branches,id'],
            'expense_date' => ['required', 'date'],
            'title'        => ['required', 'string', 'max:255'],
            'amount'       => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'notes'        => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'branch_id'    => 'الفرع',
            'expense_date' => 'تاريخ المصروف',
            'title'        => 'البيان',
            'amount'       => 'المبلغ',
            'notes'        => 'الملاحظات',
        ];
    }
}

