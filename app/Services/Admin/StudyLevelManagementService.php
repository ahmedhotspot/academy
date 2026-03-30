<?php

namespace App\Services\Admin;

use App\Models\Assessment;
use App\Models\Group;
use App\Models\StudentEnrollment;
use App\Models\StudentProgressLog;
use App\Models\StudyLevel;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StudyLevelManagementService extends BaseService
{
    public function datatable(Request $request): array
    {
        $draw = (int) $request->input('draw', 1);
        $start = max((int) $request->input('start', 0), 0);
        $length = max((int) $request->input('length', 10), 1);
        $search = trim((string) data_get($request->input('search'), 'value', ''));

        $baseQuery = StudyLevel::query()->select(['id', 'name', 'status', 'created_at']);
        $recordsTotal = StudyLevel::query()->count();

        if ($search !== '') {
            $baseQuery->where('name', 'like', "%{$search}%");
        }

        $recordsFiltered = (clone $baseQuery)->count();

        $rows = $baseQuery
            ->orderByDesc('id')
            ->skip($start)
            ->take($length)
            ->get();

        $data = $rows->map(function (StudyLevel $studyLevel) {
            return [
                'id' => $studyLevel->id,
                'name' => $studyLevel->name,
                'status' => $studyLevel->status_label,
                'status_badge' => $studyLevel->status_badge_class,
                'created_at' => optional($studyLevel->created_at)->format('Y-m-d'),
            ];
        })->values()->all();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    public function getStudyLevelProfile(StudyLevel $studyLevel): array
    {
        $groups = Group::query()
            ->with([
                'branch:id,name',
                'teacher:id,name,phone',
                'studyTrack:id,name',
                'schedules:id,group_id,day_name,start_time,end_time,status',
            ])
            ->withCount('studentEnrollments')
            ->where('study_level_id', $studyLevel->id)
            ->latest('created_at')
            ->get();

        $groupIds = $groups->pluck('id')->all();

        $enrollmentQuery = StudentEnrollment::query()
            ->with('student:id,full_name,phone,status')
            ->whereIn('group_id', $groupIds);

        $enrollments = (clone $enrollmentQuery)->get();
        $activeEnrollmentsCount = (clone $enrollmentQuery)->where('status', 'active')->count();

        $students = $enrollments
            ->pluck('student')
            ->filter()
            ->unique('id')
            ->values();

        $teachers = $groups
            ->pluck('teacher')
            ->filter()
            ->unique('id')
            ->values();

        $schedulesCount = $groups->sum(fn (Group $group) => $group->schedules->count());
        $tracksCount = $groups->pluck('study_track_id')->filter()->unique()->count();

        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        $progressThisWeek = StudentProgressLog::query()
            ->whereIn('group_id', $groupIds)
            ->whereBetween('progress_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->count();

        $assessmentsThisMonth = Assessment::query()
            ->whereIn('group_id', $groupIds)
            ->whereMonth('assessment_date', $today->month)
            ->whereYear('assessment_date', $today->year)
            ->count();

        $groupsTable = $groups->map(fn (Group $group) => [
            'id' => $group->id,
            'name' => $group->name,
            'branch' => $group->branch?->name ?? '-',
            'teacher' => $group->teacher?->name ?? '-',
            'teacher_phone' => $group->teacher?->phone ?? '-',
            'track' => $group->studyTrack?->name ?? '-',
            'students_count' => $group->student_enrollments_count,
            'schedules_count' => $group->schedules->count(),
            'status' => $group->status_label,
            'status_badge' => $group->status_badge_class,
        ])->values()->all();

        $teachersTable = $teachers->map(function ($teacher) use ($groups) {
            return [
                'id' => $teacher->id,
                'name' => $teacher->name,
                'phone' => $teacher->phone,
                'groups_count' => $groups->where('teacher_id', $teacher->id)->count(),
            ];
        })->values()->all();

        $studentsTable = $students->map(function ($student) use ($enrollments, $groups) {
            $studentGroupIds = $enrollments->where('student_id', $student->id)->pluck('group_id')->unique()->values();

            return [
                'id' => $student->id,
                'name' => $student->full_name,
                'phone' => $student->phone,
                'status' => $student->status_label,
                'status_badge' => $student->status_badge_class,
                'groups' => $groups->whereIn('id', $studentGroupIds)->pluck('name')->values()->all(),
            ];
        })->values()->all();

        $recentProgress = StudentProgressLog::query()
            ->with(['student:id,full_name', 'group:id,name'])
            ->whereIn('group_id', $groupIds)
            ->latest('progress_date')
            ->limit(8)
            ->get()
            ->map(fn (StudentProgressLog $log) => [
                'date' => optional($log->progress_date)->format('Y-m-d'),
                'student' => $log->student?->full_name ?? '-',
                'group' => $log->group?->name ?? '-',
                'memorization' => $log->memorization_amount,
                'revision' => $log->revision_amount,
                'mastery' => $log->mastery_level,
            ])
            ->values()
            ->all();

        $recentAssessments = Assessment::query()
            ->with(['student:id,full_name', 'group:id,name'])
            ->whereIn('group_id', $groupIds)
            ->latest('assessment_date')
            ->limit(8)
            ->get()
            ->map(fn (Assessment $assessment) => [
                'date' => optional($assessment->assessment_date)->format('Y-m-d'),
                'student' => $assessment->student?->full_name ?? '-',
                'group' => $assessment->group?->name ?? '-',
                'type' => $assessment->type_label,
                'average' => $assessment->average_score,
                'average_badge' => $assessment->average_badge_class,
            ])
            ->values()
            ->all();

        return [
            'stats' => [
                'groups_count' => $groups->count(),
                'active_groups_count' => $groups->where('status', 'active')->count(),
                'teachers_count' => $teachers->count(),
                'students_count' => $students->count(),
                'schedules_count' => $schedulesCount,
                'tracks_count' => $tracksCount,
                'enrollments_count' => $enrollments->count(),
                'active_enrollments_count' => $activeEnrollmentsCount,
                'progress_this_week' => $progressThisWeek,
                'assessments_this_month' => $assessmentsThisMonth,
            ],
            'groups' => $groupsTable,
            'teachers' => $teachersTable,
            'students' => $studentsTable,
            'recent_progress' => $recentProgress,
            'recent_assessments' => $recentAssessments,
        ];
    }
}

