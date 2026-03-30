<?php

namespace App\Services\Admin;

use App\Models\Guardian;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentSubscription;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GuardianManagementService extends BaseService
{
    public function datatable(Request $request): array
    {
        $draw = (int) $request->input('draw', 1);
        $start = max((int) $request->input('start', 0), 0);
        $length = max((int) $request->input('length', 10), 1);
        $search = trim((string) data_get($request->input('search'), 'value', ''));

        $baseQuery = Guardian::query()
            ->withCount('students')
            ->select(['id', 'full_name', 'phone', 'whatsapp', 'status', 'created_at']);

        $recordsTotal = Guardian::query()->count();

        if ($search !== '') {
            $baseQuery->where(function ($query) use ($search) {
                $query->where('full_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('whatsapp', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = (clone $baseQuery)->count();

        $rows = $baseQuery
            ->orderByDesc('id')
            ->skip($start)
            ->take($length)
            ->get();

        $data = $rows->map(function (Guardian $guardian) {
            return [
                'id' => $guardian->id,
                'full_name' => $guardian->full_name,
                'phone' => $guardian->phone,
                'whatsapp' => $guardian->whatsapp ?: '-',
                'status' => $guardian->status_label,
                'status_badge' => $guardian->status_badge_class,
                'students_count' => $guardian->students_count,
            ];
        })->values()->all();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    public function getGuardianProfile(Guardian $guardian): array
    {
        $guardian->load(['students.branch']);

        $studentIds = $guardian->students->pluck('id')->all();
        $studentCount = count($studentIds);

        if ($studentCount === 0) {
            return [
                'stats' => [
                    'students_count' => 0,
                    'active_students_count' => 0,
                    'branches_count' => 0,
                    'subscriptions_count' => 0,
                    'overdue_subscriptions_count' => 0,
                    'payments_count' => 0,
                ],
                'financial' => [
                    'total_paid' => '0.00 ر.س',
                    'total_remaining' => '0.00 ر.س',
                    'month_paid' => '0.00 ر.س',
                ],
                'students' => [],
                'branch_summary' => [],
                'subscriptions' => [],
                'payments' => [],
                'activity' => [],
            ];
        }

        $activeStudentsCount = $guardian->students->where('status', 'active')->count();
        $branchesCount = $guardian->students->pluck('branch_id')->filter()->unique()->count();

        $subscriptionsQuery = StudentSubscription::query()->whereIn('student_id', $studentIds);
        $paymentsQuery = Payment::query()->whereIn('student_id', $studentIds);

        $subscriptionsCount = (clone $subscriptionsQuery)->count();
        $overdueSubscriptionsCount = (clone $subscriptionsQuery)
            ->where('status', 'متأخر')
            ->where('remaining_amount', '>', 0)
            ->count();
        $paymentsCount = (clone $paymentsQuery)->count();

        $totalPaid = (clone $paymentsQuery)->sum('amount');
        $totalRemaining = (clone $subscriptionsQuery)->sum('remaining_amount');
        $monthPaid = Payment::query()
            ->whereIn('student_id', $studentIds)
            ->whereBetween('payment_date', [Carbon::now()->startOfMonth()->toDateString(), Carbon::now()->endOfMonth()->toDateString()])
            ->sum('amount');

        $students = $guardian->students
            ->sortByDesc('created_at')
            ->take(10)
            ->map(fn (Student $student) => [
                'id' => $student->id,
                'name' => $student->full_name,
                'age' => $student->age,
                'phone' => $student->phone,
                'branch' => $student->branch?->name ?? '-',
                'status' => $student->status_label,
                'status_badge' => $student->status_badge_class,
                'created_at' => optional($student->created_at)->format('Y-m-d'),
            ])->values()->all();

        $branchSummary = $guardian->students
            ->groupBy(fn ($student) => $student->branch?->name ?? 'بدون فرع')
            ->map(fn ($group, $branchName) => [
                'branch' => $branchName,
                'students_count' => $group->count(),
            ])
            ->values()
            ->all();

        $subscriptions = StudentSubscription::query()
            ->with(['student:id,full_name,phone'])
            ->whereIn('student_id', $studentIds)
            ->latest('id')
            ->limit(8)
            ->get()
            ->map(fn (StudentSubscription $subscription) => [
                'id' => $subscription->id,
                'student' => $subscription->student?->full_name ?? '-',
                'phone' => $subscription->student?->phone ?? '-',
                'final' => $subscription->formatted_final_amount,
                'paid' => $subscription->formatted_paid_amount,
                'remaining' => $subscription->formatted_remaining_amount,
                'status' => $subscription->status,
                'status_badge' => $subscription->status_badge_class,
            ])
            ->values()
            ->all();

        $payments = Payment::query()
            ->with(['student:id,full_name'])
            ->whereIn('student_id', $studentIds)
            ->latest('payment_date')
            ->latest('id')
            ->limit(8)
            ->get()
            ->map(fn (Payment $payment) => [
                'id' => $payment->id,
                'student' => $payment->student?->full_name ?? '-',
                'date' => $payment->formatted_payment_date,
                'amount' => $payment->formatted_amount,
                'receipt' => $payment->receipt_formatted,
            ])
            ->values()
            ->all();

        $activity = collect($payments)
            ->map(fn ($payment) => [
                'date' => $payment['date'],
                'title' => 'دفعة جديدة',
                'description' => $payment['student'] . ' - ' . $payment['amount'],
                'badge' => 'bg-success',
            ])
            ->concat(
                collect($subscriptions)
                    ->where('status', 'متأخر')
                    ->map(fn ($subscription) => [
                        'date' => '-',
                        'title' => 'تنبيه متأخرات',
                        'description' => $subscription['student'] . ' - متبقي: ' . $subscription['remaining'],
                        'badge' => 'bg-warning text-dark',
                    ])
            )
            ->take(10)
            ->values()
            ->all();

        return [
            'stats' => [
                'students_count' => $studentCount,
                'active_students_count' => $activeStudentsCount,
                'branches_count' => $branchesCount,
                'subscriptions_count' => $subscriptionsCount,
                'overdue_subscriptions_count' => $overdueSubscriptionsCount,
                'payments_count' => $paymentsCount,
            ],
            'financial' => [
                'total_paid' => number_format((float) $totalPaid, 2) . ' ر.س',
                'total_remaining' => number_format((float) $totalRemaining, 2) . ' ر.س',
                'month_paid' => number_format((float) $monthPaid, 2) . ' ر.س',
            ],
            'students' => $students,
            'branch_summary' => $branchSummary,
            'subscriptions' => $subscriptions,
            'payments' => $payments,
            'activity' => $activity,
        ];
    }
}

