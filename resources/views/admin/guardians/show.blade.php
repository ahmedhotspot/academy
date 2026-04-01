@extends('admin.layouts.master')

@section('title', 'إدارة أولياء الأمور - عرض')

@section('content')
    @php
        $stats = $profile['stats'] ?? [];
        $financial = $profile['financial'] ?? [];
    @endphp

    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">
                @include('admin.partials.page-header', [
                    'title' => 'الملف الشامل لولي الأمر',
                    'breadcrumbs' => $breadcrumbs,
                    'actions' => [
                        [
                            'title' => 'تعديل',
                            'url' => route('admin.guardians.edit', $guardian),
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
                                <p class="text-muted mb-1 small">بيانات ولي الأمر</p>
                                <h4 class="fw-bold mb-1">{{ $guardian->full_name }}</h4>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="badge {{ $guardian->status_badge_class }}">{{ $guardian->status_label }}</span>
                                    <span class="badge bg-light text-dark border">الهاتف: {{ $guardian->phone }}</span>
                                    <span class="badge bg-light text-dark border">واتساب: {{ $guardian->whatsapp ?: '-' }}</span>
                                </div>
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">الطلاب: {{ $stats['students_count'] ?? 0 }}</span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">النشطون: {{ $stats['active_students_count'] ?? 0 }}</span>
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2">الفروع: {{ $stats['branches_count'] ?? 0 }}</span>
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2">متأخرات: {{ $stats['overdue_subscriptions_count'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-xl-4 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">إجمالي المدفوع</p>
                                <h4 class="fw-bold text-success mb-0">{{ $financial['total_paid'] ?? '0.00 ر.س' }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">إجمالي المتبقي</p>
                                <h4 class="fw-bold text-warning mb-0">{{ $financial['total_remaining'] ?? '0.00 ر.س' }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">تحصيل هذا الشهر</p>
                                <h4 class="fw-bold text-primary mb-0">{{ $financial['month_paid'] ?? '0.00 ر.س' }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-xl-8">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-user-check me-1"></i> الطلاب المرتبطون</h6>
                                <span class="badge bg-info">{{ count($profile['students'] ?? []) }}</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>الطالب</th>
                                        <th>العمر</th>
                                        <th>الهاتف</th>
                                        <th>الفرع</th>
                                        <th>الحالة</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($profile['students'] ?? [] as $student)
                                        <tr>
                                            <td class="fw-semibold">{{ $student['name'] }}</td>
                                            <td>{{ $student['age'] }}</td>
                                            <td>{{ $student['phone'] }}</td>
                                            <td>{{ $student['branch'] }}</td>
                                            <td><span class="badge {{ $student['status_badge'] }}">{{ $student['status'] }}</span></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-3">لا يوجد طلاب مرتبطون</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-building me-1"></i> توزيع الطلاب حسب الفروع</h6>
                            </div>
                            <div class="card-body">
                                @forelse($profile['branch_summary'] ?? [] as $row)
                                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                        <span class="text-muted">{{ $row['branch'] }}</span>
                                        <span class="fw-bold text-primary">{{ $row['students_count'] }}</span>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-3">لا توجد بيانات</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-xl-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-receipt-2 me-1"></i> أحدث الاشتراكات</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>الطالب</th>
                                        <th>النهائي</th>
                                        <th>المتبقي</th>
                                        <th>الحالة</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($profile['subscriptions'] ?? [] as $subscription)
                                        <tr>
                                            <td class="fw-semibold">{{ $subscription['student'] }}</td>
                                            <td>{{ $subscription['final'] }}</td>
                                            <td class="text-danger fw-semibold">{{ $subscription['remaining'] }}</td>
                                            <td><span class="badge {{ $subscription['status_badge'] }}">{{ $subscription['status'] }}</span></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted py-3">لا توجد اشتراكات</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-receipt me-1"></i> أحدث المدفوعات</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>الطالب</th>
                                        <th>التاريخ</th>
                                        <th>المبلغ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($profile['payments'] ?? [] as $payment)
                                        <tr>
                                            <td class="fw-semibold">{{ $payment['student'] }}</td>
                                            <td>{{ $payment['date'] }}</td>
                                            <td class="text-success fw-semibold">{{ $payment['amount'] }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center text-muted py-3">لا توجد مدفوعات</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-semibold"><i class="ti ti-activity me-1"></i> أحدث النشاط</h6>
                    </div>
                    <div class="card-body">
                        @forelse($profile['activity'] ?? [] as $activity)
                            <div class="d-flex align-items-start justify-content-between py-2 border-bottom">
                                <div>
                                    <span class="badge {{ $activity['badge'] }} mb-1">{{ $activity['title'] }}</span>
                                    <p class="mb-0 small">{{ $activity['description'] }}</p>
                                </div>
                                <small class="text-muted">{{ $activity['date'] }}</small>
                            </div>
                        @empty
                            <div class="text-center text-muted py-2">لا توجد أنشطة حديثة</div>
                        @endforelse
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <a href="{{ route('admin.guardians.index') }}" class="btn btn-light">
                            <i class="ti ti-arrow-right me-1"></i> العودة إلى قائمة أولياء الأمور
                        </a>

                        <div class="d-flex gap-2">
                            @can('guardians.update')
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#portalPasswordModal">
                                    <i class="ti ti-key me-1"></i> تعيين كلمة مرور البوابة
                                </button>
                                <a href="{{ route('admin.guardians.edit', $guardian) }}" class="btn btn-primary">
                                    <i class="ti ti-pencil me-1"></i> تعديل ولي الأمر
                                </a>
                            @endcan
                            @can('guardians.delete')
                                <form action="{{ route('admin.guardians.destroy', $guardian) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('هل تريد حذف ولي الأمر؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="ti ti-trash me-1"></i> حذف
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@can('guardians.update')
@section('js')
{{-- Modal كلمة مرور البوابة --}}
<div class="modal fade" id="portalPasswordModal" tabindex="-1" aria-labelledby="portalPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.guardians.set-portal-password', $guardian) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="portalPasswordModalLabel">
                        <i class="ti ti-key me-2 text-success"></i>تعيين كلمة مرور بوابة ولي الأمر
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        سيستخدم ولي الأمر <strong>{{ $guardian->full_name }}</strong> رقم هاتفه <code>{{ $guardian->phone }}</code> مع هذه الكلمة للدخول إلى
                        <a href="{{ route('guardian.login') }}" target="_blank">بوابة أولياء الأمور</a>.
                    </p>
                    <div class="mb-3">
                        <label class="form-label">كلمة المرور الجديدة <span class="text-danger">*</span></label>
                        <input type="password" name="portal_password" class="form-control" placeholder="6 أحرف على الأقل" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                        <input type="password" name="portal_password_confirmation" class="form-control" placeholder="أعد كتابة كلمة المرور" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success">
                        <i class="ti ti-check me-1"></i> حفظ كلمة المرور
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@endcan

