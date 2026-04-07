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
     * قائمة الطلاب النشطين.
     * إذا تم تمرير $includeStudentId، يُدرج ذلك الطالب حتى لو كان غير نشط
     * (مفيد في صفحة تعديل الاشتراك إذا أصبح الطالب غير نشط لاحقاً).
     */
    public function getStudentOptions(?int $includeStudentId = null): array
    {
        $options = Student::query()
            ->where('status', 'active')
            ->orderBy('full_name')
            ->pluck('full_name', 'id')
            ->toArray();

        // أضف الطالب المُحدَّد إن لم يكن موجوداً في القائمة (طالب غير نشط)
        if ($includeStudentId && ! isset($options[$includeStudentId])) {
            $student = Student::query()->find($includeStudentId);
            if ($student) {
                $options[$includeStudentId] = $student->full_name . ' (غير نشط)';
            }
        }

        return $options;
    }

    /**
     * حالة كل طالب نشط [id => status] — للتحقق الديناميكي في الفورم.
     * إذا تم تمرير $includeStudentId، يُدرج حالته حتى لو كان غير نشط.
     */
    public function getStudentStatuses(?int $includeStudentId = null): array
    {
        $statuses = Student::query()
            ->where('status', 'active')
            ->orderBy('full_name')
            ->pluck('status', 'id')
            ->toArray();

        // أضف الطالب المُحدَّد إن لم يكن موجوداً (طالب غير نشط)
        if ($includeStudentId && ! isset($statuses[$includeStudentId])) {
            $student = Student::query()->find($includeStudentId);
            if ($student) {
                $statuses[$includeStudentId] = $student->status;
            }
        }

        return $statuses;
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
            $filterStatus = $request->input('status');
            $today        = now()->startOfDay()->toDateString();

            if ($filterStatus === 'متأخر') {
                // overdue: remaining > 0 AND due_date passed (excludes موقوف)
                $baseQuery->financiallyOverdue();
            } elseif ($filterStatus === 'منتهي') {
                // expired due date regardless of payment status
                $baseQuery->whereNotNull('due_date')
                    ->whereDate('due_date', '<', $today);
            } elseif ($filterStatus === 'مكتمل') {
                $baseQuery->where('remaining_amount', '<=', 0);
            } elseif ($filterStatus === 'موقوف') {
                $baseQuery->where('status', 'موقوف');
            } elseif ($filterStatus === 'نشط') {
                $baseQuery
                    ->where('remaining_amount', '>', 0)
                    ->where('status', '!=', 'موقوف')
                    ->where(function ($q) use ($today) {
                        $q->whereNull('due_date')
                            ->orWhereDate('due_date', '>=', $today);
                    });
            } else {
                $baseQuery->where('status', $filterStatus);
            }
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
                'id'                  => $subscription->id,
                'student_id'          => $subscription->student_id,
                'student_name'        => $subscription->student?->full_name ?? '-',
                'student_status'      => $subscription->student?->status ?? 'inactive',
                'fee_plan_name'       => $subscription->feePlan?->name ?? '-',
                'payment_cycle'       => $subscription->feePlan?->payment_cycle ?? '-',
                'amount'              => $subscription->amount,
                'formatted_amount'    => $subscription->formatted_amount,
                'discount_amount'     => $subscription->discount_amount,
                'formatted_discount'  => $subscription->formatted_discount,
                'final_amount'        => $subscription->final_amount,
                'formatted_final'     => $subscription->formatted_final_amount,
                'paid_amount'         => $subscription->paid_amount,
                'formatted_paid'      => $subscription->formatted_paid_amount,
                'remaining_amount'    => $subscription->remaining_amount,
                'formatted_remaining' => $subscription->formatted_remaining_amount,
                'status'              => $subscription->status,
                'status_badge'        => $subscription->status_badge_class,
                'payment_progress'    => $subscription->payment_progress,
                'is_overdue'          => $subscription->is_overdue,
                'is_expired'          => $subscription->is_expired,
                'is_approaching'      => $subscription->days_until_due !== null && $subscription->days_until_due >= 0 && $subscription->days_until_due <= 2 && $subscription->remaining_amount > 0,
                'start_date'          => $subscription->start_date?->format('Y-m-d'),
                'due_date'            => $subscription->due_date?->format('Y-m-d'),
                'remaining_due_date'  => $subscription->remaining_due_date?->format('Y-m-d'),
                'days_until_due'      => $subscription->days_until_due,
                'renewal_url'         => route('admin.student-subscriptions.renew', $subscription->id),
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
            ->financiallyOverdue();

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
                'status' => $subscription->is_overdue ? 'متأخر' : $subscription->status,
                'status_badge' => $subscription->is_overdue ? 'bg-warning text-dark' : $subscription->status_badge_class,
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
        $overdue     = (clone $query)->financiallyOverdue()->count();
        $complete    = (clone $query)->where('status', 'مكتمل')->count();
        $suspended   = (clone $query)->where('status', 'موقوف')->count();

        // عدد الطلاب (المميزين) الذين لديهم اشتراك متأخر مالياً
        $overdueStudents = (clone $query)
            ->financiallyOverdue()
            ->distinct('student_id')
            ->count('student_id');

        // عدد الاشتراكات القريبة الانتهاء (خلال يومين)
        $approachingExpiry = (clone $query)->approachingExpiry()->count();

        // عدد الاشتراكات المنتهية (تاريخ الاستحقاق في الماضي)
        $expiredSubscriptions = (clone $query)->hasExpiredDueDate()->count();

        return compact(
            'total',
            'active',
            'overdue',
            'complete',
            'suspended',
            'overdueStudents',
            'approachingExpiry',
            'expiredSubscriptions'
        );
    }

}

