@extends('admin.layouts.master')

@section('title', 'إدارة المعلمين - الفهرس')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    <style>
        #teachers-table th,
        #teachers-table td {
            text-align: right !important;
        }
    </style>
@endsection

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">
                @include('admin.partials.page-header', [
                    'title' => 'إدارة المعلمين - الفهرس',
                    'breadcrumbs' => $breadcrumbs,
                    'actions' => $actions,
                ])

                @include('admin.partials.alerts')

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-semibold">قائمة المعلمين</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="teachers-table" class="table table-striped table-hover align-middle w-100">
                                <thead>
                                <tr>
                                    <th>الرقم</th>
                                    <th>الاسم</th>
                                    <th>الجوال</th>
                                    <th>الواتساب</th>
                                    <th>الفرع</th>
                                    <th>الحالة</th>
                                    <th>تاريخ الإنشاء</th>
                                    <th>الإجراءات</th>
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
            $('#teachers-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.teachers.datatable') }}',
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
                    {data: 'id'},
                    {data: 'name'},
                    {data: 'phone'},
                    {data: 'whatsapp'},
                    {data: 'branch'},
                    {
                        data: 'status',
                        render: function (value) {
                            return '<span class="badge bg-info">' + value + '</span>';
                        }
                    },
                    {data: 'created_at'},
                    {
                        data: 'id',
                        searchable: false,
                        orderable: false,
                        render: function (id) {
                            const showUrl = '{{ route('admin.teachers.show', ['teacher' => '__ID__']) }}'.replace('__ID__', id);
                            const editUrl = '{{ route('admin.teachers.edit', ['teacher' => '__ID__']) }}'.replace('__ID__', id);
                            const deleteUrl = '{{ route('admin.teachers.destroy', ['teacher' => '__ID__']) }}'.replace('__ID__', id);

                            return '<div class="d-flex gap-1">'
                                + '<a class="btn btn-sm btn-outline-info" href="' + showUrl + '">عرض</a>'
                                + '<a class="btn btn-sm btn-outline-primary" href="' + editUrl + '">تعديل</a>'
                                + '<form method="POST" action="' + deleteUrl + '" onsubmit="return confirm(\'هل تريد حذف هذا المعلم؟\')">'
                                + '<input type="hidden" name="_token" value="{{ csrf_token() }}">'
                                + '<input type="hidden" name="_method" value="DELETE">'
                                + '<button type="submit" class="btn btn-sm btn-outline-danger">حذف</button>'
                                + '</form>'
                                + '</div>';
                        }
                    }
                ]
            });
        });
    </script>
@endsection

