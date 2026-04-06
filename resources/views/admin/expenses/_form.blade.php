{{-- ======================================================
     _form: حقول المصروف
====================================================== --}}

<div class="row g-3 mb-3">

    <div class="col-md-6">
        <label class="form-label fw-semibold">الفرع <span class="text-danger">*</span></label>
        <select name="branch_id" class="form-select" required>
            <option value="">— اختر الفرع —</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->id }}"
                    @selected(old('branch_id', $expense->branch_id ?? '') == $branch->id)>
                    {{ $branch->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">تاريخ المصروف <span class="text-danger">*</span></label>
        <input type="date" name="expense_date" class="form-control"
               value="{{ old('expense_date', isset($expense) ? optional($expense->expense_date)->format('Y-m-d') : now()->toDateString()) }}">
    </div>

</div>

<div class="row g-3 mb-3">

    <div class="col-md-8">
        <label class="form-label fw-semibold">البيان <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control"
               placeholder="صيانة / إصلاح / شراء معدات / إيجار / كهرباء"
               value="{{ old('title', $expense->title ?? '') }}">
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">المبلغ (ج) <span class="text-danger">*</span></label>
        <input type="number" name="amount" class="form-control"
               step="0.01" min="0" max="999999.99"
               placeholder="0.00"
               value="{{ old('amount', $expense->amount ?? '') }}">
    </div>

</div>

<div class="row g-3">
    <div class="col-12">
        <label class="form-label fw-semibold">الملاحظات</label>
        <textarea name="notes" rows="2" class="form-control"
                  placeholder="تفاصيل إضافية عن المصروف">{{ old('notes', $expense->notes ?? '') }}</textarea>
    </div>
</div>

