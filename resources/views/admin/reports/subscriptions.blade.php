@extends('admin.layouts.master')

@section('title', 'تقرير الاشتراكات والمتأخرات')

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'تقرير الاشتراكات والمتأخرات',
                    'breadcrumbs' => $breadcrumbs,
                ])

                {{-- فلاتر البحث --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">تاريخ البداية</label>
                                <input type="date" name="start_date" class="form-control"
                                       value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">تاريخ النهاية</label>
                                <input type="date" name="end_date" class="form-control"
                                       value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-4 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-search me-1"></i> بحث
                                </button>
                                <a href="{{ route('admin.reports.subscriptions') }}" class="btn btn-secondary">
                                    <i class="ti ti-refresh me-1"></i> إعادة تعيين
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <p class="text-muted small">إجمالي الاشتراكات</p>
                                <h4 class="fw-bold">{{ $report['total'] }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <p class="text-muted small">المتأخرة</p>
                                <h4 class="fw-bold text-danger">{{ $report['overdue'] }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <p class="text-muted small">المكتملة</p>
                                <h4 class="fw-bold text-success">{{ $report['complete'] }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <p class="text-muted small">المتبقي</p>
                                <h4 class="fw-bold">{{ number_format($report['totalRemaining'], 2) }} ج</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-semibold"><i class="ti ti-alert-triangle me-1 text-danger"></i> الاشتراكات المتأخرة</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>الطالب</th>
                                    <th>الخطة</th>
                                    <th>الحالة</th>
                                    <th>المتبقي</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($report['overdueList'] as $sub)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="fw-semibold">{{ $sub->student?->full_name }}</td>
                                        <td>{{ $sub->feePlan?->name }}</td>
                                        <td><span class="badge bg-warning">{{ $sub->status }}</span></td>
                                        <td class="fw-bold text-danger">{{ $sub->formatted_remaining_amount }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">لا توجد متأخرات</td>
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

