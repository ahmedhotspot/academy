<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Users\CreateUserAction;
use App\Actions\Admin\Users\DeleteUserAction;
use App\Actions\Admin\Users\UpdateUserAction;
use App\Http\Requests\Admin\Teachers\StoreTeacherRequest;
use App\Http\Requests\Admin\Teachers\UpdateTeacherRequest;
use App\Models\User;
use App\Services\Admin\TeacherManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeacherManagementController extends AdminController
{
    protected string $title = 'إدارة المعلمين';

    public function __construct(private readonly TeacherManagementService $teacherManagementService)
    {
    }

    public function index(): View
    {
        $actions = [];

        if (auth()->user()?->can('users.create')) {
            $actions[] = [
                'title' => 'إضافة معلم',
                'url' => route('admin.teachers.create'),
                'icon' => 'ti ti-plus',
                'class' => 'btn-primary',
            ];
        }

        return $this->adminView('admin.teachers.index', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة المعلمين'],
            ],
            'actions' => $actions,
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        return response()->json($this->teacherManagementService->datatable($request));
    }

    public function create(): View
    {
        return $this->adminView('admin.teachers.create', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة المعلمين', 'url' => route('admin.teachers.index')],
                ['title' => 'إضافة معلم'],
            ],
            'branches' => $this->teacherManagementService->getBranchOptions(),
            'statuses' => $this->teacherManagementService->getStatusOptions(),
        ]);
    }

    public function store(StoreTeacherRequest $request, CreateUserAction $createUserAction): RedirectResponse
    {
        $payload = $request->validated();
        $payload['role'] = 'المعلم';

        $createUserAction->handle($payload);

        return redirect()
            ->route('admin.teachers.index')
            ->with('success', 'تمت إضافة المعلم بنجاح.');
    }

    public function show(User $teacher): View
    {
        $teacher = $this->teacherManagementService->findTeacherOrFail($teacher);
        $profile = $this->teacherManagementService->getTeacherProfile($teacher);

        return $this->adminView('admin.teachers.show', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة المعلمين', 'url' => route('admin.teachers.index')],
                ['title' => 'عرض المعلم'],
            ],
            'teacher' => $teacher,
            'profile' => $profile,
        ]);
    }

    public function edit(User $teacher): View
    {
        $teacher = $this->teacherManagementService->findTeacherOrFail($teacher);

        return $this->adminView('admin.teachers.edit', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة المعلمين', 'url' => route('admin.teachers.index')],
                ['title' => 'تعديل المعلم'],
            ],
            'teacher' => $teacher,
            'branches' => $this->teacherManagementService->getBranchOptions(),
            'statuses' => $this->teacherManagementService->getStatusOptions(),
        ]);
    }

    public function update(UpdateTeacherRequest $request, User $teacher, UpdateUserAction $updateUserAction): RedirectResponse
    {
        $teacher = $this->teacherManagementService->findTeacherOrFail($teacher);

        $payload = $request->validated();
        $payload['user'] = $teacher;
        $payload['role'] = 'المعلم';

        $updateUserAction->handle($payload);

        return redirect()
            ->route('admin.teachers.show', $teacher)
            ->with('success', 'تم تحديث بيانات المعلم بنجاح.');
    }

    public function destroy(User $teacher, DeleteUserAction $deleteUserAction): RedirectResponse
    {
        $teacher = $this->teacherManagementService->findTeacherOrFail($teacher);

        $deleted = $deleteUserAction->handle(['user' => $teacher]);

        if (! $deleted) {
            return redirect()
                ->route('admin.teachers.index')
                ->with('error', 'لا يمكن حذف المستخدم الحالي.');
        }

        return redirect()
            ->route('admin.teachers.index')
            ->with('success', 'تم حذف المعلم بنجاح.');
    }
}
