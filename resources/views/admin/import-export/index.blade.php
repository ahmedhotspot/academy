@extends('admin.layouts.master')

@section('title', 'الاستيراد والتصدير')

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'استيراد وتصدير البيانات',
                    'breadcrumbs' => $breadcrumbs,
                ])

                @include('admin.partials.alerts')

                <div class="row g-4">

                    {{-- استيراد الطلاب --}}
                    <div class="col-xl-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-file-import me-1 text-primary"></i> استيراد بيانات الطلاب</h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-3">
                                    قم برفع ملف Excel يحتوي على بيانات الطلاب.
                                    يجب أن يحتوي الملف على الأعمدة التالية:
                                </p>
                                <div class="bg-light rounded p-3 mb-3">
                                    <code class="small">
                                        الاسم_الكامل | العمر | الجنسية | رقم_الهوية | الهاتف | الواتساب | الفرع
                                    </code>
                                </div>

                                <form action="{{ route('admin.import-export.students.import') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">اختر ملف Excel <span class="text-danger">*</span></label>
                                        <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv">
                                        @error('file')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-upload me-1"></i> استيراد
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- تصدير الطلاب --}}
                    <div class="col-xl-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-file-export me-1 text-success"></i> تصدير بيانات الطلاب</h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-3">
                                    تصدير جميع بيانات الطلاب إلى ملف Excel.
                                </p>
                                <div class="bg-light rounded p-3 mb-3">
                                    <p class="small mb-1 fw-semibold">سيحتوي الملف على:</p>
                                    <ul class="small mb-0">
                                        <li>الاسم الكامل</li>
                                        <li>العمر، الجنسية، رقم الهوية</li>
                                        <li>الهاتف، الواتساب</li>
                                        <li>الفرع، الحالة</li>
                                    </ul>
                                </div>
                                <a href="{{ route('admin.import-export.students.export') }}" class="btn btn-success">
                                    <i class="ti ti-download me-1"></i> تصدير إلى Excel
                                </a>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection

