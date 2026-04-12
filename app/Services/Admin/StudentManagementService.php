<?php

namespace App\Services\Admin;

use App\Models\Branch;
use App\Models\Assessment;
use App\Models\Guardian;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentProgressLog;
use App\Models\StudentSubscription;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class StudentManagementService extends BaseService
{
    public function getBranchOptions(): array
    {
        $branches = Branch::query()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        $viewer = auth()->user();

        if (! $viewer || $viewer->isSuperAdmin()) {
            return $branches;
        }

        $branchId = (int) $viewer->branch_id;

        return isset($branches[$branchId]) ? [$branchId => $branches[$branchId]] : [];
    }

    public function getGuardianOptions(): array
    {
        if (! Schema::hasTable('guardians')) {
            return [];
        }

        $viewer = auth()->user();

        return Guardian::query()
            ->when(
                $viewer && ! $viewer->isSuperAdmin() && $viewer->branch_id,
                fn ($query) => $query->where('branch_id', $viewer->branch_id)
            )
            ->where('status', 'active')
            ->orderBy('full_name')
            ->pluck('full_name', 'id')
            ->toArray();
    }

    public function getStats(): array
    {
        $total    = Student::query()->count();
        $active   = Student::query()->where('status', 'active')->count();
        $inactive = Student::query()->where('status', 'inactive')->count();

        return compact('total', 'active', 'inactive');
    }

    public function datatable(Request $request): array
    {
        $draw          = (int) $request->input('draw', 1);
        $start         = max((int) $request->input('start', 0), 0);
        $length        = max((int) $request->input('length', 10), 1);
        $search        = trim((string) data_get($request->input('search'), 'value', ''));
        $filterBranch  = $request->input('filter_branch_id');
        $filterStatus  = $request->input('filter_status');

        $baseQuery = Student::query()
            ->with(['branch'])
            ->select([
                'id',
                'branch_id',
                'student_code',
                'full_name',
                'age',
                'nationality',
                'identity_number',
                'phone',
                'whatsapp',
                'status',
            ])
            ->when($filterBranch, fn ($q) => $q->where('students.branch_id', $filterBranch))
            ->when($filterStatus,  fn ($q) => $q->where('students.status', $filterStatus));

        $recordsTotal = (clone $baseQuery)->count();

        if ($search !== '') {
            $baseQuery->where(function ($query) use ($search) {
                $query->where('full_name', 'like', "%{$search}%")
                    ->orWhere('student_code', 'like', "%{$search}%")
                    ->orWhere('nationality', 'like', "%{$search}%")
                    ->orWhere('identity_number', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('whatsapp', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = (clone $baseQuery)->count();

        $rows = $baseQuery
            ->orderByDesc('id')
            ->skip($start)
            ->take($length)
            ->get();

        $data = $rows->map(function (Student $student) {
            return [
                'id' => $student->id,
                'student_code' => $student->student_code ?: '-',
                'full_name' => $student->full_name,
                'age' => $student->age,
                'nationality' => $student->nationality,
                'identity_number' => $student->identity_number ?: '-',
                'phone' => $student->phone,
                'whatsapp' => $student->whatsapp ?: '-',
                'branch' => $student->branch?->name ?? '-',
                'status' => $student->status_label,
                'status_badge' => $student->status_badge_class,
            ];
        })->values()->all();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    public function getStudentProfile(Student $student): array
    {
        $student->load([
            'branch:id,name',
            'guardian:id,full_name,phone,whatsapp,status',
            'enrollments' => fn ($q) => $q->latest('id'),
            'enrollments.group:id,name,teacher_id,study_level_id,study_track_id,status',
            'enrollments.group.teacher:id,name',
            'enrollments.group.studyLevel:id,name',
            'enrollments.group.studyTrack:id,name',
            'subscriptions' => fn ($q) => $q->with('feePlan:id,name,payment_cycle')->latest('id'),
            'payments' => fn ($q) => $q->latest('payment_date')->latest('id'),
            'progressLogs' => fn ($q) => $q->with(['group:id,name', 'teacher:id,name'])->latest('progress_date')->limit(8),
            'assessments' => fn ($q) => $q->with(['group:id,name', 'teacher:id,name'])->latest('assessment_date')->limit(8),
        ]);

        $guardian = $student->guardian;
        $guardianName = $guardian?->full_name ?? '-';
        $guardianPhone = $guardian?->phone ?? '-';
        $guardianWhatsapp = $guardian?->whatsapp ?? '-';

        $subscriptions = $student->subscriptions->map(function (StudentSubscription $subscription) {
            return [
                'id' => $subscription->id,
                'plan' => $subscription->feePlan?->name ?? '-',
                'cycle' => $subscription->feePlan?->payment_cycle ?? '-',
                'amount' => $subscription->formatted_amount,
                'discount' => $subscription->formatted_discount,
                'final' => $subscription->formatted_final_amount,
                'paid' => $subscription->formatted_paid_amount,
                'remaining' => $subscription->formatted_remaining_amount,
                'status' => $subscription->status,
                'status_badge' => $subscription->status_badge_class,
                'progress' => $subscription->payment_progress,
            ];
        })->values()->all();

        $payments = $student->payments->take(8)->map(function (Payment $payment) {
            return [
                'id' => $payment->id,
                'receipt' => $payment->receipt_formatted,
                'date' => $payment->formatted_payment_date,
                'amount' => $payment->formatted_amount,
                'notes' => $payment->notes ?: '-',
            ];
        })->values()->all();

        $enrollments = $student->enrollments->map(function ($enrollment) {
            return [
                'id' => $enrollment->id,
                'group' => $enrollment->group?->name ?? '-',
                'teacher' => $enrollment->group?->teacher?->name ?? '-',
                'level' => $enrollment->group?->studyLevel?->name ?? '-',
                'track' => $enrollment->group?->studyTrack?->name ?? '-',
                'group_status' => $enrollment->group?->status_label ?? '-',
                'group_badge' => $enrollment->group?->status_badge_class ?? 'bg-secondary',
                'enrollment_status' => $enrollment->status_label,
                'enrollment_badge' => $enrollment->status_badge_class,
                'date' => optional($enrollment->created_at)->format('Y-m-d'),
            ];
        })->values()->all();

        $progressLogs = $student->progressLogs->map(function (StudentProgressLog $log) {
            return [
                'date' => optional($log->progress_date)->format('Y-m-d'),
                'group' => $log->group?->name ?? '-',
                'teacher' => $log->teacher?->name ?? '-',
                'memorization' => $log->memorization_amount,
                'revision' => $log->revision_amount,
                'tajweed' => $log->tajweed_evaluation,
                'tadabbur' => $log->tadabbur_evaluation,
                'mastery' => $log->mastery_level,
                'commitment' => $log->commitment_status,
            ];
        })->values()->all();

        $assessments = $student->assessments->map(function (Assessment $assessment) {
            return [
                'date' => optional($assessment->assessment_date)->format('Y-m-d'),
                'type' => $assessment->type_label,
                'group' => $assessment->group?->name ?? '-',
                'teacher' => $assessment->teacher?->name ?? '-',
                'memorization' => $assessment->memorization_result,
                'tajweed' => $assessment->tajweed_result,
                'tadabbur' => $assessment->tadabbur_result,
                'average' => $assessment->average_score,
                'average_badge' => $assessment->average_badge_class,
            ];
        })->values()->all();

        $totalPaid = (float) $student->payments->sum('amount');
        $totalRemaining = (float) $student->subscriptions->sum('remaining_amount');
        $totalFinal = (float) $student->subscriptions->sum('final_amount');
        $collectionRate = $totalFinal > 0
            ? min(100, round(($totalPaid / $totalFinal) * 100, 2))
            : 0.0;

        $assessmentAverage = (float) Assessment::query()
            ->where('student_id', $student->id)
            ->selectRaw('AVG((COALESCE(memorization_result,0) + COALESCE(tajweed_result,0) + COALESCE(tadabbur_result,0)) / (CASE WHEN tadabbur_result IS NULL THEN 2 ELSE 3 END)) as avg_score')
            ->value('avg_score');

        return [
            'guardian' => [
                'name' => $guardianName,
                'phone' => $guardianPhone,
                'whatsapp' => $guardianWhatsapp,
            ],
            'stats' => [
                'enrollments' => count($enrollments),
                'subscriptions' => count($subscriptions),
                'payments' => $student->payments->count(),
                'progress_logs' => StudentProgressLog::query()->where('student_id', $student->id)->count(),
                'assessments' => Assessment::query()->where('student_id', $student->id)->count(),
            ],
            'financial' => [
                'total_paid' => number_format($totalPaid, 2) . ' ج',
                'total_remaining' => number_format($totalRemaining, 2) . ' ج',
                'total_final' => number_format($totalFinal, 2) . ' ج',
                'collection_rate' => $collectionRate,
            ],
            'learning' => [
                'assessment_avg' => $assessmentAverage > 0 ? round($assessmentAverage, 2) : null,
                'last_progress_date' => $student->progressLogs->first()?->progress_date?->format('Y-m-d') ?? '-',
                'last_assessment_date' => $student->assessments->first()?->assessment_date?->format('Y-m-d') ?? '-',
            ],
            'enrollments' => $enrollments,
            'subscriptions' => $subscriptions,
            'payments' => $payments,
            'progress_logs' => $progressLogs,
            'assessments' => $assessments,
            'meta' => [
                'generated_at' => Carbon::now()->format('Y-m-d H:i'),
            ],
        ];
    }
}

