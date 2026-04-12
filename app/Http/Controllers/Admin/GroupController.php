<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Groups\CreateGroupAction;
use App\Actions\Admin\Groups\DeleteGroupAction;
use App\Actions\Admin\Groups\UpdateGroupAction;
use App\Http\Requests\Admin\Groups\StoreGroupRequest;
use App\Http\Requests\Admin\Groups\UpdateGroupRequest;
use App\Models\Group;
use App\Services\Admin\GroupManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GroupController extends AdminController
{
    protected string $title = 'إدارة الحلقات';

    public function __construct(private readonly GroupManagementService $groupManagementService)
    {
    }

    public function index(): View
    {
        $actions = [];

        if (auth()->user()?->can('groups.create')) {
            $actions[] = [
                'title' => 'إضافة حلقة',
                'url' => route('admin.groups.create'),
                'icon' => 'ti ti-plus',
                'class' => 'btn-primary',
            ];
        }

        return $this->adminView('admin.groups.index', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة الحلقات'],
            ],
            'actions' => $actions,
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        return response()->json($this->groupManagementService->datatable($request));
    }

    public function create(): View
    {
        return $this->adminView('admin.groups.create', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة الحلقات', 'url' => route('admin.groups.index')],
                ['title' => 'إضافة حلقة'],
            ],
            'branchOptions' => $this->groupManagementService->getBranchOptions(),
            'teacherOptions' => $this->groupManagementService->getTeacherOptions(),
            'studyLevelOptions' => $this->groupManagementService->getStudyLevelOptions(),
            'studyTrackOptions' => $this->groupManagementService->getStudyTrackOptions(),
        ]);
    }

    public function store(StoreGroupRequest $request, CreateGroupAction $createGroupAction): RedirectResponse
    {
        $createGroupAction->handle($this->normalizeBranchPayload($request->validated()));

        return redirect()
            ->route('admin.groups.index')
            ->with('success', 'تمت إضافة الحلقة بنجاح.');
    }

    public function show(Group $group): View
    {
        $profile = $this->groupManagementService->getGroupProfile($group);

        return $this->adminView('admin.groups.show', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة الحلقات', 'url' => route('admin.groups.index')],
                ['title' => 'تفاصيل الحلقة'],
            ],
            'group' => $group,
            'profile' => $profile,
        ]);
    }

    public function edit(Group $group): View
    {
        return $this->adminView('admin.groups.edit', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة الحلقات', 'url' => route('admin.groups.index')],
                ['title' => 'تعديل الحلقة'],
            ],
            'group' => $group,
            'branchOptions' => $this->groupManagementService->getBranchOptions(),
            'teacherOptions' => $this->groupManagementService->getTeacherOptions(),
            'studyLevelOptions' => $this->groupManagementService->getStudyLevelOptions(),
            'studyTrackOptions' => $this->groupManagementService->getStudyTrackOptions(),
        ]);
    }

    public function update(UpdateGroupRequest $request, Group $group, UpdateGroupAction $updateGroupAction): RedirectResponse
    {
        $payload = $this->normalizeBranchPayload($request->validated());
        $payload['group'] = $group;

        $updateGroupAction->handle($payload);

        return redirect()
            ->route('admin.groups.index')
            ->with('success', 'تم تحديث بيانات الحلقة بنجاح.');
    }

    public function destroy(Group $group, DeleteGroupAction $deleteGroupAction): RedirectResponse
    {
        $deleteGroupAction->handle(['group' => $group]);

        return redirect()
            ->route('admin.groups.index')
            ->with('success', 'تم حذف الحلقة بنجاح.');
    }

    private function normalizeBranchPayload(array $payload): array
    {
        $user = auth()->user();

        if ($user && ! $user->isSuperAdmin() && $user->branch_id) {
            $payload['branch_id'] = (int) $user->branch_id;
        }

        return $payload;
    }
}

