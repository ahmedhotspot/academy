@extends('admin.layouts.master')

@section('title', 'إدارة حضور وغياب المعلمين - تعديل')

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">
                @include('admin.partials.page-header', [
                    'title' => 'تعديل سجل حضور المعلم',
                    'breadcrumbs' => $breadcrumbs,
                ])

                @include('admin.partials.alerts')

                <form action="{{ route('admin.teacher-attendances.update', $teacherAttendance) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                            <h6 class="mb-0 fw-semibold">تحديث البيانات</h6>
                            <span class="badge bg-info">#{{ $teacherAttendance->id }}</span>
                        </div>
                        <div class="card-body">
                            @include('admin.teacher-attendances._form', ['teacherAttendance' => $teacherAttendance])
                        </div>
                        <div class="card-footer bg-white border-top d-flex gap-2 justify-content-end">
                            <a href="{{ route('admin.teacher-attendances.index') }}" class="btn btn-light">رجوع</a>
                            <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

