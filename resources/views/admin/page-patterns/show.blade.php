@extends('admin.layouts.master')

@section('title', 'قوالب الصفحات - عرض')

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">
                @include('admin.partials.page-header', [
                    'title' => 'قوالب الصفحات - صفحة العرض',
                    'breadcrumbs' => $breadcrumbs,
                    'actions' => [
                        [
                            'title' => 'تعديل',
                            'url' => route('admin.page-patterns.edit', ['id' => $record['id']]),
                            'icon' => 'ti ti-edit',
                            'class' => 'btn-primary',
                        ],
                    ],
                ])

                <div class="row g-3">
                    <div class="col-xl-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold">بيانات الصفحة</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <p class="text-muted mb-1 small">العنوان</p>
                                        <p class="fw-semibold mb-0">{{ $record['title'] }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="text-muted mb-1 small">الحالة</p>
                                        <span class="badge bg-success">{{ $record['status'] }}</span>
                                    </div>
                                    <div class="col-12">
                                        <p class="text-muted mb-1 small">الوصف</p>
                                        <p class="mb-0">{{ $record['description'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold">معلومات إضافية</h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-2"><strong>رقم السجل:</strong> {{ $record['id'] }}</p>
                                <p class="mb-2"><strong>تاريخ الإنشاء:</strong> {{ $record['created_at'] }}</p>
                                <p class="mb-0"><strong>آخر تحديث:</strong> {{ $record['updated_at'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

