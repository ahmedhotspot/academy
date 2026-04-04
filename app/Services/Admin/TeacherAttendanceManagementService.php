<?php

namespace App\Services\Admin;

use App\Models\Group;
use App\Models\TeacherPayroll;
use App\Models\TeacherAttendance;
use App\Models\User;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

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
     * تجهيز كشف يومي سريع لتسجيل حضور جميع المعلمين في شاشة واحدة.
     */
    public function getDailyAttendanceSheet(?string $date = null): array
    {
        $attendanceDate = $date ?: now()->toDateString();

        $teachers = User::query()
            ->role('المعلم')
            ->with('branch:id,name')
            ->orderBy('name')
            ->get();

        $existingAttendances = TeacherAttendance::query()
            ->whereDate('attendance_date', $attendanceDate)
            ->get()
            ->keyBy('teacher_id');

        $rows = $teachers->map(function (User $teacher) use ($existingAttendances) {
            $attendance = $existingAttendances->get($teacher->id);

            return [
                'teacher_id' => $teacher->id,
                'teacher_name' => $teacher->name,
                'branch_name' => $teacher->branch?->name ?? 'بدون فرع',
                'status' => $attendance?->status ?? 'حاضر',
                'notes' => $attendance?->notes ?? '',
                'existing_attendance_id' => $attendance?->id,
                'is_recorded' => (bool) $attendance,
            ];
        })->values();

        return [
            'attendance_date' => $attendanceDate,
            'rows' => $rows->all(),
            'summary' => [
                'teachers_count' => $rows->count(),
                'recorded_count' => $rows->where('is_recorded', true)->count(),
                'pending_count' => $rows->where('is_recorded', false)->count(),
            ],
        ];
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

        $baseQuery = TeacherAttendance::query()->with('teacher');
        $this->applyAttendanceFilters($baseQuery, $request, $search);

        $recordsTotal = TeacherAttendance::query()
            ->distinct('teacher_id')
            ->count('teacher_id');

        $aggregatedQuery = (clone $baseQuery)
            ->selectRaw('teacher_id, MIN(attendance_date) as first_attendance_date, MAX(attendance_date) as last_attendance_date, COUNT(*) as records_count')
            ->groupBy('teacher_id');

        $recordsFiltered = (clone $aggregatedQuery)->get()->count();

        $aggregatedRows = $aggregatedQuery
            ->orderByDesc('last_attendance_date')
            ->skip($start)
            ->take($length)
            ->get();

        $teacherIds = $aggregatedRows->pluck('teacher_id')->all();

        $latestAttendances = $teacherIds === []
            ? collect()
            : $this->getLatestAttendancesForTeachers($request, $search, $teacherIds);

        $data = $aggregatedRows->map(function ($row) use ($latestAttendances) {
            /** @var TeacherAttendance|null $latestAttendance */
            $latestAttendance = $latestAttendances->get((int) $row->teacher_id);

            return [
                'teacher_id' => (int) $row->teacher_id,
                'teacher_name' => $latestAttendance?->teacher?->name ?? '-',
                'first_attendance_date' => $row->first_attendance_date ? Carbon::parse($row->first_attendance_date)->format('Y-m-d') : '-',
                'last_attendance_date' => $row->last_attendance_date ? Carbon::parse($row->last_attendance_date)->format('Y-m-d') : '-',
                'status' => $latestAttendance?->status ?? '-',
                'status_badge' => $latestAttendance?->status_badge_class ?? 'bg-secondary',
                'notes' => $latestAttendance?->notes ?: '-',
                'records_count' => (int) $row->records_count,
                'latest_attendance_id' => $latestAttendance?->id,
            ];
        })->values()->all();

        return [
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ];
    }

    private function applyAttendanceFilters(Builder $query, Request $request, string $search = ''): void
    {
        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->input('teacher_id'));
        }

        if ($request->filled('attendance_date')) {
            $query->whereDate('attendance_date', $request->input('attendance_date'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($search !== '') {
            $query->where(function (Builder $q) use ($search) {
                $q->whereHas('teacher', fn (Builder $u) => $u->where('name', 'like', "%{$search}%"))
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }
    }

    private function getLatestAttendancesForTeachers(Request $request, string $search, array $teacherIds): Collection
    {
        $rows = TeacherAttendance::query()
            ->with('teacher')
            ->whereIn('teacher_id', $teacherIds);

        $this->applyAttendanceFilters($rows, $request, $search);

        return $rows
            ->orderByDesc('attendance_date')
            ->orderByDesc('id')
            ->get()
            ->unique('teacher_id')
            ->keyBy('teacher_id');
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

