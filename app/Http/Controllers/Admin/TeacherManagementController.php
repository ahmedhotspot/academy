<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Users\CreateUserAction;
use App\Http\Requests\Admin\Teachers\StoreTeacherRequest;
use App\Services\Admin\UserManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TeacherManagementController extends AdminController
{
    protected string $title = 'إضافة معلم';

    public function __construct(private readonly UserManagementService $userManagementService)
    {
    }

    public function create(): View
    {
        return $this->adminView('admin.teachers.create', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة المستخدمين', 'url' => route('admin.users.index')],
                ['title' => 'إضافة معلم'],
            ],
            'branches' => $this->userManagementService->getBranchOptions(),
            'statuses' => $this->userManagementService->getStatusOptions(),
        ]);
    }

    public function store(StoreTeacherRequest $request, CreateUserAction $createUserAction): RedirectResponse
    {
        $payload = $request->validated();
        $payload['role'] = 'المعلم';

        $createUserAction->handle($payload);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'تمت إضافة المعلم بنجاح.');
    }
}

