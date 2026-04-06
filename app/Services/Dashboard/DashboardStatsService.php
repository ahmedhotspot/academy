<?php

namespace App\Services\Dashboard;

use App\Models\Assessment;
use App\Models\Branch;
use App\Models\Expense;
use App\Models\Group;
use App\Models\Guardian;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentProgressLog;
use App\Models\StudentSubscription;
use App\Models\TeacherAttendance;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class DashboardStatsService
{
    public function buildDashboardData(?User $authUser): array
    {
        $now = now();
        $today = Carbon::today();
        $isSuperAdmin = $authUser?->isSuperAdmin() ?? false;
        $branchId = $authUser?->branch_id;

        $totalStudents = Student::query()->count();
        $totalTeachers = User::query()
            ->whereHas('roles', fn ($q) => $q->where('name', 'المعلم'))
            ->when(! $isSuperAdmin && $branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->count();
        $totalGuardians = Guardian::query()
            ->when(! $isSuperAdmin && $branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->count();
        $totalGroups = Group::query()->count();
        $totalBranches = $isSuperAdmin ? Branch::query()->count() : ($branchId ? 1 : 0);

        $studentsPresentToday = StudentProgressLog::query()
            ->whereDate('progress_date', $today)
            ->distinct('student_id')
            ->count('student_id');
        $studentsAbsentToday = max(0, $totalStudents - $studentsPresentToday);

        $teachersPresentToday = TeacherAttendance::query()
            ->whereDate('attendance_date', $today)
            ->where('status', 'حاضر')
            ->count();
        $teachersAbsentToday = TeacherAttendance::query()
            ->whereDate('attendance_date', $today)
            ->where('status', 'غائب')
            ->count();

        $todayCollection = Payment::query()
            ->whereDate('payment_date', $today)
            ->sum('amount');

        $totalRemaining = StudentSubscription::query()->sum('remaining_amount');

        $overdueStudentsCount = StudentSubscription::query()
            ->where('status', 'متأخر')
            ->where('remaining_amount', '>', 0)
            ->distinct('student_id')
            ->count('student_id');

        $assessmentsThisMonth = Assessment::query()
            ->whereMonth('assessment_date', $now->month)
            ->whereYear('assessment_date', $now->year)
            ->count();

        $progressLogsThisWeek = StudentProgressLog::query()
            ->whereBetween('progress_date', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])
            ->count();

        $monthlyExpenses = Expense::query()
            ->whereMonth('expense_date', $now->month)
            ->whereYear('expense_date', $now->year)
            ->sum('amount');

        $studentStatusStats = $this->buildStudentStatusStats($totalStudents);
        $financialStats = $this->buildMonthlyFinancialStats();

        $recentStudents = Student::query()
            ->orderByDesc('created_at')
            ->limit(6)
            ->get(['id', 'full_name', 'created_at', 'status']);

        $latestPayments = Payment::query()
            ->with('student:id,full_name')
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        $importantAlerts = $this->buildImportantAlerts($today);
        $recentActivities = $this->buildRecentActivities();

        $unreadNotifications = Notification::query()
            ->when($authUser, fn ($q) => $q->where('user_id', $authUser->id))
            ->where('is_read', false)
            ->latest('created_at')
            ->limit(5)
            ->get();

        return [
            'hero' => [
                'greeting' => $this->resolveGreeting($now),
                'user_name' => $authUser?->name ?? 'المستخدم',
                'date_text' => $now->translatedFormat('l d F Y'),
                'time_text' => $now->format('h:i:s') . ' ' . ($now->format('A') === 'AM' ? 'ص' : 'م'),
            ],
            'quickActions' => [
                ['title' => 'إضافة طالب', 'url' => route('admin.students.create'), 'icon' => 'fa fa-user-plus', 'class' => 'btn-light', 'permission' => 'students.create'],
                ['title' => 'إضافة حلقة', 'url' => route('admin.groups.create'), 'icon' => 'fa fa-plus-circle', 'class' => 'btn-outline-light', 'permission' => 'groups.create'],
                ['title' => 'مركز التقارير', 'url' => route('admin.reports.index'), 'icon' => 'fa fa-chart-bar', 'class' => 'btn-outline-light', 'permission' => 'reports.view'],
            ],
            'statsCards' => [
                ['title' => 'إجمالي الطلاب', 'value' => $totalStudents, 'hint' => 'عدد الطلاب المسجلين', 'icon' => 'fa fa-users', 'bg' => 'soft-primary'],
                ['title' => 'إجمالي المعلمين', 'value' => $totalTeachers, 'hint' => 'المعلمون النشطون', 'icon' => 'fa fa-user-check', 'bg' => 'soft-success'],
                ['title' => 'إجمالي أولياء الأمور', 'value' => $totalGuardians, 'hint' => 'بيانات أولياء الأمور', 'icon' => 'fa fa-users', 'bg' => 'soft-info'],
                ['title' => 'إجمالي الحلقات', 'value' => $totalGroups, 'hint' => 'الحلقات التعليمية', 'icon' => 'fa fa-book', 'bg' => 'soft-warning'],
                ['title' => 'حضور الطلاب اليوم', 'value' => $studentsPresentToday, 'hint' => 'تمت متابعتهم اليوم', 'icon' => 'fa fa-thumbs-up', 'bg' => 'soft-success'],
                ['title' => 'غياب الطلاب اليوم', 'value' => $studentsAbsentToday, 'hint' => 'بدون متابعة اليوم', 'icon' => 'fa fa-thumbs-down', 'bg' => 'soft-danger'],
                ['title' => 'التحصيل اليوم', 'value' => number_format($todayCollection, 2) . ' ج', 'hint' => 'مدفوعات اليوم', 'icon' => 'fa fa-coins', 'bg' => 'soft-indigo'],
                ['title' => 'الطلاب المتأخرون', 'value' => $overdueStudentsCount, 'hint' => 'في السداد', 'icon' => 'fa fa-exclamation-circle', 'bg' => 'soft-danger'],
                ['title' => 'حضور المعلمين اليوم', 'value' => $teachersPresentToday, 'hint' => 'معلم حاضر', 'icon' => 'fa fa-calendar-check', 'bg' => 'soft-success'],
                ['title' => 'غياب المعلمين اليوم', 'value' => $teachersAbsentToday, 'hint' => 'معلم غائب', 'icon' => 'fa fa-calendar-times', 'bg' => 'soft-danger'],
                ['title' => 'إجمالي المتبقي', 'value' => number_format($totalRemaining, 2) . ' ج', 'hint' => 'على الاشتراكات', 'icon' => 'fa fa-receipt', 'bg' => 'soft-warning'],
                ['title' => 'اختبارات هذا الشهر', 'value' => $assessmentsThisMonth, 'hint' => 'اختبار مسجل', 'icon' => 'fa fa-clipboard-check', 'bg' => 'soft-info'],
                ['title' => 'متابعات هذا الأسبوع', 'value' => $progressLogsThisWeek, 'hint' => 'سجل متابعة', 'icon' => 'fa fa-chart-line', 'bg' => 'soft-primary'],
                ['title' => 'عدد الفروع', 'value' => $totalBranches, 'hint' => 'الفروع العاملة', 'icon' => 'fa fa-building', 'bg' => 'soft-secondary'],
            ],
            'financialSummary' => [
                'collection' => $todayCollection,
                'expenses' => $monthlyExpenses,
            ],
            'charts' => [
                'studentsByStatus' => $studentStatusStats,
                'financialByMonth' => $financialStats,
            ],
            'recentStudents' => $recentStudents,
            'latestPayments' => $latestPayments,
            'importantAlerts' => $importantAlerts,
            'recentActivities' => $recentActivities,
            'unreadNotifications' => $unreadNotifications,
        ];
    }

    private function resolveGreeting(CarbonInterface $now): string
    {
        $hour = (int) $now->format('H');

        if ($hour >= 5 && $hour < 12) {
            return 'صباح الخير';
        }

        if ($hour >= 12 && $hour < 18) {
            return 'مساء الخير';
        }

        return 'مساء الخير';
    }

    private function buildStudentStatusStats(int $totalStudents): array
    {
        $active = Student::query()->where('status', 'active')->count();
        $inactive = Student::query()->where('status', 'inactive')->count();
        $other = max(0, $totalStudents - ($active + $inactive));

        return [
            'labels' => ['نشط', 'غير نشط', 'حالات أخرى'],
            'data' => [$active, $inactive, $other],
            'colors' => ['#16a34a', '#f59e0b', '#6b7280'],
            'total' => $totalStudents,
        ];
    }

    private function buildMonthlyFinancialStats(): array
    {
        $labels = [];
        $collections = [];
        $expenses = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->translatedFormat('M Y');

            $collections[] = (float) Payment::query()
                ->whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->sum('amount');

            $expenses[] = (float) Expense::query()
                ->whereYear('expense_date', $date->year)
                ->whereMonth('expense_date', $date->month)
                ->sum('amount');
        }

        return [
            'labels' => $labels,
            'collections' => $collections,
            'expenses' => $expenses,
        ];
    }

    private function buildImportantAlerts(Carbon $today): array
    {
        $alerts = [];

        $teacherAbsentToday = TeacherAttendance::query()
            ->with('teacher:id,name')
            ->whereDate('attendance_date', $today)
            ->where('status', 'غائب')
            ->limit(3)
            ->get();

        foreach ($teacherAbsentToday as $record) {
            $alerts[] = [
                'icon' => 'fa fa-exclamation-triangle',
                'color' => 'danger',
                'title' => 'غياب معلم',
                'message' => 'المعلم ' . ($record->teacher?->name ?? '-') . ' مسجل كغائب اليوم.',
            ];
        }

        $overdueSubs = StudentSubscription::query()
            ->with('student:id,full_name')
            ->where('status', 'متأخر')
            ->where('remaining_amount', '>', 0)
            ->orderByDesc('remaining_amount')
            ->limit(3)
            ->get();

        foreach ($overdueSubs as $sub) {
            $alerts[] = [
                'icon' => 'fa fa-exclamation-circle',
                'color' => 'warning',
                'title' => 'متأخرات مالية',
                'message' => 'الطالب ' . ($sub->student?->full_name ?? '-') . ' متأخر بمبلغ ' . number_format($sub->remaining_amount, 2) . ' ج.',
            ];
        }

        return array_slice($alerts, 0, 6);
    }

    private function buildRecentActivities(): array
    {
        $activities = [];

        $payments = Payment::query()->with('student:id,full_name')->latest('created_at')->limit(3)->get();
        foreach ($payments as $payment) {
            $activities[] = [
                'icon' => 'fa fa-money-bill',
                'color' => 'success',
                'title' => 'دفعة جديدة',
                'description' => 'تم تسجيل دفعة للطالب ' . ($payment->student?->full_name ?? '-') . ' بقيمة ' . number_format($payment->amount, 2) . ' ج',
                'time' => $payment->created_at?->diffForHumans(),
                'timestamp' => $payment->created_at?->timestamp ?? 0,
            ];
        }

        $assessments = Assessment::query()->with('student:id,full_name')->latest('created_at')->limit(2)->get();
        foreach ($assessments as $assessment) {
            $activities[] = [
                'icon' => 'fa fa-check-square',
                'color' => 'info',
                'title' => 'اختبار جديد',
                'description' => 'تم تسجيل اختبار ' . $assessment->type . ' للطالب ' . ($assessment->student?->full_name ?? '-'),
                'time' => $assessment->created_at?->diffForHumans(),
                'timestamp' => $assessment->created_at?->timestamp ?? 0,
            ];
        }

        $progressLogs = StudentProgressLog::query()->with('student:id,full_name')->latest('created_at')->limit(2)->get();
        foreach ($progressLogs as $log) {
            $activities[] = [
                'icon' => 'fa fa-arrow-up',
                'color' => 'primary',
                'title' => 'متابعة تعليمية',
                'description' => 'تم تسجيل متابعة تعليمية للطالب ' . ($log->student?->full_name ?? '-'),
                'time' => $log->created_at?->diffForHumans(),
                'timestamp' => $log->created_at?->timestamp ?? 0,
            ];
        }

        usort($activities, fn ($a, $b) => $b['timestamp'] <=> $a['timestamp']);

        return array_map(function ($item) {
            unset($item['timestamp']);
            return $item;
        }, array_slice($activities, 0, 8));
    }
}

