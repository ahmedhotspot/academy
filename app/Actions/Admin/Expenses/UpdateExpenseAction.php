<?php

namespace App\Actions\Admin\Expenses;

use App\Actions\BaseAction;
use App\Models\Expense;

class UpdateExpenseAction extends BaseAction
{
    public function handle(array $data): Expense
    {
        /** @var Expense $expense */
        $expense = $data['expense'];

        $expense->update([
            'branch_id'    => $data['branch_id'] ?? null,
            'expense_date' => $data['expense_date'],
            'title'        => $data['title'],
            'amount'       => $data['amount'],
            'notes'        => $data['notes'] ?? null,
        ]);

        return $expense->fresh();
    }
}

