<?php

namespace App\Services\Admin;

use App\Models\FeePlan;
use App\Models\Payment;
use App\Models\StudentSubscription;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FeePlanManagementService extends BaseService
{
    /**
     * قائمة دورات الدفع
     */
    public function getPaymentCycles(): array
    {
        return FeePlan::PAYMENT_CYCLES;
    }

    /**
     * قائمة الحالات
     */
    public function getStatuses(): array
    {
        return FeePlan::STATUSES;
    }

    /**
     * بيانات DataTable Ajax لخطط الرسوم
     */
    public function datatable(Request $request): array
    {
        $draw   = (int) $request->input('draw', 1);
        $start  = max((int) $request->input('start', 0), 0);
        $length = max((int) $request->input('length', 10), 1);
        $search = trim((string) data_get($request->input('search'), 'value', ''));

        $baseQuery = FeePlan::query();

        if ($request->filled('payment_cycle')) {
            $baseQuery->where('payment_cycle', $request->input('payment_cycle'));
        }

        if ($request->filled('status')) {
            $baseQuery->where('status', $request->input('status'));
        }

        $recordsTotal = FeePlan::query()->count();

        if ($search !== '') {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('payment_cycle', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = (clone $baseQuery)->count();

        $rows = $baseQuery
            ->orderByDesc('id')
            ->skip($start)
            ->take($length)
            ->get();

        $data = $rows->map(function (FeePlan $plan) {
            return [
                'id'                   => $plan->id,
                'name'                 => $plan->name,
                'payment_cycle'        => $plan->payment_cycle,
                'payment_cycle_label'  => $plan->payment_cycle_label,
                'amount'               => $plan->amount,
                'formatted_amount'     => $plan->formatted_amount,
                'has_sisters_discount' => $plan->has_sisters_discount,
                'discount_label'       => $plan->discount_label,
                'discount_badge'       => $plan->discount_badge_class,
                'status'               => $plan->status,
                'status_label'         => $plan->status_label,
                'status_badge'         => $plan->status_badge_class,
            ];
        })->values()->all();

        return [
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ];
    }

    /**
     * ملخص إحصائي
     */
    public function reportSummary(array $filters = []): array
    {
        $query = FeePlan::query();

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $total    = (clone $query)->count();
        $active   = (clone $query)->where('status', 'active')->count();
        $inactive = (clone $query)->where('status', 'inactive')->count();
        $withDiscount = (clone $query)->where('has_sisters_discount', true)->count();

        return compact('total', 'active', 'inactive', 'withDiscount');
    }

    public function getFeePlanProfile(FeePlan $feePlan): array
    {
        $feePlan->load([
            'studentSubscriptions' => fn ($query) => $query
                ->with('student:id,full_name,phone,status')
                ->latest('id'),
        ]);

        $subscriptions = $feePlan->studentSubscriptions;
        $subscriptionIds = $subscriptions->pluck('id')->all();

        $paymentsQuery = Payment::query()->whereIn('student_subscription_id', $subscriptionIds);
        $monthStart = Carbon::now()->startOfMonth()->toDateString();
        $monthEnd = Carbon::now()->endOfMonth()->toDateString();

        $totalFinal = (float) $subscriptions->sum('final_amount');
        $totalPaid = (float) $subscriptions->sum('paid_amount');
        $totalRemaining = (float) $subscriptions->sum('remaining_amount');
        $overdueCount = $subscriptions
            ->where('status', 'متأخر')
            ->where('remaining_amount', '>', 0)
            ->count();

        $paymentsCount = $subscriptionIds !== [] ? (clone $paymentsQuery)->count() : 0;
        $paymentsMonth = $subscriptionIds !== []
            ? (clone $paymentsQuery)->whereBetween('payment_date', [$monthStart, $monthEnd])->sum('amount')
            : 0;

        $subscriptionsTable = $subscriptions->take(12)->map(function (StudentSubscription $subscription) {
            return [
                'id' => $subscription->id,
                'student' => $subscription->student?->full_name ?? '-',
                'phone' => $subscription->student?->phone ?? '-',
                'final' => $subscription->formatted_final_amount,
                'paid' => $subscription->formatted_paid_amount,
                'remaining' => $subscription->formatted_remaining_amount,
                'status' => $subscription->status,
                'status_badge' => $subscription->status_badge_class,
                'progress' => $subscription->payment_progress,
            ];
        })->values()->all();

        $recentPayments = $subscriptionIds !== []
            ? Payment::query()
                ->with('student:id,full_name')
                ->whereIn('student_subscription_id', $subscriptionIds)
                ->latest('payment_date')
                ->latest('id')
                ->limit(10)
                ->get()
                ->map(fn (Payment $payment) => [
                    'student' => $payment->student?->full_name ?? '-',
                    'date' => $payment->formatted_payment_date,
                    'amount' => $payment->formatted_amount,
                    'receipt' => $payment->receipt_formatted,
                    'notes' => $payment->notes ?: '-',
                ])
                ->values()
                ->all()
            : [];

        $topOutstanding = $subscriptions
            ->where('remaining_amount', '>', 0)
            ->sortByDesc('remaining_amount')
            ->take(8)
            ->map(fn (StudentSubscription $subscription) => [
                'student' => $subscription->student?->full_name ?? '-',
                'phone' => $subscription->student?->phone ?? '-',
                'remaining' => $subscription->formatted_remaining_amount,
                'status' => $subscription->status,
                'status_badge' => $subscription->status_badge_class,
            ])
            ->values()
            ->all();

        return [
            'stats' => [
                'subscriptions_count' => $subscriptions->count(),
                'active_subscriptions_count' => $subscriptions->where('status', 'نشط')->count(),
                'overdue_subscriptions_count' => $overdueCount,
                'complete_subscriptions_count' => $subscriptions->where('status', 'مكتمل')->count(),
                'payments_count' => $paymentsCount,
            ],
            'financial' => [
                'total_final' => number_format($totalFinal, 2) . ' ج',
                'total_paid' => number_format($totalPaid, 2) . ' ج',
                'total_remaining' => number_format($totalRemaining, 2) . ' ج',
                'payments_month' => number_format((float) $paymentsMonth, 2) . ' ج',
            ],
            'subscriptions' => $subscriptionsTable,
            'recent_payments' => $recentPayments,
            'top_outstanding' => $topOutstanding,
            'generated_at' => Carbon::now()->format('Y-m-d H:i'),
        ];
    }
}

