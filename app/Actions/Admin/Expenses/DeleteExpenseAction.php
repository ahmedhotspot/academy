<?php

namespace App\Actions\Admin\Expenses;

use App\Actions\BaseAction;
use App\Models\Expense;

class DeleteExpenseAction extends BaseAction
{
    public function handle(array $data): bool
    {
        /** @var Expense $expense */
        $expense = $data['expense'];

        return (bool) $expense->delete();
    }
}

