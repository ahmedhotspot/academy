@extends('admin.layouts.master')

@section('title', 'إدارة اشتراكات الطلاب — تعديل الاشتراك')

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'تعديل الاشتراك',
                    'breadcrumbs' => $breadcrumbs,
                ])

                @include('admin.partials.alerts')

                <form action="{{ route('admin.student-subscriptions.update', $subscription) }}"
                      method="POST" autocomplete="off">
                    @csrf
                    @method('PUT')

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <i class="ti ti-pencil text-primary fs-5"></i>
                                <h6 class="mb-0 fw-semibold">تعديل بيانات الاشتراك</h6>
                            </div>
                            <span class="badge bg-light text-dark border">
                                #{{ $subscription->id }}
                            </span>
                        </div>
                        <div class="card-body">
                            @include('admin.student-subscriptions._form', [
                                'subscription'    => $subscription,
                                'studentOptions'  => $studentOptions,
                                'feePlanOptions'  => $feePlanOptions,
                                'statuses'        => $statuses,
                            ])
                        </div>
                        <div class="card-footer bg-white border-top d-flex gap-2 justify-content-end">
                            <a href="{{ route('admin.student-subscriptions.show', $subscription) }}"
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

