@extends('admin.layouts.master')

@section('title', 'تقرير الحضور والغياب')

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'تقرير الحضور والغياب',
                    'breadcrumbs' => $breadcrumbs,
                ])

                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <p class="text-muted small">حاضر</p>
                                <h4 class="fw-bold text-success">{{ $report['stats']['present'] }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <p class="text-muted small">غائب</p>
                                <h4 class="fw-bold text-danger">{{ $report['stats']['absent'] }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <p class="text-muted small">متأخر</p>
                                <h4 class="fw-bold text-warning">{{ $report['stats']['late'] }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <p class="text-muted small">بعذر</p>
                                <h4 class="fw-bold text-info">{{ $report['stats']['excused'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-semibold"><i class="ti ti-clock-check me-1"></i> سجل الحضور</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>المعلم</th>
                                    <th>التاريخ</th>
                                    <th>الحالة</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($report['records'] as $record)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $record->teacher?->name }}</td>
                                        <td>{{ optional($record->attendance_date)->format('Y-m-d') }}</td>
                                        <td>
                                            @php
                                                $badgeColor = match($record->status) {
                                                    'حاضر' => 'success',
                                                    'غائب' => 'danger',
                                                    'متأخر' => 'warning',
                                                    'بعذر' => 'info',
                                                    default => 'secondary',
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $badgeColor }}">{{ $record->status }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">لا توجد بيانات</td>
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

