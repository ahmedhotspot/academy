<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label">اسم المسار</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $studyTrack->name ?? '') }}" placeholder="اكتب اسم المسار">
    </div>

    <div class="col-md-4">
        <label class="form-label">الحالة</label>
        <select name="status" class="form-select">
            <option value="active" @selected(old('status', $studyTrack->status ?? 'active') === 'active')>نشط</option>
            <option value="inactive" @selected(old('status', $studyTrack->status ?? 'active') === 'inactive')>غير نشط</option>
        </select>
    </div>
</div>

