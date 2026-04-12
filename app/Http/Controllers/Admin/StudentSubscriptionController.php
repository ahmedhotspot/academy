<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\StudentSubscriptions\CreateStudentSubscriptionAction;
use App\Actions\Admin\StudentSubscriptions\DeleteStudentSubscriptionAction;
use App\Actions\Admin\StudentSubscriptions\RenewStudentSubscriptionAction;
use App\Actions\Admin\StudentSubscriptions\UpdateStudentSubscriptionAction;
use App\Http\Requests\Admin\StudentSubscriptions\StoreStudentSubscriptionRequest;
use App\Http\Requests\Admin\StudentSubscriptions\UpdateStudentSubscriptionRequest;
use App\Models\FeePlan;
use App\Models\StudentSubscription;
use App\Services\Admin\StudentSubscriptionManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentSubscriptionController extends AdminController
{
    protected string $title = 'إدارة اشتراكات الطلاب';

    public function __construct(
        private readonly StudentSubscriptionManagementService $service
    ) {}

    public function index(Request $request): View
    {
        $actions = [];

        if (auth()->user()?->can('student-subscriptions.create')) {
            $actions[] = [
                'title' => 'إضافة اشتراك جديد',
                'url'   => route('admin.student-subscriptions.create'),
                'icon'  => 'ti ti-plus',
                'class' => 'btn-primary',
            ];
        }

        $filters = [
            'status' => $request->input('status'),
        ];

        return $this->adminView('admin.student-subscriptions.index', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة اشتراكات الطلاب'],
            ],
            'actions'           => $actions,
            'statuses'          => $this->service->getStatuses(),
            'feePlanOptions'    => $this->service->getFeePlanOptions(),
            'reportSummary'     => $this->service->reportSummary($filters),
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        return response()->json($this->service->datatable($request));
    }

    public function overdueDatatable(Request $request): JsonResponse
    {
        return response()->json($this->service->overdueDatatable($request));
    }

    public function create(): View
    {
        return $this->adminView('admin.student-subscriptions.create', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة اشتراكات الطلاب', 'url' => route('admin.student-subscriptions.index')],
                ['title' => 'إضافة اشتراك جديد'],
            ],
            'studentOptions'  => $this->service->getStudentOptions(),
            'studentStatuses' => $this->service->getStudentStatuses(),
            'feePlanOptions'  => $this->service->getFeePlanOptions(),
            'statuses'        => $this->service->getStatuses(),
            'paymentMethodOptions' => $this->service->getPaymentMethodOptions(),
        ]);
    }

    public function store(
        StoreStudentSubscriptionRequest $request,
        CreateStudentSubscriptionAction $action
    ): RedirectResponse {
        $subscription = $action->handle($request->validated());

        return redirect()
            ->route('admin.student-subscriptions.show', $subscription)
            ->with('success', 'تم إضافة الاشتراك بنجاح.');
    }

    public function show(StudentSubscription $studentSubscription): View
    {
        $studentSubscription->load(['student', 'feePlan']);

        return $this->adminView('admin.student-subscriptions.show', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة اشتراكات الطلاب', 'url' => route('admin.student-subscriptions.index')],
                ['title' => 'تفاصيل الاشتراك'],
            ],
            'subscription' => $studentSubscription,
        ]);
    }

    public function edit(StudentSubscription $studentSubscription): View
    {
        $studentSubscription->load(['student', 'feePlan']);

        return $this->adminView('admin.student-subscriptions.edit', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة اشتراكات الطلاب', 'url' => route('admin.student-subscriptions.index')],
                ['title' => 'تعديل الاشتراك'],
            ],
            'subscription'     => $studentSubscription,
            'studentOptions'   => $this->service->getStudentOptions($studentSubscription->student_id),
            'studentStatuses'  => $this->service->getStudentStatuses($studentSubscription->student_id),
            'feePlanOptions'   => $this->service->getFeePlanOptions(),
            'statuses'         => $this->service->getStatuses(),
            'paymentMethodOptions' => $this->service->getPaymentMethodOptions(),
        ]);
    }

    public function update(
        UpdateStudentSubscriptionRequest $request,
        StudentSubscription $studentSubscription,
        UpdateStudentSubscriptionAction $action
    ): RedirectResponse {
        $payload = $request->validated();
        $payload['subscription'] = $studentSubscription;

        $updated = $action->handle($payload);

        return redirect()
            ->route('admin.student-subscriptions.show', $updated)
            ->with('success', 'تم تحديث الاشتراك بنجاح.');
    }

    public function destroy(
        StudentSubscription $studentSubscription,
        DeleteStudentSubscriptionAction $action
    ): RedirectResponse {
        $action->handle(['subscription' => $studentSubscription]);

        return redirect()
            ->route('admin.student-subscriptions.index')
            ->with('success', 'تم حذف الاشتراك بنجاح.');
    }

    public function renew(
        StudentSubscription $studentSubscription,
        RenewStudentSubscriptionAction $action
    ): RedirectResponse {
        $newSubscription = $action->handle(['subscription' => $studentSubscription]);

        return redirect()
            ->route('admin.student-subscriptions.show', $newSubscription)
            ->with('success', 'تم تجديد الاشتراك بنجاح. تم إنشاء اشتراك جديد.');
    }

    public function feePlanAmount(Request $request): JsonResponse
    {
        $feePlanId = (int) $request->input('fee_plan_id');
        $feePlan = FeePlan::query()->find($feePlanId);

        if (! $feePlan) {
            return response()->json(['message' => 'خطة الرسوم غير موجودة.'], 404);
        }

        return response()->json([
            'id'               => $feePlan->id,
            'amount'           => (float) $feePlan->amount,
            'formatted_amount' => $feePlan->formatted_amount,
            'payment_cycle'    => $feePlan->payment_cycle,
        ]);
    }
}

