@extends('admin.layouts.master')

@section('title', 'إدارة الحلقات - عرض')

@section('content')
    @php
        $stats = $profile['stats'] ?? [];
        $groupScheduleCreateUrl = route('admin.group-schedules.create', ['group_id' => $group->id]);
    @endphp

    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">
                @include('admin.partials.page-header', [
                    'title' => 'الملف الشامل للحلقة',
                    'breadcrumbs' => $breadcrumbs,
                    'actions' => array_values(array_filter([
                        auth()->user()?->can('group-schedules.create') ? [
                            'title' => 'إضافة جدول للحلقة',
                            'url' => $groupScheduleCreateUrl,
                            'icon' => 'ti ti-calendar-plus',
                            'class' => 'btn-outline-primary',
                        ] : null,
                        [
                            'title' => 'تعديل',
                            'url' => route('admin.groups.edit', $group),
                            'icon' => 'ti ti-edit',
                            'class' => 'btn-primary',
                        ],
                    ])),
                ])

                @include('admin.partials.alerts')

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div>
                                <p class="text-muted mb-1 small">بيانات الحلقة الأساسية</p>
                                <h4 class="fw-bold mb-1">{{ $group->name }}</h4>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="badge {{ $group->status_badge_class }}">{{ $group->status_label }}</span>
                                    <span class="badge bg-light text-dark border">الفرع: {{ $group->branch?->name ?? '-' }}</span>
                                    <span class="badge bg-light text-dark border">المعلم: {{ $group->teacher?->name ?? '-' }}</span>
                                    <span class="badge bg-light text-dark border">المستوى/المسار: {{ $group->studyLevel?->name ?? '-' }} / {{ $group->studyTrack?->name ?? '-' }}</span>
                                    <span class="badge bg-light text-dark border">النوع: {{ $group->type_label }}</span>
                                    <span class="badge bg-light text-dark border">النظام: {{ $group->schedule_type_label }}</span>
                                </div>
                            </div>

                            <div class="d-flex gap-2 flex-wrap">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">إجمالي المسجلين: {{ $stats['total_enrollments'] ?? 0 }}</span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">المسجلون النشطون: {{ $stats['active_enrollments'] ?? 0 }}</span>
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2">الجداول: {{ $stats['schedules_count'] ?? 0 }}</span>
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2">المتأخرات: {{ $stats['overdue_students'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-xl-4 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">المتابعات هذا الأسبوع</p>
                                <h4 class="fw-bold text-primary mb-0">{{ $stats['progress_this_week'] ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">الاختبارات هذا الشهر</p>
                                <h4 class="fw-bold text-info mb-0">{{ $stats['assessments_this_month'] ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">طلاب متأخرون ماليًا</p>
                                <h4 class="fw-bold text-warning mb-0">{{ $stats['overdue_students'] ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-xl-7">
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
                                        <th>تاريخ الانضمام</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($profile['students'] ?? [] as $student)
                                        <tr>
                                            <td class="fw-semibold">{{ $student['name'] }}</td>
                                            <td>{{ $student['phone'] }}</td>
                                            <td><span class="badge {{ $student['student_badge'] }}">{{ $student['student_status'] }}</span></td>
                                            <td><span class="badge {{ $student['enrollment_badge'] }}">{{ $student['enrollment_status'] }}</span></td>
                                            <td>{{ $student['joined_at'] }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-3">لا يوجد طلاب مرتبطون بالحلقة</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-5">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between gap-2">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-calendar-event me-1"></i> جداول الحلقة</h6>
                                @can('group-schedules.create')
                                    <a href="{{ $groupScheduleCreateUrl }}" class="btn btn-sm btn-outline-primary">
                                        <i class="ti ti-plus me-1"></i> إضافة جدول
                                    </a>
                                @endcan
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
                                    @forelse($profile['schedules'] ?? [] as $schedule)
                                        <tr>
                                            <td>{{ $schedule['day_name'] }}</td>
                                            <td>{{ $schedule['start_time'] }}</td>
                                            <td>{{ $schedule['end_time'] }}</td>
                                            <td><span class="badge {{ $schedule['status_badge'] }}">{{ $schedule['status_label'] }}</span></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted py-3">لا توجد جداول للحلقة</td></tr>
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
                                        <th>الطالب</th>
                                        <th>الحفظ</th>
                                        <th>المراجعة</th>
                                        <th>الإتقان</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($profile['recent_progress'] ?? [] as $row)
                                        <tr>
                                            <td>{{ $row['date'] }}</td>
                                            <td class="fw-semibold">{{ $row['student'] }}</td>
                                            <td>{{ $row['memorization'] }}</td>
                                            <td>{{ $row['revision'] }}</td>
                                            <td>{{ $row['mastery'] }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-3">لا توجد متابعات حديثة</td></tr>
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
                                        <th>الطالب</th>
                                        <th>النوع</th>
                                        <th>المتوسط</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($profile['recent_assessments'] ?? [] as $row)
                                        <tr>
                                            <td>{{ $row['date'] }}</td>
                                            <td class="fw-semibold">{{ $row['student'] }}</td>
                                            <td>{{ $row['type'] }}</td>
                                            <td>
                                                @if($row['average'] !== null)
                                                    <span class="badge {{ $row['average_badge'] }}">{{ $row['average'] }}%</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted py-3">لا توجد اختبارات حديثة</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-warning bg-opacity-10 border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-semibold text-warning-emphasis"><i class="ti ti-alert-triangle me-1"></i> تنبيه المتأخرات المرتبطة بالحلقة</h6>
                        <span class="badge bg-warning text-dark rounded-pill">{{ $stats['overdue_students'] ?? 0 }} طالب</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>الطالب</th>
                                <th>الهاتف</th>
                                <th>المتبقي</th>
                                <th>الحالة</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($profile['overdue_students'] ?? [] as $row)
                                <tr>
                                    <td class="fw-semibold">{{ $row['student'] }}</td>
                                    <td>{{ $row['phone'] }}</td>
                                    <td class="text-danger fw-semibold">{{ $row['remaining'] }}</td>
                                    <td><span class="badge {{ $row['status_badge'] }}">{{ $row['status'] }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-3">لا توجد متأخرات حالية</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <a href="{{ route('admin.groups.index') }}" class="btn btn-light">
                            <i class="ti ti-arrow-right me-1"></i> العودة إلى قائمة الحلقات
                        </a>

                        <div class="d-flex gap-2">
                            @can('group-schedules.create')
                                <a href="{{ $groupScheduleCreateUrl }}" class="btn btn-outline-primary">
                                    <i class="ti ti-calendar-plus me-1"></i> إضافة جدول للحلقة
                                </a>
                            @endcan
                            @can('groups.update')
                                <a href="{{ route('admin.groups.edit', $group) }}" class="btn btn-primary">
                                    <i class="ti ti-pencil me-1"></i> تعديل الحلقة
                                </a>
                            @endcan
                            @can('groups.delete')
                                <form action="{{ route('admin.groups.destroy', $group) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('هل تريد حذف هذه الحلقة؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="ti ti-trash me-1"></i> حذف الحلقة
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

