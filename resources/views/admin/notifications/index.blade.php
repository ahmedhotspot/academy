@extends('admin.layouts.master')

@section('title', 'الإشعارات')

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'الإشعارات',
                    'breadcrumbs' => $breadcrumbs,
                ])

                @include('admin.partials.alerts')

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body d-flex gap-2 align-items-center justify-content-between">
                        <div>
                            <p class="mb-0 text-muted">إجمالي الإشعارات غير المقروءة</p>
                            <h5 class="mb-0 fw-bold">{{ $unreadCount }}</h5>
                        </div>
                        @if($unreadCount > 0)
                            <form method="POST" action="{{ route('admin.notifications.mark-all-read') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="ti ti-check me-1"></i> تحديث الكل
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-semibold"><i class="ti ti-bell me-1"></i> قائمة الإشعارات</h6>
                    </div>
                    <div class="card-body p-0">
                        @forelse($notifications as $notif)
                            <div class="d-flex gap-3 p-3 border-bottom align-items-start {{ !$notif->is_read ? 'bg-light' : '' }}">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-{{ $notif->type_color }}">
                                        <i class="{{ $notif->type_icon }}"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 {{ !$notif->is_read ? 'fw-bold' : '' }}">{{ $notif->title }}</h6>
                                    <p class="mb-1 text-muted small">{{ $notif->message }}</p>
                                    <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                                </div>
                                <div class="flex-shrink-0">
                                    <a href="{{ route('admin.notifications.show', $notif) }}" class="btn btn-sm btn-outline-primary">
                                        عرض
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-muted">
                                <i class="ti ti-bell-off fs-2 d-block mb-2 opacity-50"></i>
                                لا توجد إشعارات
                            </div>
                        @endforelse
                    </div>
                    <div class="card-footer bg-white border-top">
                        {{ $notifications->links() }}
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

