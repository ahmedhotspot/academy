@extends('admin.layouts.master')

@section('title', 'إدارة حضور وغياب المعلمين - إضافة')

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">
                @include('admin.partials.page-header', [
                    'title' => 'تسجيل حضور معلم',
                    'breadcrumbs' => $breadcrumbs,
                ])

                @include('admin.partials.alerts')

                <form action="{{ route('admin.teacher-attendances.store') }}" method="POST">
                    @csrf
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-semibold">بيانات الحضور</h6>
                        </div>
                        <div class="card-body">
                            @include('admin.teacher-attendances._form')
                        </div>
                        <div class="card-footer bg-white border-top d-flex gap-2 justify-content-end">
                            <a href="{{ route('admin.teacher-attendances.index') }}" class="btn btn-light">رجوع</a>
                            <button type="submit" class="btn btn-primary">حفظ السجل</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

