<?php

namespace App\Services\Admin;

use App\Models\FeePlan;
use App\Models\Student;
use App\Models\StudentSubscription;
use App\Services\BaseService;
use Illuminate\Http\Request;

class StudentSubscriptionManagementService extends BaseService
{
    /**
     * قائمة الطلاب النشطين
     */
    public function getStudentOptions(): array
    {
        return Student::query()
            ->where('status', 'active')
            ->orderBy('full_name')
            ->pluck('full_name', 'id')
            ->toArray();
    }

    /**
     * قائمة خطط الرسوم النشطة
     */
    public function getFeePlanOptions(): array
    {
        return FeePlan::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * قائمة الحالات
     */
    public function getStatuses(): array
    {
        return StudentSubscription::STATUSES;
    }

    /**
     * بيانات DataTable Ajax
     */
    public function datatable(Request $request): array
    {
        $draw   = (int) $request->input('draw', 1);
        $start  = max((int) $request->input('start', 0), 0);
        $length = max((int) $request->input('length', 10), 1);
        $search = trim((string) data_get($request->input('search'), 'value', ''));

        $baseQuery = StudentSubscription::query()
            ->with(['student', 'feePlan']);

        if ($request->filled('status')) {
            $baseQuery->where('status', $request->input('status'));
        }

        if ($request->filled('fee_plan_id')) {
            $baseQuery->where('fee_plan_id', $request->input('fee_plan_id'));
        }

        $recordsTotal = StudentSubscription::query()->count();

        if ($search !== '') {
            $baseQuery->where(function ($q) use ($search) {
                $q->whereHas('student', fn ($s) => $s->where('full_name', 'like', "%{$search}%"))
                    ->orWhereHas('feePlan', fn ($f) => $f->where('name', 'like', "%{$search}%"));
            });
        }

        $recordsFiltered = (clone $baseQuery)->count();

        $rows = $baseQuery
            ->orderByDesc('id')
            ->skip($start)
            ->take($length)
            ->get();

        $data = $rows->map(function (StudentSubscription $subscription) {
            return [
                'id'                 => $subscription->id,
                'student_id'         => $subscription->student_id,
                'student_name'       => $subscription->student?->full_name ?? '-',
                'fee_plan_name'      => $subscription->feePlan?->name ?? '-',
                'amount'             => $subscription->amount,
                'formatted_amount'   => $subscription->formatted_amount,
                'discount_amount'    => $subscription->discount_amount,
                'formatted_discount' => $subscription->formatted_discount,
                'final_amount'       => $subscription->final_amount,
                'formatted_final'    => $subscription->formatted_final_amount,
                'paid_amount'        => $subscription->paid_amount,
                'formatted_paid'     => $subscription->formatted_paid_amount,
                'remaining_amount'   => $subscription->remaining_amount,
                'formatted_remaining'=> $subscription->formatted_remaining_amount,
                'status'             => $subscription->status,
                'status_badge'       => $subscription->status_badge_class,
                'payment_progress'   => $subscription->payment_progress,
                'is_overdue'         => $subscription->is_overdue,
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
     * بيانات DataTable Ajax للطلاب المتأخرين فقط
     */
    public function overdueDatatable(Request $request): array
    {
        $draw = (int) $request->input('draw', 1);
        $start = max((int) $request->input('start', 0), 0);
        $length = max((int) $request->input('length', 10), 1);
        $search = trim((string) data_get($request->input('search'), 'value', ''));

        $baseQuery = StudentSubscription::query()
            ->with(['student', 'feePlan'])
            ->where('status', 'متأخر')
            ->where('remaining_amount', '>', 0);

        $recordsTotal = (clone $baseQuery)->count();

        if ($search !== '') {
            $baseQuery->where(function ($q) use ($search) {
                $q->whereHas('student', fn ($s) => $s->where('full_name', 'like', "%{$search}%"))
                    ->orWhereHas('student', fn ($s) => $s->where('phone', 'like', "%{$search}%"))
                    ->orWhereHas('feePlan', fn ($f) => $f->where('name', 'like', "%{$search}%"));
            });
        }

        $recordsFiltered = (clone $baseQuery)->count();

        $rows = $baseQuery
            ->orderByDesc('remaining_amount')
            ->orderByDesc('id')
            ->skip($start)
            ->take($length)
            ->get();

        $data = $rows->map(function (StudentSubscription $subscription) {
            return [
                'id' => $subscription->id,
                'student_name' => $subscription->student?->full_name ?? '-',
                'student_phone' => $subscription->student?->phone ?? '-',
                'fee_plan_name' => $subscription->feePlan?->name ?? '-',
                'formatted_remaining' => $subscription->formatted_remaining_amount,
                'status' => $subscription->status,
                'status_badge' => $subscription->status_badge_class,
            ];
        })->values()->all();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    /**
     * ملخص إحصائي
     */
    public function reportSummary(array $filters = []): array
    {
        $query = StudentSubscription::query();

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $total       = (clone $query)->count();
        $active      = (clone $query)->where('status', 'نشط')->count();
        $overdue     = (clone $query)->where('status', 'متأخر')->count();
        $complete    = (clone $query)->where('status', 'مكتمل')->count();
        $suspended   = (clone $query)->where('status', 'موقوف')->count();

        // الطلاب المتأخرين (remaining_amount > 0 و status = متأخر)
        $overdueStudents = (clone $query)->where('status', 'متأخر')->where('remaining_amount', '>', 0)->count();

        return compact('total', 'active', 'overdue', 'complete', 'suspended', 'overdueStudents');
    }

}

