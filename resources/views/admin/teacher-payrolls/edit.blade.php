@extends('admin.layouts.master')

@section('title', 'تعديل المستحق')

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'تعديل المستحق',
                    'breadcrumbs' => $breadcrumbs,
                ])

                @include('admin.partials.alerts')

                <form action="{{ route('admin.teacher-payrolls.update', $payroll) }}"
                      method="POST" autocomplete="off">
                    @csrf
                    @method('PUT')

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <i class="ti ti-pencil text-primary fs-5"></i>
                                <h6 class="mb-0 fw-semibold">تعديل بيانات الحساب</h6>
                            </div>
                            <span class="badge bg-light text-dark border">
                                {{ $payroll->month_year }}
                            </span>
                        </div>
                        <div class="card-body">
                            {{-- بيانات اللمس فقط --}}
                            <div class="row g-3 mb-4 pb-3 border-bottom">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">المعلم</label>
                                    <input type="text" class="form-control bg-light" readonly
                                           value="{{ $payroll->teacher?->name ?? '-' }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">الشهر</label>
                                    <input type="text" class="form-control bg-light" readonly
                                           value="{{ $payroll->month }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">السنة</label>
                                    <input type="text" class="form-control bg-light" readonly
                                           value="{{ $payroll->year }}">
                                </div>
                            </div>

                            {{-- حقول التعديل --}}
                            @include('admin.teacher-payrolls._form', [
                                'payroll'        => $payroll,
                                'teacherOptions' => [],
                            ])
                        </div>
                        <div class="card-footer bg-white border-top d-flex gap-2 justify-content-end">
                            <a href="{{ route('admin.teacher-payrolls.show', $payroll) }}"
                               class="btn btn-light">
                                <i class="ti ti-arrow-right me-1"></i> رجوع
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

