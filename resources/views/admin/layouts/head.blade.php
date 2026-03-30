<title>@yield('title', 'لوحة الإدارة - أكاديمية القرآن')</title>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="description" content="نظام إدارة أكاديمية القرآن" />
<meta name="keywords" content="أكاديمية القرآن, لوحة الإدارة" />
<meta name="author" content="نظام أكاديمية القرآن" />

<link rel="icon" href="{{asset('dash/assets/images/favicon.svg')}}" type="image/x-icon" />

<link rel="stylesheet" href="{{asset('dash/assets/fonts/tabler-icons.min.css')}}">
<link rel="stylesheet" href="{{asset('dash/assets/fonts/feather.css')}}">
<link rel="stylesheet" href="{{asset('dash/assets/fonts/fontawesome.css')}}">
<link rel="stylesheet" href="{{asset('dash/assets/fonts/material.css')}}">


<link rel="stylesheet" href="{{asset('dash/assets/css/style-rtl.css')}}" id="main-style-link">
<link rel="stylesheet" href="" id="rtl-style-link">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:slnt,wght@-2,700&display=swap" rel="stylesheet">

<style>
@include('admin.partials.index.pattern-styles')
</style>
<style>
    body {
        font-family: "Cairo", sans-serif;
        font-optical-sizing: auto;
        font-weight: 700;
        font-style: normal;
        font-variation-settings: "slnt" -2;
    }
</style>
@yield('css')
