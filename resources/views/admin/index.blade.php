@extends('admin.layouts.master')

@section('title', 'الصفحة الرئيسية للإدارة')

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">
                @include('admin.partials.page-header', [
                    'title' => 'الصفحة الرئيسية للإدارة',
                    'breadcrumbs' => [
                        ['title' => 'الرئيسية'],
                    ],
                ])

                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <h5 class="mb-2">مرحبًا بك في لوحة إدارة الأكاديمية</h5>
                        <p class="text-muted mb-3">تم تجهيز البنية الأساسية بنمط Model-based ويمكنك المتابعة من لوحة التحكم.</p>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">الانتقال إلى لوحة التحكم</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
