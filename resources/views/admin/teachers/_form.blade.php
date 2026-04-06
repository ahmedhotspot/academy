<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">الاسم</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $teacher->name ?? '') }}" placeholder="الاسم الكامل للمعلم">
    </div>

    <div class="col-md-6">
        <label class="form-label">رقم الجوال</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone', $teacher->phone ?? '') }}" placeholder="05xxxxxxxx">
    </div>

    <div class="col-md-6">
        <label class="form-label">رقم الواتساب</label>
        <input type="text" name="whatsapp" class="form-control" value="{{ old('whatsapp', $teacher->whatsapp ?? '') }}" placeholder="05xxxxxxxx">
    </div>

    <div class="col-md-6">
        <label class="form-label">اسم المستخدم</label>
        <input type="text" name="username" class="form-control" value="{{ old('username', $teacher->username ?? '') }}" placeholder="اسم مستخدم اختياري">
    </div>

    <div class="col-md-6">
        <label class="form-label">كلمة المرور</label>
        <input type="password" name="password" class="form-control" placeholder="********">
    </div>

    <div class="col-md-6">
        <label class="form-label">تأكيد كلمة المرور</label>
        <input type="password" name="password_confirmation" class="form-control" placeholder="********">
    </div>

    <div class="col-md-6">
        <label class="form-label">الفرع</label>
        <select name="branch_id" class="form-select">
            <option value="">اختر الفرع</option>
            @foreach($branches as $branchId => $branchName)
                <option value="{{ $branchId }}" @selected((string) old('branch_id', $teacher->branch_id ?? '') === (string) $branchId)>
                    {{ $branchName }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">الحالة</label>
        <select name="status" class="form-select">
            <option value="">اختر الحالة</option>
            @foreach($statuses as $statusValue => $statusLabel)
                <option value="{{ $statusValue }}" @selected(old('status', $teacher->status?->value ?? '') === $statusValue)>
                    {{ $statusLabel }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-12">
        <div class="alert alert-info mb-0">
            سيتم إنشاء المستخدم بدور <strong>المعلم</strong> تلقائيًا من هذه الصفحة.
        </div>
    </div>
</div>

