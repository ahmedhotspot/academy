@extends('admin.layouts.master')

@section('title', 'اشتراك الطالب')

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'تفاصيل الاشتراك',
                    'breadcrumbs' => $breadcrumbs,
                ])

                @include('admin.partials.alerts')

                {{-- تنبيهات خاصة للاشتراكات القريبة والمنتهية --}}
                @if($subscription->is_expired)
                    <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 shadow-sm" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-alert-octagon me-2 fs-5"></i>
                            <div>
                                <strong>⚠️ اشتراك منتهي!</strong>
                                <p class="mb-0 small mt-1">تاريخ الاستحقاق: <span class="fw-bold">{{ optional($subscription->due_date)->format('Y-m-d') }}</span>
                                    @php $daysOverdue = now()->startOfDay()->diffInDays($subscription->due_date->startOfDay()); @endphp
                                    <br>المتبقي: <span class="fw-bold text-danger">{{ $daysOverdue }} يوم{{ $daysOverdue > 1 ? 's' : '' }} مضت</span></p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
                    </div>
                @elseif($subscription->days_until_due !== null && $subscription->days_until_due >= 0 && $subscription->days_until_due <= 2 && $subscription->remaining_amount > 0)
                    <div class="alert alert-warning alert-dismissible fade show border-0 rounded-3 shadow-sm" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-clock-exclamation me-2 fs-5"></i>
                            <div>
                                <strong>⏰ اشتراك قريب الانتهاء!</strong>
                                <p class="mb-0 small mt-1">سينتهي في: <span class="fw-bold">{{ $subscription->days_until_due }} يوم{{ $subscription->days_until_due != 1 ? 's' : '' }}</span></p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
                    </div>
                @endif

                {{-- بطاقة الطالب والخطة --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-auto">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:80px;height:80px;">
                                    <i class="ti ti-user fs-2 text-primary"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h5 class="mb-2 fw-bold">{{ $subscription->student?->full_name ?? '-' }}</h5>
                                <div class="d-flex flex-wrap gap-4 text-muted small">
                                    <div>
                                        <p class="mb-1">خطة الرسوم</p>
                                        <p class="fw-semibold text-dark">{{ $subscription->feePlan?->name ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="mb-1">معرّف الاشتراك</p>
                                        <p class="fw-semibold text-dark">#{{ $subscription->id }}</p>
                                    </div>
                                    <div>
                                        <p class="mb-1">تاريخ الإنشاء</p>
                                        <p class="fw-semibold text-dark">{{ optional($subscription->created_at)->format('Y-m-d') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto d-flex gap-2">
                                @can('student-subscriptions.update')
                                    <a href="{{ route('admin.student-subscriptions.edit', $subscription) }}" class="btn btn-primary">
                                        <i class="ti ti-pencil me-1"></i> تعديل
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>

                {{-- بطاقات التفاصيل المالية --}}
                <div class="row g-3 mb-4">

                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-2"><i class="ti ti-coin me-1"></i> المبلغ الأساسي</p>
                                <h5 class="fw-bold mb-0">{{ $subscription->formatted_amount }}</h5>
                                <small class="text-muted">بدون خصم</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100 border-start border-4 border-info">
                            <div class="card-body">
                                <p class="text-muted small mb-2"><i class="ti ti-discount me-1"></i> الخصم</p>
                                <h5 class="fw-bold mb-0 text-info">{{ $subscription->formatted_discount }}</h5>
                                @if($subscription->discount_amount > 0)
                                    <small class="text-muted">تم تطبيق خصم</small>
                                @else
                                    <small class="text-muted">بدون خصم</small>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100 border-start border-4 border-success">
                            <div class="card-body">
                                <p class="text-muted small mb-2"><i class="ti ti-calculator me-1"></i> المبلغ النهائي</p>
                                <h5 class="fw-bold mb-0 text-success">{{ $subscription->formatted_final_amount }}</h5>
                                <small class="text-muted">بعد الخصم</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100 border-start border-4 border-primary">
                            <div class="card-body">
                                <p class="text-muted small mb-2"><i class="ti ti-circle-check me-1"></i> الحالة</p>
                                <h5 class="fw-bold mb-0">{{ $subscription->status }}</h5>
                                <span class="badge {{ $subscription->status_badge_class }} mt-1">{{ $subscription->status }}</span>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- الدفع والمتبقي --}}
                <div class="row g-3 mb-4">

                    <div class="col-xl-6 col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="fw-semibold mb-3"><i class="ti ti-cash me-1"></i> المبلغ المدفوع</h6>
                                <h4 class="fw-bold text-success">{{ $subscription->formatted_paid_amount }}</h4>
                                <p class="text-muted small">من أصل {{ $subscription->formatted_final_amount }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6 col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="fw-semibold mb-3"><i class="ti ti-alert-circle me-1"></i> المبلغ المتبقي</h6>
                                <h4 class="fw-bold {{ $subscription->remaining_amount > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ $subscription->formatted_remaining_amount }}
                                </h4>
                                @if($subscription->remaining_amount > 0)
                                    <p class="text-muted small">مبلغ معلق</p>
                                @else
                                    <p class="text-muted small">تم الدفع بالكامل ✓</p>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>

                {{-- شريط التقدم --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-semibold small">نسبة الدفع</span>
                            <span class="fw-bold {{ $subscription->payment_progress >= 100 ? 'text-success' : ($subscription->payment_progress >= 50 ? 'text-warning' : 'text-danger') }}">
                                {{ $subscription->payment_progress }}%
                            </span>
                        </div>
                        <div class="progress" style="height:15px;">
                            <div class="progress-bar {{ $subscription->payment_progress >= 100 ? 'bg-success' : ($subscription->payment_progress >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                 role="progressbar"
                                 style="width: {{ min($subscription->payment_progress, 100) }}%"
                                 aria-valuenow="{{ $subscription->payment_progress }}"
                                 aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ملخص الاشتراك --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-semibold"><i class="ti ti-list me-1"></i> ملخص الاشتراك</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle">
                                <tr class="border-bottom">
                                    <td class="text-muted fw-semibold">معرّف الاشتراك</td>
                                    <td><code>#{{ $subscription->id }}</code></td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted fw-semibold">الطالب</td>
                                    <td>{{ $subscription->student?->full_name ?? '-' }}</td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted fw-semibold">خطة الرسوم</td>
                                    <td>{{ $subscription->feePlan?->name ?? '-' }} <span class="badge bg-info text-dark ms-1">{{ $subscription->feePlan?->payment_cycle ?? '-' }}</span></td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted fw-semibold">المبلغ الأساسي</td>
                                    <td class="fw-semibold">{{ $subscription->formatted_amount }}</td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted fw-semibold">الخصم</td>
                                    <td class="fw-semibold text-info">- {{ $subscription->formatted_discount }}</td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted fw-semibold">المبلغ النهائي</td>
                                    <td class="fw-bold text-success">{{ $subscription->formatted_final_amount }}</td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted fw-semibold">المبلغ المدفوع</td>
                                    <td class="fw-semibold">{{ $subscription->formatted_paid_amount }}</td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted fw-semibold">المبلغ المتبقي</td>
                                    <td class="fw-bold {{ $subscription->remaining_amount > 0 ? 'text-danger' : 'text-success' }}">{{ $subscription->formatted_remaining_amount }}</td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted fw-semibold">الحالة</td>
                                    <td><span class="badge {{ $subscription->status_badge_class }}">{{ $subscription->status }}</span></td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted fw-semibold">تاريخ البداية</td>
                                    <td>{{ optional($subscription->start_date)->format('Y-m-d') ?? '-' }}</td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted fw-semibold">تاريخ الاستحقاق</td>
                                    <td>
                                        {{ optional($subscription->due_date)->format('Y-m-d') ?? '-' }}
                                        @if($subscription->is_expired)
                                            <span class="badge bg-danger ms-1">منتهي</span>
                                        @elseif($subscription->due_date)
                                            @php $days = $subscription->days_until_due; @endphp
                                            @if($days !== null && $days >= 0)
                                                <span class="badge bg-success ms-1">{{ $days }} يوم متبقي</span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted fw-semibold">موعد سداد الباقي</td>
                                    <td>
                                        {{ optional($subscription->remaining_due_date)->format('Y-m-d') ?? '-' }}
                                        @if($subscription->remaining_amount > 0 && $subscription->remaining_due_date)
                                            @php $rDays = $subscription->days_until_remaining_due; @endphp
                                            @if($rDays !== null && $rDays <= 2 && $rDays >= 0)
                                                <span class="badge bg-warning text-dark ms-1">قريباً ({{ $rDays }} يوم)</span>
                                            @elseif($rDays !== null && $rDays < 0)
                                                <span class="badge bg-danger ms-1">متأخر</span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">تاريخ الإنشاء</td>
                                    <td>{{ optional($subscription->created_at)->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- أزرار الإجراءات --}}
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body d-flex gap-2 justify-content-end flex-wrap">
                        <a href="{{ route('admin.student-subscriptions.index') }}" class="btn btn-light btn-sm">
                            <i class="ti ti-arrow-right me-1"></i> العودة للقائمة
                        </a>
                        @can('student-subscriptions.update')
                            <a href="{{ route('admin.student-subscriptions.edit', $subscription) }}" class="btn btn-primary btn-sm">
                                <i class="ti ti-pencil me-1"></i> تعديل
                            </a>
                        @endcan
                        @can('student-subscriptions.create')
                            @if(($subscription->student?->status ?? '') === 'active')
                                <form method="POST"
                                      action="{{ route('admin.student-subscriptions.renew', $subscription) }}"
                                      onsubmit="return confirm('هل تريد تجديد هذا الاشتراك؟ سيتم إنشاء اشتراك جديد.')"
                                      class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="ti ti-refresh me-1"></i> تجديد الاشتراك
                                    </button>
                                </form>
                            @else
                                <button type="button" class="btn btn-secondary btn-sm" disabled title="الطالب غير نشط — لا يمكن التجديد">
                                    <i class="ti ti-refresh me-1"></i> تجديد الاشتراك
                                </button>
                            @endif
                        @endcan
                        @can('student-subscriptions.delete')
                            <form method="POST"
                                  action="{{ route('admin.student-subscriptions.destroy', $subscription) }}"
                                  onsubmit="return confirm('هل تريد حقاً حذف هذا الاشتراك؟')"
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
@endsection

