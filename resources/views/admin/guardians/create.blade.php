@extends('admin.layouts.master')

@section('title', 'إدارة أولياء الأمور - إضافة')

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">
                @include('admin.partials.page-header', [
                    'title' => 'إدارة أولياء الأمور - إضافة ولي أمر',
                    'breadcrumbs' => $breadcrumbs,
                ])

                @include('admin.partials.alerts')

                <form action="{{ route('admin.guardians.store') }}" method="POST">
                    @csrf
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-semibold">بيانات ولي الأمر</h6>
                        </div>
                        <div class="card-body">
                            @include('admin.guardians._form')
                        </div>
                        <div class="card-footer bg-white border-top d-flex gap-2 justify-content-end">
                            <a href="{{ route('admin.guardians.index') }}" class="btn btn-light">رجوع</a>
                            <button type="submit" class="btn btn-primary">حفظ ولي الأمر</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

