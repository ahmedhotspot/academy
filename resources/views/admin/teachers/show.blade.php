@extends('admin.layouts.master')

@section('title', 'إدارة المعلمين - عرض')

@section('content')
    @php
        $info = $profile['info'] ?? [];
        $stats = $profile['stats'] ?? [];
        $attendance = $profile['attendance'] ?? [];
        $payroll = $profile['payroll'] ?? [];
    @endphp

    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">
                @include('admin.partials.page-header', [
                    'title' => 'الملف الشامل للمعلم',
                    'breadcrumbs' => $breadcrumbs,
                    'actions' => [
                        [
                            'title' => 'تعديل',
                            'url' => route('admin.teachers.edit', $teacher),
                            'icon' => 'ti ti-edit',
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
                                <h4 class="fw-bold mb-1">{{ $info['name'] ?? $teacher->name }}</h4>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="badge {{ ($info['status_value'] ?? 'inactive') === 'active' ? 'bg-success' : (($info['status_value'] ?? '') === 'suspended' ? 'bg-danger' : 'bg-secondary') }}">{{ $info['status'] ?? '-' }}</span>
                                    <span class="badge bg-light text-dark border">الفرع: {{ $info['branch'] ?? '-' }}</span>
                                    <span class="badge bg-light text-dark border">الهاتف: {{ $info['phone'] ?? '-' }}</span>
                                    <span class="badge bg-light text-dark border">آخر دخول: {{ $info['last_login'] ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="d-flex gap-2 flex-wrap">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">الحلقات: {{ $stats['teaching_groups_count'] ?? 0 }}</span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">سجلات الحضور: {{ $stats['attendance_total'] ?? 0 }}</span>
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2">المستحقات: {{ $stats['payroll_count'] ?? 0 }}</span>
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2">إشعارات غير مقروءة: {{ $stats['unread_notifications_count'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-xl-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-id me-1"></i> المعلومات الأساسية</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <p class="text-muted small mb-1">رقم الواتساب</p>
                                        <p class="fw-semibold mb-0">{{ $info['whatsapp'] ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="text-muted small mb-1">اسم المستخدم</p>
                                        <p class="fw-semibold mb-0">{{ $info['username'] ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="text-muted small mb-1">تاريخ الإنشاء</p>
                                        <p class="fw-semibold mb-0">{{ $info['created_at'] ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="text-muted small mb-1">آخر تحديث</p>
                                        <p class="fw-semibold mb-0">{{ $info['updated_at'] ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-calendar-check me-1"></i> ملخص حضور المعلم</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-6"><div class="p-3 rounded bg-success-subtle text-center"><p class="small text-muted mb-1">حاضر</p><h5 class="mb-0 text-success fw-bold">{{ $attendance['present'] ?? 0 }}</h5></div></div>
                                    <div class="col-6"><div class="p-3 rounded bg-danger-subtle text-center"><p class="small text-muted mb-1">غائب</p><h5 class="mb-0 text-danger fw-bold">{{ $attendance['absent'] ?? 0 }}</h5></div></div>
                                    <div class="col-6"><div class="p-3 rounded bg-warning-subtle text-center"><p class="small text-muted mb-1">متأخر</p><h5 class="mb-0 text-warning fw-bold">{{ $attendance['late'] ?? 0 }}</h5></div></div>
                                    <div class="col-6"><div class="p-3 rounded bg-info-subtle text-center"><p class="small text-muted mb-1">بعذر</p><h5 class="mb-0 text-info fw-bold">{{ $attendance['excused'] ?? 0 }}</h5></div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-xl-7">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-book-2 me-1"></i> الحلقات المرتبطة بالتدريس</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>الحلقة</th>
                                        <th>الفرع</th>
                                        <th>المستوى/المسار</th>
                                        <th>الطلاب</th>
                                        <th>الحالة</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($profile['teaching_groups'] ?? [] as $group)
                                        <tr>
                                            <td class="fw-semibold">{{ $group['name'] }}</td>
                                            <td>{{ $group['branch'] }}</td>
                                            <td>{{ $group['level'] }} / {{ $group['track'] }}</td>
                                            <td>{{ $group['students_count'] }}</td>
                                            <td><span class="badge {{ $group['status_badge'] }}">{{ $group['status'] }}</span></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-3">لا توجد حلقات مرتبطة</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-5">
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-wallet me-1"></i> آخر مستحق مالي</h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-2"><strong>عدد السجلات:</strong> {{ $payroll['count'] ?? 0 }}</p>
                                <p class="mb-2"><strong>آخر فترة:</strong> {{ $payroll['last_month'] ?? '-' }}</p>
                                <p class="mb-2"><strong>آخر صافي:</strong> {{ $payroll['last_final'] ?? '-' }}</p>
                                <p class="mb-0"><strong>الحالة:</strong> <span class="badge {{ $payroll['last_status_badge'] ?? 'bg-secondary' }}">{{ $payroll['last_status'] ?? '-' }}</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

