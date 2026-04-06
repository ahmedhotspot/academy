<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">الفرع</label>
        <select name="branch_id" class="form-select">
            <option value="">اختر الفرع</option>
            @foreach($branches as $branchId => $branchName)
                <option value="{{ $branchId }}" @selected((string) old('branch_id', $guardian->branch_id ?? '') === (string) $branchId)>
                    {{ $branchName }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">الاسم الكامل</label>
        <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $guardian->full_name ?? '') }}" placeholder="اكتب اسم ولي الأمر">
    </div>

    <div class="col-md-4">
        <label class="form-label">رقم الهاتف</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone', $guardian->phone ?? '') }}" placeholder="05xxxxxxxx">
    </div>

    <div class="col-md-3">
        <label class="form-label">رقم الواتساب</label>
        <input type="text" name="whatsapp" class="form-control" value="{{ old('whatsapp', $guardian->whatsapp ?? '') }}" placeholder="اختياري">
    </div>

    <div class="col-md-3">
        <label class="form-label">الحالة</label>
        <select name="status" class="form-select">
            <option value="active" @selected(old('status', $guardian->status ?? 'active') === 'active')>نشط</option>
            <option value="inactive" @selected(old('status', $guardian->status ?? 'active') === 'inactive')>غير نشط</option>
        </select>
    </div>
</div>

