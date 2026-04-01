{{-- ======================================================
     _form: حقول خطة الرسوم
     المتغيرات:
       $paymentCycles  — دورات الدفع
       $statuses       — الحالات
       $feePlan        — (للتعديل اختياري)
====================================================== --}}

<div class="row g-3 mb-3">

    {{-- اسم خطة الرسوم --}}
    <div class="col-md-6">
        <label class="form-label fw-semibold">
            اسم خطة الرسوم <span class="text-danger">*</span>
        </label>
        <input type="text" name="name" class="form-control"
               placeholder="مثال: الخطة الأساسية، الخطة المتقدمة"
               value="{{ old('name', $feePlan->name ?? '') }}">
    </div>

    {{-- دورة الدفع --}}
    <div class="col-md-3">
        <label class="form-label fw-semibold">
            دورة الدفع <span class="text-danger">*</span>
        </label>
        <select name="payment_cycle" class="form-select">
            @foreach($paymentCycles as $cycle)
                <option value="{{ $cycle }}"
                    @selected(old('payment_cycle', $feePlan->payment_cycle ?? '') === $cycle)>
                    {{ $cycle }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- الحالة --}}
    <div class="col-md-3">
        <label class="form-label fw-semibold">
            الحالة <span class="text-danger">*</span>
        </label>
        <select name="status" class="form-select">
            @foreach($statuses as $status)
                <option value="{{ $status }}"
                    @selected(old('status', $feePlan->status ?? 'active') === $status)>
                    {{ $status === 'active' ? 'نشط' : 'غير نشط' }}
                </option>
            @endforeach
        </select>
    </div>

</div>

<div class="row g-3 mb-3">

    {{-- المبلغ --}}
    <div class="col-md-6">
        <label class="form-label fw-semibold">
            المبلغ (ج) <span class="text-danger">*</span>
        </label>
        <input type="number" name="amount" class="form-control"
               step="0.01" min="0" max="999999.99"
               placeholder="مثال: 150.00"
               value="{{ old('amount', $feePlan->amount ?? '') }}">
        <small class="text-muted">الحد الأقصى: 999,999.99</small>
    </div>

    {{-- خصم الأخوات --}}
    <div class="col-md-6">
        <label class="form-label fw-semibold">خصم الأخوات</label>
        <div class="d-flex gap-3 mt-2">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="has_sisters_discount"
                       id="discount_yes" value="1"
                       @checked(old('has_sisters_discount', $feePlan->has_sisters_discount ?? false))>
                <label class="form-check-label" for="discount_yes">نعم، يوجد خصم</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="has_sisters_discount"
                       id="discount_no" value="0"
                       @checked(!old('has_sisters_discount', $feePlan->has_sisters_discount ?? false))>
                <label class="form-check-label" for="discount_no">لا، بدون خصم</label>
            </div>
        </div>
        <small class="text-muted d-block mt-1">هل تطبيق خصم للأخوات على هذه الخطة؟</small>
    </div>

</div>

