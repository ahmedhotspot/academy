@extends('admin.layouts.master')

@section('title', 'حساب مستحق جديد')

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'حساب مستحق جديد',
                    'breadcrumbs' => $breadcrumbs,
                ])

                @include('admin.partials.alerts')

                <form action="{{ route('admin.teacher-payrolls.store') }}" method="POST" autocomplete="off">
                    @csrf

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom d-flex align-items-center gap-2">
                            <i class="ti ti-wallet text-primary fs-5"></i>
                            <h6 class="mb-0 fw-semibold">بيانات الحساب</h6>
                        </div>
                        <div class="card-body">
                            @include('admin.teacher-payrolls._form', [
                                'teacherOptions' => $teacherOptions,
                                'currentYear'    => $currentYear,
                                'currentMonth'   => $currentMonth,
                            ])
                        </div>
                        <div class="card-footer bg-white border-top d-flex gap-2 justify-content-end">
                            <a href="{{ route('admin.teacher-payrolls.index') }}"
                               class="btn btn-light">
                                <i class="ti ti-arrow-right me-1"></i> رجوع
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy me-1"></i> حفظ الحساب
                            </button>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection

