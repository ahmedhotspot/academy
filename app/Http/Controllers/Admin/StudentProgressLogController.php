<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\StudentProgressLogs\CreateStudentProgressLogAction;
use App\Actions\Admin\StudentAttendances\CreateStudentAttendanceAction;
use App\Actions\Admin\StudentProgressLogs\DeleteStudentProgressLogAction;
use App\Actions\Admin\StudentProgressLogs\UpdateStudentProgressLogAction;
use App\Http\Requests\Admin\StudentAttendances\StoreStudentAttendanceRequest;
use App\Http\Requests\Admin\StudentProgressLogs\StoreStudentProgressLogRequest;
use App\Http\Requests\Admin\StudentProgressLogs\UpdateStudentProgressLogRequest;
use App\Models\Student;
use App\Models\StudentProgressLog;
use App\Services\Admin\StudentProgressLogManagementService;
use App\Services\Admin\StudentAttendanceManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentProgressLogController extends AdminController
{
    protected string $title = 'المتابعة التعليمية اليومية';

    public function __construct(
        private readonly StudentProgressLogManagementService $service
    ) {}

    public function index(Request $request): View
    {
        $actions = [];

        if (auth()->user()?->can('student-progress-logs.create')) {
            $actions[] = [
                'title' => 'تسجيل متابعة جديدة',
                'url'   => route('admin.student-progress-logs.create'),
                'icon'  => 'ti ti-plus',
                'class' => 'btn-primary',
            ];
        }

        $filters = [
            'group_id'      => $request->input('group_id'),
            'progress_date' => $request->input('progress_date'),
        ];

        return $this->adminView('admin.student-progress-logs.index', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'المتابعة التعليمية اليومية'],
            ],
            'actions'       => $actions,
            'groupOptions'  => $this->service->getGroupOptions(),
            'reportSummary' => $this->service->reportSummary($filters),
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        return response()->json($this->service->datatable($request));
    }

    /**
     * AJAX: جلب طلاب حلقة معينة
     */
    public function studentsByGroup(Request $request): JsonResponse
    {
        $groupId = (int) $request->input('group_id', 0);

        return response()->json($this->service->getStudentsByGroup($groupId));
    }

    public function create(Request $request): View
    {
        $prefillStudentId = null;
        $prefillGroupId = null;

        if ($request->filled('student_id')) {
            $candidateStudentId = (int) $request->query('student_id');
            $student = Student::query()->select(['id'])->find($candidateStudentId);

            if ($student) {
                $prefillStudentId = $student->id;
                $prefillGroupId = $student->currentEnrollment()?->group_id;
            }
        }

        if ($request->filled('group_id')) {
            $candidateGroupId = (int) $request->query('group_id');
            $groupExistsInScope = array_key_exists($candidateGroupId, $this->service->getGroupOptions());

            if ($groupExistsInScope) {
                $prefillGroupId = $candidateGroupId;
            }
        }

        return $this->adminView('admin.student-progress-logs.create', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'المتابعة التعليمية', 'url' => route('admin.student-progress-logs.index')],
                ['title' => 'تسجيل متابعة جديدة'],
            ],
            'groupOptions'    => $this->service->getGroupOptions(),
            'teacherOptions'  => $this->service->getTeacherOptions(),
            'prefillStudentId' => $prefillStudentId,
            'prefillGroupId' => $prefillGroupId,
            'evaluationLevels' => StudentProgressLog::EVALUATION_LEVELS,
            'commitmentStatuses' => StudentProgressLog::COMMITMENT_STATUSES,
        ]);
    }

    public function store(
        StoreStudentProgressLogRequest $request,
        CreateStudentProgressLogAction $action
    ): RedirectResponse {
        $log = $action->handle($request->validated());

        return redirect()
            ->route('admin.student-progress-logs.show', $log->student_id)
            ->with('success', 'تم تسجيل المتابعة التعليمية بنجاح.');
    }

    public function show(Student $student): View
    {
        $report = $this->service->getStudentReport($student);

        return $this->adminView('admin.student-progress-logs.show', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'المتابعة التعليمية', 'url' => route('admin.student-progress-logs.index')],
                ['title' => 'سجل الطالب — ' . $student->full_name],
            ],
            'student' => $student,
            'report'  => $report,
        ]);
    }

    public function edit(StudentProgressLog $studentProgressLog): View
    {
        $studentProgressLog->load(['student', 'group', 'teacher']);

        return $this->adminView('admin.student-progress-logs.edit', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'المتابعة التعليمية', 'url' => route('admin.student-progress-logs.index')],
                ['title' => 'تعديل سجل المتابعة'],
            ],
            'log'                => $studentProgressLog,
            'groupOptions'       => $this->service->getGroupOptions(),
            'teacherOptions'     => $this->service->getTeacherOptions(),
            'evaluationLevels'   => StudentProgressLog::EVALUATION_LEVELS,
            'commitmentStatuses' => StudentProgressLog::COMMITMENT_STATUSES,
            'currentStudents'    => $this->service->getStudentsByGroup($studentProgressLog->group_id),
        ]);
    }

    public function update(
        UpdateStudentProgressLogRequest $request,
        StudentProgressLog $studentProgressLog,
        UpdateStudentProgressLogAction $action
    ): RedirectResponse {
        $payload = $request->validated();
        $payload['progressLog'] = $studentProgressLog;

        $updated = $action->handle($payload);

        return redirect()
            ->route('admin.student-progress-logs.show', $updated->student_id)
            ->with('success', 'تم تحديث سجل المتابعة بنجاح.');
    }

    public function destroy(
        StudentProgressLog $studentProgressLog,
        DeleteStudentProgressLogAction $action
    ): RedirectResponse {
        $studentId = $studentProgressLog->student_id;
        $action->handle(['progressLog' => $studentProgressLog]);

        return redirect()
            ->route('admin.student-progress-logs.index')
            ->with('success', 'تم حذف سجل المتابعة بنجاح.');
    }

    public function attendanceIndex(Request $request): View
    {
        $service = app(StudentAttendanceManagementService::class);
        $actions = [];

        if (auth()->user()?->can('student-attendances.create')) {
            $actions[] = [
                'title' => 'تسجيل حضور اليوم',
                'url' => route('admin.student-attendances.create', ['attendance_date' => now()->toDateString()]),
                'icon' => 'ti ti-plus',
                'class' => 'btn-primary',
            ];
        }

        $filters = [
            'student_id' => $request->input('student_id'),
            'attendance_date' => $request->input('attendance_date'),
        ];

        return $this->adminView('admin.student-attendances.index', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة حضور وغياب الطلاب'],
            ],
            'actions' => $actions,
            'studentOptions' => $service->getStudentOptions(),
            'reportSummary' => $service->reportSummary($filters),
        ]);
    }

    public function attendanceDatatable(Request $request): JsonResponse
    {
        $service = app(StudentAttendanceManagementService::class);

        return response()->json($service->datatable($request));
    }

    public function attendanceCreate(Request $request): View
    {
        $service = app(StudentAttendanceManagementService::class);
        $attendanceDate = $request->input('attendance_date', now()->toDateString());
        $dailySheet = $service->getDailyAttendanceSheet($attendanceDate);

        return $this->adminView('admin.student-attendances.create', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة حضور وغياب الطلاب', 'url' => route('admin.student-attendances.index')],
                ['title' => 'تسجيل حضور الطلاب'],
            ],
            'dailySheet' => $dailySheet,
        ]);
    }

    public function attendanceStore(StoreStudentAttendanceRequest $request, CreateStudentAttendanceAction $action): RedirectResponse
    {
        $result = $action->handle($request->validated());

        return redirect()
            ->route('admin.student-attendances.index')
            ->with('success', "تم حفظ كشف حضور الطلاب بنجاح بعدد {$result['processed']} سجل.");
    }
}

