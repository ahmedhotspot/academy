{{-- ======================================================
     _form: حقول حساب مستحق المعلم
     المتغيرات:
       $teacherOptions  — قائمة المعلمين
       $payroll         — (للتعديل اختياري)
====================================================== --}}

<div class="row g-3 mb-3">

    {{-- المعلم --}}
    <div class="col-md-6">
        <label class="form-label fw-semibold">
            المعلم <span class="text-danger">*</span>
        </label>
        <select name="teacher_id" id="teacher_id" class="form-select">
            <option value="">— اختر المعلم —</option>
            @foreach($teacherOptions as $tId => $tName)
                <option value="{{ $tId }}"
                    @selected(old('teacher_id', $payroll->teacher_id ?? '') == $tId)>
                    {{ $tName }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- الشهر --}}
    <div class="col-md-3">
        <label class="form-label fw-semibold">
            الشهر <span class="text-danger">*</span>
        </label>
        <select name="month" class="form-select">
            @php $monthNames = ['', 'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو',
                                'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر']; @endphp
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}"
                    @selected(old('month', $payroll->month ?? $currentMonth) == $i)>
                    {{ $monthNames[$i] }}
                </option>
            @endfor
        </select>
    </div>

    {{-- السنة --}}
    <div class="col-md-3">
        <label class="form-label fw-semibold">
            السنة <span class="text-danger">*</span>
        </label>
        <input type="number" name="year" class="form-control"
               min="2000" max="2100"
               value="{{ old('year', $payroll->year ?? $currentYear) }}">
    </div>

</div>

<div class="row g-3 mb-3">

    {{-- الراتب الأساسي --}}
    <div class="col-md-6">
        <label class="form-label fw-semibold">
            الراتب الأساسي (ج) <span class="text-danger">*</span>
        </label>
        <input type="number" name="base_salary" class="form-control"
               step="0.01" min="0" max="999999.99"
               placeholder="0.00"
               value="{{ old('base_salary', $payroll->base_salary ?? '') }}">
    </div>

    {{-- الاستقطاع عن كل غياب (للإنشاء فقط) --}}
    @if(! isset($payroll))
        <div class="col-md-6">
            <label class="form-label fw-semibold">
                الاستقطاع عن كل غياب (ج)
            </label>
            <input type="number" name="deduction_per_absence" class="form-control"
                   step="0.01" min="0" max="999999.99"
                   placeholder="0.00"
                   value="{{ old('deduction_per_absence', '') }}">
            <small class="text-muted">سيُحسب تلقائياً من عدد الغيابات</small>
        </div>
    @endif

</div>

<div class="row g-3 mb-3">

    {{-- الاستقطاع --}}
    <div class="col-md-4">
        <label class="form-label fw-semibold">الاستقطاع (ج)</label>
        <input type="number" name="deduction_amount" class="form-control"
               step="0.01" min="0" max="999999.99"
               placeholder="0.00"
               value="{{ old('deduction_amount', $payroll->deduction_amount ?? 0) }}">
        <small class="text-muted">استقطاعات إضافية</small>
    </div>

    {{-- الجزاء --}}
    <div class="col-md-4">
        <label class="form-label fw-semibold">الجزاء (ج)</label>
        <input type="number" name="penalty_amount" class="form-control"
               step="0.01" min="0" max="999999.99"
               placeholder="0.00"
               value="{{ old('penalty_amount', $payroll->penalty_amount ?? 0) }}">
        <small class="text-muted">جزاءات أو عقوبات</small>
    </div>

    {{-- المكافأة --}}
    <div class="col-md-4">
        <label class="form-label fw-semibold">المكافأة (ج)</label>
        <input type="number" name="bonus_amount" class="form-control"
               step="0.01" min="0" max="999999.99"
               placeholder="0.00"
               value="{{ old('bonus_amount', $payroll->bonus_amount ?? 0) }}">
        <small class="text-muted">مكافآت وحوافز</small>
    </div>

</div>

<div class="row g-3">
    <div class="col-12">
        <label class="form-label fw-semibold">الملاحظات</label>
        <textarea name="notes" rows="2" class="form-control"
                  placeholder="أضف أي ملاحظات عن الحساب…">{{ old('notes', $payroll->notes ?? '') }}</textarea>
    </div>
</div>

