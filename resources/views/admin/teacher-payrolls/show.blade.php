@extends('admin.layouts.master')

@section('title', 'مستحق المعلم')

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'تفاصيل المستحق',
                    'breadcrumbs' => $breadcrumbs,
                ])

                @include('admin.partials.alerts')

                {{-- بطاقة الهوية --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row align-items-center g-4">
                            <div class="col-auto">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:80px;height:80px;">
                                    <i class="ti ti-user fs-2 text-primary"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h5 class="mb-2 fw-bold">{{ $payroll->teacher?->name ?? '-' }}</h5>
                                <div class="d-flex flex-wrap gap-4 text-muted small">
                                    <div>
                                        <p class="mb-1">الشهر</p>
                                        <p class="fw-semibold text-dark">{{ $payroll->month_year }}</p>
                                    </div>
                                    <div>
                                        <p class="mb-1">الحالة</p>
                                        <span class="badge {{ $payroll->status_badge_class }}">{{ $payroll->status }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto d-flex gap-2">
                                @can('teacher-payrolls.update')
                                    <a href="{{ route('admin.teacher-payrolls.edit', $payroll) }}" class="btn btn-primary">
                                        <i class="ti ti-pencil me-1"></i> تعديل
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>

                {{-- بطاقات المبالغ --}}
                <div class="row g-3 mb-4">

                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-2"><i class="ti ti-coin me-1"></i> الراتب الأساسي</p>
                                <h5 class="fw-bold mb-0">{{ $payroll->formatted_base_salary }}</h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100 border-start border-4 border-danger">
                            <div class="card-body">
                                <p class="text-muted small mb-2"><i class="ti ti-minus-vertical me-1"></i> الاستقطاع</p>
                                <h5 class="fw-bold mb-0 text-danger">- {{ $payroll->formatted_deduction }}</h5>
                                <small class="text-muted">{{ $absences }} غياب</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100 border-start border-4 border-warning">
                            <div class="card-body">
                                <p class="text-muted small mb-2"><i class="ti ti-alert-circle me-1"></i> الجزاء</p>
                                <h5 class="fw-bold mb-0 text-warning">- {{ $payroll->formatted_penalty }}</h5>
                                <small class="text-muted">عقوبات</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100 border-start border-4 border-success">
                            <div class="card-body">
                                <p class="text-muted small mb-2"><i class="ti ti-gift me-1"></i> المكافأة</p>
                                <h5 class="fw-bold mb-0 text-success">+ {{ $payroll->formatted_bonus }}</h5>
                                <small class="text-muted">حوافز</small>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- الصافي النهائي --}}
                <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <div class="card-body">
                        <div class="row g-3 align-items-center">
                            <div class="col">
                                <p class="mb-1 small opacity-75">المبلغ المستحق النهائي</p>
                                <h3 class="mb-0 fw-bold">{{ $payroll->formatted_final }}</h3>
                            </div>
                            <div class="col-auto">
                                @if($payroll->status === 'غير مصروف')
                                    <form method="POST" action="{{ route('admin.teacher-payrolls.mark-as-processed', $payroll) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-light btn-sm">
                                            <i class="ti ti-circle-check me-1"></i> تحديث إلى مصروف
                                        </button>
                                    </form>
                                @else
                                    <span class="badge bg-light text-dark">✓ مصروف</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ملخص التفاصيل --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-semibold"><i class="ti ti-list me-1"></i> ملخص الحساب</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle">
                                <tr class="border-bottom">
                                    <td class="text-muted fw-semibold">المعلم</td>
                                    <td>{{ $payroll->teacher?->name ?? '-' }}</td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted fw-semibold">الفترة</td>
                                    <td>{{ $payroll->month_year }}</td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted fw-semibold">الراتب الأساسي</td>
                                    <td class="fw-semibold">{{ $payroll->formatted_base_salary }}</td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted fw-semibold">الاستقطاع ({{ $absences }} غياب)</td>
                                    <td class="fw-semibold text-danger">- {{ $payroll->formatted_deduction }}</td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted fw-semibold">الجزاء</td>
                                    <td class="fw-semibold text-warning">- {{ $payroll->formatted_penalty }}</td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted fw-semibold">المكافأة</td>
                                    <td class="fw-semibold text-success">+ {{ $payroll->formatted_bonus }}</td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted fw-semibold">المبلغ المستحق</td>
                                    <td class="fw-bold text-primary fs-5">{{ $payroll->formatted_final }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">الحالة</td>
                                    <td><span class="badge {{ $payroll->status_badge_class }}">{{ $payroll->status }}</span></td>
                                </tr>
                            </table>
                        </div>

                        @if($payroll->notes)
                            <div class="mt-3 pt-3 border-top">
                                <p class="text-muted small mb-1">الملاحظات:</p>
                                <p class="mb-0">{{ $payroll->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- أزرار الإجراءات --}}
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body d-flex gap-2 justify-content-end flex-wrap">
                        <a href="{{ route('admin.teacher-payrolls.index') }}" class="btn btn-light btn-sm">
                            <i class="ti ti-arrow-right me-1"></i> العودة للقائمة
                        </a>
                        @can('teacher-payrolls.update')
                            <a href="{{ route('admin.teacher-payrolls.edit', $payroll) }}" class="btn btn-primary btn-sm">
                                <i class="ti ti-pencil me-1"></i> تعديل
                            </a>
                        @endcan
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

