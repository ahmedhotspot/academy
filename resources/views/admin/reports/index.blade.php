@extends('admin.layouts.master')

@section('title', 'التقارير')

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'التقارير والإحصائيات',
                    'breadcrumbs' => $breadcrumbs,
                ])

                <div class="row g-3">

                    <div class="col-md-6 col-lg-4">
                        <a href="{{ route('admin.reports.students') }}" class="card border-0 shadow-sm text-decoration-none h-100 report-card">
                            <div class="card-body text-center">
                                <i class="ti ti-users fs-1 text-primary mb-3 d-block"></i>
                                <h6 class="fw-semibold">تقرير الطلاب</h6>
                                <p class="mb-0 text-muted small">قائمة الطلاب والإحصائيات</p>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <a href="{{ route('admin.reports.attendance') }}" class="card border-0 shadow-sm text-decoration-none h-100 report-card">
                            <div class="card-body text-center">
                                <i class="ti ti-clock-check fs-1 text-success mb-3 d-block"></i>
                                <h6 class="fw-semibold">الحضور والغياب</h6>
                                <p class="mb-0 text-muted small">تقرير حضور الطلاب</p>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <a href="{{ route('admin.reports.progress') }}" class="card border-0 shadow-sm text-decoration-none h-100 report-card">
                            <div class="card-body text-center">
                                <i class="ti ti-progress fs-1 text-info mb-3 d-block"></i>
                                <h6 class="fw-semibold">المتابعة التعليمية</h6>
                                <p class="mb-0 text-muted small">سجل متابعة الطلاب</p>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <a href="{{ route('admin.reports.assessments') }}" class="card border-0 shadow-sm text-decoration-none h-100 report-card">
                            <div class="card-body text-center">
                                <i class="ti ti-test-pipe fs-1 text-warning mb-3 d-block"></i>
                                <h6 class="fw-semibold">الاختبارات</h6>
                                <p class="mb-0 text-muted small">نتائج الاختبارات والمتوسطات</p>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <a href="{{ route('admin.reports.subscriptions') }}" class="card border-0 shadow-sm text-decoration-none h-100 report-card">
                            <div class="card-body text-center">
                                <i class="ti ti-receipt fs-1 text-danger mb-3 d-block"></i>
                                <h6 class="fw-semibold">الاشتراكات والمتأخرات</h6>
                                <p class="mb-0 text-muted small">الرسوم والمتبقيات</p>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <a href="{{ route('admin.reports.payrolls') }}" class="card border-0 shadow-sm text-decoration-none h-100 report-card">
                            <div class="card-body text-center">
                                <i class="ti ti-wallet fs-1 text-primary mb-3 d-block"></i>
                                <h6 class="fw-semibold">مستحقات المعلمين</h6>
                                <p class="mb-0 text-muted small">الرواتب والمكافآت</p>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <a href="{{ route('admin.reports.expenses') }}" class="card border-0 shadow-sm text-decoration-none h-100 report-card">
                            <div class="card-body text-center">
                                <i class="ti ti-coin fs-1 text-danger mb-3 d-block"></i>
                                <h6 class="fw-semibold">المصروفات</h6>
                                <p class="mb-0 text-muted small">مصروفات التشغيل</p>
                            </div>
                        </a>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <style>
        .report-card {
            transition: all 0.3s ease;
        }
        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
    </style>
@endsection

