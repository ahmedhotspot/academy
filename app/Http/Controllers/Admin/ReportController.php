<?php

namespace App\Http\Controllers\Admin;

use App\Services\Admin\ReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends AdminController
{
    protected string $title = 'التقارير';

    public function __construct(
        private readonly ReportService $service
    ) {}

    public function students(Request $request): View
    {
        $report = $this->service->studentReport($request);

        return $this->adminView('admin.reports.students', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'التقارير', 'url' => route('admin.reports.index')],
                ['title' => 'تقرير الطلاب'],
            ],
            'report' => $report,
        ]);
    }

    public function attendance(Request $request): View
    {
        $report = $this->service->attendanceReport($request);

        return $this->adminView('admin.reports.attendance', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'التقارير', 'url' => route('admin.reports.index')],
                ['title' => 'تقرير الحضور والغياب'],
            ],
            'report' => $report,
        ]);
    }

    public function progress(Request $request): View
    {
        $report = $this->service->progressReport($request);

        return $this->adminView('admin.reports.progress', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'التقارير', 'url' => route('admin.reports.index')],
                ['title' => 'تقرير المتابعة التعليمية'],
            ],
            'report' => $report,
        ]);
    }

    public function assessments(Request $request): View
    {
        $report = $this->service->assessmentReport($request);

        return $this->adminView('admin.reports.assessments', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'التقارير', 'url' => route('admin.reports.index')],
                ['title' => 'تقرير الاختبارات'],
            ],
            'report' => $report,
        ]);
    }

    public function subscriptions(Request $request): View
    {
        $report = $this->service->subscriptionReport($request);

        return $this->adminView('admin.reports.subscriptions', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'التقارير', 'url' => route('admin.reports.index')],
                ['title' => 'تقرير الاشتراكات والمتأخرات'],
            ],
            'report' => $report,
        ]);
    }

    public function payrolls(Request $request): View
    {
        $report = $this->service->payrollReport($request);

        return $this->adminView('admin.reports.payrolls', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'التقارير', 'url' => route('admin.reports.index')],
                ['title' => 'تقرير مستحقات المعلمين'],
            ],
            'report' => $report,
        ]);
    }

    public function expenses(Request $request): View
    {
        $report = $this->service->expenseReport($request);

        return $this->adminView('admin.reports.expenses', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'التقارير', 'url' => route('admin.reports.index')],
                ['title' => 'تقرير المصروفات'],
            ],
            'report' => $report,
        ]);
    }

    public function index(): View
    {
        return $this->adminView('admin.reports.index', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'التقارير'],
            ],
        ]);
    }
}

