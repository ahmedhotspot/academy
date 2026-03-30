@extends('admin.layouts.master')

@section('title', 'إدارة جداول الحلقات - عرض')

@section('content')
    @php
        $group = $profile['group'] ?? [];
        $stats = $profile['stats'] ?? [];
    @endphp

    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">
                @include('admin.partials.page-header', [
                    'title' => 'الملف الشامل لجدول الحلقة',
                    'breadcrumbs' => $breadcrumbs,
                    'actions' => [
                        [
                            'title' => 'تعديل',
                            'url' => route('admin.group-schedules.edit', $groupSchedule),
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
                                <p class="text-muted mb-1 small">تفاصيل الجدول الحالي</p>
                                <h4 class="fw-bold mb-1">{{ $group['name'] ?? ($groupSchedule->group?->name ?? '-') }}</h4>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="badge {{ $group['status_badge'] ?? 'bg-secondary' }}">{{ $group['status'] ?? '-' }}</span>
                                    <span class="badge bg-light text-dark border">اليوم: {{ $groupSchedule->day_name }}</span>
                                    <span class="badge bg-light text-dark border">من: {{ substr((string) $groupSchedule->start_time, 0, 5) }}</span>
                                    <span class="badge bg-light text-dark border">إلى: {{ substr((string) $groupSchedule->end_time, 0, 5) }}</span>
                                    <span class="badge {{ $groupSchedule->status_badge_class }}">{{ $groupSchedule->status_label }}</span>
                                </div>
                            </div>

                            <div class="d-flex gap-2 flex-wrap">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">الجداول: {{ $stats['schedules_count'] ?? 0 }}</span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">الطلاب: {{ $stats['students_count'] ?? 0 }}</span>
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2">المتابعات الأسبوعية: {{ $stats['progress_this_week'] ?? 0 }}</span>
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2">اختبارات الشهر: {{ $stats['assessments_this_month'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-xl-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-book-2 me-1"></i> معلومات الحلقة المرتبطة</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <p class="text-muted small mb-1">الفرع</p>
                                        <p class="fw-semibold mb-0">{{ $group['branch'] ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="text-muted small mb-1">المعلم</p>
                                        <p class="fw-semibold mb-0">{{ $group['teacher'] ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="text-muted small mb-1">هاتف المعلم</p>
                                        <p class="fw-semibold mb-0">{{ $group['teacher_phone'] ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="text-muted small mb-1">المستوى/المسار</p>
                                        <p class="fw-semibold mb-0">{{ ($group['level'] ?? '-') . ' / ' . ($group['track'] ?? '-') }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="text-muted small mb-1">نوع الحلقة</p>
                                        <p class="fw-semibold mb-0">{{ $group['type'] ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="text-muted small mb-1">نظام الحلقة</p>
                                        <p class="fw-semibold mb-0">{{ $group['schedule_type'] ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-calendar-event me-1"></i> كل جداول الحلقة</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>اليوم</th>
                                        <th>من</th>
                                        <th>إلى</th>
                                        <th>الحالة</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($profile['sibling_schedules'] ?? [] as $schedule)
                                        <tr class="{{ $schedule['is_current'] ? 'table-primary' : '' }}">
                                            <td>{{ $schedule['day_name'] }}</td>
                                            <td>{{ $schedule['start_time'] }}</td>
                                            <td>{{ $schedule['end_time'] }}</td>
                                            <td>
                                                <span class="badge {{ $schedule['status_badge'] }}">{{ $schedule['status'] }}</span>
                                                @if($schedule['is_current'])
                                                    <span class="badge bg-primary ms-1">الحالي</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted py-3">لا توجد جداول إضافية</td></tr>
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
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-users me-1"></i> الطلاب المرتبطون بالحلقة</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>الطالب</th>
                                        <th>الهاتف</th>
                                        <th>حالة الطالب</th>
                                        <th>حالة التسجيل</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($profile['students'] ?? [] as $student)
                                        <tr>
                                            <td class="fw-semibold">{{ $student['name'] }}</td>
                                            <td>{{ $student['phone'] }}</td>
                                            <td><span class="badge {{ $student['student_badge'] }}">{{ $student['student_status'] }}</span></td>
                                            <td><span class="badge {{ $student['enrollment_badge'] }}">{{ $student['enrollment_status'] }}</span></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted py-3">لا يوجد طلاب مرتبطون</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-progress me-1"></i> آخر المتابعات والاختبارات</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="p-3 border-bottom">
                                    <h6 class="fw-semibold mb-2">أحدث المتابعات</h6>
                                    @forelse($profile['recent_progress'] ?? [] as $row)
                                        <div class="d-flex justify-content-between small py-1 border-bottom">
                                            <span>{{ $row['student'] }} ({{ $row['date'] }})</span>
                                            <span class="text-muted">حفظ: {{ $row['memorization'] }}</span>
                                        </div>
                                    @empty
                                        <div class="text-muted small">لا توجد متابعات حديثة</div>
                                    @endforelse
                                </div>

                                <div class="p-3">
                                    <h6 class="fw-semibold mb-2">أحدث الاختبارات</h6>
                                    @forelse($profile['recent_assessments'] ?? [] as $row)
                                        <div class="d-flex justify-content-between align-items-center small py-1 border-bottom">
                                            <span>{{ $row['student'] }} - {{ $row['type'] }}</span>
                                            <span>
                                                @if($row['average'] !== null)
                                                    <span class="badge {{ $row['average_badge'] }}">{{ $row['average'] }}%</span>
                                                @else
                                                    -
                                                @endif
                                            </span>
                                        </div>
                                    @empty
                                        <div class="text-muted small">لا توجد اختبارات حديثة</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

