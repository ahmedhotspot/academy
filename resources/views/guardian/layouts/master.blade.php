<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'بوابة ولي الأمر - أكاديمية القرآن')</title>

    <link rel="icon" href="{{ asset('dash/assets/images/favicon.svg') }}" type="image/x-icon"/>
    <link rel="stylesheet" href="{{ asset('dash/assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dash/assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('dash/assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('dash/assets/css/style-rtl.css') }}" id="main-style-link">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:slnt,wght@-2,700&display=swap" rel="stylesheet">
    <style>
        body { font-family: "Cairo", sans-serif; font-weight: 600; background: #f5f6fa; }
        .portal-navbar { background: linear-gradient(135deg, #1e3a5f 0%, #2d6a9f 100%); }
        .portal-navbar .navbar-brand { color: #fff !important; font-size: 1.1rem; }
        .portal-navbar .nav-link { color: rgba(255,255,255,.85) !important; }
        .portal-navbar .nav-link:hover { color: #fff !important; }
        .portal-badge { background: rgba(255,255,255,.15); color: #fff; padding: 4px 14px; border-radius: 20px; font-size: .8rem; }
    </style>
    @yield('css')
</head>
<body>

<nav class="navbar navbar-expand-lg portal-navbar px-4 py-2 shadow-sm">
    <a class="navbar-brand fw-bold" href="{{ route('guardian.dashboard') }}">
        <i class="ti ti-book me-2"></i>بوابة ولي الأمر
    </a>
    <div class="ms-auto d-flex align-items-center gap-3">
        <span class="portal-badge"><i class="ti ti-user me-1"></i>{{ auth('guardian')->user()?->full_name }}</span>
        <form method="POST" action="{{ route('guardian.logout') }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-light">
                <i class="ti ti-logout me-1"></i>خروج
            </button>
        </form>
    </div>
</nav>

<div class="container-fluid py-4 px-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ti ti-alert-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</div>

<script src="{{ asset('dash/assets/js/plugins/jquery.min.js') }}"></script>
<script src="{{ asset('dash/assets/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ asset('dash/assets/js/plugins/feather.min.js') }}"></script>
<script>if (window.feather) feather.replace();</script>
@yield('js')
</body>
</html>

