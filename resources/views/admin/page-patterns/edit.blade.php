@extends('admin.layouts.master')

@section('title', 'قوالب الصفحات - تعديل')

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">
                @include('admin.partials.page-header', [
                    'title' => 'قوالب الصفحات - صفحة التعديل',
                    'breadcrumbs' => $breadcrumbs,
                ])

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-semibold">نموذج صفحة تعديل احترافي</h6>
                        <span class="badge bg-info">رقم {{ $record['id'] }}</span>
                    </div>
                    <div class="card-body">
                        @include('admin.page-patterns._form', ['record' => $record])
                    </div>
                    <div class="card-footer bg-white border-top d-flex gap-2 justify-content-end">
                        <a href="{{ route('admin.page-patterns.index') }}" class="btn btn-light">رجوع</a>
                        <button type="button" class="btn btn-primary">حفظ التعديلات</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

