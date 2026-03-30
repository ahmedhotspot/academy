@extends('admin.layouts.master')

@section('title', 'الإعدادات العامة')

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'الإعدادات العامة',
                    'breadcrumbs' => $breadcrumbs,
                ])

                @include('admin.partials.alerts')

                <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-semibold"><i class="ti ti-building me-1"></i> بيانات المؤسسة</h6>
                        </div>
                        <div class="card-body">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">اسم المؤسسة <span class="text-danger">*</span></label>
                                    <input type="text" name="institution_name" class="form-control"
                                           value="{{ old('institution_name', $settings['institution_name']) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">هاتف التواصل</label>
                                    <input type="text" name="institution_phone" class="form-control"
                                           value="{{ old('institution_phone', $settings['institution_phone']) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">البريد الإلكتروني</label>
                                    <input type="email" name="institution_email" class="form-control"
                                           value="{{ old('institution_email', $settings['institution_email']) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">العنوان</label>
                                    <input type="text" name="institution_address" class="form-control"
                                           value="{{ old('institution_address', $settings['institution_address']) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">شعار المؤسسة</label>
                                    <input type="file" name="institution_logo" class="form-control" accept="image/*">
                                    @if($settings['institution_logo'])
                                        <div class="mt-2">
                                            <img src="{{ $settings['institution_logo'] }}" alt="الشعار" class="img-thumbnail" style="max-height:80px">
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>
                        <div class="card-footer bg-white border-top d-flex gap-2 justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy me-1"></i> حفظ الإعدادات
                            </button>
                        </div>
                    </div>

                </form>

                {{-- روابط سريعة --}}
                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center">
                                <i class="ti ti-file-import fs-2 text-primary mb-2 d-block"></i>
                                <h6 class="fw-semibold">الاستيراد والتصدير</h6>
                                <p class="text-muted small mb-3">استيراد وتصدير بيانات الطلاب</p>
                                <a href="{{ route('admin.import-export.index') }}" class="btn btn-outline-primary btn-sm">فتح</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center">
                                <i class="ti ti-database-export fs-2 text-success mb-2 d-block"></i>
                                <h6 class="fw-semibold">النسخ الاحتياطي</h6>
                                <p class="text-muted small mb-3">
                                    آخر نسخة: {{ $settings['last_backup_at'] ?? 'لم يتم بعد' }}
                                </p>
                                <a href="{{ route('admin.backup.index') }}" class="btn btn-outline-success btn-sm">فتح</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center">
                                <i class="ti ti-bell fs-2 text-warning mb-2 d-block"></i>
                                <h6 class="fw-semibold">الإشعارات</h6>
                                <p class="text-muted small mb-3">عرض وإدارة الإشعارات</p>
                                <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-warning btn-sm">فتح</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

