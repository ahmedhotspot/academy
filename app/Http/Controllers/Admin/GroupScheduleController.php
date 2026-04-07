<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\GroupSchedules\CreateGroupScheduleAction;
use App\Actions\Admin\GroupSchedules\DeleteGroupScheduleAction;
use App\Actions\Admin\GroupSchedules\UpdateGroupScheduleAction;
use App\Http\Requests\Admin\GroupSchedules\StoreGroupScheduleRequest;
use App\Http\Requests\Admin\GroupSchedules\UpdateGroupScheduleRequest;
use App\Models\GroupSchedule;
use App\Services\Admin\GroupScheduleManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GroupScheduleController extends AdminController
{
    protected string $title = 'إدارة جداول الحلقات';

    public function __construct(private readonly GroupScheduleManagementService $groupScheduleManagementService)
    {
    }

    public function index(): View
    {
        $actions = [];

        if (auth()->user()?->can('group-schedules.create')) {
            $actions[] = [
                'title' => 'إضافة جدول حلقة',
                'url' => route('admin.group-schedules.create'),
                'icon' => 'ti ti-plus',
                'class' => 'btn-primary',
            ];
        }

        return $this->adminView('admin.group-schedules.index', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة جداول الحلقات'],
            ],
            'actions' => $actions,
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        return response()->json($this->groupScheduleManagementService->datatable($request));
    }

    public function create(Request $request): View
    {
        $selectedGroupId = null;

        if ($request->filled('group_id')) {
            $requestedGroupId = (int) $request->query('group_id');
            $groupOptions = $this->groupScheduleManagementService->getGroupOptions();

            // Preselect only if the group is available in current branch scope/options.
            if (isset($groupOptions[$requestedGroupId])) {
                $selectedGroupId = $requestedGroupId;
            }
        }

        return $this->adminView('admin.group-schedules.create', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة جداول الحلقات', 'url' => route('admin.group-schedules.index')],
                ['title' => 'إضافة جدول'],
            ],
            'groupOptions' => $this->groupScheduleManagementService->getGroupOptions(),
            'selectedGroupId' => $selectedGroupId,
        ]);
    }

    public function store(StoreGroupScheduleRequest $request, CreateGroupScheduleAction $createGroupScheduleAction): RedirectResponse
    {
        $createGroupScheduleAction->handle($request->validated());

        return redirect()
            ->route('admin.group-schedules.index')
            ->with('success', 'تمت إضافة جدول الحلقة بنجاح.');
    }

    public function show(GroupSchedule $groupSchedule): View
    {
        $profile = $this->groupScheduleManagementService->getGroupScheduleProfile($groupSchedule);

        return $this->adminView('admin.group-schedules.show', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة جداول الحلقات', 'url' => route('admin.group-schedules.index')],
                ['title' => 'تفاصيل الجدول'],
            ],
            'groupSchedule' => $groupSchedule,
            'profile' => $profile,
        ]);
    }

    public function edit(GroupSchedule $groupSchedule): View
    {
        return $this->adminView('admin.group-schedules.edit', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة جداول الحلقات', 'url' => route('admin.group-schedules.index')],
                ['title' => 'تعديل الجدول'],
            ],
            'groupSchedule' => $groupSchedule,
            'groupOptions' => $this->groupScheduleManagementService->getGroupOptions(),
        ]);
    }

    public function update(UpdateGroupScheduleRequest $request, GroupSchedule $groupSchedule, UpdateGroupScheduleAction $updateGroupScheduleAction): RedirectResponse
    {
        $payload = $request->validated();
        $payload['groupSchedule'] = $groupSchedule;

        $updateGroupScheduleAction->handle($payload);

        return redirect()
            ->route('admin.group-schedules.index')
            ->with('success', 'تم تحديث جدول الحلقة بنجاح.');
    }

    public function destroy(GroupSchedule $groupSchedule, DeleteGroupScheduleAction $deleteGroupScheduleAction): RedirectResponse
    {
        $deleteGroupScheduleAction->handle(['groupSchedule' => $groupSchedule]);

        return redirect()
            ->route('admin.group-schedules.index')
            ->with('success', 'تم حذف جدول الحلقة بنجاح.');
    }
}

