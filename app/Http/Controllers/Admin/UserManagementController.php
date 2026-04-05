<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Users\CreateUserAction;
use App\Actions\Admin\Users\DeleteUserAction;
use App\Actions\Admin\Users\UpdateUserAction;
use App\Http\Requests\Admin\Users\StoreUserRequest;
use App\Http\Requests\Admin\Users\UpdateUserRequest;
use App\Models\User;
use App\Services\Admin\UserManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserManagementController extends AdminController
{
    protected string $title = 'إدارة المستخدمين';

    public function __construct(private readonly UserManagementService $userManagementService)
    {
    }

    public function index(): View
    {
        $actions = [];

        if (auth()->user()?->can('users.create')) {
            $actions[] = [
                'title' => 'إضافة مستخدم',
                'url' => route('admin.users.create'),
                'icon' => 'ti ti-plus',
                'class' => 'btn-primary',
            ];
        }

        return $this->adminView('admin.users.index', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة المستخدمين'],
            ],
            'actions' => $actions,
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        return response()->json($this->userManagementService->datatable($request));
    }

    public function create(): View
    {
        return $this->adminView('admin.users.create', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة المستخدمين', 'url' => route('admin.users.index')],
                ['title' => 'إضافة مستخدم'],
            ],
            'roles' => $this->userManagementService->getRoleOptions(),
            'branches' => $this->userManagementService->getBranchOptions(),
            'statuses' => $this->userManagementService->getStatusOptions(),
        ]);
    }

    public function store(StoreUserRequest $request, CreateUserAction $createUserAction): RedirectResponse
    {
        $createUserAction->handle($request->validated());

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'تمت إضافة المستخدم بنجاح.');
    }

    public function show(User $user): View
    {
        $profile = $this->userManagementService->getUserProfile($user);

        return $this->adminView('admin.users.show', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة المستخدمين', 'url' => route('admin.users.index')],
                ['title' => 'عرض المستخدم'],
            ],
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    public function edit(User $user): View
    {
        return $this->adminView('admin.users.edit', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة المستخدمين', 'url' => route('admin.users.index')],
                ['title' => 'تعديل المستخدم'],
            ],
            'user' => $user->load('roles'),
            'roles' => $this->userManagementService->getRoleOptions(),
            'branches' => $this->userManagementService->getBranchOptions(),
            'statuses' => $this->userManagementService->getStatusOptions(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user, UpdateUserAction $updateUserAction): RedirectResponse
    {
        $validated = $request->validated();
        $validated['user'] = $user;

        $updateUserAction->handle($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'تم تحديث بيانات المستخدم بنجاح.');
    }

    public function destroy(User $user, DeleteUserAction $deleteUserAction): RedirectResponse
    {
        $deleted = $deleteUserAction->handle(['user' => $user]);

        if (! $deleted) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'لا يمكن حذف المستخدم الحالي.');
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'تم حذف المستخدم بنجاح.');
    }
}

