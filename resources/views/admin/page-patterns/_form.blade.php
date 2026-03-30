<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">عنوان النموذج</label>
        <input type="text" class="form-control" value="{{ $record['title'] ?? '' }}" placeholder="مثال: نموذج طالب">
    </div>

    <div class="col-md-6">
        <label class="form-label">نوع النموذج</label>
        <select class="form-select">
            <option selected>فردي</option>
            <option>مجموعات</option>
        </select>
    </div>

    <div class="col-12">
        <label class="form-label">وصف مختصر</label>
        <textarea class="form-control" rows="4" placeholder="وصف مختصر للنموذج">{{ $record['description'] ?? '' }}</textarea>
    </div>
</div>

