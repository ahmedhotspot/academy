<?php

namespace App\Services\Admin;

use App\Models\Assessment;
use App\Models\Group;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\StudentProgressLog;
use App\Models\StudentSubscription;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StudentEnrollmentManagementService extends BaseService
{
    public function getStudentOptions(): array
    {
        return Student::query()->orderBy('full_name')->pluck('full_name', 'id')->toArray();
    }

    public function getGroupOptions(): array
    {
        return Group::query()->orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function datatable(Request $request): array
    {
        $draw = (int) $request->input('draw', 1);
        $start = max((int) $request->input('start', 0), 0);
        $length = max((int) $request->input('length', 10), 1);
        $groupId = (int) $request->input('group_id', 0);
        $status = (string) $request->input('status', '');
        $search = trim((string) data_get($request->input('search'), 'value', ''));

        $baseQuery = StudentEnrollment::query()
            ->with(['student', 'group'])
            ->select(['id', 'student_id', 'group_id', 'status', 'created_at']);

        if ($groupId > 0) {
            $baseQuery->where('group_id', $groupId);
        }

        if ($status !== '') {
            $baseQuery->where('status', $status);
        }

        if ($search !== '') {
            $baseQuery->where(function ($query) use ($search) {
                $query->whereHas('student', fn ($studentQuery) => $studentQuery->where('full_name', 'like', "%{$search}%"))
                    ->orWhereHas('group', fn ($groupQuery) => $groupQuery->where('name', 'like', "%{$search}%"));
            });
        }

        $recordsTotal = StudentEnrollment::query()->count();
        $recordsFiltered = (clone $baseQuery)->count();

        $rows = $baseQuery
            ->orderByDesc('created_at')
            ->skip($start)
            ->take($length)
            ->get();

        $data = $rows->map(function (StudentEnrollment $studentEnrollment) {
            return [
                'id' => $studentEnrollment->id,
                'student_id' => $studentEnrollment->student_id,
                'student' => $studentEnrollment->student?->full_name ?? '-',
                'current_group' => $studentEnrollment->group?->name ?? '-',
                'registered_at' => optional($studentEnrollment->created_at)->format('Y-m-d H:i'),
                'status' => $studentEnrollment->status_label,
                'status_badge' => $studentEnrollment->status_badge_class,
            ];
        })->values()->all();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    public function getStudentEnrollmentProfile(Student $student): array
    {
        $student->load([
            'branch:id,name',
            'guardian:id,full_name,phone,whatsapp',
            'enrollments' => fn ($q) => $q->with([
                'group:id,name,teacher_id,study_level_id,study_track_id,status',
                'group.teacher:id,name,phone',
                'group.studyLevel:id,name',
                'group.studyTrack:id,name',
            ])->latest('created_at'),
            'subscriptions' => fn ($q) => $q->with('feePlan:id,name,payment_cycle')->latest('id'),
            'payments' => fn ($q) => $q->latest('payment_date')->latest('id'),
            'progressLogs' => fn ($q) => $q->with(['group:id,name', 'teacher:id,name'])->latest('progress_date')->limit(10),
            'assessments' => fn ($q) => $q->with(['group:id,name', 'teacher:id,name'])->latest('assessment_date')->limit(10),
        ]);

        $currentEnrollment = $student->enrollments->firstWhere('status', 'active');
        $previousEnrollments = $student->enrollments->filter(fn ($enrollment) => $enrollment->status !== 'active')->values();

        $enrollmentsData = $student->enrollments->map(function (StudentEnrollment $enrollment) {
            return [
                'id' => $enrollment->id,
                'group' => $enrollment->group?->name ?? '-',
                'teacher' => $enrollment->group?->teacher?->name ?? '-',
                'teacher_phone' => $enrollment->group?->teacher?->phone ?? '-',
                'level' => $enrollment->group?->studyLevel?->name ?? '-',
                'track' => $enrollment->group?->studyTrack?->name ?? '-',
                'group_status' => $enrollment->group?->status_label ?? '-',
                'group_badge' => $enrollment->group?->status_badge_class ?? 'bg-secondary',
                'enrollment_status' => $enrollment->status_label,
                'enrollment_badge' => $enrollment->status_badge_class,
                'registered_at' => optional($enrollment->created_at)->format('Y-m-d H:i'),
            ];
        })->values()->all();

        $subscriptions = $student->subscriptions->map(function (StudentSubscription $subscription) {
            return [
                'id' => $subscription->id,
                'plan' => $subscription->feePlan?->name ?? '-',
                'cycle' => $subscription->feePlan?->payment_cycle ?? '-',
                'final' => $subscription->formatted_final_amount,
                'paid' => $subscription->formatted_paid_amount,
                'remaining' => $subscription->formatted_remaining_amount,
                'status' => $subscription->status,
                'status_badge' => $subscription->status_badge_class,
                'progress' => $subscription->payment_progress,
            ];
        })->values()->all();

        $payments = $student->payments->take(10)->map(function (Payment $payment) {
            return [
                'id' => $payment->id,
                'receipt' => $payment->receipt_formatted,
                'date' => $payment->formatted_payment_date,
                'amount' => $payment->formatted_amount,
                'notes' => $payment->notes ?: '-',
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
                'average' => $assessment->average_score,
                'average_badge' => $assessment->average_badge_class,
            ];
        })->values()->all();

        $totalPaid = (float) $student->payments->sum('amount');
        $totalRemaining = (float) $student->subscriptions->sum('remaining_amount');
        $totalFinal = (float) $student->subscriptions->sum('final_amount');

        return [
            'student' => [
                'name' => $student->full_name,
                'age' => $student->age,
                'phone' => $student->phone,
                'whatsapp' => $student->whatsapp ?: '-',
                'branch' => $student->branch?->name ?? '-',
                'guardian' => $student->guardian?->full_name ?? '-',
                'guardian_phone' => $student->guardian?->phone ?? '-',
                'status' => $student->status_label,
                'status_badge' => $student->status_badge_class,
            ],
            'stats' => [
                'enrollments_count' => count($enrollmentsData),
                'active_enrollments_count' => $student->enrollments->where('status', 'active')->count(),
                'subscriptions_count' => count($subscriptions),
                'payments_count' => count($payments),
                'progress_count' => StudentProgressLog::query()->where('student_id', $student->id)->count(),
                'assessments_count' => Assessment::query()->where('student_id', $student->id)->count(),
            ],
            'financial' => [
                'total_final' => number_format($totalFinal, 2) . ' ج',
                'total_paid' => number_format($totalPaid, 2) . ' ج',
                'total_remaining' => number_format($totalRemaining, 2) . ' ج',
            ],
            'current_enrollment' => $currentEnrollment,
            'previous_enrollments' => $previousEnrollments,
            'enrollments' => $enrollmentsData,
            'subscriptions' => $subscriptions,
            'payments' => $payments,
            'progress_logs' => $progressLogs,
            'assessments' => $assessments,
            'generated_at' => Carbon::now()->format('Y-m-d H:i'),
        ];
    }
}

