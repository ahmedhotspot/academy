<?php

namespace App\Services\Admin;

use App\Models\Assessment;
use App\Models\Group;
use App\Models\GroupSchedule;
use App\Models\StudentProgressLog;
use Carbon\Carbon;
use App\Services\BaseService;
use Illuminate\Http\Request;

class GroupScheduleManagementService extends BaseService
{
    public function getGroupOptions(): array
    {
        return Group::query()->orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function datatable(Request $request): array
    {
        $draw = (int) $request->input('draw', 1);
        $start = max((int) $request->input('start', 0), 0);
        $length = max((int) $request->input('length', 10), 1);
        $search = trim((string) data_get($request->input('search'), 'value', ''));

        $baseQuery = GroupSchedule::query()
            ->with('group')
            ->select(['id', 'group_id', 'day_name', 'start_time', 'end_time', 'status']);

        $recordsTotal = GroupSchedule::query()->count();

        if ($search !== '') {
            $baseQuery->where(function ($query) use ($search) {
                $query->where('day_name', 'like', "%{$search}%")
                    ->orWhereHas('group', fn ($groupQuery) => $groupQuery->where('name', 'like', "%{$search}%"));
            });
        }

        $recordsFiltered = (clone $baseQuery)->count();

        $rows = $baseQuery
            ->orderByDesc('id')
            ->skip($start)
            ->take($length)
            ->get();

        $data = $rows->map(function (GroupSchedule $groupSchedule) {
            return [
                'id' => $groupSchedule->id,
                'group' => $groupSchedule->group?->name ?? '-',
                'day_name' => $groupSchedule->day_name,
                'start_time' => substr((string) $groupSchedule->start_time, 0, 5),
                'end_time' => substr((string) $groupSchedule->end_time, 0, 5),
                'status' => $groupSchedule->status_label,
                'status_badge' => $groupSchedule->status_badge_class,
            ];
        })->values()->all();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    public function getGroupScheduleProfile(GroupSchedule $groupSchedule): array
    {
        $groupSchedule->load([
            'group' => fn ($query) => $query->with([
                'branch:id,name',
                'teacher:id,name,phone',
                'studyLevel:id,name',
                'studyTrack:id,name',
                'schedules:id,group_id,day_name,start_time,end_time,status',
                'studentEnrollments.student:id,full_name,phone,status',
            ]),
        ]);

        $group = $groupSchedule->group;

        if (! $group) {
            return [
                'group' => null,
                'stats' => [],
                'sibling_schedules' => [],
                'students' => [],
                'recent_progress' => [],
                'recent_assessments' => [],
            ];
        }

        $studentIds = $group->studentEnrollments->pluck('student_id')->filter()->unique()->all();

        $siblingSchedules = $group->schedules
            ->sortBy(fn ($schedule) => $schedule->day_name . ' ' . $schedule->start_time)
            ->values()
            ->map(fn ($schedule) => [
                'id' => $schedule->id,
                'day_name' => $schedule->day_name,
                'start_time' => substr((string) $schedule->start_time, 0, 5),
                'end_time' => substr((string) $schedule->end_time, 0, 5),
                'status' => $schedule->status_label,
                'status_badge' => $schedule->status_badge_class,
                'is_current' => (int) $schedule->id === (int) $groupSchedule->id,
            ])
            ->all();

        $students = $group->studentEnrollments
            ->map(fn ($enrollment) => [
                'name' => $enrollment->student?->full_name ?? '-',
                'phone' => $enrollment->student?->phone ?? '-',
                'student_status' => $enrollment->student?->status_label ?? '-',
                'student_badge' => $enrollment->student?->status_badge_class ?? 'bg-secondary',
                'enrollment_status' => $enrollment->status_label,
                'enrollment_badge' => $enrollment->status_badge_class,
            ])
            ->values()
            ->all();

        $weekStart = Carbon::now()->startOfWeek()->toDateString();
        $weekEnd = Carbon::now()->endOfWeek()->toDateString();

        $recentProgress = StudentProgressLog::query()
            ->with(['student:id,full_name'])
            ->where('group_id', $group->id)
            ->latest('progress_date')
            ->limit(8)
            ->get()
            ->map(fn (StudentProgressLog $log) => [
                'date' => optional($log->progress_date)->format('Y-m-d'),
                'student' => $log->student?->full_name ?? '-',
                'memorization' => $log->memorization_amount,
                'revision' => $log->revision_amount,
                'mastery' => $log->mastery_level,
            ])
            ->values()
            ->all();

        $recentAssessments = Assessment::query()
            ->with(['student:id,full_name'])
            ->where('group_id', $group->id)
            ->latest('assessment_date')
            ->limit(8)
            ->get()
            ->map(fn (Assessment $assessment) => [
                'date' => optional($assessment->assessment_date)->format('Y-m-d'),
                'student' => $assessment->student?->full_name ?? '-',
                'type' => $assessment->type_label,
                'average' => $assessment->average_score,
                'average_badge' => $assessment->average_badge_class,
            ])
            ->values()
            ->all();

        return [
            'group' => [
                'id' => $group->id,
                'name' => $group->name,
                'branch' => $group->branch?->name ?? '-',
                'teacher' => $group->teacher?->name ?? '-',
                'teacher_phone' => $group->teacher?->phone ?? '-',
                'level' => $group->studyLevel?->name ?? '-',
                'track' => $group->studyTrack?->name ?? '-',
                'type' => $group->type_label,
                'schedule_type' => $group->schedule_type_label,
                'status' => $group->status_label,
                'status_badge' => $group->status_badge_class,
            ],
            'stats' => [
                'schedules_count' => count($siblingSchedules),
                'students_count' => count($students),
                'active_students_count' => $group->studentEnrollments->where('status', 'active')->count(),
                'progress_this_week' => StudentProgressLog::query()->where('group_id', $group->id)->whereBetween('progress_date', [$weekStart, $weekEnd])->count(),
                'assessments_count' => Assessment::query()->where('group_id', $group->id)->count(),
                'assessments_this_month' => Assessment::query()->where('group_id', $group->id)->whereMonth('assessment_date', Carbon::now()->month)->whereYear('assessment_date', Carbon::now()->year)->count(),
            ],
            'sibling_schedules' => $siblingSchedules,
            'students' => $students,
            'recent_progress' => $recentProgress,
            'recent_assessments' => $recentAssessments,
            'generated_at' => Carbon::now()->format('Y-m-d H:i'),
        ];
    }
}

