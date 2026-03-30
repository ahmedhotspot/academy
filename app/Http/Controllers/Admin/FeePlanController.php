<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\FeePlans\CreateFeePlanAction;
use App\Actions\Admin\FeePlans\DeleteFeePlanAction;
use App\Actions\Admin\FeePlans\UpdateFeePlanAction;
use App\Http\Requests\Admin\FeePlans\StoreFeePlanRequest;
use App\Http\Requests\Admin\FeePlans\UpdateFeePlanRequest;
use App\Models\FeePlan;
use App\Services\Admin\FeePlanManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeePlanController extends AdminController
{
    protected string $title = 'إدارة خطط الرسوم';

    public function __construct(
        private readonly FeePlanManagementService $service
    ) {}

    public function index(Request $request): View
    {
        $actions = [];

        if (auth()->user()?->can('fee-plans.create')) {
            $actions[] = [
                'title' => 'إضافة خطة رسوم جديدة',
                'url'   => route('admin.fee-plans.create'),
                'icon'  => 'ti ti-plus',
                'class' => 'btn-primary',
            ];
        }

        $filters = [
            'status' => $request->input('status'),
        ];

        return $this->adminView('admin.fee-plans.index', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة خطط الرسوم'],
            ],
            'actions'           => $actions,
            'paymentCycles'     => $this->service->getPaymentCycles(),
            'statuses'          => $this->service->getStatuses(),
            'reportSummary'     => $this->service->reportSummary($filters),
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        return response()->json($this->service->datatable($request));
    }

    public function create(): View
    {
        return $this->adminView('admin.fee-plans.create', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة خطط الرسوم', 'url' => route('admin.fee-plans.index')],
                ['title' => 'إضافة خطة رسوم جديدة'],
            ],
            'paymentCycles' => $this->service->getPaymentCycles(),
            'statuses'      => $this->service->getStatuses(),
        ]);
    }

    public function store(
        StoreFeePlanRequest $request,
        CreateFeePlanAction $action
    ): RedirectResponse {
        $feePlan = $action->handle($request->validated());

        return redirect()
            ->route('admin.fee-plans.show', $feePlan)
            ->with('success', 'تم إضافة خطة الرسوم بنجاح.');
    }

    public function show(FeePlan $feePlan): View
    {
        $profile = $this->service->getFeePlanProfile($feePlan);

        return $this->adminView('admin.fee-plans.show', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة خطط الرسوم', 'url' => route('admin.fee-plans.index')],
                ['title' => 'تفاصيل خطة الرسوم'],
            ],
            'feePlan' => $feePlan,
            'profile' => $profile,
        ]);
    }

    public function edit(FeePlan $feePlan): View
    {
        return $this->adminView('admin.fee-plans.edit', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة خطط الرسوم', 'url' => route('admin.fee-plans.index')],
                ['title' => 'تعديل خطة الرسوم'],
            ],
            'feePlan'       => $feePlan,
            'paymentCycles' => $this->service->getPaymentCycles(),
            'statuses'      => $this->service->getStatuses(),
        ]);
    }

    public function update(
        UpdateFeePlanRequest $request,
        FeePlan $feePlan,
        UpdateFeePlanAction $action
    ): RedirectResponse {
        $payload = $request->validated();
        $payload['feePlan'] = $feePlan;

        $updated = $action->handle($payload);

        return redirect()
            ->route('admin.fee-plans.show', $updated)
            ->with('success', 'تم تحديث خطة الرسوم بنجاح.');
    }

    public function destroy(
        FeePlan $feePlan,
        DeleteFeePlanAction $action
    ): RedirectResponse {
        $action->handle(['feePlan' => $feePlan]);

        return redirect()
            ->route('admin.fee-plans.index')
            ->with('success', 'تم حذف خطة الرسوم بنجاح.');
    }
}

