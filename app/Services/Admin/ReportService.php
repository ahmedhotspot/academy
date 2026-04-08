<?php

namespace App\Services\Admin;

use App\Models\Assessment;
use App\Models\Expense;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentProgressLog;
use App\Models\StudentSubscription;
use App\Models\TeacherAttendance;
use App\Models\TeacherPayroll;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ReportService extends BaseService
{
    private function applyBranchScope(Builder $query, Request $request, string $column = 'branch_id'): Builder
    {
        $user = auth()->user();

        if (! $user) {
            return $query;
        }

        if (! $user->isSuperAdmin()) {
            if (! $user->branch_id) {
                // Non-super-admin users without a branch must not access cross-branch data.
                return $query->whereRaw('1 = 0');
            }

            return $query->where($column, (int) $user->branch_id);
        }

        if ($request->filled('branch_id')) {
            return $query->where($column, (int) $request->input('branch_id'));
        }

        return $query;
    }

    private function applyDateRange(Builder $query, Request $request, string $column): Builder
    {
        if ($request->filled('start_date')) {
            $query->whereDate($column, '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate($column, '<=', $request->input('end_date'));
        }

        return $query;
    }

    private function makeDatatable(Request $request, Builder $query, callable $mapper, callable $searchHandler): array
    {
        $draw   = (int) $request->input('draw', 1);
        $start  = max((int) $request->input('start', 0), 0);
        $length = max((int) $request->input('length', 10), 1);
        $search = trim((string) data_get($request->input('search'), 'value', ''));

        $recordsTotal = (clone $query)->count();

        if ($search !== '') {
            $searchHandler($query, $search);
        }

        $recordsFiltered = (clone $query)->count();

        $rows = $query
            ->skip($start)
            ->take($length)
            ->get();

        return [
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $rows->map($mapper)->values()->all(),
        ];
    }

    /**
     * تقرير الطلاب
     */
    public function studentReport(Request $request): array
    {
        $query = Student::query()->with('branch');
        $this->applyBranchScope($query, $request);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $this->applyDateRange($query, $request, 'created_at');

        $total = (clone $query)->count();

        return [
            'total'    => $total,
            'students' => [],
        ];
    }

    /**
     * تقرير الحضور والغياب
     */
    public function attendanceReport(Request $request): array
    {
        $query = StudentAttendance::query();
        $this->applyBranchScope($query, $request);

        $this->applyDateRange($query, $request, 'attendance_date');

        $stats = [
            'present'  => (clone $query)->where('status', 'حاضر')->count(),
            'absent'   => (clone $query)->where('status', 'غائب')->count(),
            'transferred'  => (clone $query)->where('status', 'منقول')->count(),
        ];

        return ['stats' => $stats, 'records' => []];
    }

    /**
     * تقرير المتابعة التعليمية
     */
    public function progressReport(Request $request): array
    {
        $query = StudentProgressLog::query();
        $this->applyBranchScope($query, $request);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->input('student_id'));
        }

        $this->applyDateRange($query, $request, 'progress_date');

        return ['records' => []];
    }

    /**
     * تقرير الاختبارات
     */
    public function assessmentReport(Request $request): array
    {
        $query = Assessment::query();
        $this->applyBranchScope($query, $request);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->input('student_id'));
        }

        $this->applyDateRange($query, $request, 'assessment_date');

        $total = (clone $query)->count();
        $avgResult = (clone $query)->get()->avg(function ($r) {
            return ((float) $r->memorization_result + (float) $r->tajweed_result + (float) $r->tadabbur_result) / 3;
        });

        return [
            'total'    => $total,
            'average'  => round((float) $avgResult, 2),
            'records'  => [],
        ];
    }

    /**
     * تقرير الاشتراكات والمتأخرات
     */
    public function subscriptionReport(Request $request): array
    {
        $query = StudentSubscription::query();
        $this->applyBranchScope($query, $request);

        $this->applyDateRange($query, $request, 'created_at');

        $total     = (clone $query)->count();
        $active    = (clone $query)->where('status', 'نشط')->count();
        $overdue   = (clone $query)->where('status', 'متأخر')->count();
        $complete  = (clone $query)->where('status', 'مكتمل')->count();
        $remaining = (clone $query)->sum('remaining_amount');

        return [
            'total'       => $total,
            'active'      => $active,
            'overdue'     => $overdue,
            'complete'    => $complete,
            'totalRemaining' => $remaining,
            'overdueList' => [],
        ];
    }

    /**
     * تقرير مستحقات المعلمين
     */
    public function payrollReport(Request $request): array
    {
        $query = TeacherPayroll::query()->with('teacher');
        $this->applyBranchScope($query, $request);

        $this->applyDateRange($query, $request, 'created_at');

        $total     = (clone $query)->count();
        $processed = (clone $query)->where('status', 'مصروف')->count();
        $pending   = (clone $query)->where('status', 'غير مصروف')->count();
        $totalSalaries = (clone $query)->sum('final_amount');

        return [
            'total'         => $total,
            'processed'     => $processed,
            'pending'       => $pending,
            'totalSalaries' => $totalSalaries,
            'records'       => [],
        ];
    }

    /**
     * تقرير المصروفات
     */
    public function expenseReport(Request $request): array
    {
        $query = Expense::query()->with('branch');
        $this->applyBranchScope($query, $request);

        $this->applyDateRange($query, $request, 'expense_date');

        $total   = (clone $query)->count();
        $amount  = (clone $query)->sum('amount');

        return [
            'total'   => $total,
            'amount'  => $amount,
            'records' => [],
        ];
    }

    public function studentsDatatable(Request $request): array
    {
        $query = Student::query()->with('branch');
        $this->applyBranchScope($query, $request);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $this->applyDateRange($query, $request, 'created_at');

        $query->orderByDesc('created_at')->orderByDesc('id');

        return $this->makeDatatable(
            $request,
            $query,
            fn (Student $s) => [
                'id' => $s->id,
                'name' => $s->full_name,
                'branch' => $s->branch?->name ?? '-',
                'age' => $s->age,
                'status' => $s->status,
                'date' => optional($s->created_at)->format('Y-m-d'),
            ],
            function (Builder $q, string $search): void {
                $q->where(function (Builder $nested) use ($search) {
                    $nested->where('full_name', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhereHas('branch', fn (Builder $b) => $b->where('name', 'like', "%{$search}%"));
                });
            }
        );
    }

    public function attendanceDatatable(Request $request): array
    {
        $query = StudentAttendance::query()->with('student');
        $this->applyBranchScope($query, $request);

        $this->applyDateRange($query, $request, 'attendance_date');
        $query->orderByDesc('attendance_date')->orderByDesc('id');

        return $this->makeDatatable(
            $request,
            $query,
            fn (StudentAttendance $r) => [
                'id' => $r->id,
                'student' => $r->student?->full_name ?? '-',
                'date' => optional($r->attendance_date)->format('Y-m-d'),
                'status' => $r->status,
            ],
            function (Builder $q, string $search): void {
                $q->where(function (Builder $nested) use ($search) {
                    $nested->where('status', 'like', "%{$search}%")
                        ->orWhereHas('student', fn (Builder $t) => $t->where('full_name', 'like', "%{$search}%"));
                });
            }
        );
    }

    public function progressDatatable(Request $request): array
    {
        $query = StudentProgressLog::query()->with(['student', 'teacher', 'group']);
        $this->applyBranchScope($query, $request);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->input('student_id'));
        }

        $this->applyDateRange($query, $request, 'progress_date');
        $query->orderByDesc('progress_date')->orderByDesc('id');

        return $this->makeDatatable(
            $request,
            $query,
            fn (StudentProgressLog $r) => [
                'id' => $r->id,
                'student' => $r->student?->full_name ?? '-',
                'teacher' => $r->teacher?->name ?? '-',
                'group' => $r->group?->name ?? '-',
                'date' => optional($r->progress_date)->format('Y-m-d'),
                'mastery' => (int) $r->mastery_level . '%',
            ],
            function (Builder $q, string $search): void {
                $q->where(function (Builder $nested) use ($search) {
                    $nested->whereHas('student', fn (Builder $s) => $s->where('full_name', 'like', "%{$search}%"))
                        ->orWhereHas('teacher', fn (Builder $t) => $t->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('group', fn (Builder $g) => $g->where('name', 'like', "%{$search}%"));
                });
            }
        );
    }

    public function assessmentsDatatable(Request $request): array
    {
        $query = Assessment::query()->with('student');
        $this->applyBranchScope($query, $request);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->input('student_id'));
        }

        $this->applyDateRange($query, $request, 'assessment_date');
        $query->orderByDesc('assessment_date')->orderByDesc('id');

        return $this->makeDatatable(
            $request,
            $query,
            function (Assessment $r): array {
                $avg = ((float) $r->memorization_result + (float) $r->tajweed_result + (float) $r->tadabbur_result) / 3;

                return [
                    'id' => $r->id,
                    'student' => $r->student?->full_name ?? '-',
                    'type' => $r->type,
                    'date' => optional($r->assessment_date)->format('Y-m-d'),
                    'result' => round($avg, 0) . '%',
                ];
            },
            function (Builder $q, string $search): void {
                $q->where(function (Builder $nested) use ($search) {
                    $nested->where('type', 'like', "%{$search}%")
                        ->orWhereHas('student', fn (Builder $s) => $s->where('full_name', 'like', "%{$search}%"));
                });
            }
        );
    }

    public function subscriptionsDatatable(Request $request): array
    {
        $query = StudentSubscription::query()->with(['student', 'feePlan'])
            ->where('status', 'متأخر')
            ->where('remaining_amount', '>', 0);
        $this->applyBranchScope($query, $request);

        $this->applyDateRange($query, $request, 'created_at');
        $query->orderByDesc('remaining_amount')->orderByDesc('id');

        return $this->makeDatatable(
            $request,
            $query,
            fn (StudentSubscription $r) => [
                'id' => $r->id,
                'student' => $r->student?->full_name ?? '-',
                'plan' => $r->feePlan?->name ?? '-',
                'status' => $r->status,
                'remaining' => $r->formatted_remaining_amount,
                'date' => optional($r->created_at)->format('Y-m-d'),
            ],
            function (Builder $q, string $search): void {
                $q->where(function (Builder $nested) use ($search) {
                    $nested->where('status', 'like', "%{$search}%")
                        ->orWhereHas('student', fn (Builder $s) => $s->where('full_name', 'like', "%{$search}%"))
                        ->orWhereHas('feePlan', fn (Builder $p) => $p->where('name', 'like', "%{$search}%"));
                });
            }
        );
    }

    public function payrollsDatatable(Request $request): array
    {
        $query = TeacherPayroll::query()->with('teacher');
        $this->applyBranchScope($query, $request);

        $this->applyDateRange($query, $request, 'created_at');
        $query->orderByDesc('year')->orderByDesc('month')->orderByDesc('id');

        return $this->makeDatatable(
            $request,
            $query,
            fn (TeacherPayroll $r) => [
                'id' => $r->id,
                'teacher' => $r->teacher?->name ?? '-',
                'period' => $r->month_year,
                'salary' => $r->formatted_base_salary,
                'bonus' => $r->formatted_bonus,
                'final' => $r->formatted_final,
                'status' => $r->status,
                'date' => optional($r->created_at)->format('Y-m-d'),
            ],
            function (Builder $q, string $search): void {
                $q->where(function (Builder $nested) use ($search) {
                    $nested->where('status', 'like', "%{$search}%")
                        ->orWhereHas('teacher', fn (Builder $t) => $t->where('name', 'like', "%{$search}%"));
                });
            }
        );
    }

    public function expensesDatatable(Request $request): array
    {
        $query = Expense::query()->with('branch');
        $this->applyBranchScope($query, $request);

        $this->applyDateRange($query, $request, 'expense_date');
        $query->orderByDesc('expense_date')->orderByDesc('id');

        return $this->makeDatatable(
            $request,
            $query,
            fn (Expense $r) => [
                'id' => $r->id,
                'date' => $r->formatted_date,
                'title' => $r->title,
                'branch' => $r->branch?->name ?? 'عام',
                'amount' => $r->formatted_amount,
            ],
            function (Builder $q, string $search): void {
                $q->where(function (Builder $nested) use ($search) {
                    $nested->where('title', 'like', "%{$search}%")
                        ->orWhere('amount', 'like', "%{$search}%")
                        ->orWhereHas('branch', fn (Builder $b) => $b->where('name', 'like', "%{$search}%"));
                });
            }
        );
    }

    public function studentsPdfRows(Request $request): array
    {
        $query = Student::query()->with('branch');
        $this->applyBranchScope($query, $request);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $this->applyDateRange($query, $request, 'created_at');

        return $query->orderByDesc('created_at')->get()
            ->map(fn (Student $s) => [
                $s->full_name,
                $s->branch?->name ?? '-',
                $s->age,
                $s->status,
                optional($s->created_at)->format('Y-m-d'),
            ])->all();
    }

    public function attendancePdfRows(Request $request): array
    {
        $query = StudentAttendance::query()->with('student');
        $this->applyBranchScope($query, $request);
        $this->applyDateRange($query, $request, 'attendance_date');

        return $query->orderByDesc('attendance_date')->get()
            ->map(fn (StudentAttendance $r) => [
                $r->student?->full_name ?? '-',
                optional($r->attendance_date)->format('Y-m-d'),
                $r->status,
            ])->all();
    }

    public function progressPdfRows(Request $request): array
    {
        $query = StudentProgressLog::query()->with(['student', 'teacher', 'group']);
        $this->applyBranchScope($query, $request);
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->input('student_id'));
        }
        $this->applyDateRange($query, $request, 'progress_date');

        return $query->orderByDesc('progress_date')->get()
            ->map(fn (StudentProgressLog $r) => [
                $r->student?->full_name ?? '-',
                $r->teacher?->name ?? '-',
                $r->group?->name ?? '-',
                optional($r->progress_date)->format('Y-m-d'),
                (int) $r->mastery_level . '%',
            ])->all();
    }

    public function assessmentsPdfRows(Request $request): array
    {
        $query = Assessment::query()->with('student');
        $this->applyBranchScope($query, $request);
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->input('student_id'));
        }
        $this->applyDateRange($query, $request, 'assessment_date');

        return $query->orderByDesc('assessment_date')->get()
            ->map(function (Assessment $r): array {
                $avg = ((float) $r->memorization_result + (float) $r->tajweed_result + (float) $r->tadabbur_result) / 3;
                return [
                    $r->student?->full_name ?? '-',
                    $r->type,
                    optional($r->assessment_date)->format('Y-m-d'),
                    round($avg, 0) . '%',
                ];
            })->all();
    }

    public function subscriptionsPdfRows(Request $request): array
    {
        $query = StudentSubscription::query()->with(['student', 'feePlan'])
            ->where('status', 'متأخر')
            ->where('remaining_amount', '>', 0);
        $this->applyBranchScope($query, $request);
        $this->applyDateRange($query, $request, 'created_at');

        return $query->orderByDesc('remaining_amount')->get()
            ->map(fn (StudentSubscription $r) => [
                $r->student?->full_name ?? '-',
                $r->feePlan?->name ?? '-',
                $r->status,
                $r->formatted_remaining_amount,
                optional($r->created_at)->format('Y-m-d'),
            ])->all();
    }

    public function payrollsPdfRows(Request $request): array
    {
        $query = TeacherPayroll::query()->with('teacher');
        $this->applyBranchScope($query, $request);
        $this->applyDateRange($query, $request, 'created_at');

        return $query->orderByDesc('year')->orderByDesc('month')->get()
            ->map(fn (TeacherPayroll $r) => [
                $r->teacher?->name ?? '-',
                $r->month_year,
                $r->formatted_base_salary,
                $r->formatted_bonus,
                $r->formatted_final,
                $r->status,
                optional($r->created_at)->format('Y-m-d'),
            ])->all();
    }

    public function expensesPdfRows(Request $request): array
    {
        $query = Expense::query()->with('branch');
        $this->applyBranchScope($query, $request);
        $this->applyDateRange($query, $request, 'expense_date');

        return $query->orderByDesc('expense_date')->get()
            ->map(fn (Expense $r) => [
                $r->formatted_date,
                $r->title,
                $r->branch?->name ?? 'عام',
                $r->formatted_amount,
            ])->all();
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

