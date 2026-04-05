@extends('admin.layouts.master')

@section('title', 'تسجيل الطلاب في الحلقات - إضافة')

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--multiple {
            min-height: 38px;
            border: 1px solid #d9dee3;
            border-radius: 0.375rem;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            padding: 3px 10px;
        }
    </style>
@endsection

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">
                @include('admin.partials.page-header', [
                    'title' => 'تسجيل طالب/طلاب في حلقة',
                    'breadcrumbs' => $breadcrumbs,
                ])

                @include('admin.partials.alerts')

                <form action="{{ route('admin.student-enrollments.store') }}" method="POST">
                    @csrf
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-semibold">بيانات تسجيل الطلاب</h6>
                        </div>
                        <div class="card-body">
                            @include('admin.student-enrollments._form')
                        </div>
                        <div class="card-footer bg-white border-top d-flex gap-2 justify-content-end">
                            <a href="{{ route('admin.student-enrollments.index') }}" class="btn btn-light">رجوع</a>
                            <button type="submit" class="btn btn-primary">حفظ التسجيل</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        (function () {
            if (!window.jQuery || !jQuery.fn.select2) {
                return;
            }

            jQuery('.js-student-select2').select2({
                width: '100%',
                dir: 'rtl',
                placeholder: 'ابحث واختر الطلاب',
                closeOnSelect: false
            });
        })();
    </script>
@endsection

