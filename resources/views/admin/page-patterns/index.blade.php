@extends('admin.layouts.master')

@section('title', 'قوالب الصفحات - الفهرس')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    <style>
        #page-patterns-table th,
        #page-patterns-table td {
            text-align: right !important;
        }
    </style>
@endsection

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">
                @include('admin.partials.page-header', [
                    'title' => 'قوالب الصفحات - الفهرس',
                    'breadcrumbs' => $breadcrumbs,
                    'actions' => $actions,
                ])

                @include('admin.partials.alerts')

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">

                        <span class="badge bg-primary">جاهز للتوسع</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="page-patterns-table" class="table table-striped table-hover align-middle w-100">
                                <thead>
                                <tr>
                                    <th>الرقم</th>
                                    <th>العنوان</th>
                                    <th>النوع</th>
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
            $('#page-patterns-table').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ordering: false,
                ajax: '{{ route('admin.page-patterns.datatable') }}',
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
                    {data: 'title'},
                    {data: 'type'},
                    {
                        data: 'status',
                        render: function (data) {
                            return '<span class="badge bg-success">' + data + '</span>';
                        }
                    },
                    {data: 'created_at'},
                    {
                        data: 'id',
                        render: function (id) {
                            const showUrl = '{{ route('admin.page-patterns.show', ['id' => '__ID__']) }}'.replace('__ID__', id);
                            const editUrl = '{{ route('admin.page-patterns.edit', ['id' => '__ID__']) }}'.replace('__ID__', id);

                            return '<div class="d-flex gap-1">'
                                + '<a class="btn btn-sm btn-outline-info" href="' + showUrl + '">عرض</a>'
                                + '<a class="btn btn-sm btn-outline-primary" href="' + editUrl + '">تعديل</a>'
                                + '</div>';
                        }
                    }
                ]
            });
        });
    </script>
@endsection

