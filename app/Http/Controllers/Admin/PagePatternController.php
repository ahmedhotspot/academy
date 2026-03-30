<?php

namespace App\Http\Controllers\Admin;

use App\Services\Admin\PagePatternService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PagePatternController extends AdminController
{
    protected string $title = 'قوالب الصفحات';

    public function __construct(private readonly PagePatternService $pagePatternService)
    {
    }

    public function index(): View
    {
        return $this->adminView('admin.page-patterns.index', [
            'breadcrumbs' => $this->pagePatternService->indexBreadcrumbs(),
            'actions' => [
                [
                    'title' => 'فتح صفحة الإنشاء',
                    'url' => route('admin.page-patterns.create'),
                    'icon' => 'ti ti-plus',
                    'class' => 'btn-primary',
                ],
            ],
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        return response()->json($this->pagePatternService->datatablePayload($request));
    }

    public function create(): View
    {
        return $this->adminView('admin.page-patterns.create', [
            'breadcrumbs' => $this->pagePatternService->createBreadcrumbs(),
        ]);
    }

    public function show(int $id): View
    {
        return $this->adminView('admin.page-patterns.show', [
            'breadcrumbs' => $this->pagePatternService->showBreadcrumbs($id),
            'record' => $this->pagePatternService->buildPreviewRecord($id),
        ]);
    }

    public function edit(int $id): View
    {
        return $this->adminView('admin.page-patterns.edit', [
            'breadcrumbs' => $this->pagePatternService->editBreadcrumbs($id),
            'record' => $this->pagePatternService->buildPreviewRecord($id),
        ]);
    }
}

