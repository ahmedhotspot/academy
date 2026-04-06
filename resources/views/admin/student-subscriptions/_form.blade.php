{{-- ======================================================
     _form: حقول اشتراك الطالب
     المتغيرات:
       $studentOptions   — قائمة الطلاب
       $feePlanOptions   — قائمة خطط الرسوم
       $statuses         — حالات الاشتراك
       $subscription     — (للتعديل اختياري)
====================================================== --}}

<div class="row g-3 mb-3">

    {{-- الطالب --}}
    <div class="col-md-6">
        <label class="form-label fw-semibold">
            الطالب <span class="text-danger">*</span>
        </label>
        <select name="student_id" class="form-select">
            <option value="">— اختر الطالب —</option>
            @foreach($studentOptions as $sId => $sName)
                <option value="{{ $sId }}"
                    @selected(old('student_id', $subscription->student_id ?? '') == $sId)>
                    {{ $sName }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- خطة الرسوم --}}
    <div class="col-md-6">
        <label class="form-label fw-semibold">
            خطة الرسوم <span class="text-danger">*</span>
        </label>
        <select id="fee_plan_id" name="fee_plan_id" class="form-select">
            <option value="">— اختر الخطة —</option>
            @foreach($feePlanOptions as $fId => $fName)
                <option value="{{ $fId }}"
                    @selected(old('fee_plan_id', $subscription->fee_plan_id ?? '') == $fId)>
                    {{ $fName }}
                </option>
            @endforeach
        </select>
    </div>

</div>

<div class="row g-3 mb-3">

    {{-- المبلغ الأساسي --}}
    <div class="col-md-3">
        <label class="form-label fw-semibold">
            المبلغ الأساسي (ج) <span class="text-danger">*</span>
        </label>
        <input type="number" id="amount" name="amount" class="form-control"
               step="0.01" min="0" max="999999.99"
               placeholder="0.00"
               value="{{ old('amount', $subscription->amount ?? '') }}">
        <small class="text-muted">يتم تعبئة المبلغ تلقائيًا من خطة الرسوم.</small>
    </div>

    {{-- الخصم --}}
    <div class="col-md-3">
        <label class="form-label fw-semibold">خصم (ج)</label>
        <input type="number" name="discount_amount" class="form-control"
               step="0.01" min="0" max="999999.99"
               placeholder="0.00"
               value="{{ old('discount_amount', $subscription->discount_amount ?? 0) }}">
        <small class="text-muted">خصم الأخوات أو خصومات أخرى</small>
    </div>

    {{-- المبلغ المدفوع --}}
    <div class="col-md-3">
        <label class="form-label fw-semibold">المبلغ المدفوع (ج)</label>
        <input type="number" name="paid_amount" class="form-control"
               step="0.01" min="0" max="999999.99"
               placeholder="0.00"
               value="{{ old('paid_amount', $subscription->paid_amount ?? 0) }}">
    </div>

    {{-- الحالة --}}
    <div class="col-md-3">
        <label class="form-label fw-semibold">
            الحالة <span class="text-danger">*</span>
        </label>
        <select id="status" name="status" class="form-select">
            @foreach($statuses as $status)
                <option value="{{ $status }}"
                    @selected(old('status', $subscription->status ?? 'نشط') === $status)>
                    {{ $status }}
                </option>
            @endforeach
        </select>
    </div>

</div>

<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label fw-semibold">المبلغ المتبقي (ج)</label>
        <input type="text" id="remaining_amount_preview" class="form-control" readonly
               value="{{ number_format(max(0, (old('amount', $subscription->amount ?? 0) - old('discount_amount', $subscription->discount_amount ?? 0) - old('paid_amount', $subscription->paid_amount ?? 0))), 2, '.', '') }}">
        <small class="text-muted">يتم احتساب المتبقي تلقائيًا من (المبلغ الأساسي - الخصم - المدفوع)</small>
    </div>
</div>

<script>
    (function () {
        const feePlanSelect = document.getElementById('fee_plan_id');
        const amountInput = document.querySelector('input[name="amount"]');
        const discountInput = document.querySelector('input[name="discount_amount"]');
        const paidInput = document.querySelector('input[name="paid_amount"]');
        const remainingInput = document.getElementById('remaining_amount_preview');
        const feePlanAmountApi = "{{ route('admin.fee-plan-amount') }}";

        if (!feePlanSelect || !amountInput || !discountInput || !paidInput || !remainingInput) {
            return;
        }

        const toNumber = (value) => {
            const number = parseFloat(value);
            return Number.isFinite(number) ? number : 0;
        };

        const updateRemaining = () => {
            const amount = toNumber(amountInput.value);
            const discount = toNumber(discountInput.value);
            const paid = toNumber(paidInput.value);
            const remaining = Math.max(0, amount - discount - paid);
            remainingInput.value = remaining.toFixed(2);
        };

        amountInput.addEventListener('input', updateRemaining);
        discountInput.addEventListener('input', updateRemaining);
        paidInput.addEventListener('input', updateRemaining);

        function loadFeePlanAmount(feePlanId) {
            if (!feePlanId) {
                return;
            }

            fetch(feePlanAmountApi + '?fee_plan_id=' + encodeURIComponent(feePlanId), {
                headers: {'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'},
                credentials: 'same-origin'
            })
                .then(r => {
                    if (!r.ok) {
                        throw new Error('Failed to fetch fee plan amount');
                    }

                    return r.json();
                })
                .then(data => {
                    amountInput.value = Number(data.amount || 0).toFixed(2);
                    updateRemaining();
                })
                .catch(() => {
                    // keep current value if endpoint fails
                });
        }

        feePlanSelect.addEventListener('change', function () {
            loadFeePlanAmount(this.value);
        });

        if (feePlanSelect.value && !amountInput.value) {
            loadFeePlanAmount(feePlanSelect.value);
        }

        updateRemaining();
    })();
</script>

