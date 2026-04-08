@extends('admin.layouts.master')

@section('title', 'نظام الاختبارات — ' . $student->full_name)

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'تقرير الاختبارات للطالب',
                    'breadcrumbs' => $breadcrumbs,
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
                                    <span><i class="ti ti-list-check me-1"></i>{{ $report['total'] }} اختبار</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                @can('assessments.create')
                                    <a href="{{ route('admin.assessments.create') }}" class="btn btn-primary btn-sm">
                                        <i class="ti ti-plus me-1"></i> اختبار جديد
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════════════
                     بطاقات الإحصائيات
                ══════════════════════════════════════════════════════════ --}}
                <div class="row g-3 mb-4">

                    <div class="col-xl col-sm-6">
                        <div class="card border-0 shadow-sm h-100 border-start border-4 border-primary">
                            <div class="card-body">
                                <p class="text-muted small mb-1">إجمالي الاختبارات</p>
                                <h3 class="fw-bold mb-0 text-primary">{{ $report['total'] }}</h3>
                                <div class="d-flex gap-2 mt-2 text-muted small">
                                    <span><i class="ti ti-calendar-week"></i>أسبوعي: {{ $report['byType']['أسبوعي'] }}</span>
                                    <span><i class="ti ti-calendar-month"></i>شهري: {{ $report['byType']['شهري'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl col-sm-6">
                        <div class="card border-0 shadow-sm h-100 border-start border-4 border-success">
                            <div class="card-body">
                                <p class="text-muted small mb-1">التدبر </p>
                                @if(!is_null($report['avgMemoization']))
                                    <h3 class="fw-bold mb-0">
                                        <span class="text-success">{{ $report['avgMemoization'] }}</span>
                                        <span class="text-muted small fw-normal">/100</span>
                                    </h3>
                                @else
                                    <p class="text-muted">—</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-xl col-sm-6">
                        <div class="card border-0 shadow-sm h-100 border-start border-4 border-info">
                            <div class="card-body">
                                <p class="text-muted small mb-1">متوسط التجويد</p>
                                @if(!is_null($report['avgTajweed']))
                                    <h3 class="fw-bold mb-0">
                                        <span class="text-info">{{ $report['avgTajweed'] }}</span>
                                        <span class="text-muted small fw-normal">/100</span>
                                    </h3>
                                @else
                                    <p class="text-muted">—</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-xl col-sm-6">
                        <div class="card border-0 shadow-sm h-100 border-start border-4 border-secondary">
                            <div class="card-body">
                                <p class="text-muted small mb-1">متوسط التدبر</p>
                                @if(!is_null($report['avgTadabbur']))
                                    <h3 class="fw-bold mb-0">
                                        <span class="text-secondary">{{ $report['avgTadabbur'] }}</span>
                                        <span class="text-muted small fw-normal">/100</span>
                                    </h3>
                                @else
                                    <p class="text-muted">—</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-xl col-sm-6">
                        <div class="card border-0 shadow-sm h-100 border-start border-4 border-warning">
                            <div class="card-body">
                                <p class="text-muted small mb-1">أفضل / أضعف نتيجة</p>
                                <div class="d-flex align-items-center gap-2">
                                    @if(!is_null($report['bestScore']))
                                        <span class="badge bg-success">{{ $report['bestScore'] }}</span>
                                    @endif
                                    @if(!is_null($report['worstScore']))
                                        <span class="badge bg-danger">{{ $report['worstScore'] }}</span>
                                    @endif
                                    @if(is_null($report['bestScore']) && is_null($report['worstScore']))
                                        <p class="text-muted small">—</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ══════════════════════════════════════════════════════════
                     جدول الاختبارات الكامل
                ══════════════════════════════════════════════════════════ --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-semibold">
                            <i class="ti ti-history me-1 text-primary"></i>
                            السجل الكامل للاختبارات
                        </h6>
                        <span class="badge bg-secondary rounded-pill">{{ $report['total'] }} اختبار</span>
                    </div>
                    <div class="card-body p-0">
                        @if($report['assessments']->isEmpty())
                            <div class="text-center text-muted py-5">
                                <i class="ti ti-notebook-off fs-1 d-block mb-2 opacity-50"></i>
                                لا توجد اختبارات مسجّلة لهذا الطالب حتى الآن.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>النوع</th>
                                        <th>الحلقة</th>
                                        <th>المعلم</th>
                                        <th>الحفظ</th>
                                        <th>التجويد</th>
                                        <th>التدبر</th>
                                        <th>الملاحظات</th>
                                        <th>العمليات</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($report['assessments'] as $assessment)
                                        <tr>
                                            <td class="fw-semibold text-nowrap">
                                                {{ optional($assessment->assessment_date)->format('Y-m-d') }}
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    {{ $assessment->type }}
                                                </span>
                                            </td>
                                            <td>{{ $assessment->group?->name ?? '-' }}</td>
                                            <td>{{ $assessment->teacher?->name ?? '-' }}</td>
                                            <td>
                                                @if($assessment->memorization_result)
                                                    <span class="badge {{ $assessment->memorization_badge_class }}">
                                                        {{ $assessment->memorization_result }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($assessment->tajweed_result)
                                                    <span class="badge {{ $assessment->tajweed_badge_class }}">
                                                        {{ $assessment->tajweed_result }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(!is_null($assessment->tadabbur_result))
                                                    <span class="badge {{ $assessment->tadabur_badge_class }}">
                                                        {{ $assessment->tadabbur_result }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td class="text-muted small" style="max-width:150px;">
                                                {{ $assessment->notes ?: '-' }}
                                            </td>
                                            <td>
                                                @can('assessments.update')
                                                    <a href="{{ route('admin.assessments.edit', $assessment) }}"
                                                       class="btn btn-sm btn-outline-primary" title="تعديل">
                                                        <i class="ti ti-pencil"></i>
                                                    </a>
                                                @endcan
                                                @can('assessments.delete')
                                                    <form method="POST"
                                                          action="{{ route('admin.assessments.destroy', $assessment) }}"
                                                          onsubmit="return confirm('هل تريد حذف هذا الاختبار؟')"
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

