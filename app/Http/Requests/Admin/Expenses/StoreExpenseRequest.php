<?php

namespace App\Http\Requests\Admin\Expenses;

use App\Http\Requests\Admin\AdminRequest;
use Illuminate\Validation\Rule;

class StoreExpenseRequest extends AdminRequest
{
    public function rules(): array
    {
        $user = auth()->user();

        return [
            'branch_id'    => [
                'required',
                'integer',
                Rule::exists('branches', 'id')->where(function ($query) use ($user) {
                    if ($user && ! $user->isSuperAdmin() && $user->branch_id) {
                        $query->where('id', $user->branch_id);
                    }
                }),
            ],
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

