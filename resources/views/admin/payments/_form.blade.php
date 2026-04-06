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
            المبلغ (ج) <span class="text-danger">*</span>
        </label>
        <input type="number" id="amount" name="amount" class="form-control"
               step="0.01" min="0.01" max="999999.99"
               placeholder="0.00"
               value="{{ old('amount', $payment->amount ?? '') }}">
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">المتبقي</label>
        <input type="text" id="remaining_amount_preview" class="form-control bg-light" readonly
               value="{{ isset($payment) && $payment->subscription ? $payment->subscription->formatted_remaining_amount : '-' }}">
    </div>

</div>

<div class="row g-3 mb-3">
    <div class="col-12">
        <div id="payment-lock-alert" class="alert alert-warning d-none mb-0">
            لا يمكن حفظ دفعة جديدة لأن هذا الاشتراك لا يحتوي على مبلغ متبق.
        </div>
    </div>
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
        const form = document.querySelector('#student_id')?.closest('form');
        const studentSelect = document.getElementById('student_id');
        const subscriptionSelect = document.getElementById('student_subscription_id');
        const apiUrl = "{{ route('admin.student-subscriptions') }}";
        const balanceApiUrl = "{{ route('admin.subscription-balance') }}";
        const amountInput = document.getElementById('amount');
        const remainingInput = document.getElementById('remaining_amount_preview');
        const lockAlert = document.getElementById('payment-lock-alert');
        const submitButton = form ? form.querySelector('button[type="submit"]') : null;

        function setPaymentLocked(locked) {
            if (submitButton) {
                submitButton.disabled = locked;
            }

            if (lockAlert) {
                lockAlert.classList.toggle('d-none', !locked);
            }
        }

        function loadSubscriptions(studentId) {
            if (!studentId) {
                subscriptionSelect.innerHTML = '<option value="">— اختر الاشتراك —</option>';
                if (remainingInput) {
                    remainingInput.value = '-';
                }
                setPaymentLocked(false);
                return;
            }

            // Get CSRF token from meta tag
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

            fetch(apiUrl + '?student_id=' + encodeURIComponent(studentId), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(r => {
                if (!r.ok) {
                    throw new Error(`HTTP error! status: ${r.status}`);
                }
                return r.json();
            })
            .then(subs => {
                subscriptionSelect.innerHTML = '<option value="">— اختر الاشتراك —</option>';
                if (Array.isArray(subs) && subs.length > 0) {
                    subs.forEach(function (sub) {
                        const opt = document.createElement('option');
                        opt.value = sub.id;
                        opt.textContent = sub.plan_name + ' (متبقي: ' + sub.remaining + ')';
                        opt.dataset.remainingAmount = sub.remaining_amount ?? 0;
                        subscriptionSelect.appendChild(opt);
                    });
                } else {
                    subscriptionSelect.innerHTML = '<option value="">لا توجد اشتراكات متاحة</option>';
                    setPaymentLocked(true);
                }
            })
            .catch(error => {
                console.error('خطأ:', error);
                subscriptionSelect.innerHTML = '<option value="">تعذّر تحميل الاشتراكات</option>';
                setPaymentLocked(true);
            });
        }

        function loadSubscriptionBalance() {
            const subscriptionId = subscriptionSelect.value;
            const studentId = studentSelect.value;

            if (!subscriptionId || !studentId) {
                if (remainingInput) {
                    remainingInput.value = '-';
                }
                setPaymentLocked(false);
                return;
            }

            fetch(balanceApiUrl + '?student_id=' + encodeURIComponent(studentId) + '&student_subscription_id=' + encodeURIComponent(subscriptionId), {
                headers: {'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'},
                credentials: 'same-origin'
            })
                .then(r => {
                    if (!r.ok) {
                        throw new Error('Failed to fetch subscription balance');
                    }

                    return r.json();
                })
                .then(data => {
                    const remaining = Number(data.remaining_amount || 0);
                    if (remainingInput) {
                        remainingInput.value = data.formatted_remaining_amount || remaining.toFixed(2);
                    }

                    if (amountInput && !amountInput.value) {
                        amountInput.value = remaining > 0 ? remaining.toFixed(2) : '';
                    }

                    setPaymentLocked(remaining <= 0);
                })
                .catch(() => {
                    if (remainingInput) {
                        remainingInput.value = '-';
                    }
                    setPaymentLocked(true);
                });
        }

        studentSelect.addEventListener('change', function () {
            loadSubscriptions(this.value);
        });

        subscriptionSelect.addEventListener('change', function () {
            if (amountInput) {
                amountInput.value = '';
            }
            loadSubscriptionBalance();
        });

        if (studentSelect.value) {
            loadSubscriptions(studentSelect.value);
        }

        if (subscriptionSelect.value) {
            loadSubscriptionBalance();
        }
    })();
</script>

