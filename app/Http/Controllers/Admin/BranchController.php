<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Branches\CreateBranchAction;
use App\Actions\Admin\Branches\DeleteBranchAction;
use App\Actions\Admin\Branches\UpdateBranchAction;
use App\Http\Requests\Admin\Branches\StoreBranchRequest;
use App\Http\Requests\Admin\Branches\UpdateBranchRequest;
use App\Models\Branch;
use App\Services\Admin\BranchManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchController extends AdminController
{
    protected string $title = 'إدارة الفروع';

    public function __construct(private readonly BranchManagementService $branchManagementService)
    {
    }

    public function index(): View
    {
        $actions = [];

        if (auth()->user()?->can('branches.create')) {
            $actions[] = [
                'title' => 'إضافة فرع',
                'url' => route('admin.branches.create'),
                'icon' => 'ti ti-plus',
                'class' => 'btn-primary',
            ];
        }

        return $this->adminView('admin.branches.index', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة الفروع'],
            ],
            'actions' => $actions,
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        return response()->json($this->branchManagementService->datatable($request));
    }

    public function create(): View
    {
        return $this->adminView('admin.branches.create', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة الفروع', 'url' => route('admin.branches.index')],
                ['title' => 'إضافة فرع'],
            ],
        ]);
    }

    public function store(StoreBranchRequest $request, CreateBranchAction $createBranchAction): RedirectResponse
    {
        $branch = $createBranchAction->handle($request->validated());

        return redirect()
            ->route('admin.branches.show', $branch)
            ->with('success', 'تمت إضافة الفرع بنجاح.');
    }

    public function show(Branch $branch): View
    {
        $this->authorizeBranchAccess($branch);

        $branchProfile = $this->branchManagementService->getBranchProfile($branch);

        return $this->adminView('admin.branches.show', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة الفروع', 'url' => route('admin.branches.index')],
                ['title' => 'تفاصيل الفرع'],
            ],
            'branch' => $branch,
            'profile' => $branchProfile,
        ]);
    }

    public function edit(Branch $branch): View
    {
        $this->authorizeBranchAccess($branch);

        return $this->adminView('admin.branches.edit', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة الفروع', 'url' => route('admin.branches.index')],
                ['title' => 'تعديل الفرع'],
            ],
            'branch' => $branch,
        ]);
    }

    public function update(UpdateBranchRequest $request, Branch $branch, UpdateBranchAction $updateBranchAction): RedirectResponse
    {
        $this->authorizeBranchAccess($branch);

        $payload = $request->validated();
        $payload['branch'] = $branch;
        $updated = $updateBranchAction->handle($payload);

        return redirect()
            ->route('admin.branches.show', $updated)
            ->with('success', 'تم تحديث بيانات الفرع بنجاح.');
    }

    public function destroy(Branch $branch, DeleteBranchAction $deleteBranchAction): RedirectResponse
    {
        $this->authorizeBranchAccess($branch);

        $deleted = $deleteBranchAction->handle(['branch' => $branch]);

        if (! $deleted) {
            return redirect()
                ->back()
                ->withErrors(['delete' => 'لا يمكن حذف الفرع لارتباطه ببيانات تشغيلية.']);
        }

        return redirect()
            ->route('admin.branches.index')
            ->with('success', 'تم حذف الفرع بنجاح.');
    }

    private function authorizeBranchAccess(Branch $branch): void
    {
        $user = auth()->user();

        if (! $user || $user->isSuperAdmin()) {
            return;
        }

        abort_unless((int) $branch->id === (int) $user->branch_id, 403);
    }
}

