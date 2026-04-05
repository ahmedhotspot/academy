<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\StudentEnrollments\CreateStudentEnrollmentAction;
use App\Actions\Admin\StudentEnrollments\DeleteStudentEnrollmentAction;
use App\Actions\Admin\StudentEnrollments\UpdateStudentEnrollmentAction;
use App\Traits\PreservesBranchId;
use App\Http\Requests\Admin\StudentEnrollments\StoreStudentEnrollmentRequest;
use App\Http\Requests\Admin\StudentEnrollments\UpdateStudentEnrollmentRequest;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Services\Admin\StudentEnrollmentManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentEnrollmentController extends AdminController
{
    use PreservesBranchId;

    protected string $title = 'إدارة تسجيل الطلاب في الحلقات';

    public function __construct(private readonly StudentEnrollmentManagementService $studentEnrollmentManagementService)
    {
    }

    public function index(): View
    {
        $actions = [];

        if (auth()->user()?->can('student-enrollments.create')) {
            $actions[] = [
                'title' => 'تسجيل طالب في حلقة',
                'url' => route('admin.student-enrollments.create'),
                'icon' => 'ti ti-plus',
                'class' => 'btn-primary',
            ];
        }

        return $this->adminView('admin.student-enrollments.index', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة تسجيل الطلاب في الحلقات'],
            ],
            'actions' => $actions,
            'groupOptions' => $this->studentEnrollmentManagementService->getGroupOptions(),
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        return response()->json($this->studentEnrollmentManagementService->datatable($request));
    }

    public function create(): View
    {
        return $this->adminView('admin.student-enrollments.create', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة تسجيل الطلاب في الحلقات', 'url' => route('admin.student-enrollments.index')],
                ['title' => 'تسجيل طالب في حلقة'],
            ],
            'studentOptions' => $this->studentEnrollmentManagementService->getStudentOptions(),
            'groupOptions' => $this->studentEnrollmentManagementService->getGroupOptions(),
        ]);
    }

    public function store(StoreStudentEnrollmentRequest $request, CreateStudentEnrollmentAction $createStudentEnrollmentAction): RedirectResponse
    {
        $payload = $request->validated();

        // التأكد من حفظ branch_id من المستخدم الحالي
        if (!$this->validateBranchOwnership($payload)) {
            abort(403, 'لا يمكنك تسجيل طلاب من فروع أخرى');
        }

        $studentIds = collect($payload['student_ids'])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        foreach ($studentIds as $studentId) {
            $createStudentEnrollmentAction->handle([
                'branch_id' => auth()->user()->branch_id ?? 1,
                'student_id' => $studentId,
                'group_id' => $payload['group_id'],
                'status' => $payload['status'],
            ]);
        }

        return redirect()
            ->route('admin.student-enrollments.index')
            ->with('success', $studentIds->count() > 1 ? 'تم تسجيل الطلاب في الحلقة بنجاح.' : 'تم تسجيل الطالب في الحلقة بنجاح.');
    }

    public function show(Student $student): View
    {
        $profile = $this->studentEnrollmentManagementService->getStudentEnrollmentProfile($student);

        return $this->adminView('admin.student-enrollments.show', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة تسجيل الطلاب في الحلقات', 'url' => route('admin.student-enrollments.index')],
                ['title' => 'سجل الطالب'],
            ],
            'student' => $student,
            'profile' => $profile,
        ]);
    }

    public function edit(StudentEnrollment $studentEnrollment): View
    {
        return $this->adminView('admin.student-enrollments.edit', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة تسجيل الطلاب في الحلقات', 'url' => route('admin.student-enrollments.index')],
                ['title' => 'تعديل أو نقل التسجيل'],
            ],
            'studentEnrollment' => $studentEnrollment->load(['student', 'group']),
            'groupOptions' => $this->studentEnrollmentManagementService->getGroupOptions(),
        ]);
    }

    public function update(UpdateStudentEnrollmentRequest $request, StudentEnrollment $studentEnrollment, UpdateStudentEnrollmentAction $updateStudentEnrollmentAction): RedirectResponse
    {
        $payload = $request->validated();
        $payload['studentEnrollment'] = $studentEnrollment;

        $updatedEnrollment = $updateStudentEnrollmentAction->handle($payload);

        return redirect()
            ->route('admin.student-enrollments.show', $updatedEnrollment->student_id)
            ->with('success', 'تم تحديث تسجيل الطالب بنجاح.');
    }

    public function destroy(StudentEnrollment $studentEnrollment, DeleteStudentEnrollmentAction $deleteStudentEnrollmentAction): RedirectResponse
    {
        $deleteStudentEnrollmentAction->handle(['studentEnrollment' => $studentEnrollment]);

        return redirect()
            ->route('admin.student-enrollments.index')
            ->with('success', 'تم حذف سجل التسجيل بنجاح.');
    }
}

