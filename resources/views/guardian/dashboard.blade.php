@extends('guardian.layouts.master')

@section('title', 'لوحة متابعة ولي الأمر')

@section('content')
@php
    $stats    = $profile['stats']    ?? [];
    $financial= $profile['financial']?? [];
    $students = $profile['students'] ?? [];
@endphp

{{-- ترحيب --}}
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
    <div>
        <h4 class="fw-bold mb-0">أهلاً، {{ $guardian->full_name }} 👋</h4>
        <p class="text-muted mb-0 small">متابعة شاملة لأبنائك في أكاديمية القرآن</p>
    </div>
    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
        <i class="ti ti-calendar me-1"></i>{{ now()->locale('ar')->isoFormat('dddd، D MMMM YYYY') }}
    </span>
</div>

{{-- بطاقات الإحصاء --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-2">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-3">
                <div class="bg-primary-subtle rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                    <i class="ti ti-users text-primary" style="font-size:1.4rem;"></i>
                </div>
                <h3 class="fw-bold mb-0">{{ $stats['students_count'] ?? 0 }}</h3>
                <p class="text-muted small mb-0">الطلاب</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-2">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-3">
                <div class="bg-success-subtle rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                    <i class="ti ti-user-check text-success" style="font-size:1.4rem;"></i>
                </div>
                <h3 class="fw-bold mb-0">{{ $stats['active_students_count'] ?? 0 }}</h3>
                <p class="text-muted small mb-0">نشطون</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-2">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-3">
                <div class="bg-info-subtle rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                    <i class="ti ti-receipt-2 text-info" style="font-size:1.4rem;"></i>
                </div>
                <h3 class="fw-bold mb-0">{{ $stats['subscriptions_count'] ?? 0 }}</h3>
                <p class="text-muted small mb-0">الاشتراكات</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-2">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-3">
                <div class="bg-warning-subtle rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                    <i class="ti ti-alert-triangle text-warning" style="font-size:1.4rem;"></i>
                </div>
                <h3 class="fw-bold mb-0">{{ $stats['overdue_subscriptions_count'] ?? 0 }}</h3>
                <p class="text-muted small mb-0">متأخرات</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-2">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-3">
                <div class="bg-success-subtle rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                    <i class="ti ti-coin text-success" style="font-size:1.4rem;"></i>
                </div>
                <h4 class="fw-bold mb-0 text-success small">{{ $financial['total_paid'] ?? '0 ر.س' }}</h4>
                <p class="text-muted small mb-0">إجمالي المدفوع</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-2">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-3">
                <div class="bg-danger-subtle rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                    <i class="ti ti-coin-off text-danger" style="font-size:1.4rem;"></i>
                </div>
                <h4 class="fw-bold mb-0 text-danger small">{{ $financial['total_remaining'] ?? '0 ر.س' }}</h4>
                <p class="text-muted small mb-0">المتبقي</p>
            </div>
        </div>
    </div>
</div>

{{-- بطاقات الطلاب --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-bold"><i class="ti ti-users me-2 text-primary"></i>أبنائي المسجّلون</h6>
        <span class="badge bg-primary">{{ count($students) }}</span>
    </div>
    <div class="card-body">
        @forelse($students as $student)
            <div class="border rounded-3 p-3 mb-3 bg-light">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h6 class="fw-bold mb-1">
                            <i class="ti ti-user me-1 text-primary"></i>{{ $student['name'] }}
                        </h6>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-light text-dark border"><i class="ti ti-calendar me-1"></i>{{ $student['age'] }} سنة</span>
                            <span class="badge bg-light text-dark border"><i class="ti ti-phone me-1"></i>{{ $student['phone'] }}</span>
                            <span class="badge bg-light text-dark border"><i class="ti ti-building me-1"></i>{{ $student['branch'] }}</span>
                            <span class="badge {{ $student['status_badge'] }}">{{ $student['status'] }}</span>
                        </div>
                    </div>
                    <a href="{{ route('guardian.students.show', $student['id']) }}" class="btn btn-primary btn-sm">
                        <i class="ti ti-eye me-1"></i>عرض التفاصيل
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-4">
                <i class="ti ti-user-off" style="font-size:2.5rem;opacity:.4;"></i>
                <p class="mt-2 mb-0">لا يوجد طلاب مرتبطون بحسابك حالياً</p>
            </div>
        @endforelse
    </div>
</div>

{{-- أحدث الاشتراكات والمدفوعات --}}
<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-bold"><i class="ti ti-receipt-2 me-2 text-info"></i>أحدث الاشتراكات</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>الطالب</th><th>النهائي</th><th>المتبقي</th><th>الحالة</th></tr>
                    </thead>
                    <tbody>
                        @forelse($profile['subscriptions'] ?? [] as $sub)
                            <tr>
                                <td class="fw-semibold">{{ $sub['student'] }}</td>
                                <td>{{ $sub['final'] }}</td>
                                <td class="text-danger fw-semibold">{{ $sub['remaining'] }}</td>
                                <td><span class="badge {{ $sub['status_badge'] }}">{{ $sub['status'] }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">لا توجد اشتراكات</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-bold"><i class="ti ti-receipt me-2 text-success"></i>أحدث المدفوعات</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>الطالب</th><th>التاريخ</th><th>المبلغ</th></tr>
                    </thead>
                    <tbody>
                        @forelse($profile['payments'] ?? [] as $pay)
                            <tr>
                                <td class="fw-semibold">{{ $pay['student'] }}</td>
                                <td>{{ $pay['date'] }}</td>
                                <td class="text-success fw-semibold">{{ $pay['amount'] }}</td>
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
@endsection

