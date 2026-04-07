<?php

namespace App\Services\Admin;

use App\Models\Group;
use App\Models\Student;
use App\Models\StudentProgressLog;
use App\Models\User;
use App\Services\BaseService;
use Illuminate\Http\Request;

class StudentProgressLogManagementService extends BaseService
{
    /**
     * قائمة الحلقات النشطة للقوائم المنسدلة
     * يعيد: [id => 'اسم الحلقة — المعلم']
     */
    public function getGroupOptions(): array
    {
        return Group::query()
            ->with('teacher')
            ->where('status', 'active')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (Group $g) => [
                $g->id => $g->name . ($g->teacher ? ' — ' . $g->teacher->name : ''),
            ])
            ->toArray();
    }

    /**
     * قائمة الطلاب المسجلين في حلقة معينة (Ajax)
     * يعيد: [['id' => ?, 'name' => ?], ...]
     */
    public function getStudentsByGroup(int $groupId): array
    {
        $group = Group::find($groupId);
        if (! $group) {
            return [];
        }

        return $group->studentEnrollments()
            ->with('student')
            ->where('status', 'active')
            ->get()
            ->map(fn ($enrollment) => [
                'id'   => $enrollment->student->id,
                'name' => $enrollment->student->full_name,
            ])
            ->sortBy('name')
            ->values()
            ->toArray();
    }

    /**
     * قائمة المعلمين للقوائم المنسدلة
     * يعيد: [id => name]
     */
    public function getTeacherOptions(): array
    {
        $query = User::query()
            ->role('المعلم')
            ->orderBy('name');

        $user = auth()->user();
        if ($user && ! $user->isSuperAdmin() && $user->branch_id) {
            $query->where('branch_id', $user->branch_id);
        }

        return $query->pluck('name', 'id')->toArray();
    }

    /**
     * بيانات DataTable Ajax لسجلات المتابعة
     */
    public function datatable(Request $request): array
    {
        $draw   = (int) $request->input('draw', 1);
        $start  = max((int) $request->input('start', 0), 0);
        $length = max((int) $request->input('length', 10), 1);
        $search = trim((string) data_get($request->input('search'), 'value', ''));

        $baseQuery = StudentProgressLog::query()
            ->with(['student', 'group', 'teacher']);

        if ($request->filled('group_id')) {
            $baseQuery->where('group_id', $request->input('group_id'));
        }

        if ($request->filled('student_id')) {
            $baseQuery->where('student_id', $request->input('student_id'));
        }

        if ($request->filled('progress_date')) {
            $baseQuery->whereDate('progress_date', $request->input('progress_date'));
        }

        if ($request->filled('commitment_status')) {
            $baseQuery->where('commitment_status', $request->input('commitment_status'));
        }

        if ($request->filled('mastery_level')) {
            $baseQuery->where('mastery_level', $request->input('mastery_level'));
        }

        $recordsTotal = StudentProgressLog::query()->count();

        if ($search !== '') {
            $baseQuery->where(function ($q) use ($search) {
                $q->whereHas('student', fn ($s) => $s->where('full_name', 'like', "%{$search}%"))
                    ->orWhereHas('group', fn ($g) => $g->where('name', 'like', "%{$search}%"))
                    ->orWhere('memorization_amount', 'like', "%{$search}%")
                    ->orWhere('mastery_level', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = (clone $baseQuery)->count();

        $rows = $baseQuery
            ->orderByDesc('progress_date')
            ->orderByDesc('id')
            ->skip($start)
            ->take($length)
            ->get();

        $data = $rows->map(function (StudentProgressLog $log) {
            return [
                'id'                  => $log->id,
                'student_id'          => $log->student_id,
                'student_name'        => $log->student?->full_name ?? '-',
                'group_name'          => $log->group?->name ?? '-',
                'teacher_name'        => $log->teacher?->name ?? '-',
                'progress_date'       => $log->progress_date?->format('Y-m-d') ?? '-',
                'memorization_amount' => $log->memorization_amount,
                'revision_amount'     => $log->revision_amount,
                'tajweed_evaluation'  => $log->tajweed_evaluation,
                'tajweed_badge'       => $log->tajweed_badge_class,
                'mastery_level'       => $log->mastery_level,
                'mastery_badge'       => $log->mastery_badge_class,
                'commitment_status'   => $log->commitment_status,
                'commitment_badge'    => $log->commitment_badge_class,
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
     * ملخص إحصائي للصفحة الرئيسية مع دعم الفلاتر
     */
    public function reportSummary(array $filters = []): array
    {
        $query = StudentProgressLog::query();

        if (! empty($filters['group_id'])) {
            $query->where('group_id', $filters['group_id']);
        }

        if (! empty($filters['progress_date'])) {
            $query->whereDate('progress_date', $filters['progress_date']);
        }

        $total     = (clone $query)->count();
        $committed = (clone $query)->where('commitment_status', 'ملتزم')->count();
        $late      = (clone $query)->where('commitment_status', 'متأخر')->count();
        $excellent = (clone $query)->where('mastery_level', 'ممتاز')->count();

        return compact('total', 'committed', 'late', 'excellent');
    }

    /**
     * سجل متابعة طالب معين مع إحصائياته لصفحة show
     */
    public function getStudentReport(Student $student): array
    {
        $logs = $student->progressLogs()
            ->with(['group', 'teacher'])
            ->orderByDesc('progress_date')
            ->orderByDesc('id')
            ->get();

        $total     = $logs->count();
        $committed = $logs->where('commitment_status', 'ملتزم')->count();
        $late      = $logs->where('commitment_status', 'متأخر')->count();

        // احتساب نسبة الالتزام
        $commitmentRate = $total > 0 ? round(($committed / $total) * 100) : 0;

        // أكثر مستوى إتقان تكراراً
        $masteryFrequency = $logs->groupBy('mastery_level')
            ->map(fn ($group) => $group->count())
            ->sortDesc();
        $dominantMastery = $masteryFrequency->keys()->first() ?? '-';

        return [
            'logs'            => $logs,
            'total'           => $total,
            'committed'       => $committed,
            'late'            => $late,
            'commitmentRate'  => $commitmentRate,
            'dominantMastery' => $dominantMastery,
            'lastLog'         => $logs->first(),
        ];
    }
}

