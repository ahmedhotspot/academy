@extends('admin.layouts.master')

@section('title', 'إدارة المستخدمين - الفهرس')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
@endsection

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">
                @include('admin.partials.page-header', [
                    'title' => 'إدارة المستخدمين - الفهرس',
                    'breadcrumbs' => $breadcrumbs,
                    'actions' => $actions,
                ])

                @include('admin.partials.alerts')

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-semibold">قائمة المستخدمين</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="users-table" class="table table-striped table-hover align-middle w-100">
                                <thead>
                                <tr>
                                    <th>الرقم</th>
                                    <th>الاسم</th>
                                    <th>الجوال</th>
                                    <th>البريد</th>
                                    <th>الدور</th>
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
            $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.users.datatable') }}',
                language: {
                    emptyTable: 'لا توجد بيانات لعرضها',
                    processing: 'جاري التحميل...'
                },
                columns: [
                    {data: 'id'},
                    {data: 'name'},
                    {data: 'phone'},
                    {data: 'email'},
                    {data: 'role'},
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
                            const showUrl = '{{ route('admin.users.show', ['user' => '__ID__']) }}'.replace('__ID__', id);
                            const editUrl = '{{ route('admin.users.edit', ['user' => '__ID__']) }}'.replace('__ID__', id);
                            const deleteUrl = '{{ route('admin.users.destroy', ['user' => '__ID__']) }}'.replace('__ID__', id);

                            return '<div class="d-flex gap-1">'
                                + '<a class="btn btn-sm btn-outline-info" href="' + showUrl + '">عرض</a>'
                                + '<a class="btn btn-sm btn-outline-primary" href="' + editUrl + '">تعديل</a>'
                                + '<form method="POST" action="' + deleteUrl + '" onsubmit="return confirm(\'هل تريد حذف هذا المستخدم؟\')">'
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

