@extends('admin.layouts.master')

@section('title', 'إدارة حضور وغياب المعلمين - الفهرس')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    <style>
        #teacher-attendances-table th,
        #teacher-attendances-table td {
            text-align: right !important;
        }
    </style>
@endsection

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">
                @include('admin.partials.page-header', [
                    'title' => 'إدارة حضور وغياب المعلمين - الفهرس',
                    'breadcrumbs' => $breadcrumbs,
                    'actions' => $actions,
                ])

                @include('admin.partials.alerts')

                <div class="row g-3 mb-3">
                    <div class="col-xl-2 col-md-6">
                        <div class="card border-0 shadow-sm h-100"><div class="card-body"><p class="mb-1 text-muted">إجمالي السجلات</p><h5 class="mb-0">{{ $reportSummary['total'] }}</h5></div></div>
                    </div>
                    <div class="col-xl-2 col-md-6">
                        <div class="card border-0 shadow-sm h-100"><div class="card-body"><p class="mb-1 text-muted">حاضر</p><h5 class="mb-0 text-success">{{ $reportSummary['present'] }}</h5></div></div>
                    </div>
                    <div class="col-xl-2 col-md-6">
                        <div class="card border-0 shadow-sm h-100"><div class="card-body"><p class="mb-1 text-muted">غائب</p><h5 class="mb-0 text-danger">{{ $reportSummary['absent'] }}</h5></div></div>
                    </div>
                    <div class="col-xl-2 col-md-6">
                        <div class="card border-0 shadow-sm h-100"><div class="card-body"><p class="mb-1 text-muted">متأخر</p><h5 class="mb-0 text-warning">{{ $reportSummary['late'] }}</h5></div></div>
                    </div>
                    <div class="col-xl-2 col-md-6">
                        <div class="card border-0 shadow-sm h-100"><div class="card-body"><p class="mb-1 text-muted">بعذر</p><h5 class="mb-0 text-info">{{ $reportSummary['excused'] }}</h5></div></div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">فلترة حسب المعلم</label>
                                <select id="filter_teacher_id" class="form-select">
                                    <option value="">كل المعلمين</option>
                                    @foreach($teacherOptions as $teacherId => $teacherName)
                                        <option value="{{ $teacherId }}">{{ $teacherName }}</option>
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
                                    <option value="متأخر">متأخر</option>
                                    <option value="بعذر">بعذر</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <button id="apply_filters" type="button" class="btn btn-primary">تطبيق الفلاتر</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-semibold">ملخص حضور المعلمين</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="teacher-attendances-table" class="table table-striped table-hover align-middle w-100">
                                <thead>
                                <tr>
                                    <th>المعلم</th>
                                    <th>أول تسجيل</th>
                                    <th>آخر تسجيل</th>
                                    <th>آخر حالة</th>
                                    <th>آخر ملاحظات</th>
                                    <th>عدد السجلات</th>
                                    <th>العمليات</th>
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
            const table = $('#teacher-attendances-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.teacher-attendances.datatable') }}',
                    data: function (d) {
                        d.teacher_id = $('#filter_teacher_id').val();
                        d.attendance_date = $('#filter_attendance_date').val();
                        d.status = $('#filter_status').val();
                    }
                },
                language: {
                    emptyTable: 'لا توجد بيانات لعرضها',
                    processing: 'جاري التحميل...'
                },
                columnDefs: [
                    {
                        targets: '_all',
                        className: 'text-end'
                    }
                ],
                columns: [
                    {data: 'teacher_name'},
                    {data: 'first_attendance_date'},
                    {data: 'last_attendance_date'},
                    {
                        data: 'status',
                        render: function (value, type, row) {
                            return '<span class="badge ' + row.status_badge + '">' + value + '</span>';
                        }
                    },
                    {data: 'notes'},
                    {data: 'records_count'},
                    {
                        data: 'latest_attendance_id',
                        searchable: false,
                        orderable: false,
                        render: function (id, type, row) {
                            const showUrl = '{{ route('admin.teacher-attendances.show', ['teacher' => '__TEACHER__']) }}'.replace('__TEACHER__', row.teacher_id);
                            let actions = '<div class="d-flex gap-1 flex-wrap">'
                                + '<a class="btn btn-sm btn-outline-info" href="' + showUrl + '">سجل المعلم</a>';

                            if (id) {
                                const editUrl = '{{ route('admin.teacher-attendances.edit', ['teacherAttendance' => '__ID__']) }}'.replace('__ID__', id);
                                const deleteUrl = '{{ route('admin.teacher-attendances.destroy', ['teacherAttendance' => '__ID__']) }}'.replace('__ID__', id);

                                actions += '<a class="btn btn-sm btn-outline-primary" href="' + editUrl + '">تعديل آخر سجل</a>'
                                    + '<form method="POST" action="' + deleteUrl + '" onsubmit="return confirm(\'هل تريد حذف آخر سجل لهذا المعلم؟\')">'
                                    + '<input type="hidden" name="_token" value="{{ csrf_token() }}">'
                                    + '<input type="hidden" name="_method" value="DELETE">'
                                    + '<button type="submit" class="btn btn-sm btn-outline-danger">حذف آخر سجل</button>'
                                    + '</form>';
                            }

                            return actions + '</div>';
                        }
                    }
                ]
            });

            $('#apply_filters').on('click', function () {
                table.ajax.reload();
            });
        });
    </script>
@endsection

