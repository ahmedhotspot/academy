@extends('guardian.layouts.master')

@section('title', 'تفاصيل الطالب - ' . $student->full_name)

@section('content')
@php
    $stats       = $profile['stats']       ?? [];
    $financial   = $profile['financial']   ?? [];
    $learning    = $profile['learning']    ?? [];
    $enrollments = $profile['enrollments'] ?? [];
    $subscriptions=$profile['subscriptions']?? [];
    $payments    = $profile['payments']    ?? [];
    $progressLogs= $profile['progress_logs']?? [];
    $assessments = $profile['assessments'] ?? [];
@endphp

{{-- رأس الصفحة --}}
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
    <div>
        <a href="{{ route('guardian.dashboard') }}" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="ti ti-arrow-right me-1"></i>العودة
        </a>
        <h4 class="fw-bold mb-0"><i class="ti ti-user me-2 text-primary"></i>{{ $student->full_name }}</h4>
        <div class="d-flex flex-wrap gap-2 mt-1">
            <span class="badge {{ $student->status_badge_class }}">{{ $student->status_label }}</span>
            <span class="badge bg-light text-dark border"><i class="ti ti-calendar me-1"></i>{{ $student->age }} سنة</span>
            <span class="badge bg-light text-dark border"><i class="ti ti-phone me-1"></i>{{ $student->phone }}</span>
            @if($student->branch)
                <span class="badge bg-light text-dark border"><i class="ti ti-building me-1"></i>{{ $student->branch->name }}</span>
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

{{-- الملخص المالي --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <p class="text-muted small mb-1">إجمالي المدفوع</p>
                <h4 class="fw-bold text-success mb-0">{{ $financial['total_paid'] ?? '0 ر.س' }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <p class="text-muted small mb-1">المتبقي</p>
                <h4 class="fw-bold text-danger mb-0">{{ $financial['total_remaining'] ?? '0 ر.س' }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <p class="text-muted small mb-1">متوسط التقييم</p>
                <h4 class="fw-bold text-primary mb-0">
                    {{ $learning['assessment_avg'] ? $learning['assessment_avg'].'%' : '-' }}
                </h4>
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

    {{-- التسجيلات --}}
    <div class="tab-pane fade show active" id="tab-enrollments">
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>المجموعة</th><th>المعلم</th><th>المستوى</th><th>المسار</th><th>حالة التسجيل</th><th>التاريخ</th></tr>
                    </thead>
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

    {{-- الاشتراكات --}}
    <div class="tab-pane fade" id="tab-subscriptions">
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>الخطة</th><th>الدورة</th><th>النهائي</th><th>المدفوع</th><th>المتبقي</th><th>نسبة السداد</th><th>الحالة</th></tr>
                    </thead>
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

    {{-- المدفوعات --}}
    <div class="tab-pane fade" id="tab-payments">
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>رقم الإيصال</th><th>التاريخ</th><th>المبلغ</th><th>ملاحظات</th></tr>
                    </thead>
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

    {{-- سجلات التقدم --}}
    <div class="tab-pane fade" id="tab-progress">
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>التاريخ</th><th>المجموعة</th><th>المعلم</th><th>الحفظ</th><th>المراجعة</th><th>التجويد</th><th>التدبر</th><th>الإتقان</th></tr>
                    </thead>
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

    {{-- التقييمات --}}
    <div class="tab-pane fade" id="tab-assessments">
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>التاريخ</th><th>النوع</th><th>المجموعة</th><th>المعلم</th><th>الحفظ</th><th>التجويد</th><th>المتوسط</th></tr>
                    </thead>
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

