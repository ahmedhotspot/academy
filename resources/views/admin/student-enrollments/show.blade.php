@extends('admin.layouts.master')

@section('title', 'تسجيل الطلاب في الحلقات - سجل الطالب')

@section('content')
    @php
        $stats = $profile['stats'] ?? [];
        $financial = $profile['financial'] ?? [];
        $studentProfile = $profile['student'] ?? [];
    @endphp

    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">
                @include('admin.partials.page-header', [
                    'title' => 'الملف الشامل لتسجيل الطالب في الحلقات',
                    'breadcrumbs' => $breadcrumbs,
                    'actions' => [
                        [
                            'title' => 'تعديل بيانات الطالب',
                            'url' => route('admin.students.edit', $student),
                            'icon' => 'ti ti-pencil',
                            'class' => 'btn-primary',
                        ],
                    ],
                ])

                @include('admin.partials.alerts')

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div>
                                <p class="text-muted mb-1 small">سجل الطالب الأكاديمي داخل الحلقات</p>
                                <h4 class="fw-bold mb-1">{{ $studentProfile['name'] ?? $student->full_name }}</h4>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="badge {{ $studentProfile['status_badge'] ?? 'bg-secondary' }}">{{ $studentProfile['status'] ?? '-' }}</span>
                                    <span class="badge bg-light text-dark border">الفرع: {{ $studentProfile['branch'] ?? '-' }}</span>
                                    <span class="badge bg-light text-dark border">الهاتف: {{ $studentProfile['phone'] ?? '-' }}</span>
                                    <span class="badge bg-light text-dark border">ولي الأمر: {{ $studentProfile['guardian'] ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="d-flex gap-2 flex-wrap">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">التسجيلات: {{ $stats['enrollments_count'] ?? 0 }}</span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">النشطة: {{ $stats['active_enrollments_count'] ?? 0 }}</span>
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2">الاشتراكات: {{ $stats['subscriptions_count'] ?? 0 }}</span>
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2">الدفعات: {{ $stats['payments_count'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-xl-4 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">إجمالي المستحق</p>
                                <h4 class="fw-bold text-primary mb-0">{{ $financial['total_final'] ?? '0.00 ج' }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">إجمالي المدفوع</p>
                                <h4 class="fw-bold text-success mb-0">{{ $financial['total_paid'] ?? '0.00 ج' }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">إجمالي المتبقي</p>
                                <h4 class="fw-bold text-warning mb-0">{{ $financial['total_remaining'] ?? '0.00 ج' }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-semibold"><i class="ti ti-book-2 me-1"></i> سجل التسجيلات داخل الحلقات</h6>
                        <span class="badge bg-info rounded-pill">{{ count($profile['enrollments'] ?? []) }}</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>الحلقة</th>
                                <th>المعلم</th>
                                <th>المستوى/المسار</th>
                                <th>حالة الحلقة</th>
                                <th>حالة التسجيل</th>
                                <th>تاريخ التسجيل</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($profile['enrollments'] ?? [] as $enrollment)
                                <tr>
                                    <td class="fw-semibold">{{ $enrollment['group'] }}</td>
                                    <td>{{ $enrollment['teacher'] }}</td>
                                    <td>{{ $enrollment['level'] }} / {{ $enrollment['track'] }}</td>
                                    <td><span class="badge {{ $enrollment['group_badge'] }}">{{ $enrollment['group_status'] }}</span></td>
                                    <td><span class="badge {{ $enrollment['enrollment_badge'] }}">{{ $enrollment['enrollment_status'] }}</span></td>
                                    <td>{{ $enrollment['registered_at'] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-3">لا توجد تسجيلات للطالب</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-xl-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-receipt-2 me-1"></i> الاشتراكات المالية</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>الخطة</th>
                                        <th>النهائي</th>
                                        <th>المدفوع</th>
                                        <th>المتبقي</th>
                                        <th>الحالة</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($profile['subscriptions'] ?? [] as $subscription)
                                        <tr>
                                            <td class="fw-semibold">{{ $subscription['plan'] }}</td>
                                            <td>{{ $subscription['final'] }}</td>
                                            <td>{{ $subscription['paid'] }}</td>
                                            <td class="text-danger fw-semibold">{{ $subscription['remaining'] }}</td>
                                            <td><span class="badge {{ $subscription['status_badge'] }}">{{ $subscription['status'] }}</span></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-3">لا توجد اشتراكات</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-receipt me-1"></i> آخر الدفعات</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>الإيصال</th>
                                        <th>التاريخ</th>
                                        <th>المبلغ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($profile['payments'] ?? [] as $payment)
                                        <tr>
                                            <td class="fw-semibold">{{ $payment['receipt'] }}</td>
                                            <td>{{ $payment['date'] }}</td>
                                            <td class="text-success fw-semibold">{{ $payment['amount'] }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center text-muted py-3">لا توجد دفعات</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-xl-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-progress me-1"></i> أحدث المتابعات التعليمية</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>الحلقة</th>
                                        <th>الحفظ</th>
                                        <th>المراجعة</th>
                                        <th>الالتزام</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($profile['progress_logs'] ?? [] as $log)
                                        <tr>
                                            <td>{{ $log['date'] }}</td>
                                            <td>{{ $log['group'] }}</td>
                                            <td>{{ $log['memorization'] }}</td>
                                            <td>{{ $log['revision'] }}</td>
                                            <td>
                                                <span class="badge {{ $log['commitment'] === 'ملتزم' ? 'bg-success' : 'bg-warning text-dark' }}">
                                                    {{ $log['commitment'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-3">لا توجد متابعات تعليمية</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-test-pipe me-1"></i> أحدث الاختبارات</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>النوع</th>
                                        <th>الحلقة</th>
                                        <th>المتوسط</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($profile['assessments'] ?? [] as $assessment)
                                        <tr>
                                            <td>{{ $assessment['date'] }}</td>
                                            <td>{{ $assessment['type'] }}</td>
                                            <td>{{ $assessment['group'] }}</td>
                                            <td>
                                                @if($assessment['average'] !== null)
                                                    <span class="badge {{ $assessment['average_badge'] }}">{{ $assessment['average'] }}%</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted py-3">لا توجد اختبارات</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <a href="{{ route('admin.student-enrollments.index') }}" class="btn btn-light">
                            <i class="ti ti-arrow-right me-1"></i> العودة إلى تسجيلات الطلاب
                        </a>

                        <div class="d-flex gap-2">
                            @can('student-enrollments.create')
                                <a href="{{ route('admin.student-enrollments.create') }}" class="btn btn-primary">
                                    <i class="ti ti-plus me-1"></i> تسجيل جديد
                                </a>
                            @endcan
                            @can('students.update')
                                <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-outline-primary">
                                    <i class="ti ti-pencil me-1"></i> تعديل الطالب
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

