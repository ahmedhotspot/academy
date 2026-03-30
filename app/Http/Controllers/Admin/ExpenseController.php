<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Expenses\CreateExpenseAction;
use App\Actions\Admin\Expenses\DeleteExpenseAction;
use App\Actions\Admin\Expenses\UpdateExpenseAction;
use App\Http\Requests\Admin\Expenses\StoreExpenseRequest;
use App\Http\Requests\Admin\Expenses\UpdateExpenseRequest;
use App\Models\Branch;
use App\Models\Expense;
use App\Services\Admin\ExpenseManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends AdminController
{
    protected string $title = 'إدارة مصروفات التشغيل';

    public function __construct(
        private readonly ExpenseManagementService $service
    ) {}

    public function index(Request $request): View
    {
        $actions = [];
        if (auth()->user()?->can('expenses.create')) {
            $actions[] = [
                'title' => 'إضافة مصروف جديد',
                'url'   => route('admin.expenses.create'),
                'icon'  => 'ti ti-plus',
                'class' => 'btn-primary',
            ];
        }

        return $this->adminView('admin.expenses.index', [
            'breadcrumbs'   => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة المصروفات'],
            ],
            'actions'       => $actions,
            'branches'      => Branch::all(),
            'reportSummary' => $this->service->reportSummary(),
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        return response()->json($this->service->datatable($request));
    }

    public function create(): View
    {
        return $this->adminView('admin.expenses.create', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة المصروفات', 'url' => route('admin.expenses.index')],
                ['title' => 'إضافة مصروف'],
            ],
            'branches' => Branch::all(),
        ]);
    }

    public function store(
        StoreExpenseRequest $request,
        CreateExpenseAction $action
    ): RedirectResponse {
        $expense = $action->handle($request->validated());

        return redirect()
            ->route('admin.expenses.show', $expense)
            ->with('success', 'تم إضافة المصروف بنجاح.');
    }

    public function show(Expense $expense): View
    {
        $profile = $this->service->getExpenseProfile($expense);

        return $this->adminView('admin.expenses.show', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة المصروفات', 'url' => route('admin.expenses.index')],
                ['title' => 'تفاصيل المصروف'],
            ],
            'expense' => $expense,
            'profile' => $profile,
        ]);
    }

    public function edit(Expense $expense): View
    {
        $expense->load('branch');

        return $this->adminView('admin.expenses.edit', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة المصروفات', 'url' => route('admin.expenses.index')],
                ['title' => 'تعديل المصروف'],
            ],
            'expense'  => $expense,
            'branches' => Branch::all(),
        ]);
    }

    public function update(
        UpdateExpenseRequest $request,
        Expense $expense,
        UpdateExpenseAction $action
    ): RedirectResponse {
        $payload = $request->validated();
        $payload['expense'] = $expense;

        $updated = $action->handle($payload);

        return redirect()
            ->route('admin.expenses.show', $updated)
            ->with('success', 'تم تحديث المصروف بنجاح.');
    }

    public function destroy(
        Expense $expense,
        DeleteExpenseAction $action
    ): RedirectResponse {
        $action->handle(['expense' => $expense]);

        return redirect()
            ->route('admin.expenses.index')
            ->with('success', 'تم حذف المصروف بنجاح.');
    }
}

