<?php

namespace App\Services\Admin;

use App\Models\Student;
use App\Models\StudentAttendance;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class StudentAttendanceManagementService extends BaseService
{
    public function getStudentOptions(): array
    {
        return Student::query()
            ->orderBy('full_name')
            ->pluck('full_name', 'id')
            ->toArray();
    }

    public function getDailyAttendanceSheet(?string $date = null): array
    {
        $attendanceDate = $date ?: now()->toDateString();

        $students = Student::query()
            ->with('branch:id,name')
            ->where('status', 'active')
            ->orderBy('full_name')
            ->get();

        $existingAttendances = StudentAttendance::query()
            ->whereDate('attendance_date', $attendanceDate)
            ->get()
            ->keyBy('student_id');

        $rows = $students->map(function (Student $student) use ($existingAttendances) {
            $attendance = $existingAttendances->get($student->id);

            return [
                'student_id' => $student->id,
                'student_name' => $student->full_name,
                'branch_name' => $student->branch?->name ?? 'بدون فرع',
                'status' => $attendance?->status ?? 'حاضر',
                'notes' => $attendance?->notes ?? '',
                'is_recorded' => (bool) $attendance,
            ];
        })->values();

        return [
            'attendance_date' => $attendanceDate,
            'rows' => $rows->all(),
            'summary' => [
                'students_count' => $rows->count(),
                'recorded_count' => $rows->where('is_recorded', true)->count(),
                'pending_count' => $rows->where('is_recorded', false)->count(),
            ],
        ];
    }

    public function reportSummary(array $filters = []): array
    {
        $query = StudentAttendance::query();

        if (! empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        if (! empty($filters['attendance_date'])) {
            $query->whereDate('attendance_date', $filters['attendance_date']);
        }

        $all = (clone $query)->get();

        return [
            'total' => $all->count(),
            'present' => $all->where('status', 'حاضر')->count(),
            'absent' => $all->where('status', 'غائب')->count(),
            'transferred' => $all->where('status', 'منقول')->count(),
        ];
    }

    public function datatable(Request $request): array
    {
        $draw = (int) $request->input('draw', 1);
        $start = max((int) $request->input('start', 0), 0);
        $length = max((int) $request->input('length', 10), 1);
        $search = trim((string) data_get($request->input('search'), 'value', ''));

        $baseQuery = StudentAttendance::query()->with('student');
        $this->applyAttendanceFilters($baseQuery, $request, $search);

        $recordsTotal = StudentAttendance::query()->distinct('student_id')->count('student_id');

        $aggregatedQuery = (clone $baseQuery)
            ->selectRaw('student_id, MIN(attendance_date) as first_attendance_date, MAX(attendance_date) as last_attendance_date, COUNT(*) as records_count')
            ->groupBy('student_id');

        $recordsFiltered = (clone $aggregatedQuery)->get()->count();

        $aggregatedRows = $aggregatedQuery
            ->orderByDesc('last_attendance_date')
            ->skip($start)
            ->take($length)
            ->get();

        $studentIds = $aggregatedRows->pluck('student_id')->all();

        $latestAttendances = $studentIds === []
            ? collect()
            : $this->getLatestAttendancesForStudents($request, $search, $studentIds);

        $data = $aggregatedRows->map(function ($row) use ($latestAttendances) {
            $latestAttendance = $latestAttendances->get((int) $row->student_id);

            return [
                'student_id' => (int) $row->student_id,
                'student_name' => $latestAttendance?->student?->full_name ?? '-',
                'first_attendance_date' => $row->first_attendance_date ? Carbon::parse($row->first_attendance_date)->format('Y-m-d') : '-',
                'last_attendance_date' => $row->last_attendance_date ? Carbon::parse($row->last_attendance_date)->format('Y-m-d') : '-',
                'status' => $latestAttendance?->status ?? '-',
                'status_badge' => $latestAttendance?->status_badge_class ?? 'bg-secondary',
                'notes' => $latestAttendance?->notes ?: '-',
                'records_count' => (int) $row->records_count,
            ];
        })->values()->all();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    private function applyAttendanceFilters(Builder $query, Request $request, string $search = ''): void
    {
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->input('student_id'));
        }

        if ($request->filled('attendance_date')) {
            $query->whereDate('attendance_date', $request->input('attendance_date'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($search !== '') {
            $query->where(function (Builder $q) use ($search) {
                $q->whereHas('student', fn (Builder $s) => $s->where('full_name', 'like', "%{$search}%"))
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }
    }

    private function getLatestAttendancesForStudents(Request $request, string $search, array $studentIds)
    {
        $rows = StudentAttendance::query()
            ->with('student')
            ->whereIn('student_id', $studentIds);

        $this->applyAttendanceFilters($rows, $request, $search);

        return $rows
            ->orderByDesc('attendance_date')
            ->orderByDesc('id')
            ->get()
            ->unique('student_id')
            ->keyBy('student_id');
    }
}

