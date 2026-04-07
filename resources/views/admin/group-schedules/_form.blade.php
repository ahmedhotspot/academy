<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">الحلقة</label>
        <select name="group_id" class="form-select">
            <option value="">اختر الحلقة</option>
            @foreach($groupOptions as $groupId => $groupName)
                <option value="{{ $groupId }}" @selected((string) old('group_id', $selectedGroupId ?? ($groupSchedule->group_id ?? '')) === (string) $groupId)>{{ $groupName }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">اليوم</label>
        <select name="day_name" class="form-select">
            @foreach(['الأحد','الاثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت'] as $dayName)
                <option value="{{ $dayName }}" @selected(old('day_name', $groupSchedule->day_name ?? 'الأحد') === $dayName)>{{ $dayName }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">الحالة</label>
        <select name="status" class="form-select">
            <option value="active" @selected(old('status', $groupSchedule->status ?? 'active') === 'active')>نشط</option>
            <option value="inactive" @selected(old('status', $groupSchedule->status ?? 'active') === 'inactive')>غير نشط</option>
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">وقت البداية</label>
        <input type="time" name="start_time" class="form-control" value="{{ old('start_time', isset($groupSchedule) ? substr((string) $groupSchedule->start_time, 0, 5) : '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">وقت النهاية</label>
        <input type="time" name="end_time" class="form-control" value="{{ old('end_time', isset($groupSchedule) ? substr((string) $groupSchedule->end_time, 0, 5) : '') }}">
    </div>
</div>
