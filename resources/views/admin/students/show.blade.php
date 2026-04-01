@extends('admin.layouts.master')

@section('title', 'إدارة الطلاب - عرض')

@section('content')
    @php
        $stats = $profile['stats'] ?? [];
        $guardian = $profile['guardian'] ?? [];
        $financial = $profile['financial'] ?? [];
        $learning = $profile['learning'] ?? [];
    @endphp

    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">
                @include('admin.partials.page-header', [
                    'title' => 'الملف الشامل للطالب',
                    'breadcrumbs' => $breadcrumbs,
                    'actions' => [
                        [
                            'title' => 'تعديل',
                            'url' => route('admin.students.edit', $student),
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
                                <p class="text-muted mb-1 small">ملف الطالب</p>
                                <h4 class="fw-bold mb-1">{{ $student->full_name }}</h4>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="badge {{ $student->status_badge_class }}">{{ $student->status_label }}</span>
                                    <span class="badge bg-light text-dark border">الفرع: {{ $student->branch?->name ?? '-' }}</span>
                                    <span class="badge bg-light text-dark border">رقم الطالب: #{{ $student->id }}</span>
                                </div>
                            </div>

                            <div class="d-flex gap-2 flex-wrap">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">التسجيلات: {{ $stats['enrollments'] ?? 0 }}</span>
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2">الاشتراكات: {{ $stats['subscriptions'] ?? 0 }}</span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">الدفعات: {{ $stats['payments'] ?? 0 }}</span>
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2">الاختبارات: {{ $stats['assessments'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-xl-7">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-user me-1"></i> البيانات الأساسية</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <p class="text-muted small mb-1">العمر</p>
                                        <p class="fw-semibold mb-0">{{ $student->age }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="text-muted small mb-1">الجنسية</p>
                                        <p class="fw-semibold mb-0">{{ $student->nationality }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="text-muted small mb-1">الهاتف</p>
                                        <p class="fw-semibold mb-0">{{ $student->phone }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="text-muted small mb-1">واتساب</p>
                                        <p class="fw-semibold mb-0">{{ $student->whatsapp ?: '-' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="text-muted small mb-1">رقم الهوية/الجواز</p>
                                        <p class="fw-semibold mb-0">{{ $student->identity_number ?: '-' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="text-muted small mb-1">تاريخ التسجيل</p>
                                        <p class="fw-semibold mb-0">{{ optional($student->created_at)->format('Y-m-d') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-5">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-users me-1"></i> ولي الأمر والمؤشرات</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3 pb-3 border-bottom">
                                    <p class="text-muted small mb-1">اسم ولي الأمر</p>
                                    <p class="fw-semibold mb-2">{{ $guardian['name'] ?? '-' }}</p>
                                    <p class="text-muted small mb-1">هاتف ولي الأمر</p>
                                    <p class="fw-semibold mb-2">{{ $guardian['phone'] ?? '-' }}</p>
                                    <p class="text-muted small mb-1">واتساب ولي الأمر</p>
                                    <p class="fw-semibold mb-0">{{ $guardian['whatsapp'] ?? '-' }}</p>
                                </div>

                                <div class="d-flex justify-content-between align-items-center py-1">
                                    <span class="text-muted">إجمالي المدفوع</span>
                                    <span class="fw-bold text-success">{{ $financial['total_paid'] ?? '0.00 ر.س' }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-1">
                                    <span class="text-muted">إجمالي المتبقي</span>
                                    <span class="fw-bold text-warning">{{ $financial['total_remaining'] ?? '0.00 ر.س' }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-1">
                                    <span class="text-muted">متوسط الاختبارات</span>
                                    <span class="fw-bold text-primary">{{ $learning['assessment_avg'] !== null ? $learning['assessment_avg'] . '%' : '-' }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-1">
                                    <span class="text-muted">آخر متابعة</span>
                                    <span class="fw-semibold">{{ $learning['last_progress_date'] ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-xl-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-book-2 me-1"></i> التسجيلات في الحلقات</h6>
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
                                        <th>التاريخ</th>
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
                                            <td>{{ $enrollment['date'] }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted py-3">لا توجد تسجيلات في الحلقات</td></tr>
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
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-receipt-2 me-1"></i> الاشتراكات</h6>
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
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-progress me-1"></i> آخر المتابعات التعليمية</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>الحفظ</th>
                                        <th>المراجعة</th>
                                        <th>الالتزام</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($profile['progress_logs'] ?? [] as $log)
                                        <tr>
                                            <td>{{ $log['date'] }}</td>
                                            <td>{{ $log['memorization'] }}</td>
                                            <td>{{ $log['revision'] }}</td>
                                            <td>
                                                <span class="badge {{ $log['commitment'] === 'ملتزم' ? 'bg-success' : 'bg-warning text-dark' }}">
                                                    {{ $log['commitment'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted py-3">لا توجد متابعات</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-test-pipe me-1"></i> آخر الاختبارات</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>النوع</th>
                                        <th>المتوسط</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($profile['assessments'] ?? [] as $assessment)
                                        <tr>
                                            <td>{{ $assessment['date'] }}</td>
                                            <td>{{ $assessment['type'] }}</td>
                                            <td>
                                                @if($assessment['average'] !== null)
                                                    <span class="badge {{ $assessment['average_badge'] }}">{{ $assessment['average'] }}%</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center text-muted py-3">لا توجد اختبارات</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <a href="{{ route('admin.students.index') }}" class="btn btn-light">
                            <i class="ti ti-arrow-right me-1"></i> العودة إلى قائمة الطلاب
                        </a>

                        <div class="d-flex gap-2">
                            @can('students.update')
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#portalPasswordModal">
                                    <i class="ti ti-key me-1"></i> تعيين كلمة مرور البوابة
                                </button>
                                <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-primary">
                                    <i class="ti ti-pencil me-1"></i> تعديل الطالب
                                </a>
                            @endcan
                            @can('students.delete')
                                <form action="{{ route('admin.students.destroy', $student) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('هل تريد حذف هذا الطالب؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="ti ti-trash me-1"></i> حذف الطالب
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

@can('students.update')
@section('js')
<div class="modal fade" id="portalPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.students.set-portal-password', $student) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="ti ti-key me-2 text-success"></i>تعيين كلمة مرور بوابة الطالب
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        سيستخدم الطالب <strong>{{ $student->full_name }}</strong> رقم هاتفه
                        <code>{{ $student->phone }}</code> مع هذه الكلمة للدخول إلى
                        <a href="{{ route('student.login') }}" target="_blank">بوابة الطلاب</a>.
                    </p>
                    <div class="mb-3">
                        <label class="form-label">كلمة المرور الجديدة <span class="text-danger">*</span></label>
                        <input type="password" name="portal_password" class="form-control"
                               placeholder="6 أحرف على الأقل" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                        <input type="password" name="portal_password_confirmation" class="form-control"
                               placeholder="أعد كتابة كلمة المرور" required>
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



