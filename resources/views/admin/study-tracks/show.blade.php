@extends('admin.layouts.master')

@section('title', 'إدارة المسارات - عرض')

@section('css')
    <style>
        .study-track-profile-table th,
        .study-track-profile-table td {
            text-align: right !important;
        }
    </style>
@endsection

@section('content')
    @php
        $stats = $profile['stats'] ?? [];
    @endphp

    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">
                @include('admin.partials.page-header', [
                    'title' => 'الملف الشامل للمسار',
                    'breadcrumbs' => $breadcrumbs,
                    'actions' => [
                        [
                            'title' => 'تعديل',
                            'url' => route('admin.study-tracks.edit', $studyTrack),
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
                                <p class="text-muted mb-1 small">بيانات المسار</p>
                                <h4 class="fw-bold mb-1">{{ $studyTrack->name }}</h4>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="badge {{ $studyTrack->status_badge_class }}">{{ $studyTrack->status_label }}</span>
                                    <span class="badge bg-light text-dark border">رقم المسار: #{{ $studyTrack->id }}</span>
                                    <span class="badge bg-light text-dark border">تاريخ الإنشاء: {{ optional($studyTrack->created_at)->format('Y-m-d') }}</span>
                                </div>
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">الحلقات: {{ $stats['groups_count'] ?? 0 }}</span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">المعلمون: {{ $stats['teachers_count'] ?? 0 }}</span>
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2">الطلاب: {{ $stats['students_count'] ?? 0 }}</span>
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2">المتابعات الأسبوعية: {{ $stats['progress_this_week'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-xl-7">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-book-2 me-1"></i> الحلقات المرتبطة بالمسار</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 text-end study-track-profile-table">
                                    <thead class="table-light">
                                    <tr>
                                        <th>الحلقة</th>
                                        <th>الفرع</th>
                                        <th>المعلم</th>
                                        <th>المستوى</th>
                                        <th>الطلاب</th>
                                        <th>الحالة</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($profile['groups'] ?? [] as $group)
                                        <tr>
                                            <td class="fw-semibold">{{ $group['name'] }}</td>
                                            <td>{{ $group['branch'] }}</td>
                                            <td>{{ $group['teacher'] }}</td>
                                            <td>{{ $group['level'] }}</td>
                                            <td>{{ $group['students_count'] }}</td>
                                            <td><span class="badge {{ $group['status_badge'] }}">{{ $group['status'] }}</span></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted py-3">لا توجد حلقات مرتبطة</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-5">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-school me-1"></i> المعلمون المرتبطون</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 text-end study-track-profile-table">
                                    <thead class="table-light">
                                    <tr>
                                        <th>المعلم</th>
                                        <th>الهاتف</th>
                                        <th>عدد الحلقات</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($profile['teachers'] ?? [] as $teacher)
                                        <tr>
                                            <td class="fw-semibold">{{ $teacher['name'] }}</td>
                                            <td>{{ $teacher['phone'] }}</td>
                                            <td>{{ $teacher['groups_count'] }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center text-muted py-3">لا يوجد معلمون مرتبطون</td></tr>
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
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-users me-1"></i> الطلاب المرتبطون</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 text-end study-track-profile-table">
                                    <thead class="table-light">
                                    <tr>
                                        <th>الطالب</th>
                                        <th>الهاتف</th>
                                        <th>الحالة</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($profile['students'] ?? [] as $student)
                                        <tr>
                                            <td class="fw-semibold">{{ $student['name'] }}</td>
                                            <td>{{ $student['phone'] }}</td>
                                            <td><span class="badge {{ $student['status_badge'] }}">{{ $student['status'] }}</span></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center text-muted py-3">لا يوجد طلاب مرتبطون</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-progress me-1"></i> آخر المتابعات التعليمية</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 text-end study-track-profile-table">
                                    <thead class="table-light">
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>الطالب</th>
                                        <th>الحلقة</th>
                                        <th>الحفظ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($profile['recent_progress'] ?? [] as $row)
                                        <tr>
                                            <td>{{ $row['date'] }}</td>
                                            <td class="fw-semibold">{{ $row['student'] }}</td>
                                            <td>{{ $row['group'] }}</td>
                                            <td>{{ $row['memorization'] }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted py-3">لا توجد متابعات حديثة</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-semibold"><i class="ti ti-test-pipe me-1"></i> آخر الاختبارات</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-end study-track-profile-table">
                            <thead class="table-light">
                            <tr>
                                <th>التاريخ</th>
                                <th>الطالب</th>
                                <th>الحلقة</th>
                                <th>النوع</th>
                                <th>المتوسط</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($profile['recent_assessments'] ?? [] as $row)
                                <tr>
                                    <td>{{ $row['date'] }}</td>
                                    <td class="fw-semibold">{{ $row['student'] }}</td>
                                    <td>{{ $row['group'] }}</td>
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
                                <tr><td colspan="5" class="text-center text-muted py-3">لا توجد اختبارات حديثة</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

