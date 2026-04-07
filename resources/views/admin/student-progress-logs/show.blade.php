@extends('admin.layouts.master')

@section('title', 'سجل المتابعة التعليمية — ' . $student->full_name)

@section('content')
    @php
        $quickAddParams = ['student_id' => $student->id];
        if (!empty($report['lastLog']?->group_id)) {
            $quickAddParams['group_id'] = $report['lastLog']->group_id;
        }
    @endphp

    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'تقرير المتابعة التعليمية للطالب',
                    'breadcrumbs' => $breadcrumbs,
                    'actions' => array_values(array_filter([
                        auth()->user()?->can('student-progress-logs.create') ? [
                            'title' => 'تسجيل جلسة جديدة',
                            'url' => route('admin.student-progress-logs.create', $quickAddParams),
                            'icon' => 'ti ti-plus',
                            'class' => 'btn-primary',
                        ] : null,
                        auth()->user()?->can('students.view') ? [
                            'title' => 'ملف الطالب',
                            'url' => route('admin.students.show', $student),
                            'icon' => 'ti ti-user',
                            'class' => 'btn-outline-secondary',
                        ] : null,
                    ])),
                ])

                @include('admin.partials.alerts')

                {{-- ══════════════════════════════════════════════════════════
                     بطاقة هوية الطالب
                ══════════════════════════════════════════════════════════ --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row align-items-center g-3">
                            <div class="col-auto">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center
                                            justify-content-center" style="width:64px;height:64px;">
                                    <i class="ti ti-user fs-2 text-primary"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h5 class="mb-1 fw-bold">{{ $student->full_name }}</h5>
                                <div class="d-flex flex-wrap gap-3 text-muted small">
                                    @if($student->branch)
                                        <span><i class="ti ti-building me-1"></i>{{ $student->branch->name }}</span>
                                    @endif
                                    @if($student->age)
                                        <span><i class="ti ti-calendar me-1"></i>{{ $student->age }} سنة</span>
                                    @endif
                                    @if($student->phone)
                                        <span><i class="ti ti-phone me-1"></i>{{ $student->phone }}</span>
                                    @endif
                                    <span>
                                        <i class="ti ti-notebook me-1"></i>
                                        {{ $report['total'] }} جلسة مسجّلة
                                    </span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="d-flex gap-2">
                                    @can('students.view')
                                        <a href="{{ route('admin.students.show', $student) }}" class="btn btn-light btn-sm">
                                            <i class="ti ti-user me-1"></i> ملف الطالب
                                        </a>
                                    @endcan
                                    @can('student-progress-logs.create')
                                        <a href="{{ route('admin.student-progress-logs.create', $quickAddParams) }}"
                                           class="btn btn-primary btn-sm">
                                            <i class="ti ti-plus me-1"></i> تسجيل جلسة جديدة
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════════════
                     بطاقات الإحصائيات
                ══════════════════════════════════════════════════════════ --}}
                <div class="row g-3 mb-4">

                    <div class="col-xl-3 col-sm-6">
                        <div class="card border-0 shadow-sm h-100 border-start border-4 border-primary">
                            <div class="card-body">
                                <p class="text-muted small mb-1">إجمالي الجلسات</p>
                                <h3 class="fw-bold mb-0 text-primary">{{ $report['total'] }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-sm-6">
                        <div class="card border-0 shadow-sm h-100 border-start border-4 border-success">
                            <div class="card-body">
                                <p class="text-muted small mb-1">نسبة الالتزام</p>
                                <h3 class="fw-bold mb-0 text-success">{{ $report['commitmentRate'] }}%</h3>
                                <p class="text-muted small mb-0">
                                    {{ $report['committed'] }} ملتزم /
                                    {{ $report['late'] }} متأخر
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-sm-6">
                        <div class="card border-0 shadow-sm h-100 border-start border-4 border-info">
                            <div class="card-body">
                                <p class="text-muted small mb-1">مستوى الإتقان الغالب</p>
                                @php
                                    $masteryBadge = match($report['dominantMastery']) {
                                        'ممتاز'    => 'bg-success',
                                        'جيد جداً' => 'bg-primary',
                                        'جيد'      => 'bg-info text-dark',
                                        'مقبول'    => 'bg-warning text-dark',
                                        'ضعيف'     => 'bg-danger',
                                        default    => 'bg-secondary',
                                    };
                                @endphp
                                <h3 class="fw-bold mb-0">
                                    <span class="badge {{ $masteryBadge }} fs-6">
                                        {{ $report['dominantMastery'] }}
                                    </span>
                                </h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-sm-6">
                        <div class="card border-0 shadow-sm h-100 border-start border-4 border-warning">
                            <div class="card-body">
                                <p class="text-muted small mb-1">آخر جلسة مسجّلة</p>
                                @if($report['lastLog'])
                                    <h6 class="fw-bold mb-1">
                                        {{ optional($report['lastLog']->progress_date)->format('Y-m-d') }}
                                    </h6>
                                    <p class="text-muted small mb-0">
                                        حفظ: {{ $report['lastLog']->memorization_amount }}
                                    </p>
                                @else
                                    <p class="text-muted small mb-0">لا يوجد سجل</p>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ══════════════════════════════════════════════════════════
                     شريط التقدم — نسبة الالتزام بصريًا
                ══════════════════════════════════════════════════════════ --}}
                @if($report['total'] > 0)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-semibold small">نسبة الالتزام الكلي</span>
                                <span class="fw-bold text-success">{{ $report['commitmentRate'] }}%</span>
                            </div>
                            <div class="progress" style="height:10px;">
                                <div class="progress-bar bg-success" role="progressbar"
                                     style="width: {{ $report['commitmentRate'] }}%"
                                     aria-valuenow="{{ $report['commitmentRate'] }}"
                                     aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- ══════════════════════════════════════════════════════════
                     جدول السجلات الكامل
                ══════════════════════════════════════════════════════════ --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-semibold">
                            <i class="ti ti-history me-1 text-primary"></i>
                            السجل الكامل للمتابعة التعليمية
                        </h6>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-secondary rounded-pill">{{ $report['total'] }} سجل</span>
                            @can('student-progress-logs.create')
                                <a href="{{ route('admin.student-progress-logs.create', $quickAddParams) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="ti ti-plus me-1"></i> إضافة متابعة
                                </a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($report['logs']->isEmpty())
                            <div class="text-center text-muted py-5">
                                <i class="ti ti-notebook-off fs-1 d-block mb-2 opacity-50"></i>
                                لا يوجد سجل متابعة لهذا الطالب حتى الآن.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>الحلقة</th>
                                        <th>المعلم</th>
                                        <th>الحفظ</th>
                                        <th>المراجعة</th>
                                        <th>التجويد</th>
                                        <th>التدبر</th>
                                        <th>الإتقان</th>
                                        <th>الالتزام</th>
                                        <th>الأخطاء / الملاحظات</th>
                                        <th>العمليات</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($report['logs'] as $log)
                                        <tr>
                                            <td class="fw-semibold text-nowrap">
                                                {{ optional($log->progress_date)->format('Y-m-d') }}
                                            </td>
                                            <td>{{ $log->group?->name ?? '-' }}</td>
                                            <td>{{ $log->teacher?->name ?? '-' }}</td>
                                            <td>{{ $log->memorization_amount }}</td>
                                            <td>{{ $log->revision_amount }}</td>
                                            <td>
                                                <span class="badge {{ $log->tajweed_badge_class }}">
                                                    {{ $log->tajweed_evaluation }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $log->tadabur_badge_class }}">
                                                    {{ $log->tadabbur_evaluation }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $log->mastery_badge_class }}">
                                                    {{ $log->mastery_level }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $log->commitment_badge_class }}">
                                                    {{ $log->commitment_status }}
                                                </span>
                                            </td>
                                            <td class="text-muted small" style="max-width:220px;">
                                                @if($log->repeated_mistakes)
                                                    <div class="text-danger small mb-1">
                                                        <i class="ti ti-alert-triangle me-1"></i>
                                                        {{ $log->repeated_mistakes }}
                                                    </div>
                                                @endif
                                                {{ $log->notes ?: '-' }}
                                            </td>
                                            <td>
                                                @can('student-progress-logs.update')
                                                    <a href="{{ route('admin.student-progress-logs.edit', $log) }}"
                                                       class="btn btn-sm btn-outline-primary" title="تعديل">
                                                        <i class="ti ti-pencil"></i>
                                                    </a>
                                                @endcan
                                                @can('student-progress-logs.delete')
                                                    <form method="POST"
                                                          action="{{ route('admin.student-progress-logs.destroy', $log) }}"
                                                          onsubmit="return confirm('هل تريد حذف هذا السجل؟')"
                                                          class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="btn btn-sm btn-outline-danger" title="حذف">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

