@extends('admin.layouts.master')

@section('title', 'تعديل بيانات الطالب')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .form-card-header {
        background: linear-gradient(135deg, #1565C0 0%, #1976D2 100%);
        border-radius: 12px 12px 0 0;
        padding: 20px 24px;
        color: #fff;
    }
    .form-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,.08);
        overflow: hidden;
    }
    .form-control:focus, .form-select:focus {
        border-color: #43A047;
        box-shadow: 0 0 0 3px rgba(27,94,32,.12);
    }
    .select2-container .select2-selection--single {
        min-height: 38px;
        border: 1px solid #d9dee3;
        border-radius: 0.375rem;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
        padding-right: 12px;
        padding-left: 28px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        top: 6px;
        right: 8px;
    }
</style>
@endsection

@section('content')
<div class="page-content-wrapper">
    <div class="content-container">
        <div class="page-content">

            @include('admin.partials.page-header', [
                'title'       => 'تعديل بيانات الطالب',
                'breadcrumbs' => $breadcrumbs,
            ])
            @include('admin.partials.alerts')

            <form action="{{ route('admin.students.update', $student) }}" method="POST" id="student-form" novalidate>
                @csrf
                @method('PUT')
                <div class="form-card">
                    <div class="form-card-header">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width:44px;height:44px;border-radius:10px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;font-size:1.4rem;">
                                    <i class="ti ti-pencil"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold">تعديل بيانات الطالب</h5>
                                    <p class="mb-0 small" style="opacity:.8;">{{ $student->full_name }}</p>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <span class="badge bg-white px-3 py-2" style="color:#1565C0;">
                                    رقم الطالب: #{{ $student->id }}
                                </span>
                                <span class="badge {{ $student->status === 'active' ? 'bg-success' : 'bg-secondary' }} px-3 py-2">
                                    {{ $student->status_label }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        @include('admin.students._form', ['student' => $student])
                    </div>

                    <div class="card-footer bg-white border-top p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <a href="{{ route('admin.students.show', $student) }}" class="btn btn-light">
                            <i class="ti ti-arrow-right me-1"></i>العودة لملف الطالب
                        </a>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.students.show', $student) }}" class="btn btn-outline-secondary">
                                <i class="ti ti-eye me-1"></i>عرض الملف
                            </a>
                            <button type="submit" class="btn btn-primary px-4" id="submit-btn">
                                <i class="ti ti-check me-1"></i>حفظ التعديلات
                            </button>
                        </div>
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
    if (window.jQuery && jQuery.fn.select2) {
        jQuery('.js-student-single-select').select2({
            width: '100%',
            dir: 'rtl'
        });
    }

    // ─── تبديل حقول ولي الأمر ───
    function toggleGuardianFields() {
        const selected = document.querySelector('input[name="guardian_mode"]:checked');
        const mode = selected ? selected.value : 'none';
        const existingBox = document.getElementById('guardian-existing-box');
        const newBox = document.getElementById('guardian-new-box');
        if (existingBox) existingBox.style.display = (mode === 'existing') ? 'flex' : 'none';
        if (newBox)      newBox.style.display      = (mode === 'new')      ? 'block' : 'none';
    }

    document.querySelectorAll('.guardian-radio').forEach(function (radio) {
        radio.addEventListener('change', toggleGuardianFields);
    });

    toggleGuardianFields();

    // ─── تأثير زر الحفظ ───
    const form = document.getElementById('student-form');
    const submitBtn = document.getElementById('submit-btn');
    if (form && submitBtn) {
        form.addEventListener('submit', function () {
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> جاري الحفظ...';
            submitBtn.disabled = true;
        });
    }
})();
</script>
@endsection
