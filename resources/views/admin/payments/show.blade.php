@extends('admin.layouts.master')

@section('title', 'إيصال قبض')

@section('css')
    <style>
        .payment-details-table th,
        .payment-details-table td {
            text-align: right !important;
        }
    </style>
@endsection

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'تفاصيل الدفعة والإيصال',
                    'breadcrumbs' => $breadcrumbs,
                ])

                @include('admin.partials.alerts')

                {{-- الإيصال (بتصميم احترافي) --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 10px;">
                        <div class="row g-3">
                            <div class="col-auto">
                                <div class="display-6 fw-bold">📄</div>
                            </div>
                            <div class="col">
                                <h5 class="fw-bold mb-0">إيصال قبض</h5>
                                <p class="mb-0 small">رقم الإيصال: {{ $payment->receipt_number }}</p>
                            </div>
                            <div class="col-auto text-end">
                                <p class="mb-1 fw-semibold">التاريخ: {{ $payment->formatted_payment_date }}</p>
                                <button onclick="window.print()" class="btn btn-sm btn-light">
                                    <i class="ti ti-printer me-1"></i> طباعة
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- بيانات الدفعة --}}
                <div class="row g-3 mb-4">

                    <div class="col-xl-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-user me-1"></i> بيانات الطالب</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <p class="text-muted small mb-1">اسم الطالب</p>
                                    <h6 class="fw-bold">{{ $payment->student?->full_name ?? '-' }}</h6>
                                </div>
                                <div class="mb-2">
                                    <p class="text-muted small mb-1">الفرع</p>
                                    <h6 class="fw-bold">{{ $payment->student?->branch?->name ?? '-' }}</h6>
                                </div>
                                <div>
                                    <p class="text-muted small mb-1">الهاتف</p>
                                    <h6 class="fw-bold">{{ $payment->student?->phone ?? '-' }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-receipt me-1"></i> بيانات الاشتراك</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <p class="text-muted small mb-1">خطة الرسوم</p>
                                    <h6 class="fw-bold">{{ $payment->subscription?->feePlan?->name ?? '-' }}</h6>
                                </div>
                                <div class="mb-2">
                                    <p class="text-muted small mb-1">دورة الدفع</p>
                                    <h6 class="fw-bold">{{ $payment->subscription?->feePlan?->payment_cycle ?? '-' }}</h6>
                                </div>
                                <div>
                                    <p class="text-muted small mb-1">الحالة</p>
                                    <span class="badge {{ $payment->subscription?->status_badge_class }}">
                                        {{ $payment->subscription?->status ?? '-' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- تفاصيل المبلغ --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-semibold"><i class="ti ti-coin me-1"></i> تفاصيل المبلغ</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0 text-end payment-details-table">
                                <tr class="border-bottom">
                                    <td class="text-muted fw-semibold">المبلغ المدفوع</td>
                                    <td class="text-end fw-bold text-success fs-5">{{ $payment->formatted_amount }}</td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted fw-semibold">تاريخ الدفع</td>
                                    <td class="text-end fw-semibold">{{ $payment->formatted_payment_date }}</td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted fw-semibold">رقم الإيصال</td>
                                    <td class="text-end fw-semibold">{{ $payment->receipt_number }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">المتبقي على الاشتراك</td>
                                    <td class="text-end fw-bold {{ $payment->subscription?->remaining_amount > 0 ? 'text-danger' : 'text-success' }}">
                                        {{ $payment->subscription?->formatted_remaining_amount ?? '-' }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- الملاحظات --}}
                @if($payment->notes)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-semibold"><i class="ti ti-notes me-1"></i> ملاحظات</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $payment->notes }}</p>
                        </div>
                    </div>
                @endif

                {{-- حالة الاشتراك --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-semibold"><i class="ti ti-progress me-1"></i> حالة الاشتراك</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div>
                                    <p class="text-muted small mb-1">الإجمالي</p>
                                    <h6 class="fw-bold">{{ $payment->subscription?->formatted_final_amount ?? '-' }}</h6>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div>
                                    <p class="text-muted small mb-1">المدفوع</p>
                                    <h6 class="fw-bold text-success">{{ $payment->subscription?->formatted_paid_amount ?? '-' }}</h6>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div>
                                    <p class="text-muted small mb-1">المتبقي</p>
                                    <h6 class="fw-bold {{ ($payment->subscription?->remaining_amount ?? 0) > 0 ? 'text-danger' : 'text-success' }}">
                                        {{ $payment->subscription?->formatted_remaining_amount ?? '-' }}
                                    </h6>
                                </div>
                            </div>
                        </div>

                        {{-- شريط التقدم --}}
                        @if($payment->subscription)
                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small text-muted">نسبة الدفع</span>
                                    <span class="fw-bold text-primary">{{ $payment->subscription->payment_progress }}%</span>
                                </div>
                                <div class="progress" style="height: 12px;">
                                    <div class="progress-bar {{ $payment->subscription->payment_progress >= 100 ? 'bg-success' : 'bg-primary' }}"
                                         style="width: {{ min($payment->subscription->payment_progress, 100) }}%">
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- أزرار الإجراءات --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex gap-2 justify-content-between flex-wrap align-items-center">
                        <div>
                            <p class="text-muted small mb-0">
                                <i class="ti ti-info-circle me-1"></i>
                                تم إنشاء هذا الإيصال في {{ optional($payment->created_at)->format('Y-m-d H:i:s') }}
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.payments.index') }}" class="btn btn-light btn-sm">
                                <i class="ti ti-arrow-right me-1"></i> العودة للقائمة
                            </a>
                            <button onclick="window.print()" class="btn btn-primary btn-sm">
                                <i class="ti ti-printer me-1"></i> طباعة الإيصال
                            </button>
                            @can('payments.update')
                                <a href="{{ route('admin.payments.edit', $payment) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="ti ti-pencil me-1"></i> تعديل
                                </a>
                            @endcan
                            @can('payments.delete')
                                <form method="POST"
                                      action="{{ route('admin.payments.destroy', $payment) }}"
                                      onsubmit="return confirm('هل تريد حقاً حذف هذه الدفعة؟')"
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="ti ti-trash me-1"></i> حذف
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- طباعة الإيصال --}}
    <style media="print">
        .page-content-wrapper, .page-header, .btn:not(.no-print) {
            margin: 0; padding: 0;
        }
        .card { page-break-inside: avoid; }
        button, .btn { display: none !important; }
        .page-content { padding: 20px; }
    </style>
@endsection

