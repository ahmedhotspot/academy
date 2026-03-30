<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Payments\CreatePaymentAction;
use App\Actions\Admin\Payments\DeletePaymentAction;
use App\Actions\Admin\Payments\UpdatePaymentAction;
use App\Http\Requests\Admin\Payments\StorePaymentRequest;
use App\Http\Requests\Admin\Payments\UpdatePaymentRequest;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentSubscription;
use App\Services\Admin\PaymentManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends AdminController
{
    protected string $title = 'إدارة المدفوعات';

    public function __construct(
        private readonly PaymentManagementService $service
    ) {}

    public function index(Request $request): View
    {
        $actions = [];

        if (auth()->user()?->can('payments.create')) {
            $actions[] = [
                'title' => 'إضافة دفعة جديدة',
                'url'   => route('admin.payments.create'),
                'icon'  => 'ti ti-plus',
                'class' => 'btn-primary',
            ];
        }

        $filters = [
            'student_id' => $request->input('student_id'),
        ];

        return $this->adminView('admin.payments.index', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة المدفوعات'],
            ],
            'actions'       => $actions,
            'reportSummary' => $this->service->reportSummary($filters),
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        return response()->json($this->service->datatable($request));
    }

    public function create(): View
    {
        return $this->adminView('admin.payments.create', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة المدفوعات', 'url' => route('admin.payments.index')],
                ['title' => 'إضافة دفعة جديدة'],
            ],
            'studentOptions'       => Student::where('status', 'active')->orderBy('full_name')->pluck('full_name', 'id')->toArray(),
        ]);
    }

    public function store(
        StorePaymentRequest $request,
        CreatePaymentAction $action
    ): RedirectResponse {
        $payment = $action->handle($request->validated());

        return redirect()
            ->route('admin.payments.show', $payment)
            ->with('success', 'تم تسجيل الدفعة بنجاح.');
    }

    public function show(Payment $payment): View
    {
        $payment->load(['student', 'subscription']);

        return $this->adminView('admin.payments.show', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة المدفوعات', 'url' => route('admin.payments.index')],
                ['title' => 'تفاصيل الدفعة والإيصال'],
            ],
            'payment' => $payment,
        ]);
    }

    public function edit(Payment $payment): View
    {
        $payment->load(['student', 'subscription']);

        return $this->adminView('admin.payments.edit', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة المدفوعات', 'url' => route('admin.payments.index')],
                ['title' => 'تعديل الدفعة'],
            ],
            'payment' => $payment,
        ]);
    }

    public function update(
        UpdatePaymentRequest $request,
        Payment $payment,
        UpdatePaymentAction $action
    ): RedirectResponse {
        $payload = $request->validated();
        $payload['payment'] = $payment;

        $updated = $action->handle($payload);

        return redirect()
            ->route('admin.payments.show', $updated)
            ->with('success', 'تم تحديث الدفعة بنجاح.');
    }

    public function destroy(
        Payment $payment,
        DeletePaymentAction $action
    ): RedirectResponse {
        $action->handle(['payment' => $payment]);

        return redirect()
            ->route('admin.payments.index')
            ->with('success', 'تم حذف الدفعة بنجاح.');
    }
}

