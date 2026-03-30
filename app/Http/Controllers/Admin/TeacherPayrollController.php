<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\TeacherPayrolls\CreateTeacherPayrollAction;
use App\Actions\Admin\TeacherPayrolls\UpdatePayrollStatusAction;
use App\Actions\Admin\TeacherPayrolls\UpdateTeacherPayrollAction;
use App\Http\Requests\Admin\TeacherPayrolls\StoreTeacherPayrollRequest;
use App\Http\Requests\Admin\TeacherPayrolls\UpdateTeacherPayrollRequest;
use App\Models\TeacherPayroll;
use App\Services\Admin\TeacherPayrollManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeacherPayrollController extends AdminController
{
    protected string $title = 'إدارة مستحقات المعلمين';

    public function __construct(
        private readonly TeacherPayrollManagementService $service
    ) {}

    public function index(Request $request): View
    {
        $actions = [];

        if (auth()->user()?->can('teacher-payrolls.create')) {
            $actions[] = [
                'title' => 'حساب مستحق جديد',
                'url'   => route('admin.teacher-payrolls.create'),
                'icon'  => 'ti ti-plus',
                'class' => 'btn-primary',
            ];
        }

        $filters = [
            'status' => $request->input('status'),
            'month'  => $request->input('month'),
            'year'   => $request->input('year'),
        ];

        return $this->adminView('admin.teacher-payrolls.index', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة مستحقات المعلمين'],
            ],
            'actions'       => $actions,
            'reportSummary' => $this->service->reportSummary($filters),
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        return response()->json($this->service->datatable($request));
    }

    public function create(): View
    {
        return $this->adminView('admin.teacher-payrolls.create', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة مستحقات المعلمين', 'url' => route('admin.teacher-payrolls.index')],
                ['title' => 'حساب مستحق جديد'],
            ],
            'teacherOptions' => $this->service->getTeacherOptions(),
            'currentYear'    => now()->year,
            'currentMonth'   => now()->month,
        ]);
    }

    public function store(
        StoreTeacherPayrollRequest $request,
        CreateTeacherPayrollAction $action
    ): RedirectResponse {
        $payroll = $action->handle($request->validated());

        return redirect()
            ->route('admin.teacher-payrolls.show', $payroll)
            ->with('success', 'تم حساب المستحق بنجاح.');
    }

    public function show(TeacherPayroll $teacherPayroll): View
    {
        $payroll = $teacherPayroll;

        abort_if(! $payroll->teacher_id, 404);

        $payroll->load('teacher');
        $absences = $this->service->getTeacherAbsences($payroll->teacher_id, $payroll->month, $payroll->year);

        return $this->adminView('admin.teacher-payrolls.show', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة مستحقات المعلمين', 'url' => route('admin.teacher-payrolls.index')],
                ['title' => 'تفاصيل المستحق'],
            ],
            'payroll'  => $payroll,
            'absences' => $absences,
        ]);
    }

    public function edit(TeacherPayroll $teacherPayroll): View
    {
        $payroll = $teacherPayroll;

        $payroll->load('teacher');

        return $this->adminView('admin.teacher-payrolls.edit', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة مستحقات المعلمين', 'url' => route('admin.teacher-payrolls.index')],
                ['title' => 'تعديل المستحق'],
            ],
            'payroll' => $payroll,
        ]);
    }

    public function update(
        UpdateTeacherPayrollRequest $request,
        TeacherPayroll $teacherPayroll,
        UpdateTeacherPayrollAction $action
    ): RedirectResponse {
        $payroll = $teacherPayroll;

        $payload = $request->validated();
        $payload['payroll'] = $payroll;

        $updated = $action->handle($payload);

        return redirect()
            ->route('admin.teacher-payrolls.show', $updated)
            ->with('success', 'تم تحديث المستحق بنجاح.');
    }

    public function markAsProcessed(
        TeacherPayroll $teacherPayroll,
        UpdatePayrollStatusAction $action
    ): RedirectResponse {
        $payroll = $teacherPayroll;

        $action->handle([
            'payroll' => $payroll,
            'status'  => 'مصروف',
        ]);

        return redirect()
            ->back()
            ->with('success', 'تم تحديث حالة المستحق إلى مصروف.');
    }
}

