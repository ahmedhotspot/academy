{{-- ══════════════════════════════════════════════════════
     نموذج بيانات الطالب — مشترك بين الإضافة والتعديل
     ══════════════════════════════════════════════════════ --}}

{{-- ─── القسم 1: البيانات الشخصية ─── --}}
<div class="section-block mb-4">
    <div class="section-heading d-flex align-items-center gap-2 mb-3 pb-2"
         style="border-bottom:2px solid #e8f5e9;">
        <div style="width:34px;height:34px;border-radius:8px;background:#e8f5e9;display:flex;align-items:center;justify-content:center;">
            <i class="ti ti-user" style="color:#1B5E20;font-size:1.1rem;"></i>
        </div>
        <div>
            <h6 class="mb-0 fw-bold" style="color:#1B5E20;">البيانات الشخصية</h6>
            <small class="text-muted">المعلومات الأساسية للطالب</small>
        </div>
    </div>
    <div class="row g-3">
        <div class="col-md-3">
            <label class="form-label fw-semibold">
                <i class="ti ti-hash me-1 text-muted"></i>كود الطالب
                <span class="text-danger">*</span>
            </label>
            <input type="text"
                   name="student_code"
                   class="form-control @error('student_code') is-invalid @enderror"
                   value="{{ old('student_code', $student->student_code ?? '') }}"
                   placeholder="مثال: STD-1001"
                   required>
            @error('student_code')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-9">
            <label class="form-label fw-semibold">
                <i class="ti ti-user me-1 text-muted"></i>الاسم الكامل
                <span class="text-danger">*</span>
            </label>
            <input type="text"
                   name="full_name"
                   class="form-control @error('full_name') is-invalid @enderror"
                   value="{{ old('full_name', $student->full_name ?? '') }}"
                   placeholder="أدخل الاسم الرباعي للطالب"
                   required>
            @error('full_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">
                <i class="ti ti-calendar-plus me-1 text-muted"></i>تاريخ الالتحاق
                <span class="text-danger">*</span>
            </label>
            <input type="date"
                   name="enrollment_date"
                   class="form-control @error('enrollment_date') is-invalid @enderror"
                   value="{{ old('enrollment_date', optional($student->enrollment_date ?? null)->format('Y-m-d')) }}"
                   required>
            @error('enrollment_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">
                <i class="ti ti-cake me-1 text-muted"></i>تاريخ الميلاد
                <span class="text-danger">*</span>
            </label>
            <input type="date"
                   id="birth_date"
                   name="birth_date"
                   class="form-control @error('birth_date') is-invalid @enderror"
                   value="{{ old('birth_date', optional($student->birth_date ?? null)->format('Y-m-d')) }}"
                   required>
            @error('birth_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">
                <i class="ti ti-calendar-time me-1 text-muted"></i>العمر
                <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <input type="number"
                       id="age"
                       name="age"
                       class="form-control @error('age') is-invalid @enderror"
                       min="5" max="100"
                       value="{{ old('age', $student->age ?? '') }}"
                       placeholder="العمر" required>
                <span class="input-group-text">سنة</span>
                @error('age')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">
                <i class="ti ti-activity me-1 text-muted"></i>الحالة
                <span class="text-danger">*</span>
            </label>
            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                <option value="active"   @selected(old('status', $student->status ?? 'active') === 'active')>✅ نشط</option>
                <option value="inactive" @selected(old('status', $student->status ?? 'active') === 'inactive')>⛔ غير نشط</option>
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">
                <i class="ti ti-flag me-1 text-muted"></i>الجنسية
                <span class="text-danger">*</span>
            </label>
            <input type="text"
                   name="nationality"
                   class="form-control @error('nationality') is-invalid @enderror"
                   value="{{ old('nationality', $student->nationality ?? '') }}"
                   placeholder="مثال: سعودي، مصري، سوداني..." required>
            @error('nationality')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">
                <i class="ti ti-gender-bigender me-1 text-muted"></i>الجنس
                <span class="text-danger">*</span>
            </label>
            <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                <option value="">— اختر الجنس —</option>
                <option value="male" @selected(old('gender', $student->gender ?? '') === 'male')>ذكر</option>
                <option value="female" @selected(old('gender', $student->gender ?? '') === 'female')>أنثى</option>
            </select>
            @error('gender')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">
                <i class="ti ti-id-badge me-1 text-muted"></i>رقم الهوية أو جواز السفر
                <span class="text-danger">*</span>
            </label>
            <input type="text"
                   name="identity_number"
                   class="form-control @error('identity_number') is-invalid @enderror"
                   value="{{ old('identity_number', $student->identity_number ?? '') }}"
                   placeholder="أدخل رقم الهوية أو الجواز"
                   required>
            @error('identity_number')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">
                <i class="ti ti-calendar-x me-1 text-muted"></i>تاريخ انتهاء الهوية أو الجواز
            </label>
            <input type="date"
                   name="identity_expiry_date"
                   class="form-control @error('identity_expiry_date') is-invalid @enderror"
                   value="{{ old('identity_expiry_date', optional($student->identity_expiry_date ?? null)->format('Y-m-d')) }}">
            <small class="text-muted d-block mt-1">اختياري — اتركه فارغًا إذا كانت الوثيقة بلا تاريخ انتهاء مثل شهادة الميلاد.</small>
            @error('identity_expiry_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">
                <i class="ti ti-id me-1 text-muted"></i>رقم الإقامة
            </label>
            <input type="text"
                   name="residency_number"
                   class="form-control @error('residency_number') is-invalid @enderror"
                   value="{{ old('residency_number', $student->residency_number ?? '') }}"
                   placeholder="اختياري">
            @error('residency_number')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">
                <i class="ti ti-calendar-clock me-1 text-muted"></i>تاريخ انتهاء الإقامة
            </label>
            <input type="date"
                   name="residency_expiry_date"
                   class="form-control @error('residency_expiry_date') is-invalid @enderror"
                   value="{{ old('residency_expiry_date', optional($student->residency_expiry_date ?? null)->format('Y-m-d')) }}">
            @error('residency_expiry_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

{{-- ─── القسم 2: بيانات التواصل ─── --}}
<div class="section-block mb-4">
    <div class="section-heading d-flex align-items-center gap-2 mb-3 pb-2"
         style="border-bottom:2px solid #e3f2fd;">
        <div style="width:34px;height:34px;border-radius:8px;background:#e3f2fd;display:flex;align-items:center;justify-content:center;">
            <i class="ti ti-device-mobile" style="color:#1565C0;font-size:1.1rem;"></i>
        </div>
        <div>
            <h6 class="mb-0 fw-bold" style="color:#1565C0;">بيانات التواصل</h6>
            <small class="text-muted">أرقام الهاتف والتواصل المباشر</small>
        </div>
    </div>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label fw-semibold">
                <i class="ti ti-phone me-1 text-muted"></i>رقم الهاتف
                <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <span class="input-group-text"><i class="ti ti-phone" style="color:#1565C0;"></i></span>
                <input type="text"
                       name="phone"
                       class="form-control @error('phone') is-invalid @enderror"
                       value="{{ old('phone', $student->phone ?? '') }}"
                       placeholder="05XXXXXXXX" required>
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">
                <i class="ti ti-brand-whatsapp me-1 text-muted"></i>رقم الواتساب
            </label>
            <div class="input-group">
                <span class="input-group-text" style="color:#25D366;"><i class="ti ti-brand-whatsapp"></i></span>
                <input type="text"
                       name="whatsapp"
                       class="form-control @error('whatsapp') is-invalid @enderror"
                       value="{{ old('whatsapp', $student->whatsapp ?? '') }}"
                       placeholder="اختياري — 05XXXXXXXX">
                @error('whatsapp')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>

{{-- ─── القسم 3: الإعدادات الإدارية ─── --}}
<div class="section-block mb-4">
    <div class="section-heading d-flex align-items-center gap-2 mb-3 pb-2"
         style="border-bottom:2px solid #fff3e0;">
        <div style="width:34px;height:34px;border-radius:8px;background:#fff3e0;display:flex;align-items:center;justify-content:center;">
            <i class="ti ti-settings" style="color:#e65100;font-size:1.1rem;"></i>
        </div>
        <div>
            <h6 class="mb-0 fw-bold" style="color:#e65100;">الإعدادات الإدارية</h6>
            <small class="text-muted">الفرع وربط بيانات الأكاديمية</small>
        </div>
    </div>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label fw-semibold">
                <i class="ti ti-building me-1 text-muted"></i>الفرع
                <span class="text-danger">*</span>
            </label>
            <select name="branch_id"
                    id="branch_id"
                    class="form-select js-student-single-select @error('branch_id') is-invalid @enderror"
                    required>
                <option value="">— اختر الفرع —</option>
                @foreach($branchOptions as $branchId => $branchName)
                    <option value="{{ $branchId }}"
                            @selected((string) old('branch_id', $student->branch_id ?? '') === (string) $branchId)>
                        {{ $branchName }}
                    </option>
                @endforeach
            </select>
            @error('branch_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

{{-- ─── القسم 4: ولي الأمر ─── --}}
<div class="section-block">
    <div class="section-heading d-flex align-items-center gap-2 mb-3 pb-2"
         style="border-bottom:2px solid #fce4ec;">
        <div style="width:34px;height:34px;border-radius:8px;background:#fce4ec;display:flex;align-items:center;justify-content:center;">
            <i class="ti ti-users" style="color:#c62828;font-size:1.1rem;"></i>
        </div>
        <div>
            <h6 class="mb-0 fw-bold" style="color:#c62828;">بيانات ولي الأمر</h6>
            <small class="text-muted">الشخص المسؤول عن الطالب</small>
        </div>
    </div>

    @php
        $selectedMode = old('guardian_mode');
        if (!$selectedMode) {
            $selectedMode = old('guardian_id', $student->guardian_id ?? null) ? 'existing' : 'none';
        }
    @endphp

    {{-- خيارات ولي الأمر --}}
    <div class="row g-3 mb-3">
        <div class="col-12">
            <div class="d-flex flex-wrap gap-3">
                <div class="guardian-option">
                    <input class="form-check-input guardian-radio" type="radio"
                           name="guardian_mode" id="gm_none"
                           value="none" @checked($selectedMode === 'none')>
                    <label class="guardian-option-label" for="gm_none">
                        <i class="ti ti-user-off me-1"></i>بدون ولي أمر
                    </label>
                </div>
                <div class="guardian-option">
                    <input class="form-check-input guardian-radio" type="radio"
                           name="guardian_mode" id="gm_existing"
                           value="existing" @checked($selectedMode === 'existing')>
                    <label class="guardian-option-label" for="gm_existing">
                        <i class="ti ti-user-search me-1"></i>اختيار ولي أمر موجود
                    </label>
                </div>
                <div class="guardian-option">
                    <input class="form-check-input guardian-radio" type="radio"
                           name="guardian_mode" id="gm_new"
                           value="new" @checked($selectedMode === 'new')>
                    <label class="guardian-option-label" for="gm_new">
                        <i class="ti ti-user-plus me-1"></i>إضافة ولي أمر جديد
                    </label>
                </div>
            </div>
        </div>
    </div>

    {{-- اختيار ولي الأمر الموجود --}}
    <div id="guardian-existing-box" class="row g-3 mb-3" style="display:none;">
        <div class="col-md-8">
            <label class="form-label fw-semibold">
                <i class="ti ti-search me-1 text-muted"></i>اختر ولي الأمر من القائمة
            </label>
            <select name="guardian_id"
                    id="guardian_id"
                    class="form-select js-student-single-select @error('guardian_id') is-invalid @enderror">
                <option value="">— ابحث واختر ولي الأمر —</option>
                @foreach($guardianOptions as $guardianId => $guardianName)
                    <option value="{{ $guardianId }}"
                            @selected((string) old('guardian_id', $student->guardian_id ?? '') === (string) $guardianId)>
                        {{ $guardianName }}
                    </option>
                @endforeach
            </select>
            @error('guardian_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- إضافة ولي أمر جديد --}}
    <div id="guardian-new-box" style="display:none;">
        <div class="p-3 rounded-3" style="background:#fff5f5;border:1px dashed #f8bbd0;">
            <p class="small text-muted mb-3">
                <i class="ti ti-info-circle me-1"></i>
                سيتم إنشاء حساب ولي أمر جديد وربطه تلقائياً بهذا الطالب.
            </p>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        <i class="ti ti-user me-1 text-muted"></i>اسم ولي الأمر الجديد
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="guardian_full_name"
                           class="form-control @error('guardian_full_name') is-invalid @enderror"
                           value="{{ old('guardian_full_name') }}"
                           placeholder="الاسم الكامل">
                    @error('guardian_full_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        <i class="ti ti-phone me-1 text-muted"></i>هاتف ولي الأمر
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="guardian_phone"
                           class="form-control @error('guardian_phone') is-invalid @enderror"
                           value="{{ old('guardian_phone') }}"
                           placeholder="05XXXXXXXX">
                    @error('guardian_phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        <i class="ti ti-brand-whatsapp me-1 text-muted"></i>واتساب ولي الأمر
                    </label>
                    <input type="text" name="guardian_whatsapp"
                           class="form-control @error('guardian_whatsapp') is-invalid @enderror"
                           value="{{ old('guardian_whatsapp') }}"
                           placeholder="اختياري">
                    @error('guardian_whatsapp')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.guardian-option { display:inline-flex; align-items:center; gap:8px; }
.guardian-option .form-check-input { display:none; }
.guardian-option-label {
    padding: 7px 16px;
    border: 2px solid #dee2e6;
    border-radius: 30px;
    cursor: pointer;
    font-size: .875rem;
    color: #555;
    transition: all .2s;
    user-select: none;
}
.guardian-option .form-check-input:checked + .guardian-option-label {
    border-color: #c62828;
    background: #fce4ec;
    color: #c62828;
    font-weight: 600;
}
</style>

<script>
    (function () {
        const birthDateInput = document.getElementById('birth_date');
        const ageInput = document.getElementById('age');

        if (!birthDateInput || !ageInput) {
            return;
        }

        function calculateAge(dateValue) {
            if (!dateValue) {
                return null;
            }

            const birthDate = new Date(dateValue + 'T00:00:00');

            if (Number.isNaN(birthDate.getTime())) {
                return null;
            }

            const today = new Date();
            const thisYearBirthday = new Date(
                today.getFullYear(),
                birthDate.getMonth(),
                birthDate.getDate()
            );

            let age = today.getFullYear() - birthDate.getFullYear();

            if (today < thisYearBirthday) {
                age -= 1;
            }

            return age >= 0 ? age : null;
        }

        function updateAgeFromBirthDate() {
            const age = calculateAge(birthDateInput.value);
            ageInput.value = age === null ? '' : age;
        }

        birthDateInput.addEventListener('change', updateAgeFromBirthDate);
        birthDateInput.addEventListener('input', updateAgeFromBirthDate);

        // Ensure edit pages and old input always show computed age on load.
        updateAgeFromBirthDate();
    })();
</script>

