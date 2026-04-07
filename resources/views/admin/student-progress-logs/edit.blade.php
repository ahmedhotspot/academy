@extends('admin.layouts.master')

@section('title', 'المتابعة التعليمية — تعديل السجل')

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'تعديل سجل المتابعة التعليمية',
                    'breadcrumbs' => $breadcrumbs,
                ])

                @include('admin.partials.alerts')

                <form action="{{ route('admin.student-progress-logs.update', $log) }}"
                      method="POST" autocomplete="off">
                    @csrf
                    @method('PUT')

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <i class="ti ti-pencil text-primary fs-5"></i>
                                <h6 class="mb-0 fw-semibold">تعديل بيانات المتابعة</h6>
                            </div>
                            <span class="badge bg-light text-dark border">
                                رقم السجل: #{{ $log->id }}
                                &nbsp;|&nbsp;
                                {{ optional($log->progress_date)->format('Y-m-d') }}
                            </span>
                        </div>
                        <div class="card-body">
                            @include('admin.student-progress-logs._form', [
                                'log'                => $log,
                                'groupOptions'       => $groupOptions,
                                'teacherOptions'     => $teacherOptions,
                                'evaluationLevels'   => $evaluationLevels,
                                'commitmentStatuses' => $commitmentStatuses,
                                'currentStudents'    => $currentStudents,
                            ])
                        </div>
                        <div class="card-footer bg-white border-top d-flex gap-2 justify-content-end">
                            <a href="{{ route('admin.student-progress-logs.show', $log->student_id) }}"
                               class="btn btn-light">
                                <i class="ti ti-arrow-right me-1"></i> رجوع لسجل الطالب
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy me-1"></i> حفظ التعديلات
                            </button>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection

