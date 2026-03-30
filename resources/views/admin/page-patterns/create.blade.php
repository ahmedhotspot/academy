@extends('admin.layouts.master')

@section('title', 'قوالب الصفحات - إنشاء')

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">
                @include('admin.partials.page-header', [
                    'title' => 'قوالب الصفحات - صفحة الإنشاء',
                    'breadcrumbs' => $breadcrumbs,
                ])

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-semibold">نموذج صفحة إنشاء احترافي</h6>
                    </div>
                    <div class="card-body">
                        @include('admin.page-patterns._form')
                    </div>
                    <div class="card-footer bg-white border-top d-flex gap-2 justify-content-end">
                        <a href="{{ route('admin.page-patterns.index') }}" class="btn btn-light">رجوع</a>
                        <button type="button" class="btn btn-primary">حفظ</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

