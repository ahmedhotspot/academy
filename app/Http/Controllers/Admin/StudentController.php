<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Students\CreateStudentAction;
use App\Actions\Admin\Students\DeleteStudentAction;
use App\Actions\Admin\Students\UpdateStudentAction;
use App\Http\Requests\Admin\Students\StoreStudentRequest;
use App\Http\Requests\Admin\Students\UpdateStudentRequest;
use App\Models\Student;
use App\Services\Admin\StudentManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StudentController extends AdminController
{
    protected string $title = 'إدارة الطلاب';

    public function __construct(private readonly StudentManagementService $studentManagementService)
    {
    }

    public function index(): View
    {
        $actions = [];

        if (auth()->user()?->can('students.create')) {
            $actions[] = [
                'title' => 'إضافة طالب',
                'url' => route('admin.students.create'),
                'icon' => 'ti ti-plus',
                'class' => 'btn-primary',
            ];
        }

        return $this->adminView('admin.students.index', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة الطلاب'],
            ],
            'actions' => $actions,
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        return response()->json($this->studentManagementService->datatable($request));
    }

    public function create(): View
    {
        return $this->adminView('admin.students.create', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة الطلاب', 'url' => route('admin.students.index')],
                ['title' => 'إضافة طالب'],
            ],
            'branchOptions' => $this->studentManagementService->getBranchOptions(),
            'guardianOptions' => $this->studentManagementService->getGuardianOptions(),
        ]);
    }

    public function store(StoreStudentRequest $request, CreateStudentAction $createStudentAction): RedirectResponse
    {
        $createStudentAction->handle($request->validated());

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'تمت إضافة الطالب بنجاح.');
    }

    public function show(Student $student): View
    {
        $profile = $this->studentManagementService->getStudentProfile($student);

        return $this->adminView('admin.students.show', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة الطلاب', 'url' => route('admin.students.index')],
                ['title' => 'تفاصيل الطالب'],
            ],
            'student' => $student,
            'profile' => $profile,
        ]);
    }

    public function edit(Student $student): View
    {
        return $this->adminView('admin.students.edit', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة الطلاب', 'url' => route('admin.students.index')],
                ['title' => 'تعديل الطالب'],
            ],
            'student' => $student,
            'branchOptions' => $this->studentManagementService->getBranchOptions(),
            'guardianOptions' => $this->studentManagementService->getGuardianOptions(),
        ]);
    }

    public function update(UpdateStudentRequest $request, Student $student, UpdateStudentAction $updateStudentAction): RedirectResponse
    {
        $payload = $request->validated();
        $payload['student'] = $student;

        $updateStudentAction->handle($payload);

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'تم تحديث بيانات الطالب بنجاح.');
    }

    public function destroy(Student $student, DeleteStudentAction $deleteStudentAction): RedirectResponse
    {
        $deleteStudentAction->handle(['student' => $student]);

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'تم حذف الطالب بنجاح.');
    }

    public function setPortalPassword(Request $request, Student $student): RedirectResponse
    {
        $request->validate([
            'portal_password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'portal_password.required'  => 'كلمة المرور مطلوبة.',
            'portal_password.min'       => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل.',
            'portal_password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
        ]);

        $student->update(['password' => Hash::make($request->portal_password)]);

        return redirect()
            ->route('admin.students.show', $student)
            ->with('success', 'تم تعيين كلمة مرور البوابة بنجاح.');
    }
}

