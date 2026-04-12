{{-- ======================================================
     _form: حقول اشتراك الطالب
     المتغيرات:
       $studentOptions       — قائمة الطلاب  [id => name]
       $studentStatuses      — حالة كل طالب  [id => 'active'|'inactive'] (اختياري)
       $feePlanOptions       — قائمة خطط الرسوم
       $statuses             — حالات الاشتراك
       $subscription         — (للتعديل اختياري)
====================================================== --}}

{{-- ─── حالات الطلاب مُمرَّرة للـ JS ───────────────────── --}}
<script>
    window._studentStatuses = @json($studentStatuses ?? []);
</script>

<div class="row g-3 mb-3">

    {{-- الطالب --}}
    <div class="col-md-6">
        <label class="form-label fw-semibold">
            الطالب <span class="text-danger">*</span>
        </label>
        <select id="js-student-select" name="student_id" class="form-select js-student-select2" data-placeholder="ابحث واختر الطالب">
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
        {{-- دورة الدفع --}}
        <div id="payment_cycle_badge" class="mt-1" style="display:none;">
            <span class="badge bg-info text-dark"><i class="ti ti-refresh me-1"></i>دورة الدفع: <span id="payment_cycle_label">-</span></span>
        </div>
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

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <label class="form-label fw-semibold">
            طريقة الدفع <span class="text-danger">*</span>
        </label>
        <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror">
            <option value="">— اختر طريقة الدفع —</option>
            @foreach($paymentMethodOptions as $paymentMethodValue => $paymentMethodLabel)
                <option value="{{ $paymentMethodValue }}"
                    @selected(old('payment_method', $subscription->payment_method ?? 'cash') === $paymentMethodValue)>
                    {{ $paymentMethodLabel }}
                </option>
            @endforeach
        </select>
        @error('payment_method')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <label class="form-label fw-semibold">المبلغ المتبقي (ج)</label>
        <input type="text" id="remaining_amount_preview" class="form-control" readonly
               value="{{ number_format(max(0, (old('amount', $subscription->amount ?? 0) - old('discount_amount', $subscription->discount_amount ?? 0) - old('paid_amount', $subscription->paid_amount ?? 0))), 2, '.', '') }}">
        <small class="text-muted">يتم احتساب المتبقي تلقائيًا من (المبلغ الأساسي - الخصم - المدفوع)</small>
    </div>
</div>

{{-- ─── التواريخ ──────────────────────────────────────── --}}
<hr class="my-3">
<h6 class="fw-semibold mb-3 text-primary"><i class="ti ti-calendar me-1"></i> تواريخ الاشتراك</h6>

<div class="row g-3 mb-3">

    {{-- تاريخ البداية --}}
    <div class="col-md-4">
        <label class="form-label fw-semibold">
            تاريخ البداية <span class="text-danger">*</span>
        </label>
        <input type="date" id="start_date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
               value="{{ old('start_date', optional($subscription->start_date ?? null)->format('Y-m-d') ?? now()->format('Y-m-d')) }}">
        @error('start_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="text-muted">تاريخ بداية الاشتراك</small>
    </div>

    {{-- تاريخ الاستحقاق --}}
    <div class="col-md-4">
        <label class="form-label fw-semibold" id="due_date_label">
            تاريخ الاستحقاق
            <span class="text-danger" id="due_date_required_star" style="display:none;">*</span>
        </label>
        <input type="date" id="due_date" name="due_date"
               class="form-control @error('due_date') is-invalid @enderror"
               value="{{ old('due_date', optional($subscription->due_date ?? null)->format('Y-m-d') ?? '') }}">
        @error('due_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="text-muted" id="due_date_hint">يُحسب تلقائيًا من دورة الدفع. لا يُقبل دفع جديد بعد هذا التاريخ.</small>
        <small class="text-danger d-none" id="due_date_active_hint">مطلوب — الطالب نشط ويجب تحديد تاريخ الاستحقاق لتجديد اشتراكه.</small>
    </div>

    {{-- تاريخ استحقاق الباقي --}}
    <div class="col-md-4">
        <label class="form-label fw-semibold">تاريخ استحقاق الباقي</label>
        <input type="date" id="remaining_due_date" name="remaining_due_date" class="form-control @error('remaining_due_date') is-invalid @enderror"
               value="{{ old('remaining_due_date', optional($subscription->remaining_due_date ?? null)->format('Y-m-d') ?? '') }}">
        @error('remaining_due_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="text-muted">قبل هذا الموعد بيومين يصل تنبيه في الإشعارات</small>
    </div>

</div>

<script>
    (function () {
        const feePlanSelect    = document.getElementById('fee_plan_id');
        const amountInput      = document.querySelector('input[name="amount"]');
        const discountInput    = document.querySelector('input[name="discount_amount"]');
        const paidInput        = document.querySelector('input[name="paid_amount"]');
        const remainingInput   = document.getElementById('remaining_amount_preview');
        const startDateInput   = document.getElementById('start_date');
        const dueDateInput     = document.getElementById('due_date');
        const remainingDueInput = document.getElementById('remaining_due_date');
        const cycleBadge       = document.getElementById('payment_cycle_badge');
        const cycleLabel       = document.getElementById('payment_cycle_label');
        const feePlanAmountApi = "{{ route('admin.fee-plan-amount') }}";

        // ─── due_date required indicator ─────────────────────────────
        const dueDateStar      = document.getElementById('due_date_required_star');
        const dueDateHint      = document.getElementById('due_date_hint');
        const dueDateActiveHint = document.getElementById('due_date_active_hint');
        const studentSelect    = document.getElementById('js-student-select');
        const studentStatuses  = window._studentStatuses || {};

        function applyStudentActiveState(studentId) {
            const isActive = studentStatuses[studentId] === 'active';
            if (dueDateStar) dueDateStar.style.display = isActive ? '' : 'none';
            if (dueDateInput) dueDateInput.required = isActive;
            if (dueDateHint) dueDateHint.classList.toggle('d-none', isActive);
            if (dueDateActiveHint) dueDateActiveHint.classList.toggle('d-none', !isActive);
        }

        if (studentSelect) {
            studentSelect.addEventListener('change', function () {
                applyStudentActiveState(this.value);
            });
            // تطبيق الحالة عند تحميل الصفحة (للتعديل)
            applyStudentActiveState(studentSelect.value);
        }
        // ─────────────────────────────────────────────────────────────

        if (!feePlanSelect || !amountInput) return;

        const toNumber = (value) => {
            const n = parseFloat(value);
            return Number.isFinite(n) ? n : 0;
        };

        const updateRemaining = () => {
            const amount   = toNumber(amountInput.value);
            const discount = toNumber(discountInput.value);
            const paid     = toNumber(paidInput.value);
            remainingInput.value = Math.max(0, amount - discount - paid).toFixed(2);
        };

        // حساب تاريخ الاستحقاق تلقائيًا من تاريخ البداية + دورة الدفع
        let currentCycle = null;

        const computeDueDate = () => {
            if (!startDateInput || !currentCycle) return;
            const start = new Date(startDateInput.value);
            if (isNaN(start.getTime())) return;

            let due = null;
            if (currentCycle === 'شهري') {
                due = new Date(start);
                due.setMonth(due.getMonth() + 1);
            } else if (currentCycle === 'نصف شهري') {
                due = new Date(start);
                due.setDate(due.getDate() + 15);
            } else if (currentCycle === 'أسبوعي') {
                due = new Date(start);
                due.setDate(due.getDate() + 7);
            } else {
                // بالحلقة - لا حساب تلقائي
                return;
            }

            const fmt = (d) => d.toISOString().split('T')[0];
            if (dueDateInput && !dueDateInput.value) dueDateInput.value = fmt(due);
            if (remainingDueInput && !remainingDueInput.value) remainingDueInput.value = fmt(due);
        };

        amountInput.addEventListener('input', updateRemaining);
        discountInput.addEventListener('input', updateRemaining);
        paidInput.addEventListener('input', updateRemaining);
        if (startDateInput) startDateInput.addEventListener('change', computeDueDate);

        function loadFeePlanData(feePlanId) {
            if (!feePlanId) return;

            fetch(feePlanAmountApi + '?fee_plan_id=' + encodeURIComponent(feePlanId), {
                headers: {'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'},
                credentials: 'same-origin'
            }).then(function (r) {
                return r.ok ? r.json() : Promise.reject();
            }).then(function (data) {
                amountInput.value = Number(data.amount || 0).toFixed(2);
                updateRemaining();

                // إظهار دورة الدفع
                if (data.payment_cycle) {
                    currentCycle = data.payment_cycle;
                    cycleLabel.textContent = data.payment_cycle;
                    cycleBadge.style.display = '';
                    // احسب التاريخ إن لم يكن محددًا
                    computeDueDate();
                } else {
                    cycleBadge.style.display = 'none';
                }
            }).catch(function () {
                cycleBadge.style.display = 'none';
            });
        }

        feePlanSelect.addEventListener('change', function () {
            loadFeePlanData(this.value);
        });

        if (feePlanSelect.value) {
            loadFeePlanData(feePlanSelect.value);
        }

        updateRemaining();
    })();
</script>
