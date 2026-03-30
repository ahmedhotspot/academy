<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\StudyTracks\CreateStudyTrackAction;
use App\Actions\Admin\StudyTracks\DeleteStudyTrackAction;
use App\Actions\Admin\StudyTracks\UpdateStudyTrackAction;
use App\Http\Requests\Admin\StudyTracks\StoreStudyTrackRequest;
use App\Http\Requests\Admin\StudyTracks\UpdateStudyTrackRequest;
use App\Models\StudyTrack;
use App\Services\Admin\StudyTrackManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudyTrackController extends AdminController
{
    protected string $title = 'إدارة المسارات';

    public function __construct(private readonly StudyTrackManagementService $studyTrackManagementService)
    {
    }

    public function index(): View
    {
        $actions = [];

        if (auth()->user()?->can('study-tracks.create')) {
            $actions[] = [
                'title' => 'إضافة مسار',
                'url' => route('admin.study-tracks.create'),
                'icon' => 'ti ti-plus',
                'class' => 'btn-primary',
            ];
        }

        return $this->adminView('admin.study-tracks.index', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة المسارات'],
            ],
            'actions' => $actions,
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        return response()->json($this->studyTrackManagementService->datatable($request));
    }

    public function create(): View
    {
        return $this->adminView('admin.study-tracks.create', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة المسارات', 'url' => route('admin.study-tracks.index')],
                ['title' => 'إضافة مسار'],
            ],
        ]);
    }

    public function store(StoreStudyTrackRequest $request, CreateStudyTrackAction $createStudyTrackAction): RedirectResponse
    {
        $createStudyTrackAction->handle($request->validated());

        return redirect()
            ->route('admin.study-tracks.index')
            ->with('success', 'تمت إضافة المسار بنجاح.');
    }

    public function show(StudyTrack $studyTrack): View
    {
        $profile = $this->studyTrackManagementService->getStudyTrackProfile($studyTrack);

        return $this->adminView('admin.study-tracks.show', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة المسارات', 'url' => route('admin.study-tracks.index')],
                ['title' => 'تفاصيل المسار'],
            ],
            'studyTrack' => $studyTrack,
            'profile' => $profile,
        ]);
    }

    public function edit(StudyTrack $studyTrack): View
    {
        return $this->adminView('admin.study-tracks.edit', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'إدارة المسارات', 'url' => route('admin.study-tracks.index')],
                ['title' => 'تعديل المسار'],
            ],
            'studyTrack' => $studyTrack,
        ]);
    }

    public function update(UpdateStudyTrackRequest $request, StudyTrack $studyTrack, UpdateStudyTrackAction $updateStudyTrackAction): RedirectResponse
    {
        $payload = $request->validated();
        $payload['studyTrack'] = $studyTrack;

        $updateStudyTrackAction->handle($payload);

        return redirect()
            ->route('admin.study-tracks.index')
            ->with('success', 'تم تحديث المسار بنجاح.');
    }

    public function destroy(StudyTrack $studyTrack, DeleteStudyTrackAction $deleteStudyTrackAction): RedirectResponse
    {
        $deleteStudyTrackAction->handle(['studyTrack' => $studyTrack]);

        return redirect()
            ->route('admin.study-tracks.index')
            ->with('success', 'تم حذف المسار بنجاح.');
    }
}

