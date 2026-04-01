@extends('student.layouts.master')

@section('title', 'لوحة متابعة الطالب')

@section('content')
@php
    $stats        = $profile['stats']        ?? [];
    $financial    = $profile['financial']    ?? [];
    $learning     = $profile['learning']     ?? [];
    $enrollments  = $profile['enrollments']  ?? [];
    $subscriptions= $profile['subscriptions']?? [];
    $payments     = $profile['payments']     ?? [];
    $progressLogs = $profile['progress_logs']?? [];
    $assessments  = $profile['assessments']  ?? [];
    $guardian     = $profile['guardian']     ?? [];
@endphp

{{-- ترحيب --}}
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
    <div>
        <h4 class="fw-bold mb-0">أهلاً، {{ $student->full_name }} 👋</h4>
        <p class="text-muted mb-0 small">متابعة مسيرتك التعليمية في أكاديمية القرآن</p>
    </div>
    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
        <i class="ti ti-calendar me-1"></i>{{ now()->locale('ar')->isoFormat('dddd، D MMMM YYYY') }}
    </span>
</div>

{{-- بطاقة المعلومات الشخصية --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <h6 class="fw-bold mb-3 text-success"><i class="ti ti-id-badge me-2"></i>بياناتي الشخصية</h6>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-light text-dark border px-3 py-2"><i class="ti ti-calendar me-1"></i>العمر: {{ $student->age }} سنة</span>
                    <span class="badge bg-light text-dark border px-3 py-2"><i class="ti ti-flag me-1"></i>{{ $student->nationality }}</span>
                    <span class="badge bg-light text-dark border px-3 py-2"><i class="ti ti-phone me-1"></i>{{ $student->phone }}</span>
                    @if($student->branch)
                        <span class="badge bg-light text-dark border px-3 py-2"><i class="ti ti-building me-1"></i>{{ $student->branch->name }}</span>
                    @endif
                    <span class="badge {{ $student->status_badge_class }} px-3 py-2">{{ $student->status_label }}</span>
                </div>
            </div>
            @if(!empty($guardian['name']) && $guardian['name'] !== '-')
            <div class="col-md-6">
                <h6 class="fw-bold mb-3 text-primary"><i class="ti ti-user-heart me-2"></i>ولي الأمر</h6>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-light text-dark border px-3 py-2"><i class="ti ti-user me-1"></i>{{ $guardian['name'] }}</span>
                    <span class="badge bg-light text-dark border px-3 py-2"><i class="ti ti-phone me-1"></i>{{ $guardian['phone'] }}</span>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- بطاقات الإحصاء --}}
<div class="row g-3 mb-4">
    @foreach([
        ['val'=>$stats['enrollments']??0,    'label'=>'التسجيلات',  'color'=>'primary',  'icon'=>'ti-book'],
        ['val'=>$stats['subscriptions']??0,  'label'=>'الاشتراكات', 'color'=>'info',     'icon'=>'ti-receipt-2'],
        ['val'=>$stats['payments']??0,       'label'=>'المدفوعات',  'color'=>'success',  'icon'=>'ti-coin'],
        ['val'=>$stats['progress_logs']??0,  'label'=>'سجلات التقدم','color'=>'warning', 'icon'=>'ti-trending-up'],
        ['val'=>$stats['assessments']??0,    'label'=>'التقييمات',  'color'=>'danger',   'icon'=>'ti-star'],
    ] as $card)
    <div class="col-6 col-xl">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-3">
                <div class="bg-{{ $card['color'] }}-subtle rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width:44px;height:44px;">
                    <i class="ti {{ $card['icon'] }} text-{{ $card['color'] }}" style="font-size:1.3rem;"></i>
                </div>
                <h3 class="fw-bold mb-0">{{ $card['val'] }}</h3>
                <p class="text-muted small mb-0">{{ $card['label'] }}</p>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- الملخص المالي والتعليمي --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <p class="text-muted small mb-1">إجمالي المدفوع</p>
                <h5 class="fw-bold text-success mb-0">{{ $financial['total_paid'] ?? '0 ج' }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <p class="text-muted small mb-1">المتبقي</p>
                <h5 class="fw-bold text-danger mb-0">{{ $financial['total_remaining'] ?? '0 ج' }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <p class="text-muted small mb-1">متوسط التقييم</p>
                <h5 class="fw-bold text-primary mb-0">
                    {{ $learning['assessment_avg'] ? $learning['assessment_avg'].'%' : '-' }}
                </h5>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <p class="text-muted small mb-1">آخر تقدم</p>
                <h5 class="fw-bold text-info mb-0">{{ $learning['last_progress_date'] ?? '-' }}</h5>
            </div>
        </div>
    </div>
</div>

{{-- تبويبات --}}
<ul class="nav nav-tabs mb-3" id="studentTab" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-enrollments"><i class="ti ti-book me-1"></i>التسجيلات</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-subscriptions"><i class="ti ti-receipt-2 me-1"></i>الاشتراكات</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-payments"><i class="ti ti-coin me-1"></i>المدفوعات</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-progress"><i class="ti ti-trending-up me-1"></i>سجلات التقدم</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-assessments"><i class="ti ti-star me-1"></i>التقييمات</button></li>
</ul>

<div class="tab-content">

    <div class="tab-pane fade show active" id="tab-enrollments">
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>المجموعة</th><th>المعلم</th><th>المستوى</th><th>المسار</th><th>الحالة</th><th>التاريخ</th></tr></thead>
                    <tbody>
                        @forelse($enrollments as $e)
                            <tr>
                                <td class="fw-semibold">{{ $e['group'] }}</td>
                                <td>{{ $e['teacher'] }}</td>
                                <td>{{ $e['level'] }}</td>
                                <td>{{ $e['track'] }}</td>
                                <td><span class="badge {{ $e['enrollment_badge'] }}">{{ $e['enrollment_status'] }}</span></td>
                                <td>{{ $e['date'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-3">لا توجد تسجيلات</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="tab-subscriptions">
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>الخطة</th><th>الدورة</th><th>النهائي</th><th>المدفوع</th><th>المتبقي</th><th>نسبة السداد</th><th>الحالة</th></tr></thead>
                    <tbody>
                        @forelse($subscriptions as $sub)
                            <tr>
                                <td class="fw-semibold">{{ $sub['plan'] }}</td>
                                <td>{{ $sub['cycle'] }}</td>
                                <td>{{ $sub['final'] }}</td>
                                <td class="text-success fw-semibold">{{ $sub['paid'] }}</td>
                                <td class="text-danger fw-semibold">{{ $sub['remaining'] }}</td>
                                <td>
                                    <div class="progress" style="height:6px;width:80px;">
                                        <div class="progress-bar bg-success" style="width:{{ $sub['progress'] }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ $sub['progress'] }}%</small>
                                </td>
                                <td><span class="badge {{ $sub['status_badge'] }}">{{ $sub['status'] }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-3">لا توجد اشتراكات</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="tab-payments">
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>رقم الإيصال</th><th>التاريخ</th><th>المبلغ</th><th>ملاحظات</th></tr></thead>
                    <tbody>
                        @forelse($payments as $pay)
                            <tr>
                                <td><span class="badge bg-light text-dark border">{{ $pay['receipt'] }}</span></td>
                                <td>{{ $pay['date'] }}</td>
                                <td class="text-success fw-bold">{{ $pay['amount'] }}</td>
                                <td class="text-muted">{{ $pay['notes'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">لا توجد مدفوعات</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="tab-progress">
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>التاريخ</th><th>المجموعة</th><th>المعلم</th><th>الحفظ</th><th>المراجعة</th><th>التجويد</th><th>التدبر</th><th>الإتقان</th></tr></thead>
                    <tbody>
                        @forelse($progressLogs as $log)
                            <tr>
                                <td>{{ $log['date'] }}</td>
                                <td>{{ $log['group'] }}</td>
                                <td>{{ $log['teacher'] }}</td>
                                <td>{{ $log['memorization'] }}</td>
                                <td>{{ $log['revision'] }}</td>
                                <td>{{ $log['tajweed'] }}</td>
                                <td>{{ $log['tadabbur'] }}</td>
                                <td>{{ $log['mastery'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-3">لا توجد سجلات تقدم</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="tab-assessments">
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>التاريخ</th><th>النوع</th><th>المجموعة</th><th>المعلم</th><th>الحفظ</th><th>التجويد</th><th>المتوسط</th></tr></thead>
                    <tbody>
                        @forelse($assessments as $assess)
                            <tr>
                                <td>{{ $assess['date'] }}</td>
                                <td>{{ $assess['type'] }}</td>
                                <td>{{ $assess['group'] }}</td>
                                <td>{{ $assess['teacher'] }}</td>
                                <td>{{ $assess['memorization'] }}</td>
                                <td>{{ $assess['tajweed'] }}</td>
                                <td><span class="badge {{ $assess['average_badge'] }}">{{ $assess['average'] }}%</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-3">لا توجد تقييمات</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

