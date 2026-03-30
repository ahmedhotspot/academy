<?php

namespace App\Services\Admin;

use App\Services\BaseService;
use Illuminate\Http\Request;

class PagePatternService extends BaseService
{
    public function indexBreadcrumbs(): array
    {
        return [
            ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
            ['title' => 'قوالب الصفحات'],
        ];
    }

    public function createBreadcrumbs(): array
    {
        return [
            ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
            ['title' => 'قوالب الصفحات', 'url' => route('admin.page-patterns.index')],
            ['title' => 'صفحة إنشاء'],
        ];
    }

    public function showBreadcrumbs(int $id): array
    {
        return [
            ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
            ['title' => 'قوالب الصفحات', 'url' => route('admin.page-patterns.index')],
            ['title' => 'صفحة عرض رقم ' . $id],
        ];
    }

    public function editBreadcrumbs(int $id): array
    {
        return [
            ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
            ['title' => 'قوالب الصفحات', 'url' => route('admin.page-patterns.index')],
            ['title' => 'صفحة تعديل رقم ' . $id],
        ];
    }

    public function datatablePayload(Request $request): array
    {
        return [
            'draw' => (int) $request->integer('draw', 1),
            'recordsTotal' => 2,
            'recordsFiltered' => 2,
            'data' => [
                [
                    'id' => 1,
                    'title' => 'نموذج طالب',
                    'type' => 'فردي',
                    'status' => 'نشط',
                    'created_at' => '2026-03-01',
                ],
                [
                    'id' => 2,
                    'title' => 'نموذج حلقة',
                    'type' => 'مجموعات',
                    'status' => 'نشط',
                    'created_at' => '2026-03-10',
                ],
            ],
        ];
    }

    public function buildPreviewRecord(int $id): array
    {
        return [
            'id' => $id,
            'title' => 'نموذج صفحة رقم ' . $id,
            'description' => 'هذه بيانات تجريبية لتوحيد تصميم صفحات الإدارة قبل البدء في CRUD الفعلي.',
            'status' => 'نشط',
            'created_at' => '2026-03-01',
            'updated_at' => '2026-03-20',
        ];
    }
}

