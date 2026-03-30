<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">الاسم الكامل</label>
        <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $student->full_name ?? '') }}" placeholder="اكتب اسم الطالب">
    </div>

    <div class="col-md-3">
        <label class="form-label">العمر</label>
        <input type="number" name="age" class="form-control" min="5" max="100" value="{{ old('age', $student->age ?? '') }}" placeholder="العمر">
    </div>

    <div class="col-md-3">
        <label class="form-label">الحالة</label>
        <select name="status" class="form-select">
            <option value="active" @selected(old('status', $student->status ?? 'active') === 'active')>نشط</option>
            <option value="inactive" @selected(old('status', $student->status ?? 'active') === 'inactive')>غير نشط</option>
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">الجنسية</label>
        <input type="text" name="nationality" class="form-control" value="{{ old('nationality', $student->nationality ?? '') }}" placeholder="الجنسية">
    </div>

    <div class="col-md-6">
        <label class="form-label">رقم الهوية أو الجواز</label>
        <input type="text" name="identity_number" class="form-control" value="{{ old('identity_number', $student->identity_number ?? '') }}" placeholder="اختياري">
    </div>

    <div class="col-md-6">
        <label class="form-label">الهاتف</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone', $student->phone ?? '') }}" placeholder="05xxxxxxxx">
    </div>

    <div class="col-md-6">
        <label class="form-label">الواتساب</label>
        <input type="text" name="whatsapp" class="form-control" value="{{ old('whatsapp', $student->whatsapp ?? '') }}" placeholder="اختياري">
    </div>

    <div class="col-md-6">
        <label class="form-label">الفرع</label>
        <select name="branch_id" class="form-select">
            <option value="">اختر الفرع</option>
            @foreach($branchOptions as $branchId => $branchName)
                <option value="{{ $branchId }}" @selected((string) old('branch_id', $student->branch_id ?? '') === (string) $branchId)>
                    {{ $branchName }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">ولي الأمر</label>
        @php
            $selectedMode = old('guardian_mode');
            if (! $selectedMode) {
                $selectedMode = old('guardian_id', $student->guardian_id ?? null) ? 'existing' : 'none';
            }
        @endphp

        <div class="d-flex flex-wrap gap-3 mt-2">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="guardian_mode" id="guardian_mode_none" value="none" @checked($selectedMode === 'none')>
                <label class="form-check-label" for="guardian_mode_none">بدون ولي أمر</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="guardian_mode" id="guardian_mode_existing" value="existing" @checked($selectedMode === 'existing')>
                <label class="form-check-label" for="guardian_mode_existing">اختيار ولي أمر موجود</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="guardian_mode" id="guardian_mode_new" value="new" @checked($selectedMode === 'new')>
                <label class="form-check-label" for="guardian_mode_new">إنشاء ولي أمر جديد</label>
            </div>
        </div>
    </div>

    <div class="col-12" id="guardian-existing-box" style="display: none;">
        <label class="form-label">اختيار ولي الأمر</label>
        <select name="guardian_id" class="form-select">
            <option value="">اختر ولي الأمر</option>
            @foreach($guardianOptions as $guardianId => $guardianName)
                <option value="{{ $guardianId }}" @selected((string) old('guardian_id', $student->guardian_id ?? '') === (string) $guardianId)>
                    {{ $guardianName }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-12" id="guardian-new-box" style="display: none;">
        <div class="row g-3 p-3 border rounded-3 bg-light">
            <div class="col-md-4">
                <label class="form-label">اسم ولي الأمر الجديد</label>
                <input type="text" name="guardian_full_name" class="form-control" value="{{ old('guardian_full_name') }}" placeholder="الاسم الكامل">
            </div>
            <div class="col-md-4">
                <label class="form-label">هاتف ولي الأمر الجديد</label>
                <input type="text" name="guardian_phone" class="form-control" value="{{ old('guardian_phone') }}" placeholder="05xxxxxxxx">
            </div>
            <div class="col-md-4">
                <label class="form-label">واتساب ولي الأمر الجديد</label>
                <input type="text" name="guardian_whatsapp" class="form-control" value="{{ old('guardian_whatsapp') }}" placeholder="اختياري">
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const existingBox = document.getElementById('guardian-existing-box');
        const newBox = document.getElementById('guardian-new-box');
        const radios = document.querySelectorAll('input[name="guardian_mode"]');

        function toggleGuardianFields() {
            const selected = document.querySelector('input[name="guardian_mode"]:checked')?.value;

            existingBox.style.display = selected === 'existing' ? 'block' : 'none';
            newBox.style.display = selected === 'new' ? 'block' : 'none';
        }

        radios.forEach(function (radio) {
            radio.addEventListener('change', toggleGuardianFields);
        });

        toggleGuardianFields();
    });
</script>

