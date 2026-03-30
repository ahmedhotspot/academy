<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\StudyLevels\CreateStudyLevelAction;
use App\Actions\Admin\StudyLevels\DeleteStudyLevelAction;
use App\Actions\Admin\StudyLevels\UpdateStudyLevelAction;
use App\Http\Requests\Admin\StudyLevels\StoreStudyLevelRequest;
use App\Http\Requests\Admin\StudyLevels\UpdateStudyLevelRequest;
use App\Models\StudyLevel;
use App\Services\Admin\StudyLevelManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudyLevelController extends AdminController
{
    protected string $title = 'إدارة المستويات';

    public function __construct(private readonly StudyLevelManagementService $studyLevelManagementService)
    {
    }

    public function index(): View
    {
        $actions = [];

        if (auth()->user()?->can('study-levels.create')) {
            $actions[] = [
                'title' => 'إضافة مستوى',
                'url' => route('admin.study-levels.create'),
                'icon' => 'ti ti-plus',
                'class' => 'btn-primary',
            ];
        }

        return $this->adminView('admin.study-levels.index', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة المستويات'],
            ],
            'actions' => $actions,
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        return response()->json($this->studyLevelManagementService->datatable($request));
    }

    public function create(): View
    {
        return $this->adminView('admin.study-levels.create', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة المستويات', 'url' => route('admin.study-levels.index')],
                ['title' => 'إضافة مستوى'],
            ],
        ]);
    }

    public function store(StoreStudyLevelRequest $request, CreateStudyLevelAction $createStudyLevelAction): RedirectResponse
    {
        $createStudyLevelAction->handle($request->validated());

        return redirect()
            ->route('admin.study-levels.index')
            ->with('success', 'تمت إضافة المستوى بنجاح.');
    }

    public function show(StudyLevel $studyLevel): View
    {
        $profile = $this->studyLevelManagementService->getStudyLevelProfile($studyLevel);

        return $this->adminView('admin.study-levels.show', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة المستويات', 'url' => route('admin.study-levels.index')],
                ['title' => 'تفاصيل المستوى'],
            ],
            'studyLevel' => $studyLevel,
            'profile' => $profile,
        ]);
    }

    public function edit(StudyLevel $studyLevel): View
    {
        return $this->adminView('admin.study-levels.edit', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة المستويات', 'url' => route('admin.study-levels.index')],
                ['title' => 'تعديل المستوى'],
            ],
            'studyLevel' => $studyLevel,
        ]);
    }

    public function update(UpdateStudyLevelRequest $request, StudyLevel $studyLevel, UpdateStudyLevelAction $updateStudyLevelAction): RedirectResponse
    {
        $payload = $request->validated();
        $payload['studyLevel'] = $studyLevel;

        $updateStudyLevelAction->handle($payload);

        return redirect()
            ->route('admin.study-levels.index')
            ->with('success', 'تم تحديث المستوى بنجاح.');
    }

    public function destroy(StudyLevel $studyLevel, DeleteStudyLevelAction $deleteStudyLevelAction): RedirectResponse
    {
        $deleteStudyLevelAction->handle(['studyLevel' => $studyLevel]);

        return redirect()
            ->route('admin.study-levels.index')
            ->with('success', 'تم حذف المستوى بنجاح.');
    }
}

