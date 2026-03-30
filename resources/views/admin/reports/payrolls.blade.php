@extends('admin.layouts.master')

@section('title', 'تقرير مستحقات المعلمين')

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'تقرير مستحقات المعلمين',
                    'breadcrumbs' => $breadcrumbs,
                ])

                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <p class="text-muted small">إجمالي الحسابات</p>
                                <h4 class="fw-bold">{{ $report['total'] }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <p class="text-muted small">مصروفة</p>
                                <h4 class="fw-bold text-success">{{ $report['processed'] }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <p class="text-muted small">قيد الانتظار</p>
                                <h4 class="fw-bold text-warning">{{ $report['pending'] }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <p class="text-muted small">الإجمالي المستحق</p>
                                <h4 class="fw-bold">{{ number_format($report['totalSalaries'], 2) }} ر.س</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-semibold"><i class="ti ti-wallet me-1"></i> تفاصيل المستحقات</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>المعلم</th>
                                    <th>الفترة</th>
                                    <th>الراتب</th>
                                    <th>المكافأة</th>
                                    <th>الصافي</th>
                                    <th>الحالة</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($report['records'] as $payroll)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $payroll->teacher?->name }}</td>
                                        <td>{{ $payroll->month_year }}</td>
                                        <td>{{ $payroll->formatted_base_salary }}</td>
                                        <td class="text-success">+ {{ $payroll->formatted_bonus }}</td>
                                        <td class="fw-bold">{{ $payroll->formatted_final }}</td>
                                        <td><span class="badge {{ $payroll->status_badge_class }}">{{ $payroll->status }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">لا توجد بيانات</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

