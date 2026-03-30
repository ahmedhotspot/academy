<?php

namespace App\Services\Admin;

use App\Models\Group;
use App\Models\TeacherPayroll;
use App\Models\TeacherAttendance;
use App\Models\User;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TeacherAttendanceManagementService extends BaseService
{
    /**
     * قائمة المعلمين للقوائم المنسدلة
     * يعيد: [id => name]
     */
    public function getTeacherOptions(): array
    {
        return User::query()
            ->role('المعلم')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * ملخص إحصائي لتقرير الحضور مع دعم الفلاتر
     *
     * @param array $filters ['teacher_id' => ?, 'attendance_date' => ?]
     */
    public function reportSummary(array $filters = []): array
    {
        $query = TeacherAttendance::query();

        if (! empty($filters['teacher_id'])) {
            $query->where('teacher_id', $filters['teacher_id']);
        }

        if (! empty($filters['attendance_date'])) {
            $query->whereDate('attendance_date', $filters['attendance_date']);
        }

        $all = (clone $query)->get();

        return [
            'total'   => $all->count(),
            'present' => $all->where('status', 'حاضر')->count(),
            'absent'  => $all->where('status', 'غائب')->count(),
            'late'    => $all->where('status', 'متأخر')->count(),
            'excused' => $all->where('status', 'بعذر')->count(),
        ];
    }

    /**
     * بيانات DataTable Ajax لسجلات الحضور
     */
    public function datatable(Request $request): array
    {
        $draw   = (int) $request->input('draw', 1);
        $start  = max((int) $request->input('start', 0), 0);
        $length = max((int) $request->input('length', 10), 1);
        $search = trim((string) data_get($request->input('search'), 'value', ''));

        $baseQuery = TeacherAttendance::query()
            ->with('teacher')
            ->select([
                'id',
                'teacher_id',
                'attendance_date',
                'status',
                'notes',
                'created_at',
            ]);

        // فلترة حسب المعلم
        if ($request->filled('teacher_id')) {
            $baseQuery->where('teacher_id', $request->input('teacher_id'));
        }

        // فلترة حسب التاريخ
        if ($request->filled('attendance_date')) {
            $baseQuery->whereDate('attendance_date', $request->input('attendance_date'));
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $baseQuery->where('status', $request->input('status'));
        }

        $recordsTotal = TeacherAttendance::query()->count();

        if ($search !== '') {
            $baseQuery->where(function ($q) use ($search) {
                $q->whereHas('teacher', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = (clone $baseQuery)->count();

        $rows = $baseQuery
            ->orderByDesc('attendance_date')
            ->orderByDesc('id')
            ->skip($start)
            ->take($length)
            ->get();

        $data = $rows->map(function (TeacherAttendance $attendance) {
            return [
                'id'              => $attendance->id,
                'teacher_id'      => $attendance->teacher_id,
                'teacher_name'    => $attendance->teacher?->name ?? '-',
                'attendance_date' => $attendance->attendance_date?->format('Y-m-d') ?? '-',
                'status'          => $attendance->status,
                'status_badge'    => $attendance->status_badge_class,
                'notes'           => $attendance->notes ?: '-',
            ];
        })->values()->all();

        return [
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ];
    }

    public function getTeacherAttendanceProfile(User $teacher): array
    {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $attendancesQuery = TeacherAttendance::query()
            ->where('teacher_id', $teacher->id)
            ->orderByDesc('attendance_date')
            ->orderByDesc('id');

        $attendances = (clone $attendancesQuery)->get();

        $monthAttendances = (clone $attendancesQuery)
            ->whereBetween('attendance_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->get();

        $recentAttendances = $attendances->take(20)->map(fn (TeacherAttendance $attendance) => [
            'id' => $attendance->id,
            'date' => optional($attendance->attendance_date)->format('Y-m-d') ?? '-',
            'status' => $attendance->status,
            'status_badge' => $attendance->status_badge_class,
            'notes' => $attendance->notes ?: '-',
        ])->values()->all();

        $teachingGroups = Group::query()
            ->with(['branch:id,name', 'studyLevel:id,name', 'studyTrack:id,name'])
            ->withCount('studentEnrollments')
            ->where('teacher_id', $teacher->id)
            ->latest('id')
            ->limit(8)
            ->get()
            ->map(fn (Group $group) => [
                'id' => $group->id,
                'name' => $group->name,
                'branch' => $group->branch?->name ?? '-',
                'level' => $group->studyLevel?->name ?? '-',
                'track' => $group->studyTrack?->name ?? '-',
                'students_count' => $group->student_enrollments_count,
                'status' => $group->status_label,
                'status_badge' => $group->status_badge_class,
            ])
            ->values()
            ->all();

        $latestPayroll = TeacherPayroll::query()
            ->where('teacher_id', $teacher->id)
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->first();

        $currentMonthSummary = [
            'total' => $monthAttendances->count(),
            'present' => $monthAttendances->where('status', 'حاضر')->count(),
            'absent' => $monthAttendances->where('status', 'غائب')->count(),
            'late' => $monthAttendances->where('status', 'متأخر')->count(),
            'excused' => $monthAttendances->where('status', 'بعذر')->count(),
        ];

        $allTimeSummary = [
            'total' => $attendances->count(),
            'present' => $attendances->where('status', 'حاضر')->count(),
            'absent' => $attendances->where('status', 'غائب')->count(),
            'late' => $attendances->where('status', 'متأخر')->count(),
            'excused' => $attendances->where('status', 'بعذر')->count(),
        ];

        return [
            'teacher' => [
                'id' => $teacher->id,
                'name' => $teacher->name,
                'phone' => $teacher->phone,
                'status' => $teacher->status?->label() ?? '-',
                'branch' => $teacher->branch?->name ?? '-',
                'last_login' => optional($teacher->last_login_at)->format('Y-m-d H:i') ?? '-',
            ],
            'current_month' => $currentMonthSummary,
            'all_time' => $allTimeSummary,
            'recent_attendances' => $recentAttendances,
            'teaching_groups' => $teachingGroups,
            'latest_payroll' => $latestPayroll ? [
                'month_year' => $latestPayroll->month_year,
                'final_amount' => $latestPayroll->formatted_final,
                'status' => $latestPayroll->status,
                'status_badge' => $latestPayroll->status_badge_class,
            ] : null,
            'generated_at' => Carbon::now()->format('Y-m-d H:i'),
            'is_present_today' => TeacherAttendance::query()
                ->where('teacher_id', $teacher->id)
                ->whereDate('attendance_date', $today->toDateString())
                ->where('status', 'حاضر')
                ->exists(),
        ];
    }
}

