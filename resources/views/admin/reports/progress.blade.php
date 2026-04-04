@extends('admin.layouts.master')

@section('title', 'تقرير المتابعة التعليمية')

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'تقرير المتابعة التعليمية',
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
                                <a href="{{ route('admin.reports.progress') }}" class="btn btn-secondary">
                                    <i class="ti ti-refresh me-1"></i> إعادة تعيين
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-semibold"><i class="ti ti-progress me-1"></i> سجل المتابعة</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>الطالب</th>
                                    <th>المعلم</th>
                                    <th>الحلقة</th>
                                    <th>التاريخ</th>
                                    <th>مستوى الإتقان</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($report['records'] as $log)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $log->student?->full_name }}</td>
                                        <td>{{ $log->teacher?->name }}</td>
                                        <td>{{ $log->group?->name }}</td>
                                        <td>{{ optional($log->progress_date)->format('Y-m-d') }}</td>
                                        <td>{{ $log->mastery_level }}%</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">لا توجد بيانات</td>
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

