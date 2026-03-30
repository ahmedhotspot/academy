@extends('admin.layouts.master')

@section('title', 'إدارة حضور وغياب المعلمين - سجل المعلم')

@section('content')
    @php
        $teacherProfile = $profile['teacher'] ?? [];
        $month = $profile['current_month'] ?? [];
        $all = $profile['all_time'] ?? [];
    @endphp

    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">
                @include('admin.partials.page-header', [
                    'title' => 'الملف الشامل لحضور المعلم',
                    'breadcrumbs' => $breadcrumbs,
                    'actions' => [
                        [
                            'title' => 'تسجيل حضور جديد',
                            'url' => route('admin.teacher-attendances.create'),
                            'icon' => 'ti ti-plus',
                            'class' => 'btn-primary',
                        ],
                    ],
                ])

                @include('admin.partials.alerts')

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div>
                                <p class="text-muted mb-1 small">بيانات المعلم</p>
                                <h4 class="fw-bold mb-1">{{ $teacherProfile['name'] ?? $teacher->name }}</h4>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="badge bg-light text-dark border">الهاتف: {{ $teacherProfile['phone'] ?? '-' }}</span>
                                    <span class="badge bg-light text-dark border">الفرع: {{ $teacherProfile['branch'] ?? '-' }}</span>
                                    <span class="badge bg-light text-dark border">الحالة: {{ $teacherProfile['status'] ?? '-' }}</span>
                                    <span class="badge {{ ($profile['is_present_today'] ?? false) ? 'bg-success' : 'bg-secondary' }}">
                                        {{ ($profile['is_present_today'] ?? false) ? 'حاضر اليوم' : 'لم يسجل حضور اليوم' }}
                                    </span>
                                </div>
                            </div>

                            <div class="text-end">
                                <p class="text-muted small mb-1">آخر دخول للنظام</p>
                                <p class="fw-semibold mb-0">{{ $teacherProfile['last_login'] ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">حضور هذا الشهر</p>
                                <h4 class="fw-bold text-success mb-0">{{ $month['present'] ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">غياب هذا الشهر</p>
                                <h4 class="fw-bold text-danger mb-0">{{ $month['absent'] ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">تأخر هذا الشهر</p>
                                <h4 class="fw-bold text-warning mb-0">{{ $month['late'] ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">إجمالي السجلات</p>
                                <h4 class="fw-bold text-primary mb-0">{{ $all['total'] ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-xl-8">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-calendar-check me-1"></i> أحدث سجلات الحضور</h6>
                                <span class="badge bg-info rounded-pill">{{ count($profile['recent_attendances'] ?? []) }}</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>الحالة</th>
                                        <th>الملاحظات</th>
                                        <th>العمليات</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($profile['recent_attendances'] ?? [] as $attendance)
                                        <tr>
                                            <td>{{ $attendance['date'] }}</td>
                                            <td><span class="badge {{ $attendance['status_badge'] }}">{{ $attendance['status'] }}</span></td>
                                            <td>{{ $attendance['notes'] }}</td>
                                            <td>
                                                @can('teacher-attendances.update')
                                                    <a href="{{ route('admin.teacher-attendances.edit', $attendance['id']) }}" class="btn btn-sm btn-outline-primary">تعديل</a>
                                                @endcan
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted py-3">لا يوجد سجل حضور لهذا المعلم</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4">
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-book-2 me-1"></i> الحلقات التي يدرّسها</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>الحلقة</th>
                                        <th>الطلاب</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($profile['teaching_groups'] ?? [] as $group)
                                        <tr>
                                            <td class="fw-semibold">{{ $group['name'] }}</td>
                                            <td>{{ $group['students_count'] }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="2" class="text-center text-muted py-3">لا توجد حلقات مرتبطة</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-wallet me-1"></i> آخر مستحق مالي</h6>
                            </div>
                            <div class="card-body">
                                @if(!empty($profile['latest_payroll']))
                                    <p class="mb-2"><strong>الفترة:</strong> {{ $profile['latest_payroll']['month_year'] }}</p>
                                    <p class="mb-2"><strong>الصافي:</strong> {{ $profile['latest_payroll']['final_amount'] }}</p>
                                    <p class="mb-0">
                                        <strong>الحالة:</strong>
                                        <span class="badge {{ $profile['latest_payroll']['status_badge'] }}">{{ $profile['latest_payroll']['status'] }}</span>
                                    </p>
                                @else
                                    <div class="text-center text-muted py-3">لا توجد مستحقات مسجلة بعد</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <a href="{{ route('admin.teacher-attendances.index') }}" class="btn btn-light">
                            <i class="ti ti-arrow-right me-1"></i> العودة إلى سجل الحضور
                        </a>

                        <div class="d-flex gap-2">
                            @can('teacher-attendances.create')
                                <a href="{{ route('admin.teacher-attendances.create') }}" class="btn btn-primary">
                                    <i class="ti ti-plus me-1"></i> إضافة سجل جديد
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

