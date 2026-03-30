{{-- ======================================================
     _form: حقول تسجيل الدفعة
     المتغيرات:
       $studentOptions  — قائمة الطلاب
       $payment         — (للتعديل اختياري)
====================================================== --}}

<div class="row g-3 mb-3">

    {{-- الطالب --}}
    <div class="col-md-6">
        <label class="form-label fw-semibold">
            الطالب <span class="text-danger">*</span>
        </label>
        <select id="student_id" name="student_id" class="form-select">
            <option value="">— اختر الطالب —</option>
            @foreach($studentOptions as $sId => $sName)
                <option value="{{ $sId }}"
                    @selected(old('student_id', $payment->student_id ?? '') == $sId)
                    data-subscriptions="true">
                    {{ $sName }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- الاشتراك --}}
    <div class="col-md-6">
        <label class="form-label fw-semibold">
            الاشتراك <span class="text-danger">*</span>
        </label>
        <select id="student_subscription_id" name="student_subscription_id" class="form-select">
            <option value="">— اختر الاشتراك —</option>
            @if(isset($payment) && $payment->subscription)
                <option value="{{ $payment->subscription->id }}" selected>
                    {{ $payment->subscription->feePlan?->name }} (متبقي: {{ $payment->subscription->formatted_remaining_amount }})
                </option>
            @endif
        </select>
    </div>

</div>

<div class="row g-3 mb-3">

    {{-- تاريخ الدفع --}}
    <div class="col-md-4">
        <label class="form-label fw-semibold">
            تاريخ الدفع <span class="text-danger">*</span>
        </label>
        <input type="date" name="payment_date" class="form-control"
               value="{{ old('payment_date', isset($payment) ? optional($payment->payment_date)->format('Y-m-d') : now()->toDateString()) }}">
    </div>

    {{-- المبلغ --}}
    <div class="col-md-4">
        <label class="form-label fw-semibold">
            المبلغ (ر.س) <span class="text-danger">*</span>
        </label>
        <input type="number" name="amount" class="form-control"
               step="0.01" min="0.01" max="999999.99"
               placeholder="0.00"
               value="{{ old('amount', $payment->amount ?? '') }}">
    </div>

    {{-- المتبقي (قراءة فقط) --}}
    @if(isset($payment) && $payment->subscription)
        <div class="col-md-4">
            <label class="form-label fw-semibold">المتبقي</label>
            <input type="text" class="form-control bg-light" readonly
                   value="{{ $payment->subscription->formatted_remaining_amount }}">
        </div>
    @endif

</div>

<div class="row g-3">
    <div class="col-12">
        <label class="form-label fw-semibold">ملاحظات</label>
        <textarea name="notes" rows="2" class="form-control"
                  placeholder="أضف أي ملاحظات عن الدفعة…">{{ old('notes', $payment->notes ?? '') }}</textarea>
    </div>
</div>

{{-- Ajax: تحميل الاشتراكات عند اختيار الطالب --}}
<script>
    (function () {
        const studentSelect = document.getElementById('student_id');
        const subscriptionSelect = document.getElementById('student_subscription_id');

        function loadSubscriptions(studentId) {
            if (!studentId) {
                subscriptionSelect.innerHTML = '<option value="">— اختر الاشتراك —</option>';
                return;
            }

            fetch('/admin/api/student-subscriptions?student_id=' + studentId, {
                headers: {'X-Requested-With': 'XMLHttpRequest'}
            })
            .then(r => r.json())
            .then(subs => {
                subscriptionSelect.innerHTML = '<option value="">— اختر الاشتراك —</option>';
                subs.forEach(function (sub) {
                    const opt = document.createElement('option');
                    opt.value = sub.id;
                    opt.textContent = sub.plan_name + ' (متبقي: ' + sub.remaining + ')';
                    subscriptionSelect.appendChild(opt);
                });
            })
            .catch(() => {
                subscriptionSelect.innerHTML = '<option value="">تعذّر تحميل الاشتراكات</option>';
            });
        }

        studentSelect.addEventListener('change', function () {
            loadSubscriptions(this.value);
        });

        if (studentSelect.value) {
            loadSubscriptions(studentSelect.value);
        }
    })();
</script>

