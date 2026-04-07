<div class="loader-bg">
    <div class="loader-track">
        <div class="loader-fill"></div>
    </div>
</div>
<!-- { Pre-loader } End -->
<!-- { Header } start -->
<header class="site-header">
    <div class="header-wrapper">
        <div class="me-auto flex-grow-1 d-flex align-items-center flex-wrap gap-2">
            <ul class="list-unstyled header-menu-nav">
                <li class="hdr-itm mob-hamburger">
                    <a href="#!" class="app-head-link" id="mobile-collapse">
                        <div class="hamburger hamburger-arrowturn">
                            <div class="hamburger-box">
                                <div class="hamburger-inner"></div>
                            </div>
                        </div>
                    </a>
                </li>
                <li class="hdr-itm d-lg-none">
                    <button class="app-head-link border-0 bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#mobile-header-search" aria-expanded="false" aria-controls="mobile-header-search">
                        <i class="ti ti-search"></i>
                    </button>
                </li>
            </ul>
            <div class="d-none d-md-none d-lg-block header-search ms-3">
                <form action="#">
                    <div class="input-group ">
                        <input class="form-control rounded-3" type="search" value="" id="searchInput" placeholder="Search">
                        <div class="search-btn">
                            <button class="p-0 btn rounded-0 rounded-end" type="button">
                                <i data-feather="search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <nav class="ms-auto header-actions-nav">
            <ul class="header-menu-nav list-unstyled">

                {{-- ═══ زر الإشعارات ═══ --}}
                <li class="hdr-itm dropdown">
                    <a class="app-head-link dropdown-toggle no-caret position-relative"
                       data-bs-toggle="dropdown" href="#" role="button"
                       aria-haspopup="false" aria-expanded="false"
                       title="الإشعارات">
                        <i class="ti ti-bell fs-5"></i>
                        @if(($headerUnreadCount ?? 0) > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                  style="font-size:10px; padding:3px 5px;">
                                {{ $headerUnreadCount > 99 ? '99+' : $headerUnreadCount }}
                            </span>
                        @endif
                    </a>

                    <div class="dropdown-menu dropdown-menu-end header-dropdown p-0"
                         style="min-width:340px; max-width:380px;">

                        {{-- رأس القائمة --}}
                        <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom bg-light rounded-top">
                            <span class="fw-semibold small">
                                <i class="ti ti-bell me-1 text-primary"></i> الإشعارات
                            </span>
                            @if(($headerUnreadCount ?? 0) > 0)
                                <span class="badge bg-danger rounded-pill">{{ $headerUnreadCount }}</span>
                            @endif
                        </div>

                        {{-- قائمة الإشعارات --}}
                        <ul class="list-unstyled mb-0" style="max-height:320px; overflow-y:auto;">
                            @forelse($headerRecentNotifications ?? [] as $notif)
                                <li class="{{ $notif->is_read ? '' : 'bg-light' }} border-bottom">
                                    <a href="{{ route('admin.notifications.show', $notif->id) }}"
                                       class="d-flex align-items-start gap-2 px-3 py-2 text-decoration-none text-dark">
                                        <span class="flex-shrink-0 mt-1">
                                            <i class="{{ $notif->type_icon }} text-{{ $notif->type_color }} fs-5"></i>
                                        </span>
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="mb-0 small fw-{{ $notif->is_read ? 'normal' : 'semibold' }} text-truncate">
                                                {{ $notif->title }}
                                            </p>
                                            <p class="mb-0 text-muted" style="font-size:11px;">
                                                {{ optional($notif->created_at)->diffForHumans() }}
                                            </p>
                                        </div>
                                        @if(!$notif->is_read)
                                            <span class="flex-shrink-0 mt-2">
                                                <span class="badge bg-danger rounded-circle" style="width:8px;height:8px;padding:0;"></span>
                                            </span>
                                        @endif
                                    </a>
                                </li>
                            @empty
                                <li class="px-3 py-4 text-center text-muted small">
                                    <i class="ti ti-bell-off d-block fs-3 mb-1"></i>
                                    لا توجد إشعارات
                                </li>
                            @endforelse
                        </ul>

                        {{-- تذييل --}}
                        <div class="border-top px-3 py-2 d-flex justify-content-between align-items-center bg-light rounded-bottom">
                            <a href="{{ route('admin.notifications.index') }}" class="small text-primary text-decoration-none">
                                عرض كل الإشعارات
                            </a>
                            @if(($headerUnreadCount ?? 0) > 0)
                                <form method="POST" action="{{ route('admin.notifications.mark-all-read') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-link btn-sm text-muted p-0 text-decoration-none small">
                                        تحديد الكل كمقروء
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </li>

                {{-- ═══ قائمة المستخدم ═══ --}}
                <li class="hdr-itm dropdown user-dropdown ">
                    <a class="app-head-link dropdown-toggle no-caret me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <span class="avtar"><img src="{{asset('dash/assets/images/user/avatar-2.jpg')}}" alt=""></span>
                    </a>
                    <div class="dropdown-menu header-dropdown">
                        <ul class="p-0">
                            <li class="dropdown-item px-3 py-2">
                                <span class="fw-semibold small">{{ auth()->user()?->name }}</span>
                                <br>
                                <small class="text-muted">{{ auth()->user()?->getRoleNames()->first() }}</small>
                            </li>
                            <hr class="dropdown-divider">
                            <li class="dropdown-item">
                                <a href="{{ route('admin.notifications.index') }}" class="drp-link">
                                    <i data-feather="bell"></i>
                                    <span>الإشعارات
                                        @if(($headerUnreadCount ?? 0) > 0)
                                            <span class="badge bg-danger ms-1">{{ $headerUnreadCount }}</span>
                                        @endif
                                    </span>
                                </a>
                            </li>
                            <hr class="dropdown-divider">
                            <li class="dropdown-item">
                                <a href="#"
                                   class="drp-link"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i data-feather="log-out"></i>
                                    <span>تسجيل الخروج</span>
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>

                        </ul>
                    </div>
                </li>
            </ul>
        </nav>

        <div class="collapse d-lg-none mobile-header-search w-100" id="mobile-header-search">
            <form action="#">
                <div class="input-group">
                    <input class="form-control rounded-3" type="search" value="" placeholder="Search">
                    <div class="search-btn">
                        <button class="p-0 btn rounded-0 rounded-end" type="button">
                            <i data-feather="search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</header>
