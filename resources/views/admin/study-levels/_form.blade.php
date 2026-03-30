<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label">اسم المستوى</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $studyLevel->name ?? '') }}" placeholder="اكتب اسم المستوى">
    </div>

    <div class="col-md-4">
        <label class="form-label">الحالة</label>
        <select name="status" class="form-select">
            <option value="active" @selected(old('status', $studyLevel->status ?? 'active') === 'active')>نشط</option>
            <option value="inactive" @selected(old('status', $studyLevel->status ?? 'active') === 'inactive')>غير نشط</option>
        </select>
    </div>
</div>

