<?php

namespace App\Http\Controllers\Admin;

use App\Services\Admin\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

    public function studentsDatatable(Request $request): JsonResponse
    {
        return response()->json($this->service->studentsDatatable($request));
    }

    public function studentsPdf(Request $request): Response
    {
        return $this->exportPdf(
            title: 'تقرير الطلاب',
            columns: ['الاسم', 'الفرع', 'العمر', 'الحالة', 'التاريخ'],
            rows: $this->service->studentsPdfRows($request),
            fileName: 'students-report.pdf',
        );
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

    public function attendanceDatatable(Request $request): JsonResponse
    {
        return response()->json($this->service->attendanceDatatable($request));
    }

    public function attendancePdf(Request $request): Response
    {
        return $this->exportPdf(
            title: 'تقرير الحضور والغياب',
            columns: ['المعلم', 'التاريخ', 'الحالة'],
            rows: $this->service->attendancePdfRows($request),
            fileName: 'attendance-report.pdf',
        );
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

    public function progressDatatable(Request $request): JsonResponse
    {
        return response()->json($this->service->progressDatatable($request));
    }

    public function progressPdf(Request $request): Response
    {
        return $this->exportPdf(
            title: 'تقرير المتابعة التعليمية',
            columns: ['الطالب', 'المعلم', 'الحلقة', 'التاريخ', 'مستوى الإتقان'],
            rows: $this->service->progressPdfRows($request),
            fileName: 'progress-report.pdf',
        );
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

    public function assessmentsDatatable(Request $request): JsonResponse
    {
        return response()->json($this->service->assessmentsDatatable($request));
    }

    public function assessmentsPdf(Request $request): Response
    {
        return $this->exportPdf(
            title: 'تقرير الاختبارات',
            columns: ['الطالب', 'النوع', 'التاريخ', 'النتيجة'],
            rows: $this->service->assessmentsPdfRows($request),
            fileName: 'assessments-report.pdf',
        );
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

    public function subscriptionsDatatable(Request $request): JsonResponse
    {
        return response()->json($this->service->subscriptionsDatatable($request));
    }

    public function subscriptionsPdf(Request $request): Response
    {
        return $this->exportPdf(
            title: 'تقرير الاشتراكات والمتأخرات',
            columns: ['الطالب', 'الخطة', 'الحالة', 'المتبقي', 'التاريخ'],
            rows: $this->service->subscriptionsPdfRows($request),
            fileName: 'subscriptions-report.pdf',
        );
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

    public function payrollsDatatable(Request $request): JsonResponse
    {
        return response()->json($this->service->payrollsDatatable($request));
    }

    public function payrollsPdf(Request $request): Response
    {
        return $this->exportPdf(
            title: 'تقرير مستحقات المعلمين',
            columns: ['المعلم', 'الفترة', 'الراتب', 'المكافأة', 'الصافي', 'الحالة', 'التاريخ'],
            rows: $this->service->payrollsPdfRows($request),
            fileName: 'payrolls-report.pdf',
        );
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

    public function expensesDatatable(Request $request): JsonResponse
    {
        return response()->json($this->service->expensesDatatable($request));
    }

    public function expensesPdf(Request $request): Response
    {
        return $this->exportPdf(
            title: 'تقرير المصروفات',
            columns: ['التاريخ', 'البيان', 'الفرع', 'المبلغ'],
            rows: $this->service->expensesPdfRows($request),
            fileName: 'expenses-report.pdf',
        );
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

    private function exportPdf(string $title, array $columns, array $rows, string $fileName): Response
    {
        $pdf = Pdf::loadView('admin.reports.pdf', [
            'title' => $title,
            'columns' => $columns,
            'rows' => $rows,
            'generatedAt' => now()->format('Y-m-d H:i'),
        ])->setPaper('a4', 'landscape');

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}

