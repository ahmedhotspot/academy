<?php

namespace App\Actions\Admin\Dashboard;

use App\Actions\BaseAction;
use App\Models\Branch;
use App\Models\Expense;
use App\Models\Group;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentSubscription;
use App\Models\TeacherAttendance;
use App\Models\User;
use Carbon\Carbon;

class LoadDashboardStatsAction extends BaseAction
{
    public function handle(array $data = []): array
    {
        $today = Carbon::today();

        // الأرقام الإجمالية
        $branchCount  = Branch::query()->count();
        $studentCount = Student::query()->count();
        $teacherCount = User::query()->whereHas('roles', fn ($q) => $q->where('name', 'معلم'))->count();
        $groupCount   = Group::query()->count();

        // حضور اليوم
        $presentToday = TeacherAttendance::query()
            ->whereDate('attendance_date', $today)
            ->where('status', 'حاضر')
            ->count();

        $absentToday = TeacherAttendance::query()
            ->whereDate('attendance_date', $today)
            ->where('status', 'غائب')
            ->count();

        // الطلاب المتأخرين (اشتراكات متأخرة)
        $overdueStudents = StudentSubscription::query()
            ->where('status', 'متأخر')
            ->where('remaining_amount', '>', 0)
            ->count();

        // التحصيل الإجمالي للشهر الحالي
        $monthlyCollection = Payment::query()
            ->whereMonth('payment_date', $today->month)
            ->whereYear('payment_date', $today->year)
            ->sum('amount');

        // المصروفات للشهر الحالي
        $monthlyExpenses = Expense::query()
            ->whereMonth('expense_date', $today->month)
            ->whereYear('expense_date', $today->year)
            ->sum('amount');

        // آخر الدفعات
        $recentPayments = Payment::query()
            ->with('student')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // الاشتراكات المتأخرة
        $overdueList = StudentSubscription::query()
            ->with('student')
            ->where('status', 'متأخر')
            ->where('remaining_amount', '>', 0)
            ->orderByDesc('remaining_amount')
            ->limit(5)
            ->get();

        return [
            'counters' => [
                ['title' => 'الفروع',    'value' => $branchCount,  'icon' => 'ti ti-building',      'color' => 'primary'],
                ['title' => 'الطلاب',    'value' => $studentCount, 'icon' => 'ti ti-users',          'color' => 'success'],
                ['title' => 'المعلمون',  'value' => $teacherCount, 'icon' => 'ti ti-school',         'color' => 'warning'],
                ['title' => 'الحلقات',   'value' => $groupCount,   'icon' => 'ti ti-book-2',         'color' => 'info'],
            ],
            'today' => [
                'present'         => $presentToday,
                'absent'          => $absentToday,
                'overdue_students'=> $overdueStudents,
            ],
            'financial' => [
                'monthly_collection' => $monthlyCollection,
                'monthly_expenses'   => $monthlyExpenses,
                'net'                => $monthlyCollection - $monthlyExpenses,
            ],
            'recent_payments' => $recentPayments,
            'overdue_list'    => $overdueList,
        ];
    }
}
