{{-- ======================================================
     Financial Panel: قسم مالي للطالب
     يعرض: الدفعات + آخر إيصال + المتبقي
====================================================== --}}

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-semibold"><i class="ti ti-coin me-1"></i> البيانات المالية</h6>
        @if($student->subscriptions()->count())
            <a href="{{ route('admin.student-subscriptions.index', ['student_id' => $student->id]) }}" class="badge bg-primary">
                عرض الاشتراكات
            </a>
        @endif
    </div>
    <div class="card-body">

        {{-- الملخص المالي السريع --}}
        <div class="row g-3 mb-4">

            <div class="col-md-4">
                <div class="p-3 bg-light rounded">
                    <p class="text-muted small mb-1"><i class="ti ti-receipt me-1"></i> إجمالي الدفعات</p>
                    <h5 class="fw-bold mb-0 text-primary">
                        {{ count($studentPayments['payments']) }}
                    </h5>
                    <small class="text-muted">{{ number_format($studentPayments['totalPaid'], 2) }} ر.س</small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="p-3 bg-light rounded">
                    <p class="text-muted small mb-1"><i class="ti ti-wallet me-1"></i> آخر دفعة</p>
                    @if($studentPayments['lastPayment'])
                        <h6 class="fw-bold mb-0">{{ optional($studentPayments['lastPayment']->payment_date)->format('Y-m-d') }}</h6>
                        <small class="text-muted">{{ $studentPayments['lastPayment']->formatted_amount }}</small>
                    @else
                        <h6 class="fw-bold mb-0 text-muted">—</h6>
                        <small class="text-muted">لا توجد دفعات</small>
                    @endif
                </div>
            </div>

            <div class="col-md-4">
                <div class="p-3 bg-light rounded">
                    <p class="text-muted small mb-1"><i class="ti ti-alert-circle me-1"></i> المتبقي</p>
                    @php
                        $subscription = $student->subscriptions()->latest()->first();
                        $remaining = $subscription?->remaining_amount ?? 0;
                    @endphp
                    <h5 class="fw-bold mb-0 {{ $remaining > 0 ? 'text-danger' : 'text-success' }}">
                        {{ number_format($remaining, 2) }} ر.س
                    </h5>
                    <small class="text-muted">
                        @if($remaining > 0)
                            متبقي
                        @else
                            مكتمل ✓
                        @endif
                    </small>
                </div>
            </div>

        </div>

        {{-- جدول آخر الدفعات --}}
        @if(count($studentPayments['payments']))
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>الإيصال</th>
                            <th>التاريخ</th>
                            <th>المبلغ</th>
                            <th>العملية</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(array_slice($studentPayments['payments'], 0, 5) as $payment)
                            <tr>
                                <td class="small fw-semibold">{{ $payment['receipt'] }}</td>
                                <td class="small">{{ $payment['date'] }}</td>
                                <td class="small fw-bold text-success">{{ $payment['amount'] }}</td>
                                <td>
                                    <a href="{{ route('admin.payments.show', ['payment' => $payment['id']]) }}"
                                       class="btn btn-sm btn-outline-primary py-1">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">
                                    لا توجد دفعات مسجلة
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(count($studentPayments['payments']) > 5)
                <div class="mt-2 text-center">
                    <a href="{{ route('admin.payments.index', ['student_id' => $student->id]) }}" class="btn btn-sm btn-outline-primary">
                        عرض جميع الدفعات ({{ count($studentPayments['payments']) }})
                    </a>
                </div>
            @endif
        @else
            <div class="text-center text-muted py-4">
                <i class="ti ti-receipt-off fs-2 d-block mb-2 opacity-50"></i>
                لا توجد دفعات مسجلة لهذا الطالب
            </div>
        @endif

    </div>
</div>

