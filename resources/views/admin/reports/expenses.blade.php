@extends('admin.layouts.master')

@section('title', 'تقرير المصروفات')

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'تقرير مصروفات التشغيل',
                    'breadcrumbs' => $breadcrumbs,
                ])

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <p class="text-muted small">إجمالي المصروفات</p>
                                <h4 class="fw-bold">{{ $report['total'] }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <p class="text-muted small">الإجمالي</p>
                                <h4 class="fw-bold text-danger">{{ number_format($report['amount'], 2) }} ج</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-semibold"><i class="ti ti-coin me-1"></i> تفاصيل المصروفات</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>التاريخ</th>
                                    <th>البيان</th>
                                    <th>الفرع</th>
                                    <th>المبلغ</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($report['records'] as $expense)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $expense->formatted_date }}</td>
                                        <td>{{ $expense->title }}</td>
                                        <td>{{ $expense->branch?->name ?? 'عام' }}</td>
                                        <td class="fw-bold text-danger">{{ $expense->formatted_amount }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">لا توجد بيانات</td>
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

