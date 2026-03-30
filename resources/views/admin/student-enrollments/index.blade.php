@extends('admin.layouts.master')

@section('title', 'تسجيل الطلاب في الحلقات - الفهرس')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
@endsection

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">
                @include('admin.partials.page-header', [
                    'title' => 'تسجيل الطلاب في الحلقات - الفهرس',
                    'breadcrumbs' => $breadcrumbs,
                    'actions' => $actions,
                ])

                @include('admin.partials.alerts')

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">فلترة حسب الحلقة</label>
                                <select id="filter_group_id" class="form-select">
                                    <option value="">كل الحلقات</option>
                                    @foreach($groupOptions as $groupId => $groupName)
                                        <option value="{{ $groupId }}">{{ $groupName }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">فلترة حسب حالة التسجيل</label>
                                <select id="filter_status" class="form-select">
                                    <option value="">كل الحالات</option>
                                    <option value="active">نشط</option>
                                    <option value="transferred">منقول</option>
                                    <option value="suspended">موقوف</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button id="apply_filters" type="button" class="btn btn-primary w-100">تطبيق الفلاتر</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-semibold">سجلات تسجيل الطلاب في الحلقات</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="student-enrollments-table" class="table table-striped table-hover align-middle w-100">
                                <thead>
                                <tr>
                                    <th>الطالب</th>
                                    <th>الحلقة الحالية</th>
                                    <th>تاريخ التسجيل</th>
                                    <th>حالة التسجيل</th>
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
            const table = $('#student-enrollments-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.student-enrollments.datatable') }}',
                    data: function (d) {
                        d.group_id = $('#filter_group_id').val();
                        d.status = $('#filter_status').val();
                    }
                },
                language: {
                    emptyTable: 'لا توجد بيانات لعرضها',
                    processing: 'جاري التحميل...'
                },
                columns: [
                    {data: 'student'},
                    {data: 'current_group'},
                    {data: 'registered_at'},
                    {
                        data: 'status',
                        render: function (value, type, row) {
                            return '<span class="badge ' + row.status_badge + '">' + value + '</span>';
                        }
                    },
                    {
                        data: 'id',
                        searchable: false,
                        orderable: false,
                        render: function (id, type, row) {
                            const showUrl = '{{ route('admin.student-enrollments.show', ['student' => '__STUDENT__']) }}'.replace('__STUDENT__', row.student_id ?? id);
                            const editUrl = '{{ route('admin.student-enrollments.edit', ['studentEnrollment' => '__ID__']) }}'.replace('__ID__', id);
                            const deleteUrl = '{{ route('admin.student-enrollments.destroy', ['studentEnrollment' => '__ID__']) }}'.replace('__ID__', id);

                            return '<div class="d-flex gap-1">'
                                + '<a class="btn btn-sm btn-outline-info" href="' + showUrl + '">سجل الطالب</a>'
                                + '<a class="btn btn-sm btn-outline-primary" href="' + editUrl + '">تعديل/نقل</a>'
                                + '<form method="POST" action="' + deleteUrl + '" onsubmit="return confirm(\'هل تريد حذف هذا السجل؟\')">'
                                + '<input type="hidden" name="_token" value="{{ csrf_token() }}">'
                                + '<input type="hidden" name="_method" value="DELETE">'
                                + '<button type="submit" class="btn btn-sm btn-outline-danger">حذف</button>'
                                + '</form>'
                                + '</div>';
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

