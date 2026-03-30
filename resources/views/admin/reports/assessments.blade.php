@extends('admin.layouts.master')

@section('title', 'تقرير الاختبارات')

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'تقرير الاختبارات',
                    'breadcrumbs' => $breadcrumbs,
                ])

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <p class="text-muted small">إجمالي الاختبارات</p>
                                <h4 class="fw-bold">{{ $report['total'] }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <p class="text-muted small">المتوسط</p>
                                <h4 class="fw-bold">{{ $report['average'] }}%</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-semibold"><i class="ti ti-test-pipe me-1"></i> نتائج الاختبارات</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>الطالب</th>
                                    <th>النوع</th>
                                    <th>التاريخ</th>
                                    <th>النتيجة</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($report['records'] as $assessment)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $assessment->student?->full_name }}</td>
                                        <td><span class="badge bg-info">{{ $assessment->type }}</span></td>
                                        <td>{{ optional($assessment->assessment_date)->format('Y-m-d') }}</td>
                                        <td>
                                            @php
                                                $avg = ($assessment->memorization_result + $assessment->tajweed_result + $assessment->tadabbur_result) / 3;
                                                $color = $avg >= 90 ? 'success' : ($avg >= 70 ? 'warning' : 'danger');
                                            @endphp
                                            <span class="badge bg-{{ $color }}">{{ round($avg, 0) }}%</span>
                                        </td>
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

