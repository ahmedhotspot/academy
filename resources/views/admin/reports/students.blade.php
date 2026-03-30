@extends('admin.layouts.master')

@section('title', 'تقرير الطلاب')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
@endsection

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'تقرير الطلاب',
                    'breadcrumbs' => $breadcrumbs,
                ])

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <p class="text-muted small mb-1">إجمالي الطلاب</p>
                                <h4 class="fw-bold">{{ $report['total'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-semibold"><i class="ti ti-users me-1"></i> قائمة الطلاب</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>الاسم</th>
                                    <th>الفرع</th>
                                    <th>العمر</th>
                                    <th>الحالة</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($report['students'] as $student)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="fw-semibold">{{ $student['name'] }}</td>
                                        <td>{{ $student['branch'] ?? '-' }}</td>
                                        <td>{{ $student['age'] }}</td>
                                        <td><span class="badge bg-success">{{ $student['status'] }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">لا توجد بيانات</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

