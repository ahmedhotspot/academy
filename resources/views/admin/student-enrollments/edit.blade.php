@extends('admin.layouts.master')

@section('title', 'تسجيل الطلاب في الحلقات - تعديل')

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">
                @include('admin.partials.page-header', [
                    'title' => 'تعديل أو نقل تسجيل الطالب',
                    'breadcrumbs' => $breadcrumbs,
                ])

                @include('admin.partials.alerts')

                <div class="alert alert-info">
                    عند تغيير الحلقة سيتم نقل الطالب تلقائيًا من الحلقة الحالية إلى الحلقة الجديدة مع حفظ السجل السابق.
                </div>

                <form action="{{ route('admin.student-enrollments.update', $studentEnrollment) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                            <h6 class="mb-0 fw-semibold">بيانات التسجيل</h6>
                            <span class="badge bg-info">{{ $studentEnrollment->student?->full_name }}</span>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">الطالب</label>
                                <input type="text" class="form-control" value="{{ $studentEnrollment->student?->full_name ?? '-' }}" disabled>
                            </div>

                            @include('admin.student-enrollments._form', ['studentEnrollment' => $studentEnrollment])
                        </div>
                        <div class="card-footer bg-white border-top d-flex gap-2 justify-content-end">
                            <a href="{{ route('admin.student-enrollments.index') }}" class="btn btn-light">رجوع</a>
                            <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

