@extends('admin.layouts.master')

@section('title', 'إدارة الفروع - إضافة')

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">
                @include('admin.partials.page-header', [
                    'title' => 'إدارة الفروع - إضافة فرع',
                    'breadcrumbs' => $breadcrumbs,
                ])

                @include('admin.partials.alerts')

                <form action="{{ route('admin.branches.store') }}" method="POST" autocomplete="off">
                    @csrf

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                            <h6 class="mb-0 fw-semibold"><i class="ti ti-building-plus me-1"></i> بيانات الفرع</h6>
                            <span class="badge bg-primary">إضافة جديدة</span>
                        </div>

                        <div class="card-body">
                            <div class="alert alert-light border mb-3" role="alert">
                                <i class="ti ti-info-circle me-1 text-primary"></i>
                                أدخل اسم الفرع وحالته، ثم احفظ لإضافته إلى النظام.
                            </div>

                            @include('admin.branches._form')
                        </div>

                        <div class="card-footer bg-white border-top d-flex gap-2 justify-content-end">
                            <a href="{{ route('admin.branches.index') }}" class="btn btn-light">
                                <i class="ti ti-arrow-right me-1"></i> رجوع
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy me-1"></i> حفظ الفرع
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

