@extends('admin.layouts.master')

@section('title', 'تعديل الدفعة')

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'تعديل الدفعة',
                    'breadcrumbs' => $breadcrumbs,
                ])

                @include('admin.partials.alerts')

                <form action="{{ route('admin.payments.update', $payment) }}"
                      method="POST" autocomplete="off">
                    @csrf
                    @method('PUT')

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <i class="ti ti-pencil text-primary fs-5"></i>
                                <h6 class="mb-0 fw-semibold">تعديل بيانات الدفعة</h6>
                            </div>
                            <span class="badge bg-light text-dark border">
                                {{ $payment->receipt_formatted }}
                            </span>
                        </div>
                        <div class="card-body">
                            {{-- حقول اللمس فقط --}}
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">الطالب</label>
                                    <input type="text" class="form-control bg-light" readonly
                                           value="{{ $payment->student?->full_name ?? '-' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">الاشتراك</label>
                                    <input type="text" class="form-control bg-light" readonly
                                           value="{{ $payment->subscription?->feePlan?->name ?? '-' }}">
                                </div>
                            </div>

                            {{-- حقول التعديل --}}
                            @include('admin.payments._form', [
                                'payment'        => $payment,
                                'studentOptions' => [],
                            ])
                        </div>
                        <div class="card-footer bg-white border-top d-flex gap-2 justify-content-end">
                            <a href="{{ route('admin.payments.show', $payment) }}"
                               class="btn btn-light">
                                <i class="ti ti-arrow-right me-1"></i> رجوع
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy me-1"></i> حفظ التعديلات
                            </button>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection

