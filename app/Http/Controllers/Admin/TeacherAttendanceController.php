<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\TeacherAttendances\CreateTeacherAttendanceAction;
use App\Actions\Admin\TeacherAttendances\DeleteTeacherAttendanceAction;
use App\Actions\Admin\TeacherAttendances\UpdateTeacherAttendanceAction;
use App\Http\Requests\Admin\TeacherAttendances\StoreTeacherAttendanceRequest;
use App\Http\Requests\Admin\TeacherAttendances\UpdateTeacherAttendanceRequest;
use App\Models\TeacherAttendance;
use App\Models\User;
use App\Services\Admin\TeacherAttendanceManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeacherAttendanceController extends AdminController
{
    protected string $title = 'إدارة حضور وغياب المعلمين';

    public function __construct(private readonly TeacherAttendanceManagementService $teacherAttendanceManagementService)
    {
    }

    public function index(Request $request): View
    {
        $actions = [];

        if (auth()->user()?->can('teacher-attendances.create')) {
            $actions[] = [
                'title' => 'تسجيل حضور اليوم',
                'url' => route('admin.teacher-attendances.create', ['attendance_date' => now()->toDateString()]),
                'icon' => 'ti ti-plus',
                'class' => 'btn-primary',
            ];
        }

        $filters = [
            'teacher_id' => $request->input('teacher_id'),
            'attendance_date' => $request->input('attendance_date'),
        ];

        return $this->adminView('admin.teacher-attendances.index', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة حضور وغياب المعلمين'],
            ],
            'actions' => $actions,
            'teacherOptions' => $this->teacherAttendanceManagementService->getTeacherOptions(),
            'reportSummary' => $this->teacherAttendanceManagementService->reportSummary($filters),
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        return response()->json($this->teacherAttendanceManagementService->datatable($request));
    }

    public function create(Request $request): View
    {
        $attendanceDate = $request->input('attendance_date', now()->toDateString());
        $dailySheet = $this->teacherAttendanceManagementService->getDailyAttendanceSheet($attendanceDate);

        return $this->adminView('admin.teacher-attendances.create', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة حضور وغياب المعلمين', 'url' => route('admin.teacher-attendances.index')],
                ['title' => 'تسجيل حضور المعلمين'],
            ],
            'teacherOptions' => $this->teacherAttendanceManagementService->getTeacherOptions(),
            'dailySheet' => $dailySheet,
        ]);
    }

    public function store(StoreTeacherAttendanceRequest $request, CreateTeacherAttendanceAction $createTeacherAttendanceAction): RedirectResponse
    {
        $result = $createTeacherAttendanceAction->handle($request->validated());

        $message = $result['updated'] > 0
            ? "تم حفظ كشف الحضور بنجاح. تمت إضافة {$result['created']} سجل وتحديث {$result['updated']} سجل موجود."
            : "تم حفظ كشف الحضور بنجاح بعدد {$result['processed']} سجل.";

        return redirect()
            ->route('admin.teacher-attendances.index')
            ->with('success', $message);
    }

    public function show(User $teacher): View
    {
        $profile = $this->teacherAttendanceManagementService->getTeacherAttendanceProfile($teacher);

        return $this->adminView('admin.teacher-attendances.show', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة حضور وغياب المعلمين', 'url' => route('admin.teacher-attendances.index')],
                ['title' => 'سجل حضور المعلم'],
            ],
            'teacher' => $teacher,
            'profile' => $profile,
        ]);
    }

    public function edit(TeacherAttendance $teacherAttendance): View
    {
        return $this->adminView('admin.teacher-attendances.edit', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة حضور وغياب المعلمين', 'url' => route('admin.teacher-attendances.index')],
                ['title' => 'تعديل سجل الحضور'],
            ],
            'teacherAttendance' => $teacherAttendance->load('teacher'),
            'teacherOptions' => $this->teacherAttendanceManagementService->getTeacherOptions(),
        ]);
    }

    public function update(UpdateTeacherAttendanceRequest $request, TeacherAttendance $teacherAttendance, UpdateTeacherAttendanceAction $updateTeacherAttendanceAction): RedirectResponse
    {
        $payload = $request->validated();
        $payload['teacherAttendance'] = $teacherAttendance;

        $updatedAttendance = $updateTeacherAttendanceAction->handle($payload);

        return redirect()
            ->route('admin.teacher-attendances.show', $updatedAttendance->teacher_id)
            ->with('success', 'تم تحديث سجل حضور المعلم بنجاح.');
    }

    public function destroy(TeacherAttendance $teacherAttendance, DeleteTeacherAttendanceAction $deleteTeacherAttendanceAction): RedirectResponse
    {
        $deleteTeacherAttendanceAction->handle(['teacherAttendance' => $teacherAttendance]);

        return redirect()
            ->route('admin.teacher-attendances.index')
            ->with('success', 'تم حذف سجل حضور المعلم بنجاح.');
    }
}

