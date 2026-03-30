<?php

namespace App\Services\Admin;

use App\Models\Assessment;
use App\Models\Expense;
use App\Models\Student;
use App\Models\StudentProgressLog;
use App\Models\StudentSubscription;
use App\Models\TeacherAttendance;
use App\Models\TeacherPayroll;
use App\Services\BaseService;
use Illuminate\Http\Request;

class ReportService extends BaseService
{
    /**
     * تقرير الطلاب
     */
    public function studentReport(Request $request): array
    {
        $query = Student::query();

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->input('branch_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $students = $query->with(['branch', 'enrollments'])
            ->orderBy('full_name')
            ->get()
            ->map(fn ($s) => [
                'id'        => $s->id,
                'name'      => $s->full_name,
                'branch'    => $s->branch?->name,
                'age'       => $s->age,
                'status'    => $s->status,
            ])->values()->all();

        return [
            'total'    => count($students),
            'students' => $students,
        ];
    }

    /**
     * تقرير الحضور والغياب
     */
    public function attendanceReport(Request $request): array
    {
        $query = TeacherAttendance::query()->with('teacher');

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->input('teacher_id'));
        }

        $records = $query->orderByDesc('attendance_date')->get();

        $stats = [
            'present'  => $records->where('status', 'حاضر')->count(),
            'absent'   => $records->where('status', 'غائب')->count(),
            'late'     => $records->where('status', 'متأخر')->count(),
            'excused'  => $records->where('status', 'بعذر')->count(),
        ];

        return ['stats' => $stats, 'records' => $records];
    }

    /**
     * تقرير المتابعة التعليمية
     */
    public function progressReport(Request $request): array
    {
        $query = StudentProgressLog::query()->with(['student', 'teacher']);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->input('student_id'));
        }

        return ['records' => $query->orderByDesc('progress_date')->get()];
    }

    /**
     * تقرير الاختبارات
     */
    public function assessmentReport(Request $request): array
    {
        $query = Assessment::query()->with('student');

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->input('student_id'));
        }

        $records = $query->orderByDesc('assessment_date')->get();

        $avgResult = $records->avg(function ($r) {
            return ($r->memorization_result + $r->tajweed_result + $r->tadabbur_result) / 3;
        });

        return [
            'total'    => count($records),
            'average'  => round($avgResult, 2),
            'records'  => $records,
        ];
    }

    /**
     * تقرير الاشتراكات والمتأخرات
     */
    public function subscriptionReport(Request $request): array
    {
        $query = StudentSubscription::query()->with('student');

        $total     = (clone $query)->count();
        $active    = (clone $query)->where('status', 'نشط')->count();
        $overdue   = (clone $query)->where('status', 'متأخر')->count();
        $complete  = (clone $query)->where('status', 'مكتمل')->count();
        $remaining = (clone $query)->sum('remaining_amount');

        $overdueList = StudentSubscription::query()
            ->with('student')
            ->where('status', 'متأخر')
            ->where('remaining_amount', '>', 0)
            ->orderByDesc('remaining_amount')
            ->get();

        return [
            'total'       => $total,
            'active'      => $active,
            'overdue'     => $overdue,
            'complete'    => $complete,
            'totalRemaining' => $remaining,
            'overdueList' => $overdueList,
        ];
    }

    /**
     * تقرير مستحقات المعلمين
     */
    public function payrollReport(Request $request): array
    {
        $query = TeacherPayroll::query()->with('teacher');

        $total     = (clone $query)->count();
        $processed = (clone $query)->where('status', 'مصروف')->count();
        $pending   = (clone $query)->where('status', 'غير مصروف')->count();
        $totalSalaries = (clone $query)->sum('final_amount');

        return [
            'total'         => $total,
            'processed'     => $processed,
            'pending'       => $pending,
            'totalSalaries' => $totalSalaries,
            'records'       => $query->orderByDesc('year')->orderByDesc('month')->get(),
        ];
    }

    /**
     * تقرير المصروفات
     */
    public function expenseReport(Request $request): array
    {
        $query = Expense::query()->with('branch');

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->input('branch_id'));
        }

        $total   = (clone $query)->count();
        $amount  = (clone $query)->sum('amount');

        return [
            'total'   => $total,
            'amount'  => $amount,
            'records' => $query->orderByDesc('expense_date')->get(),
        ];
    }

    /**
     * تقرير حسب الفرع
     */
    public function branchReport(Request $request): array
    {
        $branches = $request->input('branches', []);

        return [
            'branches' => $branches,
        ];
    }
}

