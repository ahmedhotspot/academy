@extends('admin.layouts.master')

@section('title', 'إدارة الفروع - عرض')

@section('content')
    @php
        $stats = $profile['stats'] ?? [];
        $attendance = $profile['attendance'] ?? [];
        $finance = $profile['finance'] ?? [];
        $recent = $profile['recent'] ?? [];
    @endphp

    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">
                @include('admin.partials.page-header', [
                    'title' => 'تفاصيل الفرع',
                    'breadcrumbs' => $breadcrumbs,
                    'actions' => [
                        [
                            'title' => 'تعديل',
                            'url' => route('admin.branches.edit', $branch),
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
                                <p class="text-muted mb-1 small">ملف الفرع التشغيلي</p>
                                <h4 class="fw-bold mb-1">{{ $branch->name }}</h4>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge {{ $branch->status_badge_class }}">{{ $branch->status_label }}</span>
                                    <span class="badge bg-light text-dark border">رقم الفرع: #{{ $branch->id }}</span>
                                    <span class="badge bg-light text-dark border">تاريخ الإنشاء: {{ optional($branch->created_at)->format('Y-m-d') }}</span>
                                </div>
                            </div>

                            <div class="d-flex gap-2 flex-wrap">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">الطلاب: {{ $stats['students_count'] ?? 0 }}</span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">المعلمون: {{ $stats['teachers_count'] ?? 0 }}</span>
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2">الحلقات: {{ $stats['groups_count'] ?? 0 }}</span>
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2">المتأخرات: {{ $stats['overdue_subscriptions_count'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">الطلاب النشطون</p>
                                <h4 class="fw-bold mb-1 text-primary">{{ $stats['active_students_count'] ?? 0 }}</h4>
                                <p class="small text-muted mb-0">من إجمالي {{ $stats['students_count'] ?? 0 }} طالب</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">المعلمون النشطون</p>
                                <h4 class="fw-bold mb-1 text-success">{{ $stats['active_teachers_count'] ?? 0 }}</h4>
                                <p class="small text-muted mb-0">من إجمالي {{ $stats['teachers_count'] ?? 0 }} معلم</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">الحلقات النشطة</p>
                                <h4 class="fw-bold mb-1 text-info">{{ $stats['active_groups_count'] ?? 0 }}</h4>
                                <p class="small text-muted mb-0">من إجمالي {{ $stats['groups_count'] ?? 0 }} حلقة</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">اشتراكات متأخرة</p>
                                <h4 class="fw-bold mb-1 text-warning">{{ $stats['overdue_subscriptions_count'] ?? 0 }}</h4>
                                <p class="small text-muted mb-0">من إجمالي {{ $stats['subscriptions_count'] ?? 0 }} اشتراك</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-xl-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-calendar-check me-1"></i> حضور المعلمين اليوم</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="p-3 rounded bg-success-subtle text-center">
                                            <p class="small text-muted mb-1">حاضر</p>
                                            <h5 class="mb-0 text-success fw-bold">{{ $attendance['teacher_present_today'] ?? 0 }}</h5>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 rounded bg-danger-subtle text-center">
                                            <p class="small text-muted mb-1">غائب</p>
                                            <h5 class="mb-0 text-danger fw-bold">{{ $attendance['teacher_absent_today'] ?? 0 }}</h5>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 rounded bg-warning-subtle text-center">
                                            <p class="small text-muted mb-1">متأخر</p>
                                            <h5 class="mb-0 text-warning fw-bold">{{ $attendance['teacher_late_today'] ?? 0 }}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-coin me-1"></i> المؤشر المالي لهذا الشهر</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <span class="text-muted">التحصيل الشهري</span>
                                    <span class="fw-bold text-success">{{ $finance['payments_month'] ?? '0.00 ج' }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <span class="text-muted">مصروفات الشهر</span>
                                    <span class="fw-bold text-danger">{{ $finance['expenses_month'] ?? '0.00 ج' }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <span class="text-muted">إجمالي المتبقي على الاشتراكات</span>
                                    <span class="fw-bold text-warning">{{ $finance['remaining_subscriptions'] ?? '0.00 ج' }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center pt-2">
                                    <span class="text-muted">الصافي الشهري</span>
                                    <span class="fw-bold text-primary">{{ $finance['net_month'] ?? '0.00 ج' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-xl-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-user-check me-1"></i> أحدث الطلاب</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>الاسم</th>
                                        <th>الهاتف</th>
                                        <th>الحالة</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($recent['students'] ?? [] as $student)
                                        <tr>
                                            <td class="fw-semibold">{{ $student['name'] }}</td>
                                            <td>{{ $student['phone'] }}</td>
                                            <td>
                                                <span class="badge {{ $student['status'] === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $student['status'] === 'active' ? 'نشط' : 'غير نشط' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center text-muted py-3">لا توجد بيانات</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-school me-1"></i> أحدث المعلمين</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>الاسم</th>
                                        <th>الهاتف</th>
                                        <th>الحالة</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($recent['teachers'] ?? [] as $teacher)
                                        <tr>
                                            <td class="fw-semibold">{{ $teacher['name'] }}</td>
                                            <td>{{ $teacher['phone'] }}</td>
                                            <td>
                                                <span class="badge {{ $teacher['status'] === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $teacher['status'] === 'active' ? 'نشط' : 'غير نشط' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center text-muted py-3">لا توجد بيانات</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-xl-7">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-book-2 me-1"></i> الحلقات المرتبطة</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>الحلقة</th>
                                        <th>المعلم</th>
                                        <th>المستوى/المسار</th>
                                        <th>الطلاب</th>
                                        <th>الحالة</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($recent['groups'] ?? [] as $group)
                                        <tr>
                                            <td class="fw-semibold">{{ $group['name'] }}</td>
                                            <td>{{ $group['teacher'] }}</td>
                                            <td>{{ $group['level'] }} / {{ $group['track'] }}</td>
                                            <td>{{ $group['students_count'] }}</td>
                                            <td><span class="badge {{ $group['status_badge'] }}">{{ $group['status_label'] }}</span></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-3">لا توجد بيانات</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-5">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold text-warning"><i class="ti ti-alert-triangle me-1"></i> أعلى المتأخرات</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>الطالب</th>
                                        <th>الهاتف</th>
                                        <th>المتبقي</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($recent['overdue_subscriptions'] ?? [] as $sub)
                                        <tr>
                                            <td class="fw-semibold">{{ $sub['student'] }}</td>
                                            <td>{{ $sub['phone'] }}</td>
                                            <td class="text-danger fw-bold">{{ $sub['remaining'] }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center text-muted py-3">لا توجد متأخرات حالية</td></tr>
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
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-receipt me-1"></i> آخر المدفوعات</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>الطالب</th>
                                        <th>المبلغ</th>
                                        <th>التاريخ</th>
                                        <th>الإيصال</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($recent['payments'] ?? [] as $payment)
                                        <tr>
                                            <td class="fw-semibold">{{ $payment['student'] }}</td>
                                            <td>{{ $payment['amount'] }}</td>
                                            <td>{{ $payment['payment_date'] }}</td>
                                            <td>#{{ $payment['receipt'] }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted py-3">لا توجد مدفوعات حديثة</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-coin me-1"></i> آخر المصروفات</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>البند</th>
                                        <th>المبلغ</th>
                                        <th>التاريخ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($recent['expenses'] ?? [] as $expense)
                                        <tr>
                                            <td class="fw-semibold">{{ $expense['title'] }}</td>
                                            <td>{{ $expense['amount'] }}</td>
                                            <td>{{ $expense['expense_date'] }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center text-muted py-3">لا توجد مصروفات حديثة</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <a href="{{ route('admin.branches.index') }}" class="btn btn-light">
                            <i class="ti ti-arrow-right me-1"></i> العودة إلى الفروع
                        </a>

                        <div class="d-flex gap-2">
                            @can('branches.update')
                                <a href="{{ route('admin.branches.edit', $branch) }}" class="btn btn-primary">
                                    <i class="ti ti-pencil me-1"></i> تعديل
                                </a>
                            @endcan

                            @can('branches.delete')
                                <form action="{{ route('admin.branches.destroy', $branch) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('هل تريد حذف هذا الفرع؟ سيتم منع الحذف إذا كان الفرع مرتبطًا بطلاب أو معلمين أو حلقات.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="ti ti-trash me-1"></i> حذف الفرع
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

