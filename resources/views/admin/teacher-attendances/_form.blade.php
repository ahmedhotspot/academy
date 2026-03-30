<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">المعلم</label>
        <select name="teacher_id" class="form-select">
            <option value="">اختر المعلم</option>
            @foreach($teacherOptions as $teacherId => $teacherName)
                <option value="{{ $teacherId }}" @selected((string) old('teacher_id', $teacherAttendance->teacher_id ?? '') === (string) $teacherId)>
                    {{ $teacherName }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">تاريخ الحضور</label>
        <input type="date" name="attendance_date" class="form-control" value="{{ old('attendance_date', isset($teacherAttendance) ? optional($teacherAttendance->attendance_date)->format('Y-m-d') : now()->toDateString()) }}">
    </div>

    <div class="col-md-3">
        <label class="form-label">الحالة</label>
        <select name="status" class="form-select">
            @foreach(['حاضر', 'غائب', 'متأخر', 'بعذر'] as $statusValue)
                <option value="{{ $statusValue }}" @selected(old('status', $teacherAttendance->status ?? 'حاضر') === $statusValue)>
                    {{ $statusValue }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-12">
        <label class="form-label">ملاحظات</label>
        <textarea name="notes" rows="3" class="form-control" placeholder="اكتب أي ملاحظات إضافية">{{ old('notes', $teacherAttendance->notes ?? '') }}</textarea>
    </div>
</div>

