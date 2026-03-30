<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('admin.layouts.head')
</head>
<body class="theme-1">
@include('admin.layouts.header')
@include('admin.layouts.sidebar')

@yield('content')
@include('admin.layouts.footer')
@include('admin.layouts.footerjs')
@yield('js')
</body>
</html>


