<?php

namespace App\Services\Admin;

use App\Models\Branch;
use App\Models\Expense;
use App\Models\Group;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentSubscription;
use App\Models\TeacherAttendance;
use App\Models\User;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BranchManagementService extends BaseService
{
    public function datatable(Request $request): array
    {
        $draw = (int) $request->input('draw', 1);
        $start = max((int) $request->input('start', 0), 0);
        $length = max((int) $request->input('length', 10), 1);
        $search = trim((string) data_get($request->input('search'), 'value', ''));

        $baseQuery = Branch::query()->select(['id', 'name', 'status', 'created_at']);
        $recordsTotal = Branch::query()->count();

        if ($search !== '') {
            $baseQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = (clone $baseQuery)->count();

        $rows = $baseQuery
            ->orderByDesc('id')
            ->skip($start)
            ->take($length)
            ->get();

        $data = $rows->map(function (Branch $branch) {
            return [
                'id' => $branch->id,
                'name' => $branch->name,
                'status' => $branch->status_label,
                'status_badge' => $branch->status_badge_class,
                'created_at' => optional($branch->created_at)->format('Y-m-d'),
            ];
        })->values()->all();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    public function getBranchStats(int $branchId): array
    {
        return $this->getBranchProfile(Branch::query()->findOrFail($branchId))['stats'];
    }

    public function getBranchProfile(Branch $branch): array
    {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $studentsQuery = Student::query()->where('branch_id', $branch->id);
        $teachersQuery = User::query()->role('المعلم')->where('branch_id', $branch->id);
        $groupsQuery = Group::query()->where('branch_id', $branch->id);

        $studentsCount = (clone $studentsQuery)->count();
        $activeStudentsCount = (clone $studentsQuery)->where('status', 'active')->count();

        $teachersCount = (clone $teachersQuery)->count();
        $activeTeachersCount = (clone $teachersQuery)->where('status', 'active')->count();

        $groupsCount = (clone $groupsQuery)->count();
        $activeGroupsCount = (clone $groupsQuery)->where('status', 'active')->count();

        $subscriptionsQuery = StudentSubscription::query()->whereHas('student', fn ($q) => $q->where('branch_id', $branch->id));

        $subscriptionsCount = (clone $subscriptionsQuery)->count();
        $overdueSubscriptionsCount = (clone $subscriptionsQuery)
            ->where('status', 'متأخر')
            ->where('remaining_amount', '>', 0)
            ->count();

        $paymentsInMonth = Payment::query()
            ->whereHas('student', fn ($q) => $q->where('branch_id', $branch->id))
            ->whereBetween('payment_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->sum('amount');

        $expensesInMonth = Expense::query()
            ->where('branch_id', $branch->id)
            ->whereBetween('expense_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->sum('amount');

        $remainingSubscriptions = (clone $subscriptionsQuery)->sum('remaining_amount');

        $attendanceQuery = TeacherAttendance::query()
            ->whereDate('attendance_date', $today->toDateString())
            ->whereHas('teacher', fn ($q) => $q->where('branch_id', $branch->id));

        $teacherPresentToday = (clone $attendanceQuery)->where('status', 'حاضر')->count();
        $teacherAbsentToday = (clone $attendanceQuery)->where('status', 'غائب')->count();
        $teacherLateToday = (clone $attendanceQuery)->where('status', 'متأخر')->count();

        $recentStudents = Student::query()
            ->where('branch_id', $branch->id)
            ->latest('created_at')
            ->limit(6)
            ->get(['id', 'full_name', 'phone', 'status', 'created_at'])
            ->map(fn (Student $student) => [
                'id' => $student->id,
                'name' => $student->full_name,
                'phone' => $student->phone,
                'status' => $student->status,
                'created_at' => optional($student->created_at)->format('Y-m-d'),
            ])->toArray();

        $recentTeachers = User::query()
            ->role('المعلم')
            ->where('branch_id', $branch->id)
            ->latest('created_at')
            ->limit(6)
            ->get(['id', 'name', 'phone', 'status', 'created_at'])
            ->map(fn (User $teacher) => [
                'id' => $teacher->id,
                'name' => $teacher->name,
                'phone' => $teacher->phone,
                'status' => $teacher->status?->value ?? 'inactive',
                'created_at' => optional($teacher->created_at)->format('Y-m-d'),
            ])->toArray();

        $recentGroups = Group::query()
            ->with(['teacher:id,name', 'studyLevel:id,name', 'studyTrack:id,name'])
            ->withCount('studentEnrollments')
            ->where('branch_id', $branch->id)
            ->latest('created_at')
            ->limit(6)
            ->get()
            ->map(fn (Group $group) => [
                'id' => $group->id,
                'name' => $group->name,
                'teacher' => $group->teacher?->name ?? '-',
                'level' => $group->studyLevel?->name ?? '-',
                'track' => $group->studyTrack?->name ?? '-',
                'students_count' => $group->student_enrollments_count,
                'status_label' => $group->status_label,
                'status_badge' => $group->status_badge_class,
            ])->toArray();

        $overdueSubscriptions = StudentSubscription::query()
            ->with('student:id,full_name,phone')
            ->where('status', 'متأخر')
            ->where('remaining_amount', '>', 0)
            ->whereHas('student', fn ($q) => $q->where('branch_id', $branch->id))
            ->orderByDesc('remaining_amount')
            ->limit(6)
            ->get()
            ->map(fn (StudentSubscription $subscription) => [
                'id' => $subscription->id,
                'student' => $subscription->student?->full_name ?? '-',
                'phone' => $subscription->student?->phone ?? '-',
                'remaining' => $subscription->formatted_remaining_amount,
                'status' => $subscription->status,
            ])->toArray();

        $recentPayments = Payment::query()
            ->with('student:id,full_name')
            ->whereHas('student', fn ($q) => $q->where('branch_id', $branch->id))
            ->latest('payment_date')
            ->limit(6)
            ->get(['id', 'student_id', 'payment_date', 'amount', 'receipt_number'])
            ->map(fn (Payment $payment) => [
                'id' => $payment->id,
                'student' => $payment->student?->full_name ?? '-',
                'amount' => $payment->formatted_amount,
                'payment_date' => $payment->formatted_payment_date,
                'receipt' => $payment->receipt_number,
            ])->toArray();

        $recentExpenses = Expense::query()
            ->where('branch_id', $branch->id)
            ->latest('expense_date')
            ->limit(6)
            ->get(['id', 'title', 'amount', 'expense_date'])
            ->map(fn (Expense $expense) => [
                'id' => $expense->id,
                'title' => $expense->title,
                'amount' => $expense->formatted_amount,
                'expense_date' => $expense->formatted_date,
            ])->toArray();

        return [
            'stats' => [
                'students_count' => $studentsCount,
                'active_students_count' => $activeStudentsCount,
                'teachers_count' => $teachersCount,
                'active_teachers_count' => $activeTeachersCount,
                'groups_count' => $groupsCount,
                'active_groups_count' => $activeGroupsCount,
                'subscriptions_count' => $subscriptionsCount,
                'overdue_subscriptions_count' => $overdueSubscriptionsCount,
            ],
            'attendance' => [
                'teacher_present_today' => $teacherPresentToday,
                'teacher_absent_today' => $teacherAbsentToday,
                'teacher_late_today' => $teacherLateToday,
            ],
            'finance' => [
                'payments_month' => number_format((float) $paymentsInMonth, 2) . ' ر.س',
                'expenses_month' => number_format((float) $expensesInMonth, 2) . ' ر.س',
                'remaining_subscriptions' => number_format((float) $remainingSubscriptions, 2) . ' ر.س',
                'net_month' => number_format((float) ($paymentsInMonth - $expensesInMonth), 2) . ' ر.س',
            ],
            'recent' => [
                'students' => $recentStudents,
                'teachers' => $recentTeachers,
                'groups' => $recentGroups,
                'overdue_subscriptions' => $overdueSubscriptions,
                'payments' => $recentPayments,
                'expenses' => $recentExpenses,
            ],
        ];
    }
}

