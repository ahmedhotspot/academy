<?php

namespace App\Services\Admin;

use App\Models\Assessment;
use App\Models\Group;
use App\Models\Student;
use App\Models\User;
use App\Services\BaseService;
use Illuminate\Http\Request;

class AssessmentManagementService extends BaseService
{
    /**
     * قائمة الحلقات النشطة للقوائم المنسدلة
     * تصفية الحلقات حسب فرع المستخدم الحالي
     */
    public function getGroupOptions(): array
    {
        $userBranchId = auth()->user()?->branch_id;

        $query = Group::query()
            ->with('teacher')
            ->where('status', 'active');

        // إذا كان المستخدم مرتبطاً بفرع معين، احصر الحلقات على هذا الفرع
        if ($userBranchId) {
            $query->where('branch_id', $userBranchId);
        }

        return $query->orderBy('name')
            ->get()
            ->mapWithKeys(fn (Group $g) => [
                $g->id => $g->name . ($g->teacher ? ' — ' . $g->teacher->name : ''),
            ])
            ->toArray();
    }

    /**
     * قائمة الطلاب بناءً على الحلقة
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
     * قائمة المعلمين حسب الفرع الحالي
     * يجلب فقط المعلمين المرتبطين بنفس فرع المستخدم الحالي
     * (لا يشمل المستخدم الحالي نفسه)
     */
    public function getTeachersByBranch(?int $branchId = null): array
    {
        // إذا لم يكن هناك فرع محدد، أرجع قائمة فارغة
        if (!$branchId) {
            return [];
        }

        $currentUserId = auth()->id();

        $teachers = User::query()
            ->where('branch_id', $branchId)  // فقط المعلمين من هذا الفرع
            ->where('id', '!=', $currentUserId)  // استبعاد المستخدم الحالي
            ->where('status', 'active')       // النشطين فقط
            ->orderBy('name')                 // مرتبة حسب الاسم
            ->get();

        return $teachers
            ->mapWithKeys(fn ($teacher) => [
                $teacher->id => $teacher->name,
            ])
            ->toArray();
    }

    /**
     * بيانات DataTable Ajax للاختبارات
     */
    public function datatable(Request $request): array
    {
        $draw   = (int) $request->input('draw', 1);
        $start  = max((int) $request->input('start', 0), 0);
        $length = max((int) $request->input('length', 10), 1);
        $search = trim((string) data_get($request->input('search'), 'value', ''));

        $baseQuery = Assessment::query()
            ->with(['student', 'group', 'teacher']);

        // تصفية الاختبارات حسب الفرع الحالي للمستخدم
        $userBranchId = auth()->user()?->branch_id;
        if ($userBranchId) {
            $baseQuery->whereHas('group', fn ($q) => $q->where('branch_id', $userBranchId));
        }

        if ($request->filled('group_id')) {
            $baseQuery->where('group_id', $request->input('group_id'));
        }

        if ($request->filled('type')) {
            $baseQuery->where('type', $request->input('type'));
        }

        if ($request->filled('assessment_date')) {
            $baseQuery->whereDate('assessment_date', $request->input('assessment_date'));
        }

        // إجمالي الاختبارات (بعد تطبيق filter الفرع)
        $recordsTotal = Assessment::query()
            ->with(['group'])
            ->when($userBranchId, fn ($q) =>
                $q->whereHas('group', fn ($q2) => $q2->where('branch_id', $userBranchId))
            )->count();

        if ($search !== '') {
            $baseQuery->where(function ($q) use ($search) {
                $q->whereHas('student', fn ($s) => $s->where('full_name', 'like', "%{$search}%"))
                    ->orWhereHas('group', fn ($g) => $g->where('name', 'like', "%{$search}%"))
                    ->orWhere('type', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = (clone $baseQuery)->count();

        $rows = $baseQuery
            ->orderByDesc('assessment_date')
            ->orderByDesc('id')
            ->skip($start)
            ->take($length)
            ->get();

        $data = $rows->map(function (Assessment $assessment) {
            return [
                'id'                  => $assessment->id,
                'student_id'          => $assessment->student_id,
                'student_name'        => $assessment->student?->full_name ?? '-',
                'group_name'          => $assessment->group?->name ?? '-',
                'teacher_name'        => $assessment->teacher?->name ?? '-',
                'assessment_date'     => $assessment->assessment_date?->format('Y-m-d') ?? '-',
                'type'                => $assessment->type,
                'type_label'          => $assessment->type_label,
                'memorization_result' => $assessment->memorization_result ?? '-',
                'memorization_badge'  => $assessment->memorization_badge_class,
                'tajweed_result'      => $assessment->tajweed_result ?? '-',
                'tajweed_badge'       => $assessment->tajweed_badge_class,
                'tadabbur_result'     => $assessment->tadabbur_result ?? '-',
                'tadabbur_badge'      => $assessment->tadabbur_badge_class,
                'average_score'       => $assessment->average_score ?? '-',
                'average_badge'       => $assessment->average_badge_class,
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
     * ملخص إحصائي للاختبارات
     */
    public function reportSummary(array $filters = []): array
    {
        $query = Assessment::query();

        // تصفية الاختبارات حسب الفرع الحالي للمستخدم
        $userBranchId = auth()->user()?->branch_id;
        if ($userBranchId) {
            $query->whereHas('group', fn ($q) => $q->where('branch_id', $userBranchId));
        }

        if (! empty($filters['group_id'])) {
            $query->where('group_id', $filters['group_id']);
        }

        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (! empty($filters['assessment_date'])) {
            $query->whereDate('assessment_date', $filters['assessment_date']);
        }

        $total    = (clone $query)->count();
        $weekly   = (clone $query)->where('type', 'أسبوعي')->count();
        $monthly  = (clone $query)->where('type', 'شهري')->count();
        $complete = (clone $query)->where('type', 'ختم جزء')->count();

        return compact('total', 'weekly', 'monthly', 'complete');
    }

    /**
     * تقرير اختبارات الطالب
     */
    public function getStudentAssessmentReport(Student $student): array
    {
        $assessments = $student->assessments()
            ->with(['group', 'teacher'])
            ->orderByDesc('assessment_date')
            ->orderByDesc('id')
            ->get();

        // توزيع الاختبارات حسب النوع
        $byType = [
            'أسبوعي'  => $assessments->where('type', 'أسبوعي')->count(),
            'شهري'    => $assessments->where('type', 'شهري')->count(),
            'ختم جزء' => $assessments->where('type', 'ختم جزء')->count(),
        ];

        // متوسط النتائج
        $memorizations = $assessments->whereNotNull('memorization_result')
            ->pluck('memorization_result')
            ->toArray();
        $tajweeds = $assessments->whereNotNull('tajweed_result')
            ->pluck('tajweed_result')
            ->toArray();
        $tadaburs = $assessments->whereNotNull('tadabbur_result')
            ->pluck('tadabbur_result')
            ->toArray();

        $avgMemo  = count($memorizations) > 0 ? round(array_sum($memorizations) / count($memorizations), 2) : null;
        $avgTajw  = count($tajweeds) > 0 ? round(array_sum($tajweeds) / count($tajweeds), 2) : null;
        $avgTada  = count($tadaburs) > 0 ? round(array_sum($tadaburs) / count($tadaburs), 2) : null;

        // أعلى وأقل النتائج
        $allScores = array_merge($memorizations, $tajweeds, $tadaburs);
        $bestScore = count($allScores) > 0 ? max($allScores) : null;
        $worstScore = count($allScores) > 0 ? min($allScores) : null;

        return [
            'assessments'    => $assessments,
            'total'          => $assessments->count(),
            'byType'         => $byType,
            'avgMemoization' => $avgMemo,
            'avgTajweed'     => $avgTajw,
            'avgTadabbur'    => $avgTada,
            'bestScore'      => $bestScore,
            'worstScore'     => $worstScore,
            'lastAssessment' => $assessments->first(),
        ];
    }
}

