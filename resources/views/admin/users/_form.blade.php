<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">الاسم</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name ?? '') }}" placeholder="الاسم الكامل">
    </div>

    <div class="col-md-6">
        <label class="form-label">رقم الجوال</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone ?? '') }}" placeholder="05xxxxxxxx">
    </div>

    <div class="col-md-6">
        <label class="form-label">البريد الإلكتروني</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email ?? '') }}" placeholder="example@email.com">
    </div>

    <div class="col-md-6">
        <label class="form-label">اسم المستخدم</label>
        <input type="text" name="username" class="form-control" value="{{ old('username', $user->username ?? '') }}" placeholder="اسم مستخدم اختياري">
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
        <label class="form-label">الدور</label>
        <select name="role" class="form-select">
            <option value="">اختر الدور</option>
            @foreach($roles as $roleValue => $roleName)
                <option value="{{ $roleValue }}"
                    @selected(old('role', isset($user) ? $user->roles->first()?->name : '') === $roleValue)>
                    {{ $roleName }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">الفرع</label>
        <select name="branch_id" class="form-select">
            <option value="">بدون فرع (للمشرف العام)</option>
            @foreach($branches as $branchId => $branchName)
                <option value="{{ $branchId }}" @selected((string) old('branch_id', $user->branch_id ?? '') === (string) $branchId)>
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
                <option value="{{ $statusValue }}" @selected(old('status', $user->status?->value ?? '') === $statusValue)>
                    {{ $statusLabel }}
                </option>
            @endforeach
        </select>
    </div>
</div>

