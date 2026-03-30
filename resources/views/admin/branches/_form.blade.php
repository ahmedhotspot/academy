<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label fw-semibold">اسم الفرع <span class="text-danger">*</span></label>
        <input
            type="text"
            name="name"
            class="form-control"
            value="{{ old('name', $branch->name ?? '') }}"
            placeholder="مثال: فرع الرياض - الشمال"
            maxlength="255"
        >
        <small class="text-muted">اكتب اسمًا واضحًا ومميزًا للفرع.</small>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">الحالة <span class="text-danger">*</span></label>
        <select name="status" class="form-select">
            <option value="active" @selected(old('status', $branch->status?->value ?? 'active') === 'active')>نشط</option>
            <option value="inactive" @selected(old('status', $branch->status?->value ?? 'active') === 'inactive')>غير نشط</option>
        </select>
        <small class="text-muted">يمكنك تعطيل الفرع دون حذفه.</small>
    </div>
</div>
