@extends('admin.layouts.master')

@section('title', 'إدارة حضور وغياب الطلاب - الفهرس')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
@endsection

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">
                @include('admin.partials.page-header', [
                    'title' => 'إدارة حضور وغياب الطلاب - الفهرس',
                    'breadcrumbs' => $breadcrumbs,
                    'actions' => $actions,
                ])

                @include('admin.partials.alerts')

                <div class="row g-3 mb-3">
                    <div class="col-xl-3 col-md-6"><div class="card border-0 shadow-sm h-100"><div class="card-body"><p class="mb-1 text-muted">إجمالي السجلات</p><h5 class="mb-0">{{ $reportSummary['total'] }}</h5></div></div></div>
                    <div class="col-xl-3 col-md-6"><div class="card border-0 shadow-sm h-100"><div class="card-body"><p class="mb-1 text-muted">حاضر</p><h5 class="mb-0 text-success">{{ $reportSummary['present'] }}</h5></div></div></div>
                    <div class="col-xl-3 col-md-6"><div class="card border-0 shadow-sm h-100"><div class="card-body"><p class="mb-1 text-muted">غائب</p><h5 class="mb-0 text-danger">{{ $reportSummary['absent'] }}</h5></div></div></div>
                    <div class="col-xl-3 col-md-6"><div class="card border-0 shadow-sm h-100"><div class="card-body"><p class="mb-1 text-muted">منقول</p><h5 class="mb-0 text-info">{{ $reportSummary['transferred'] }}</h5></div></div></div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">فلترة حسب الطالب</label>
                                <select id="filter_student_id" class="form-select">
                                    <option value="">كل الطلاب</option>
                                    @foreach($studentOptions as $studentId => $studentName)
                                        <option value="{{ $studentId }}">{{ $studentName }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">فلترة حسب التاريخ</label>
                                <input id="filter_attendance_date" type="date" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">فلترة حسب الحالة</label>
                                <select id="filter_status" class="form-select">
                                    <option value="">كل الحالات</option>
                                    <option value="حاضر">حاضر</option>
                                    <option value="غائب">غائب</option>
                                    <option value="منقول">منقول</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <button id="apply_filters" type="button" class="btn btn-primary">تطبيق الفلاتر</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom"><h6 class="mb-0 fw-semibold">ملخص حضور الطلاب</h6></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="student-attendances-table" class="table table-striped table-hover align-middle w-100">
                                <thead>
                                <tr>
                                    <th>الطالب</th>
                                    <th>أول تسجيل</th>
                                    <th>آخر تسجيل</th>
                                    <th>آخر حالة</th>
                                    <th>آخر ملاحظات</th>
                                    <th>عدد السجلات</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script>
        $(function () {
            const table = $('#student-attendances-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.student-attendances.datatable') }}',
                    data: function (d) {
                        d.student_id = $('#filter_student_id').val();
                        d.attendance_date = $('#filter_attendance_date').val();
                        d.status = $('#filter_status').val();
                    }
                },
                language: {
                    emptyTable: 'لا توجد بيانات لعرضها',
                    processing: 'جاري التحميل...'
                },
                columns: [
                    {data: 'student_name'},
                    {data: 'first_attendance_date'},
                    {data: 'last_attendance_date'},
                    {
                        data: 'status',
                        render: function (value, type, row) {
                            return '<span class="badge ' + row.status_badge + '">' + value + '</span>';
                        }
                    },
                    {data: 'notes'},
                    {data: 'records_count'}
                ]
            });

            $('#apply_filters').on('click', function () {
                table.ajax.reload();
            });
        });
    </script>
@endsection

