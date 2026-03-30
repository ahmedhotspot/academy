<?php

namespace App\Services\Admin;

use App\Models\Assessment;
use App\Models\Branch;
use App\Models\Group;
use App\Models\StudentEnrollment;
use App\Models\StudentProgressLog;
use App\Models\StudentSubscription;
use App\Models\StudyLevel;
use App\Models\StudyTrack;
use App\Models\User;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GroupManagementService extends BaseService
{
    public function getBranchOptions(): array
    {
        return Branch::query()->orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function getTeacherOptions(): array
    {
        return User::query()
            ->role('المعلم')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function getStudyLevelOptions(): array
    {
        return StudyLevel::query()->where('status', 'active')->orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function getStudyTrackOptions(): array
    {
        return StudyTrack::query()->where('status', 'active')->orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function datatable(Request $request): array
    {
        $draw = (int) $request->input('draw', 1);
        $start = max((int) $request->input('start', 0), 0);
        $length = max((int) $request->input('length', 10), 1);
        $search = trim((string) data_get($request->input('search'), 'value', ''));

        $baseQuery = Group::query()
            ->with(['branch', 'teacher', 'studyLevel', 'studyTrack'])
            ->select([
                'id',
                'branch_id',
                'teacher_id',
                'study_level_id',
                'study_track_id',
                'name',
                'type',
                'schedule_type',
                'status',
            ]);

        $recordsTotal = Group::query()->count();

        if ($search !== '') {
            $baseQuery->where('name', 'like', "%{$search}%");
        }

        $recordsFiltered = (clone $baseQuery)->count();

        $rows = $baseQuery
            ->orderByDesc('id')
            ->skip($start)
            ->take($length)
            ->get();

        $data = $rows->map(function (Group $group) {
            return [
                'id' => $group->id,
                'name' => $group->name,
                'branch' => $group->branch?->name ?? '-',
                'teacher' => $group->teacher?->name ?? '-',
                'study_level' => $group->studyLevel?->name ?? '-',
                'study_track' => $group->studyTrack?->name ?? '-',
                'type' => $group->type_label,
                'schedule_type' => $group->schedule_type_label,
                'status' => $group->status_label,
                'status_badge' => $group->status_badge_class,
            ];
        })->values()->all();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    public function getGroupProfile(Group $group): array
    {
        $group->load([
            'branch:id,name',
            'teacher:id,name,phone',
            'studyLevel:id,name',
            'studyTrack:id,name',
            'schedules' => fn ($query) => $query->orderBy('day_name')->orderBy('start_time'),
            'studentEnrollments' => fn ($query) => $query->with('student:id,full_name,phone,status')->latest('created_at'),
        ]);

        $enrollments = $group->studentEnrollments;
        $studentIds = $enrollments->pluck('student_id')->filter()->unique()->values()->all();

        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        $activeEnrollmentsCount = $enrollments->where('status', 'active')->count();
        $totalEnrollmentsCount = $enrollments->count();
        $schedulesCount = $group->schedules->count();

        $progressThisWeek = StudentProgressLog::query()
            ->where('group_id', $group->id)
            ->whereBetween('progress_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->count();

        $assessmentsThisMonth = Assessment::query()
            ->where('group_id', $group->id)
            ->whereMonth('assessment_date', $today->month)
            ->whereYear('assessment_date', $today->year)
            ->count();

        $overdueStudents = [];
        $overdueCount = 0;

        if ($studentIds !== []) {
            $overdueSubscriptions = StudentSubscription::query()
                ->with('student:id,full_name,phone')
                ->whereIn('student_id', $studentIds)
                ->where('status', 'متأخر')
                ->where('remaining_amount', '>', 0)
                ->orderByDesc('remaining_amount')
                ->limit(8)
                ->get();

            $overdueCount = $overdueSubscriptions->count();

            $overdueStudents = $overdueSubscriptions->map(fn (StudentSubscription $sub) => [
                'student' => $sub->student?->full_name ?? '-',
                'phone' => $sub->student?->phone ?? '-',
                'remaining' => $sub->formatted_remaining_amount,
                'status' => $sub->status,
                'status_badge' => $sub->status_badge_class,
            ])->values()->all();
        }

        $students = $enrollments->map(function (StudentEnrollment $enrollment) {
            return [
                'name' => $enrollment->student?->full_name ?? '-',
                'phone' => $enrollment->student?->phone ?? '-',
                'student_status' => $enrollment->student?->status_label ?? '-',
                'student_badge' => $enrollment->student?->status_badge_class ?? 'bg-secondary',
                'enrollment_status' => $enrollment->status_label,
                'enrollment_badge' => $enrollment->status_badge_class,
                'joined_at' => optional($enrollment->created_at)->format('Y-m-d'),
            ];
        })->values()->all();

        $schedules = $group->schedules->map(function ($schedule) {
            return [
                'day_name' => $schedule->day_name,
                'start_time' => substr((string) $schedule->start_time, 0, 5),
                'end_time' => substr((string) $schedule->end_time, 0, 5),
                'status_label' => $schedule->status_label,
                'status_badge' => $schedule->status_badge_class,
            ];
        })->values()->all();

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
                'tajweed' => $log->tajweed_evaluation,
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
            'stats' => [
                'total_enrollments' => $totalEnrollmentsCount,
                'active_enrollments' => $activeEnrollmentsCount,
                'schedules_count' => $schedulesCount,
                'progress_this_week' => $progressThisWeek,
                'assessments_this_month' => $assessmentsThisMonth,
                'overdue_students' => $overdueCount,
            ],
            'students' => $students,
            'schedules' => $schedules,
            'recent_progress' => $recentProgress,
            'recent_assessments' => $recentAssessments,
            'overdue_students' => $overdueStudents,
        ];
    }
}

