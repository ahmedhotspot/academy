<?php

namespace App\Services\Admin;

use App\Models\TeacherAttendance;
use App\Models\TeacherPayroll;
use App\Models\User;
use App\Services\BaseService;
use Illuminate\Http\Request;

class TeacherPayrollManagementService extends BaseService
{
    /**
     * قائمة المعلمين النشطين
     */
    public function getTeacherOptions(): array
    {
        return User::query()
            ->whereHas('roles', fn ($q) => $q->where('name', 'المعلم'))
            ->where('status', 'active')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
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

        $baseQuery = TeacherPayroll::query()->with('teacher');

        if ($request->filled('status')) {
            $baseQuery->where('status', $request->input('status'));
        }

        if ($request->filled('month')) {
            $baseQuery->where('month', $request->input('month'));
        }

        if ($request->filled('year')) {
            $baseQuery->where('year', $request->input('year'));
        }

        $recordsTotal = TeacherPayroll::query()->count();

        if ($search !== '') {
            $baseQuery->where(function ($q) use ($search) {
                $q->whereHas('teacher', fn ($t) => $t->where('name', 'like', "%{$search}%"))
                    ->orWhere('final_amount', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = (clone $baseQuery)->count();

        $rows = $baseQuery
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->orderByDesc('id')
            ->skip($start)
            ->take($length)
            ->get();

        $data = $rows->map(function (TeacherPayroll $payroll) {
            return [
                'id'                => $payroll->id,
                'teacher_id'        => $payroll->teacher_id,
                'teacher_name'      => $payroll->teacher?->name ?? '-',
                'month_year'        => $payroll->month_year,
                'base_salary'       => $payroll->base_salary,
                'formatted_base'    => $payroll->formatted_base_salary,
                'deduction'         => $payroll->deduction_amount,
                'penalty'           => $payroll->penalty_amount,
                'bonus'             => $payroll->bonus_amount,
                'final_amount'      => $payroll->final_amount,
                'formatted_final'   => $payroll->formatted_final,
                'status'            => $payroll->status,
                'status_badge'      => $payroll->status_badge_class,
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
     * حساب الغياب للمعلم
     */
    public function getTeacherAbsences(int $teacherId, int $month, int $year): int
    {
        return TeacherAttendance::query()
            ->where('teacher_id', $teacherId)
            ->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)
            ->where('status', 'غائب')
            ->count();
    }

    /**
     * ملخص إحصائي
     */
    public function reportSummary(array $filters = []): array
    {
        $query = TeacherPayroll::query();

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['month'])) {
            $query->where('month', $filters['month']);
        }

        if (! empty($filters['year'])) {
            $query->where('year', $filters['year']);
        }

        $total        = (clone $query)->count();
        $processed    = (clone $query)->where('status', 'مصروف')->count();
        $pending      = (clone $query)->where('status', 'غير مصروف')->count();
        $totalSalaries = (clone $query)->sum('final_amount');

        return compact('total', 'processed', 'pending', 'totalSalaries');
    }
}

