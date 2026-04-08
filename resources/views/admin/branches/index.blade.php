@extends('admin.layouts.master')

@section('title', 'إدارة الفروع - الفهرس')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    <style>
        /* Force table headers/cells to stay right-aligned with DataTables styling */
        #branches-table th,
        #branches-table td {
            text-align: right !important;
        }
    </style>
@endsection

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">


                @include('admin.partials.alerts')

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body d-flex flex-wrap gap-3 align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-1 fw-semibold">لوحة الفروع</h6>
                            <p class="mb-0 text-muted small">إدارة الفروع، الحالات، وعرض التفاصيل والإحصائيات.</p>
                        </div>
                        @include('admin.partials.page-header', [

                                         'breadcrumbs' => $breadcrumbs,
                                         'actions' => $actions,
                                     ])                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-semibold"><i class="ti ti-building me-1"></i> قائمة الفروع</h6>
                        <span class="badge bg-light text-dark">بحث وترتيب مباشر</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="branches-table" class="table table-striped table-hover align-middle w-100 text-end" dir="rtl">
                                <thead>
                                <tr>
                                    <th>الرقم</th>
                                    <th>اسم الفرع</th>
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
            $('#branches-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.branches.datatable') }}',
                language: {
                    emptyTable: 'لا توجد بيانات لعرضها',
                    processing: 'جاري التحميل...',
                    search: 'بحث:',
                    lengthMenu: 'عرض _MENU_',
                    info: 'عرض _START_ إلى _END_ من _TOTAL_',
                    paginate: {
                        first: 'الأول',
                        previous: 'السابق',
                        next: 'التالي',
                        last: 'الأخير'
                    }
                },
                columnDefs: [
                    {targets: '_all', className: 'text-end'}
                ],
                columns: [
                    {data: 'id'},
                    {data: 'name'},
                    {
                        data: 'status',
                        render: function (value, type, row) {
                            return '<span class="badge ' + row.status_badge + '">' + value + '</span>';
                        }
                    },
                    {data: 'created_at'},
                    {
                        data: 'id',
                        searchable: false,
                        orderable: false,
                        render: function (id) {
                            const showUrl = '{{ route('admin.branches.show', ['branch' => '__ID__']) }}'.replace('__ID__', id);
                            const editUrl = '{{ route('admin.branches.edit', ['branch' => '__ID__']) }}'.replace('__ID__', id);
                            const deleteUrl = '{{ route('admin.branches.destroy', ['branch' => '__ID__']) }}'.replace('__ID__', id);

                            return '<div class="d-flex gap-1 flex-nowrap">'
                                + '<a class="btn btn-sm btn-outline-info" href="' + showUrl + '" title="عرض"><i class="ti ti-eye"></i></a>'
                                + '<a class="btn btn-sm btn-outline-primary" href="' + editUrl + '" title="تعديل"><i class="ti ti-pencil"></i></a>'
                                + '<form method="POST" action="' + deleteUrl + '" onsubmit="return confirm(\'هل تريد حذف هذا الفرع؟\')" class="d-inline">'
                                + '<input type="hidden" name="_token" value="{{ csrf_token() }}">'
                                + '<input type="hidden" name="_method" value="DELETE">'
                                + '<button type="submit" class="btn btn-sm btn-outline-danger" title="حذف"><i class="ti ti-trash"></i></button>'
                                + '</form>'
                                + '</div>';
                        }
                    }
                ]
            });
        });
    </script>
@endsection

