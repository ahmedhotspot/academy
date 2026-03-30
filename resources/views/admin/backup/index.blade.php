@extends('admin.layouts.master')

@section('title', 'النسخ الاحتياطي')

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'النسخ الاحتياطي',
                    'breadcrumbs' => $breadcrumbs,
                ])

                @include('admin.partials.alerts')

                <div class="row g-4">

                    {{-- إنشاء نسخة احتياطية --}}
                    <div class="col-xl-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-database-export me-1 text-primary"></i> إنشاء نسخة احتياطية جديدة</h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-3">
                                    ينشئ هذا الإجراء نسخة احتياطية من قاعدة البيانات الحالية.
                                </p>

                                @if($lastBackup)
                                    <div class="alert alert-info mb-3">
                                        <i class="ti ti-clock me-1"></i>
                                        آخر نسخة احتياطية: <strong>{{ $lastBackup }}</strong>
                                    </div>
                                @else
                                    <div class="alert alert-warning mb-3">
                                        <i class="ti ti-alert-triangle me-1"></i>
                                        لم يتم إنشاء أي نسخة احتياطية حتى الآن.
                                    </div>
                                @endif

                                <form action="{{ route('admin.backup.create') }}" method="POST"
                                      onsubmit="return confirm('هل تريد إنشاء نسخة احتياطية الآن؟');">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-database me-1"></i> إنشاء نسخة احتياطية الآن
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- قائمة النسخ --}}
                    <div class="col-xl-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-files me-1 text-success"></i> النسخ المحفوظة</h6>
                            </div>
                            <div class="card-body p-0">
                                @if(count($backupFiles) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0">
                                            <thead class="table-light">
                                            <tr>
                                                <th>الملف</th>
                                                <th>الحجم</th>
                                                <th>التاريخ</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($backupFiles as $file)
                                                <tr>
                                                    <td class="small fw-semibold">{{ $file['name'] }}</td>
                                                    <td class="small">{{ $file['size'] }}</td>
                                                    <td class="small text-muted">{{ $file['date'] }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="p-4 text-center text-muted">
                                        <i class="ti ti-database-off fs-2 d-block mb-2 opacity-50"></i>
                                        لا توجد نسخ احتياطية محفوظة
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection

