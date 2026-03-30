<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Assessments\CreateAssessmentAction;
use App\Actions\Admin\Assessments\DeleteAssessmentAction;
use App\Actions\Admin\Assessments\UpdateAssessmentAction;
use App\Http\Requests\Admin\Assessments\StoreAssessmentRequest;
use App\Http\Requests\Admin\Assessments\UpdateAssessmentRequest;
use App\Models\Assessment;
use App\Models\Student;
use App\Services\Admin\AssessmentManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssessmentController extends AdminController
{
    protected string $title = 'نظام الاختبارات';

    public function __construct(
        private readonly AssessmentManagementService $service
    ) {}

    public function index(Request $request): View
    {
        $actions = [];

        if (auth()->user()?->can('assessments.create')) {
            $actions[] = [
                'title' => 'تسجيل اختبار جديد',
                'url'   => route('admin.assessments.create'),
                'icon'  => 'ti ti-plus',
                'class' => 'btn-primary',
            ];
        }

        $filters = [
            'group_id'        => $request->input('group_id'),
            'type'            => $request->input('type'),
            'assessment_date' => $request->input('assessment_date'),
        ];

        return $this->adminView('admin.assessments.index', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'نظام الاختبارات'],
            ],
            'actions'       => $actions,
            'groupOptions'  => $this->service->getGroupOptions(),
            'assessmentTypes' => Assessment::TYPES,
            'reportSummary' => $this->service->reportSummary($filters),
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        return response()->json($this->service->datatable($request));
    }

    /**
     * AJAX: جلب طلاب الحلقة
     */
    public function studentsByGroup(Request $request): JsonResponse
    {
        $groupId = (int) $request->input('group_id', 0);

        return response()->json($this->service->getStudentsByGroup($groupId));
    }

    public function create(): View
    {
        return $this->adminView('admin.assessments.create', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'نظام الاختبارات', 'url' => route('admin.assessments.index')],
                ['title' => 'تسجيل اختبار جديد'],
            ],
            'groupOptions'      => $this->service->getGroupOptions(),
            'assessmentTypes'   => Assessment::TYPES,
            'maxScore'          => Assessment::MAX_SCORE,
        ]);
    }

    public function store(
        StoreAssessmentRequest $request,
        CreateAssessmentAction $action
    ): RedirectResponse {
        $assessment = $action->handle($request->validated());

        return redirect()
            ->route('admin.assessments.show', $assessment->student_id)
            ->with('success', 'تم تسجيل الاختبار بنجاح.');
    }

    public function show(Student $student): View
    {
        $report = $this->service->getStudentAssessmentReport($student);

        return $this->adminView('admin.assessments.show', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'نظام الاختبارات', 'url' => route('admin.assessments.index')],
                ['title' => 'اختبارات الطالب — ' . $student->full_name],
            ],
            'student' => $student,
            'report'  => $report,
        ]);
    }

    public function edit(Assessment $assessment): View
    {
        $assessment->load(['student', 'group', 'teacher']);

        return $this->adminView('admin.assessments.edit', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'نظام الاختبارات', 'url' => route('admin.assessments.index')],
                ['title' => 'تعديل الاختبار'],
            ],
            'assessment'         => $assessment,
            'groupOptions'       => $this->service->getGroupOptions(),
            'assessmentTypes'    => Assessment::TYPES,
            'maxScore'           => Assessment::MAX_SCORE,
            'currentStudents'    => $this->service->getStudentsByGroup($assessment->group_id ?? 0),
        ]);
    }

    public function update(
        UpdateAssessmentRequest $request,
        Assessment $assessment,
        UpdateAssessmentAction $action
    ): RedirectResponse {
        $payload = $request->validated();
        $payload['assessment'] = $assessment;

        $updated = $action->handle($payload);

        return redirect()
            ->route('admin.assessments.show', $updated->student_id)
            ->with('success', 'تم تحديث الاختبار بنجاح.');
    }

    public function destroy(
        Assessment $assessment,
        DeleteAssessmentAction $action
    ): RedirectResponse {
        $action->handle(['assessment' => $assessment]);

        return redirect()
            ->route('admin.assessments.index')
            ->with('success', 'تم حذف الاختبار بنجاح.');
    }
}

