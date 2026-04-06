<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Guardians\CreateGuardianAction;
use App\Actions\Admin\Guardians\DeleteGuardianAction;
use App\Actions\Admin\Guardians\UpdateGuardianAction;
use App\Http\Requests\Admin\Guardians\StoreGuardianRequest;
use App\Http\Requests\Admin\Guardians\UpdateGuardianRequest;
use App\Models\Guardian;
use App\Services\Admin\GuardianManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class GuardianController extends AdminController
{
    protected string $title = 'إدارة أولياء الأمور';

    public function __construct(private readonly GuardianManagementService $guardianManagementService)
    {
    }

    public function index(): View
    {
        $actions = [];

        if (auth()->user()?->can('guardians.create')) {
            $actions[] = [
                'title' => 'إضافة ولي أمر',
                'url' => route('admin.guardians.create'),
                'icon' => 'ti ti-plus',
                'class' => 'btn-primary',
            ];
        }

        return $this->adminView('admin.guardians.index', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة أولياء الأمور'],
            ],
            'actions' => $actions,
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        return response()->json($this->guardianManagementService->datatable($request));
    }

    public function create(): View
    {
        return $this->adminView('admin.guardians.create', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة أولياء الأمور', 'url' => route('admin.guardians.index')],
                ['title' => 'إضافة ولي أمر'],
            ],
            'branches' => $this->guardianManagementService->getBranchOptions(),
        ]);
    }

    public function store(StoreGuardianRequest $request, CreateGuardianAction $createGuardianAction): RedirectResponse
    {
        $payload = $this->prepareGuardianPayload($request->validated());

        $createGuardianAction->handle($this->normalizeBranchPayload($payload));

        return redirect()
            ->route('admin.guardians.index')
            ->with('success', 'تمت إضافة ولي الأمر بنجاح.');
    }

    public function show(Guardian $guardian): View
    {
        $this->authorizeGuardianAccess($guardian);

        $profile = $this->guardianManagementService->getGuardianProfile($guardian);

        return $this->adminView('admin.guardians.show', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة أولياء الأمور', 'url' => route('admin.guardians.index')],
                ['title' => 'تفاصيل ولي الأمر'],
            ],
            'guardian' => $guardian,
            'profile' => $profile,
        ]);
    }

    public function edit(Guardian $guardian): View
    {
        $this->authorizeGuardianAccess($guardian);

        return $this->adminView('admin.guardians.edit', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة أولياء الأمور', 'url' => route('admin.guardians.index')],
                ['title' => 'تعديل ولي الأمر'],
            ],
            'guardian' => $guardian,
            'branches' => $this->guardianManagementService->getBranchOptions(),
        ]);
    }

    public function update(UpdateGuardianRequest $request, Guardian $guardian, UpdateGuardianAction $updateGuardianAction): RedirectResponse
    {
        $this->authorizeGuardianAccess($guardian);

        $payload = $this->normalizeBranchPayload($this->prepareGuardianPayload($request->validated()));
        $payload['guardian'] = $guardian;

        $updateGuardianAction->handle($payload);

        return redirect()
            ->route('admin.guardians.index')
            ->with('success', 'تم تحديث بيانات ولي الأمر بنجاح.');
    }

    public function destroy(Guardian $guardian, DeleteGuardianAction $deleteGuardianAction): RedirectResponse
    {
        $this->authorizeGuardianAccess($guardian);

        $deleted = $deleteGuardianAction->handle(['guardian' => $guardian]);

        if (! $deleted) {
            return redirect()
                ->route('admin.guardians.index')
                ->with('error', 'لا يمكن حذف ولي الأمر لوجود طلاب مرتبطين به.');
        }

        return redirect()
            ->route('admin.guardians.index')
            ->with('success', 'تم حذف ولي الأمر بنجاح.');
    }

    public function setPortalPassword(Request $request, Guardian $guardian): RedirectResponse
    {
        $this->authorizeGuardianAccess($guardian);

        $request->validate([
            'portal_password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'portal_password.required'  => 'كلمة المرور مطلوبة.',
            'portal_password.min'       => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل.',
            'portal_password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
        ]);

        $guardian->update(['password' => Hash::make($request->portal_password)]);

        return redirect()
            ->route('admin.guardians.show', $guardian)
            ->with('success', 'تم تعيين كلمة مرور البوابة بنجاح.');
    }

    private function authorizeGuardianAccess(Guardian $guardian): void
    {
        $user = auth()->user();

        if (! $user || $user->isSuperAdmin()) {
            return;
        }

        abort_unless((int) $guardian->branch_id === (int) $user->branch_id, 403);
    }

    private function normalizeBranchPayload(array $payload): array
    {
        $user = auth()->user();

        if ($user && ! $user->isSuperAdmin() && $user->branch_id) {
            $payload['branch_id'] = (int) $user->branch_id;
        }

        return $payload;
    }

    private function prepareGuardianPayload(array $payload): array
    {
        if (! empty($payload['portal_password'])) {
            $payload['password'] = Hash::make($payload['portal_password']);
        }

        unset($payload['portal_password'], $payload['portal_password_confirmation']);

        return $payload;
    }
}

