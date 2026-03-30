@extends('admin.layouts.master')

@section('title', 'إضافة مصروف')

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'إضافة مصروف جديد',
                    'breadcrumbs' => $breadcrumbs,
                ])

                @include('admin.partials.alerts')

                <form action="{{ route('admin.expenses.store') }}" method="POST">
                    @csrf

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-semibold"><i class="ti ti-receipt me-1"></i> بيانات المصروف</h6>
                        </div>
                        <div class="card-body">
                            @include('admin.expenses._form')
                        </div>
                        <div class="card-footer bg-white border-top d-flex gap-2 justify-content-end">
                            <a href="{{ route('admin.expenses.index') }}" class="btn btn-light">رجوع</a>
                            <button type="submit" class="btn btn-primary">حفظ</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

