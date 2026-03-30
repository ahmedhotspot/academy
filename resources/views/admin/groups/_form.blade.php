<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">اسم الحلقة</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $group->name ?? '') }}" placeholder="اكتب اسم الحلقة">
    </div>

    <div class="col-md-3">
        <label class="form-label">نوع الحلقة</label>
        <select name="type" class="form-select">
            <option value="individual" @selected(old('type', $group->type ?? 'group') === 'individual')>فردي</option>
            <option value="group" @selected(old('type', $group->type ?? 'group') === 'group')>مجموعة</option>
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">نظام الحلقة</label>
        <select name="schedule_type" class="form-select">
            <option value="daily" @selected(old('schedule_type', $group->schedule_type ?? 'weekly') === 'daily')>يومي</option>
            <option value="weekly" @selected(old('schedule_type', $group->schedule_type ?? 'weekly') === 'weekly')>أسبوعي</option>
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">الفرع</label>
        <select name="branch_id" class="form-select">
            <option value="">اختر الفرع</option>
            @foreach($branchOptions as $branchId => $branchName)
                <option value="{{ $branchId }}" @selected((string) old('branch_id', $group->branch_id ?? '') === (string) $branchId)>{{ $branchName }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">المعلم</label>
        <select name="teacher_id" class="form-select">
            <option value="">اختر المعلم</option>
            @foreach($teacherOptions as $teacherId => $teacherName)
                <option value="{{ $teacherId }}" @selected((string) old('teacher_id', $group->teacher_id ?? '') === (string) $teacherId)>{{ $teacherName }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">المستوى</label>
        <select name="study_level_id" class="form-select">
            <option value="">اختر المستوى</option>
            @foreach($studyLevelOptions as $studyLevelId => $studyLevelName)
                <option value="{{ $studyLevelId }}" @selected((string) old('study_level_id', $group->study_level_id ?? '') === (string) $studyLevelId)>{{ $studyLevelName }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">المسار</label>
        <select name="study_track_id" class="form-select">
            <option value="">اختر المسار</option>
            @foreach($studyTrackOptions as $studyTrackId => $studyTrackName)
                <option value="{{ $studyTrackId }}" @selected((string) old('study_track_id', $group->study_track_id ?? '') === (string) $studyTrackId)>{{ $studyTrackName }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">الحالة</label>
        <select name="status" class="form-select">
            <option value="active" @selected(old('status', $group->status ?? 'active') === 'active')>نشط</option>
            <option value="inactive" @selected(old('status', $group->status ?? 'active') === 'inactive')>غير نشط</option>
        </select>
    </div>
</div>

