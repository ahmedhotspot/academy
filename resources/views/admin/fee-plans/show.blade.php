@extends('admin.layouts.master')

@section('title', 'خطة الرسوم — ' . $feePlan->name)

@section('content')
    @php
        $stats = $profile['stats'] ?? [];
        $financial = $profile['financial'] ?? [];
    @endphp

    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'الملف الشامل لخطة الرسوم',
                    'breadcrumbs' => $breadcrumbs,
                    'actions' => [
                        [
                            'title' => 'تعديل',
                            'url' => route('admin.fee-plans.edit', $feePlan),
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
                                <p class="text-muted mb-1 small">بيانات خطة الرسوم</p>
                                <h4 class="fw-bold mb-1">{{ $feePlan->name }}</h4>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="badge {{ $feePlan->status_badge_class }}">{{ $feePlan->status_label }}</span>
                                    <span class="badge bg-light text-dark border">دورة الدفع: {{ $feePlan->payment_cycle_label }}</span>
                                    <span class="badge bg-light text-dark border">المبلغ: {{ $feePlan->formatted_amount }}</span>
                                    <span class="badge {{ $feePlan->discount_badge_class }}">خصم الأخوات: {{ $feePlan->discount_label }}</span>
                                </div>
                            </div>

                            <div class="text-end">
                                <p class="text-muted small mb-1">تاريخ الإنشاء</p>
                                <p class="fw-semibold mb-0">{{ optional($feePlan->created_at)->format('Y-m-d H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">إجمالي الاشتراكات</p>
                                <h4 class="fw-bold text-primary mb-0">{{ $stats['subscriptions_count'] ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">الاشتراكات النشطة</p>
                                <h4 class="fw-bold text-success mb-0">{{ $stats['active_subscriptions_count'] ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">المتأخرات</p>
                                <h4 class="fw-bold text-warning mb-0">{{ $stats['overdue_subscriptions_count'] ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">عدد الدفعات</p>
                                <h4 class="fw-bold text-info mb-0">{{ $stats['payments_count'] ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">إجمالي المستحق</p>
                                <h5 class="fw-bold text-primary mb-0">{{ $financial['total_final'] ?? '0.00 ج' }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">إجمالي المدفوع</p>
                                <h5 class="fw-bold text-success mb-0">{{ $financial['total_paid'] ?? '0.00 ج' }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">إجمالي المتبقي</p>
                                <h5 class="fw-bold text-danger mb-0">{{ $financial['total_remaining'] ?? '0.00 ج' }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">تحصيل هذا الشهر</p>
                                <h5 class="fw-bold text-info mb-0">{{ $financial['payments_month'] ?? '0.00 ج' }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-semibold"><i class="ti ti-receipt-2 me-1"></i> الاشتراكات المرتبطة بالخطة</h6>
                        <span class="badge bg-info rounded-pill">{{ count($profile['subscriptions'] ?? []) }}</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>الطالب</th>
                                <th>الهاتف</th>
                                <th>النهائي</th>
                                <th>المدفوع</th>
                                <th>المتبقي</th>
                                <th>الحالة</th>
                                <th>التقدم</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($profile['subscriptions'] ?? [] as $subscription)
                                <tr>
                                    <td class="fw-semibold">{{ $subscription['student'] }}</td>
                                    <td>{{ $subscription['phone'] }}</td>
                                    <td>{{ $subscription['final'] }}</td>
                                    <td>{{ $subscription['paid'] }}</td>
                                    <td class="text-danger fw-semibold">{{ $subscription['remaining'] }}</td>
                                    <td><span class="badge {{ $subscription['status_badge'] }}">{{ $subscription['status'] }}</span></td>
                                    <td style="min-width:140px;">
                                        <div class="progress" style="height: 18px;">
                                            <div class="progress-bar {{ $subscription['progress'] >= 100 ? 'bg-success' : ($subscription['progress'] >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                                 role="progressbar"
                                                 style="width: {{ $subscription['progress'] }}%"
                                                 aria-valuenow="{{ $subscription['progress'] }}"
                                                 aria-valuemin="0"
                                                 aria-valuemax="100">
                                                {{ $subscription['progress'] }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted py-3">لا توجد اشتراكات مرتبطة بهذه الخطة</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-xl-7">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-receipt me-1"></i> أحدث المدفوعات المرتبطة بالخطة</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>الطالب</th>
                                        <th>التاريخ</th>
                                        <th>المبلغ</th>
                                        <th>الإيصال</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($profile['recent_payments'] ?? [] as $payment)
                                        <tr>
                                            <td class="fw-semibold">{{ $payment['student'] }}</td>
                                            <td>{{ $payment['date'] }}</td>
                                            <td class="text-success fw-semibold">{{ $payment['amount'] }}</td>
                                            <td>{{ $payment['receipt'] }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted py-3">لا توجد دفعات مرتبطة بهذه الخطة</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-5">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-warning bg-opacity-10 border-bottom d-flex align-items-center justify-content-between">
                                <h6 class="mb-0 fw-semibold text-warning-emphasis"><i class="ti ti-alert-triangle me-1"></i> أعلى المتأخرات</h6>
                                <span class="badge bg-warning text-dark rounded-pill">{{ count($profile['top_outstanding'] ?? []) }}</span>
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
                                    @forelse($profile['top_outstanding'] ?? [] as $row)
                                        <tr>
                                            <td class="fw-semibold">{{ $row['student'] }}</td>
                                            <td>{{ $row['phone'] }}</td>
                                            <td class="text-danger fw-semibold">{{ $row['remaining'] }}</td>
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

                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex gap-2 justify-content-between align-items-center flex-wrap">
                        <a href="{{ route('admin.fee-plans.index') }}" class="btn btn-light btn-sm">
                            <i class="ti ti-arrow-right me-1"></i> العودة للقائمة
                        </a>

                        <div class="d-flex gap-2">
                            @can('fee-plans.update')
                                <a href="{{ route('admin.fee-plans.edit', $feePlan) }}" class="btn btn-primary btn-sm">
                                    <i class="ti ti-pencil me-1"></i> تعديل الخطة
                                </a>
                            @endcan
                            @can('fee-plans.delete')
                                <form method="POST"
                                      action="{{ route('admin.fee-plans.destroy', $feePlan) }}"
                                      onsubmit="return confirm('هل تريد حقاً حذف هذه الخطة؟')"
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="ti ti-trash me-1"></i> حذف الخطة
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

