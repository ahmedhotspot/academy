@extends('admin.layouts.master')

@section('title', 'الملف الشامل للطالب')

@section('css')
<style>
    :root {
        --student-green: #1B5E20;
        --student-green-2: #2E7D32;
        --student-gold: #C9991A;
        --student-soft: #F6FBF6;
    }

    .student-hero {
        background: linear-gradient(135deg, var(--student-green) 0%, var(--student-green-2) 70%, #3E8E41 100%);
        border-radius: 18px;
        color: #fff;
        position: relative;
        overflow: hidden;
    }

    .student-hero::after {
        content: '﷽';
        position: absolute;
        left: -16px;
        top: -12px;
        font-size: 9rem;
        opacity: .06;
        line-height: 1;
        font-family: 'Cairo', sans-serif;
    }

    .student-avatar-lg {
        width: 74px;
        height: 74px;
        border-radius: 50%;
        background: rgba(255,255,255,.14);
        border: 2px solid rgba(255,255,255,.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        font-weight: 700;
    }

    .kpi-card,
    .info-card,
    .section-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, .06);
    }

    .kpi-card:hover,
    .info-card:hover,
    .section-card:hover {
        transform: translateY(-2px);
        transition: .2s ease;
    }

    .kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }

    .soft-chip {
        background: rgba(255,255,255,.16);
        border: 1px solid rgba(255,255,255,.14);
        color: #fff;
        border-radius: 999px;
        padding: .45rem .9rem;
        font-size: .86rem;
    }

    .metric-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: .75rem 0;
        border-bottom: 1px dashed #e9ecef;
    }

    .metric-row:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .label-muted {
        color: #6c757d;
        font-size: .88rem;
        margin-bottom: .2rem;
    }

    .value-strong {
        font-weight: 700;
        color: #1f2937;
    }

    .section-header-green {
        background: linear-gradient(90deg, var(--student-green), #347E3A);
        color: #fff;
        border-radius: 16px 16px 0 0;
    }

    .section-header-soft {
        background: #fcfdfc;
        border-bottom: 1px solid #eef2f7;
    }

    .styled-table thead th {
        background: #F3FAF3;
        color: var(--student-green);
        border-bottom: 1px solid #dcefdc;
        font-weight: 700;
        white-space: nowrap;
    }

    .mini-progress {
        height: 9px;
        border-radius: 999px;
        background: #e9ecef;
        overflow: hidden;
    }

    .mini-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, var(--student-green), var(--student-gold));
        border-radius: 999px;
    }

    .assessment-ring {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 5px solid rgba(27,94,32,.14);
        color: var(--student-green);
        font-weight: 700;
        font-size: 1rem;
        background: #fff;
    }

    .portal-box {
        background: linear-gradient(135deg, #f7fff7, #eef7ee);
        border: 1px solid #dfeee0;
        border-radius: 14px;
    }
</style>
@endsection

@section('content')
    @php
        $stats = $profile['stats'] ?? [];
        $guardian = $profile['guardian'] ?? [];
        $financial = $profile['financial'] ?? [];
        $learning = $profile['learning'] ?? [];
        $currentEnrollment = $student->currentEnrollment();
        $progressQuickAddParams = array_filter([
            'student_id' => $student->id,
            'group_id' => $currentEnrollment?->group_id,
        ]);
        $collectionRate = (float) ($financial['collection_rate'] ?? 0);
        $assessmentAvg = $learning['assessment_avg'] ?? null;
    @endphp

    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">
                @include('admin.partials.page-header', [
                    'title' => 'الملف الشامل للطالب',
                    'breadcrumbs' => $breadcrumbs,
                    'actions' => [
                        [
                            'title' => 'تعديل الطالب',
                            'url' => route('admin.students.edit', $student),
                            'icon' => 'ti ti-edit',
                            'class' => 'btn-primary',
                        ],
                    ],
                ])

                @include('admin.partials.alerts')

                <div class="student-hero p-4 p-lg-5 mb-4 shadow-sm">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-4 position-relative" style="z-index:1;">
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <div class="student-avatar-lg">
                                {{ mb_substr($student->full_name, 0, 1) }}
                            </div>
                            <div>
                                <p class="mb-1 small" style="opacity:.8;">ملف الطالب القرآني والإداري</p>
                                <h3 class="fw-bold mb-2">{{ $student->full_name }}</h3>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="soft-chip"><i class="ti ti-id me-1"></i>رقم الطالب #{{ $student->id }}</span>
                                    <span class="soft-chip"><i class="ti ti-building me-1"></i>{{ $student->branch?->name ?? 'بدون فرع' }}</span>
                                    <span class="soft-chip"><i class="ti ti-activity-heartbeat me-1"></i>{{ $student->status_label }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <div class="small mb-2" style="opacity:.85;">آخر تحديث للملف</div>
                            <div class="fw-semibold">{{ $profile['meta']['generated_at'] ?? now()->format('Y-m-d H:i') }}</div>

                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-6 col-xl">
                        <div class="card kpi-card h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="kpi-icon" style="background:#E8F5E9;color:#1B5E20;"><i class="ti ti-book-2"></i></div>
                                <div>
                                    <div class="text-muted small">التسجيلات</div>
                                    <div class="fs-4 fw-bold">{{ $stats['enrollments'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-xl">
                        <div class="card kpi-card h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="kpi-icon" style="background:#E3F2FD;color:#1565C0;"><i class="ti ti-receipt-2"></i></div>
                                <div>
                                    <div class="text-muted small">الاشتراكات</div>
                                    <div class="fs-4 fw-bold">{{ $stats['subscriptions'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-xl">
                        <div class="card kpi-card h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="kpi-icon" style="background:#E8F5E9;color:#2E7D32;"><i class="ti ti-cash-banknote"></i></div>
                                <div>
                                    <div class="text-muted small">الدفعات</div>
                                    <div class="fs-4 fw-bold">{{ $stats['payments'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-xl">
                        <div class="card kpi-card h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="kpi-icon" style="background:#FFF8E1;color:#F57F17;"><i class="ti ti-chart-line"></i></div>
                                <div>
                                    <div class="text-muted small">المتابعات</div>
                                    <div class="fs-4 fw-bold">{{ $stats['progress_logs'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-xl">
                        <div class="card kpi-card h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="kpi-icon" style="background:#F3E5F5;color:#7B1FA2;"><i class="ti ti-test-pipe"></i></div>
                                <div>
                                    <div class="text-muted small">الاختبارات</div>
                                    <div class="fs-4 fw-bold">{{ $stats['assessments'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-xl-7">
                        <div class="card info-card h-100">
                            <div class="card-header section-header-soft py-3 px-4">
                                <h6 class="mb-0 fw-bold text-success"><i class="ti ti-user me-2"></i>البيانات الأساسية للطالب</h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="label-muted">كود الطالب</div>
                                        <div class="value-strong">{{ $student->student_code ?: '-' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="label-muted">تاريخ الالتحاق</div>
                                        <div class="value-strong">{{ optional($student->enrollment_date)->format('Y-m-d') ?: '-' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="label-muted">تاريخ الميلاد</div>
                                        <div class="value-strong">{{ optional($student->birth_date)->format('Y-m-d') ?: '-' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="label-muted">العمر</div>
                                        <div class="value-strong">{{ $student->age }} سنة</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="label-muted">الجنس</div>
                                        <div class="value-strong">
                                            {{ $student->gender === 'male' ? 'ذكر' : ($student->gender === 'female' ? 'أنثى' : '-') }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="label-muted">الجنسية</div>
                                        <div class="value-strong">{{ $student->nationality }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="label-muted">رقم الهاتف</div>
                                        <div class="value-strong">{{ $student->phone }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="label-muted">رقم الواتساب</div>
                                        <div class="value-strong">{{ $student->whatsapp ?: '-' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="label-muted">رقم الهوية / الجواز</div>
                                        <div class="value-strong">{{ $student->identity_number ?: '-' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="label-muted">تاريخ انتهاء الهوية / الجواز</div>
                                        <div class="value-strong">{{ optional($student->identity_expiry_date)->format('Y-m-d') ?: '-' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="label-muted">رقم الإقامة</div>
                                        <div class="value-strong">{{ $student->residency_number ?: '-' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="label-muted">تاريخ انتهاء الإقامة</div>
                                        <div class="value-strong">{{ optional($student->residency_expiry_date)->format('Y-m-d') ?: '-' }}</div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="portal-box p-3">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <div>
                                            <div class="fw-bold text-success mb-1"><i class="ti ti-login-2 me-1"></i>بيانات دخول بوابة الطالب</div>
                                            <div class="text-muted small">يمكن للطالب استخدام رقم الهاتف للدخول إلى البوابة التعليمية.</div>
                                        </div>
                                        <a href="{{ route('student.login') }}" target="_blank" class="btn btn-sm btn-outline-success">
                                            <i class="ti ti-external-link me-1"></i>فتح بوابة الطالب
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-5">
                        <div class="card info-card h-100">
                            <div class="card-header section-header-soft py-3 px-4 d-flex align-items-center justify-content-between">
                                <h6 class="mb-0 fw-bold text-success"><i class="ti ti-users me-2"></i>ولي الأمر والمؤشرات</h6>
                                <div class="assessment-ring">{{ $assessmentAvg !== null ? $assessmentAvg.'%' : '--' }}</div>
                            </div>
                            <div class="card-body p-4">
                                <div class="metric-row">
                                    <div>
                                        <div class="label-muted">اسم ولي الأمر</div>
                                        <div class="value-strong">{{ $guardian['name'] ?? '-' }}</div>
                                    </div>
                                    <i class="ti ti-user-heart text-danger fs-4"></i>
                                </div>
                                <div class="metric-row">
                                    <div>
                                        <div class="label-muted">هاتف ولي الأمر</div>
                                        <div class="value-strong">{{ $guardian['phone'] ?? '-' }}</div>
                                    </div>
                                    <i class="ti ti-phone text-primary fs-4"></i>
                                </div>
                                <div class="metric-row">
                                    <div>
                                        <div class="label-muted">واتساب ولي الأمر</div>
                                        <div class="value-strong">{{ $guardian['whatsapp'] ?? '-' }}</div>
                                    </div>
                                    <i class="ti ti-brand-whatsapp text-success fs-4"></i>
                                </div>
                                <div class="metric-row">
                                    <div>
                                        <div class="label-muted">آخر متابعة</div>
                                        <div class="value-strong">{{ $learning['last_progress_date'] ?? '-' }}</div>
                                    </div>
                                    <i class="ti ti-calendar-event text-warning fs-4"></i>
                                </div>
                                <div class="metric-row">
                                    <div>
                                        <div class="label-muted">آخر اختبار</div>
                                        <div class="value-strong">{{ $learning['last_assessment_date'] ?? '-' }}</div>
                                    </div>
                                    <i class="ti ti-calendar-stats text-info fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card info-card h-100 border-start border-4 border-success">
                            <div class="card-body p-4">
                                <div class="text-muted small mb-1">إجمالي المستحق النهائي</div>
                                <div class="fs-4 fw-bold text-dark">{{ $financial['total_final'] ?? '0.00 ج' }}</div>
                                <div class="small text-muted mt-2">القيمة الكلية بعد الخصومات على جميع الاشتراكات</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card info-card h-100 border-start border-4 border-primary">
                            <div class="card-body p-4">
                                <div class="text-muted small mb-1">إجمالي المدفوع</div>
                                <div class="fs-4 fw-bold text-success">{{ $financial['total_paid'] ?? '0.00 ج' }}</div>
                                <div class="small text-muted mt-2">إجمالي ما تم سداده من رسوم الطالب</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card info-card h-100 border-start border-4 border-warning">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div>
                                        <div class="text-muted small mb-1">المتبقي + نسبة التحصيل</div>
                                        <div class="fs-4 fw-bold text-warning">{{ $financial['total_remaining'] ?? '0.00 ج' }}</div>
                                    </div>
                                    <span class="badge bg-success-subtle text-success px-3 py-2">{{ $collectionRate }}%</span>
                                </div>
                                <div class="mini-progress mt-3">
                                    <div class="mini-progress-bar" style="width: {{ $collectionRate }}%;"></div>
                                </div>
                                <div class="small text-muted mt-2">نسبة التحصيل الإجمالية لجميع اشتراكات الطالب</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card section-card mb-4">
                    <div class="card-header section-header-green py-3 px-4">
                        <h6 class="mb-0 fw-bold"><i class="ti ti-book-2 me-2"></i>التسجيلات في الحلقات والمستويات</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table styled-table table-hover align-middle mb-0">
                            <thead>
                            <tr>
                                <th>الحلقة</th>
                                <th>المعلم</th>
                                <th>المستوى / المسار</th>
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
                                <tr><td colspan="6" class="text-center text-muted py-4">لا توجد تسجيلات في الحلقات لهذا الطالب</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-xl-6">
                        <div class="card section-card h-100">
                            <div class="card-header section-header-soft py-3 px-4 d-flex align-items-center justify-content-between">
                                <h6 class="mb-0 fw-bold text-success"><i class="ti ti-receipt-2 me-2"></i>الاشتراكات والخطط المالية</h6>
                                <span class="badge bg-light text-success border">{{ $stats['subscriptions'] ?? 0 }} اشتراك</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table styled-table table-hover align-middle mb-0">
                                    <thead>
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
                                            <td class="text-success fw-semibold">{{ $subscription['paid'] }}</td>
                                            <td class="text-danger fw-semibold">{{ $subscription['remaining'] }}</td>
                                            <td><span class="badge {{ $subscription['status_badge'] }}">{{ $subscription['status'] }}</span></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-4">لا توجد اشتراكات مالية حالياً</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="card section-card h-100">
                            <div class="card-header section-header-soft py-3 px-4 d-flex align-items-center justify-content-between">
                                <h6 class="mb-0 fw-bold text-success"><i class="ti ti-cash-banknote me-2"></i>آخر الدفعات المسجلة</h6>
                                <span class="badge bg-light text-success border">{{ $stats['payments'] ?? 0 }} دفعة</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table styled-table table-hover align-middle mb-0">
                                    <thead>
                                    <tr>
                                        <th>الإيصال</th>
                                        <th>التاريخ</th>
                                        <th>المبلغ</th>
                                        <th>ملاحظات</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($profile['payments'] ?? [] as $payment)
                                        <tr>
                                            <td class="fw-semibold">{{ $payment['receipt'] }}</td>
                                            <td>{{ $payment['date'] }}</td>
                                            <td class="text-success fw-semibold">{{ $payment['amount'] }}</td>
                                            <td class="text-muted small">{{ $payment['notes'] }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted py-4">لا توجد دفعات مسجلة</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-xl-6">
                        <div class="card section-card h-100">
                            <div class="card-header section-header-soft py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <h6 class="mb-0 fw-bold text-success"><i class="ti ti-chart-line me-2"></i>آخر المتابعات التعليمية</h6>
                                <div class="d-flex gap-2">
                                    @can('student-progress-logs.view')
                                        <a href="{{ route('admin.student-progress-logs.show', $student) }}" class="btn btn-sm btn-light border">
                                            <i class="ti ti-history me-1"></i>السجل الكامل
                                        </a>
                                    @endcan
                                    @can('student-progress-logs.create')
                                        <a href="{{ route('admin.student-progress-logs.create', $progressQuickAddParams) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="ti ti-plus me-1"></i>إضافة متابعة
                                        </a>
                                    @endcan
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table styled-table table-hover align-middle mb-0">
                                    <thead>
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>الحفظ</th>
                                        <th>المراجعة</th>
                                        <th>التجويد</th>
                                        <th>الالتزام</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($profile['progress_logs'] ?? [] as $log)
                                        <tr>
                                            <td>{{ $log['date'] }}</td>
                                            <td>{{ $log['memorization'] ?: '-' }}</td>
                                            <td>{{ $log['revision'] ?: '-' }}</td>
                                            <td>{{ $log['tajweed'] ?: '-' }}</td>
                                            <td>
                                                <span class="badge {{ $log['commitment'] === 'ملتزم' ? 'bg-success' : 'bg-warning text-dark' }}">
                                                    {{ $log['commitment'] ?: '-' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-4">لا توجد متابعات تعليمية مسجلة</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="card section-card h-100">
                            <div class="card-header section-header-soft py-3 px-4 d-flex align-items-center justify-content-between">
                                <h6 class="mb-0 fw-bold text-success"><i class="ti ti-test-pipe me-2"></i>آخر الاختبارات والتقييمات</h6>
                                <span class="badge bg-light text-success border">متوسط {{ $assessmentAvg !== null ? $assessmentAvg.'%' : '-' }}</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table styled-table table-hover align-middle mb-0">
                                    <thead>
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>النوع</th>
                                        <th>المعلم</th>
                                        <th>المتوسط</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($profile['assessments'] ?? [] as $assessment)
                                        <tr>
                                            <td>{{ $assessment['date'] }}</td>
                                            <td>{{ $assessment['type'] }}</td>
                                            <td>{{ $assessment['teacher'] }}</td>
                                            <td>
                                                @if($assessment['average'] !== null)
                                                    <span class="badge {{ $assessment['average_badge'] }}">{{ $assessment['average'] }}%</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted py-4">لا توجد اختبارات مسجلة لهذا الطالب</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card section-card">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h6 class="mb-1 fw-bold text-success">إجراءات سريعة على ملف الطالب</h6>
                            <p class="mb-0 text-muted small">إدارة الملف، المتابعة التعليمية، والبوابة الخاصة بالطالب من مكان واحد.</p>
                        </div>

                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('admin.students.index') }}" class="btn btn-light">
                                <i class="ti ti-arrow-right me-1"></i>العودة إلى قائمة الطلاب
                            </a>
                            @can('student-progress-logs.create')
                                <a href="{{ route('admin.student-progress-logs.create', $progressQuickAddParams) }}" class="btn btn-outline-primary">
                                    <i class="ti ti-plus me-1"></i>إضافة متابعة تعليمية
                                </a>
                            @endcan
                            @can('students.update')
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#portalPasswordModal">
                                    <i class="ti ti-key me-1"></i>تعيين كلمة مرور البوابة
                                </button>
                                <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-primary">
                                    <i class="ti ti-pencil me-1"></i>تعديل الطالب
                                </a>
                            @endcan
                            @can('students.delete')
                                <form action="{{ route('admin.students.destroy', $student) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا الطالب؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="ti ti-trash me-1"></i>حذف الطالب
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>

                @can('students.update')
                    <div class="modal fade" id="portalPasswordModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow">
                                <form action="{{ route('admin.students.set-portal-password', $student) }}" method="POST">
                                    @csrf
                                    <div class="modal-header bg-light border-0">
                                        <h5 class="modal-title fw-bold text-success">
                                            <i class="ti ti-key me-2"></i>تعيين كلمة مرور بوابة الطالب
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <p class="text-muted small mb-3">
                                            سيستخدم الطالب <strong>{{ $student->full_name }}</strong> رقم هاتفه
                                            <code>{{ $student->phone }}</code> مع كلمة المرور الجديدة للدخول إلى
                                            <a href="{{ route('student.login') }}" target="_blank">بوابة الطلاب</a>.
                                        </p>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">كلمة المرور الجديدة <span class="text-danger">*</span></label>
                                            <input type="password" name="portal_password" class="form-control"
                                                   placeholder="6 أحرف على الأقل" required minlength="6">
                                        </div>
                                        <div>
                                            <label class="form-label fw-semibold">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                                            <input type="password" name="portal_password_confirmation" class="form-control"
                                                   placeholder="أعد كتابة كلمة المرور" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 bg-light">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                                        <button type="submit" class="btn btn-success">
                                            <i class="ti ti-check me-1"></i>حفظ كلمة المرور
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endcan
            </div>
        </div>
    </div>
@endsection

