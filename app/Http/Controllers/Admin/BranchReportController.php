<?php

namespace App\Http\Controllers\Admin;

use App\Services\BranchReportService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

/**
 * Class BranchReportController
 *
 * التقارير المنفصلة لكل فرع
 */
class BranchReportController extends AdminController
{
    protected string $title = 'تقارير الفرع';

    /**
     * عرض لوحة التقارير الرئيسية
     */
    public function index(): View
    {
        $branchId = auth()->user()->isSuperAdmin() ? null : auth()->user()->branch_id;
        $service = new BranchReportService($branchId);

        return $this->adminView('admin.reports.branch-reports', [
            'summary' => $service->getSummary(),
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'تقارير الفرع'],
            ],
        ]);
    }

    /**
     * تقرير الطلاب
     */
    public function studentsReport(): View
    {
        $branchId = auth()->user()->isSuperAdmin() ? null : auth()->user()->branch_id;
        $service = new BranchReportService($branchId);

        return $this->adminView('admin.reports.students-report', [
            'students' => $service->getStudentsReport(),
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'التقارير', 'url' => route('admin.reports.index')],
                ['title' => 'تقرير الطلاب'],
            ],
        ]);
    }

    /**
     * تقرير المعلمين
     */
    public function teachersReport(): View
    {
        $branchId = auth()->user()->isSuperAdmin() ? null : auth()->user()->branch_id;
        $service = new BranchReportService($branchId);

        return $this->adminView('admin.reports.teachers-report', [
            'teachers' => $service->getTeachersReport(),
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'التقارير', 'url' => route('admin.reports.index')],
                ['title' => 'تقرير المعلمين'],
            ],
        ]);
    }

    /**
     * تقرير الحلقات
     */
    public function groupsReport(): View
    {
        $branchId = auth()->user()->isSuperAdmin() ? null : auth()->user()->branch_id;
        $service = new BranchReportService($branchId);

        return $this->adminView('admin.reports.groups-report', [
            'groups' => $service->getGroupsReport(),
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'التقارير', 'url' => route('admin.reports.index')],
                ['title' => 'تقرير الحلقات'],
            ],
        ]);
    }

    /**
     * التقرير المالي
     */
    public function financialReport(Request $request): View
    {
        $startDate = $request->input('start_date') ? \Carbon\Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? \Carbon\Carbon::parse($request->input('end_date')) : null;

        $branchId = auth()->user()->isSuperAdmin() ? null : auth()->user()->branch_id;
        $service = new BranchReportService($branchId);

        return $this->adminView('admin.reports.financial-report', [
            'financial' => $service->getFinancialReport($startDate, $endDate),
            'start_date' => $startDate?->toDateString(),
            'end_date' => $endDate?->toDateString(),
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'التقارير', 'url' => route('admin.reports.index')],
                ['title' => 'التقرير المالي'],
            ],
        ]);
    }

    /**
     * تقرير حضور المعلمين
     */
    public function attendanceReport(): View
    {
        $branchId = auth()->user()->isSuperAdmin() ? null : auth()->user()->branch_id;
        $service = new BranchReportService($branchId);

        return $this->adminView('admin.reports.attendance-report', [
            'attendance' => $service->getTeacherAttendanceReport(),
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'التقارير', 'url' => route('admin.reports.index')],
                ['title' => 'تقرير الحضور'],
            ],
        ]);
    }

    /**
     * تحميل التقرير كـ JSON (للـ AJAX)
     */
    public function exportJson(Request $request): JsonResponse
    {
        $reportType = $request->input('type');
        $branchId = auth()->user()->isSuperAdmin() ? null : auth()->user()->branch_id;
        $service = new BranchReportService($branchId);

        $data = match ($reportType) {
            'students' => $service->getStudentsReport(),
            'teachers' => $service->getTeachersReport(),
            'groups' => $service->getGroupsReport(),
            'financial' => $service->getFinancialReport(),
            'attendance' => $service->getTeacherAttendanceReport(),
            default => $service->getSummary(),
        };

        return response()->json([
            'success' => true,
            'data' => $data,
            'branch' => $service->getBranch()?->name ?? 'جميع الفروع',
        ]);
    }
}

