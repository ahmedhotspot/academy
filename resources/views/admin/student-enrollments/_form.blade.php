<div class="row g-3">
    @isset($studentOptions)
        <div class="col-md-6">
            <label class="form-label">الطالب</label>
            <select name="student_id" class="form-select">
                <option value="">اختر الطالب</option>
                @foreach($studentOptions as $studentId => $studentName)
                    <option value="{{ $studentId }}" @selected((string) old('student_id', $studentEnrollment->student_id ?? '') === (string) $studentId)>
                        {{ $studentName }}
                    </option>
                @endforeach
            </select>
        </div>
    @endisset

    <div class="col-md-6">
        <label class="form-label">الحلقة</label>
        <select name="group_id" class="form-select">
            <option value="">اختر الحلقة</option>
            @foreach($groupOptions as $groupId => $groupName)
                <option value="{{ $groupId }}" @selected((string) old('group_id', $studentEnrollment->group_id ?? '') === (string) $groupId)>
                    {{ $groupName }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">حالة التسجيل</label>
        <select name="status" class="form-select">
            <option value="active" @selected(old('status', $studentEnrollment->status ?? 'active') === 'active')>نشط</option>
            <option value="transferred" @selected(old('status', $studentEnrollment->status ?? 'active') === 'transferred')>منقول</option>
            <option value="suspended" @selected(old('status', $studentEnrollment->status ?? 'active') === 'suspended')>موقوف</option>
        </select>
    </div>
</div>

