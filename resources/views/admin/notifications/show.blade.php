@extends('admin.layouts.master')

@section('title', 'تفاصيل الإشعار')

@section('content')
    @php
        $notificationInfo = $profile['notification'] ?? [];
        $stats = $profile['stats'] ?? [];
    @endphp

    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'الملف الشامل للإشعار',
                    'breadcrumbs' => $breadcrumbs,
                ])

                @include('admin.partials.alerts')

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div>
                                <p class="text-muted small mb-1">تفاصيل الإشعار</p>
                                <h4 class="fw-bold mb-1">{{ $notificationInfo['title'] ?? $notification->title }}</h4>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="badge bg-{{ $notificationInfo['type_color'] ?? $notification->type_color }}">
                                        <i class="{{ $notificationInfo['type_icon'] ?? $notification->type_icon }} me-1"></i>
                                        {{ $notificationInfo['type_label'] ?? $notification->type }}
                                    </span>
                                    <span class="badge {{ ($notificationInfo['is_read'] ?? $notification->is_read) ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ ($notificationInfo['is_read'] ?? $notification->is_read) ? 'مقروء' : 'غير مقروء' }}
                                    </span>
                                    <span class="badge bg-light text-dark border">{{ $notificationInfo['created_at'] ?? optional($notification->created_at)->format('Y-m-d H:i') }}</span>
                                </div>
                            </div>

                            <a href="{{ route('admin.notifications.index') }}" class="btn btn-light">
                                <i class="ti ti-arrow-right me-1"></i> رجوع
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">إجمالي الإشعارات</p>
                                <h4 class="fw-bold text-primary mb-0">{{ $stats['total'] ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">غير مقروءة</p>
                                <h4 class="fw-bold text-warning mb-0">{{ $stats['unread'] ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">من نفس النوع</p>
                                <h4 class="fw-bold text-info mb-0">{{ $stats['same_type_count'] ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">إشعارات اليوم</p>
                                <h4 class="fw-bold text-success mb-0">{{ $stats['today_count'] ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-xl-8">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-message me-1"></i> نص الإشعار</h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">{{ $notificationInfo['message'] ?? $notification->message }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-list-details me-1"></i> بيانات إضافية</h6>
                            </div>
                            <div class="card-body">
                                @forelse($profile['related_data'] ?? [] as $item)
                                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                        <span class="text-muted">{{ $item['key'] }}</span>
                                        <span class="fw-semibold">{{ $item['value'] }}</span>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-3">لا توجد بيانات إضافية</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-semibold"><i class="ti ti-history me-1"></i> أحدث إشعارات من نفس النوع</h6>
                        <span class="badge bg-info rounded-pill">{{ count($profile['recent_same_type'] ?? []) }}</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>العنوان</th>
                                <th>التاريخ</th>
                                <th>الحالة</th>
                                <th>العمليات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($profile['recent_same_type'] ?? [] as $item)
                                <tr>
                                    <td class="fw-semibold">{{ $item['title'] }}</td>
                                    <td>{{ $item['date'] }}</td>
                                    <td>
                                        <span class="badge {{ $item['is_read'] ? 'bg-success' : 'bg-warning text-dark' }}">
                                            {{ $item['is_read'] ? 'مقروء' : 'غير مقروء' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.notifications.show', $item['id']) }}" class="btn btn-sm btn-outline-primary">عرض</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-3">لا توجد إشعارات مشابهة</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

