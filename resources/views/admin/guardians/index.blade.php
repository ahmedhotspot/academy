@extends('admin.layouts.master')

@section('title', 'إدارة أولياء الأمور - الفهرس')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
@endsection

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">
                @include('admin.partials.page-header', [
                    'title' => 'إدارة أولياء الأمور - الفهرس',
                    'breadcrumbs' => $breadcrumbs,
                    'actions' => $actions,
                ])

                @include('admin.partials.alerts')

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-semibold">قائمة أولياء الأمور</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="guardians-table" class="table table-striped table-hover align-middle w-100">
                                <thead>
                                <tr>
                                    <th>الفرع</th>
                                    <th>الاسم</th>
                                    <th>الهاتف</th>
                                    <th>الواتساب</th>
                                    <th>عدد الطلاب</th>
                                    <th>الحالة</th>
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
            $('#guardians-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.guardians.datatable') }}',
                language: {
                    emptyTable: 'لا توجد بيانات لعرضها',
                    processing: 'جاري التحميل...'
                },
                columns: [
                    {data: 'branch'},
                    {data: 'full_name'},
                    {data: 'phone'},
                    {data: 'whatsapp'},
                    {data: 'students_count'},
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
                        render: function (id) {
                            const showUrl = '{{ route('admin.guardians.show', ['guardian' => '__ID__']) }}'.replace('__ID__', id);
                            const editUrl = '{{ route('admin.guardians.edit', ['guardian' => '__ID__']) }}'.replace('__ID__', id);
                            const deleteUrl = '{{ route('admin.guardians.destroy', ['guardian' => '__ID__']) }}'.replace('__ID__', id);

                            return '<div class="d-flex gap-1">'
                                + '<a class="btn btn-sm btn-outline-info" href="' + showUrl + '">عرض</a>'
                                + '<a class="btn btn-sm btn-outline-primary" href="' + editUrl + '">تعديل</a>'
                                + '<form method="POST" action="' + deleteUrl + '" onsubmit="return confirm(\'هل تريد حذف ولي الأمر؟\')">'
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

