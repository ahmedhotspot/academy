@extends('admin.layouts.master')

@section('title', 'إدارة المصروفات')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
@endsection

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'إدارة مصروفات التشغيل',
                    'breadcrumbs' => $breadcrumbs,
                    'actions'     => $actions,
                ])

                @include('admin.partials.alerts')

                <div class="row g-3 mb-4">
                    <div class="col-xl-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="flex-shrink-0 bg-primary bg-opacity-10 rounded p-3">
                                    <i class="ti ti-list-check fs-3 text-primary"></i>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted small">إجمالي المصروفات</p>
                                    <h4 class="mb-0 fw-bold">{{ $reportSummary['total'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="flex-shrink-0 bg-danger bg-opacity-10 rounded p-3">
                                    <i class="ti ti-coin fs-3 text-danger"></i>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted small">الإجمالي</p>
                                    <h4 class="mb-0 fw-bold">{{ number_format($reportSummary['totalAmount'], 2) }} ج</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-semibold"><i class="ti ti-receipt me-1"></i> المصروفات</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="expenses-table" class="table table-hover align-middle mb-0 w-100">
                                <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>التاريخ</th>
                                    <th>البيان</th>
                                    <th>الفرع</th>
                                    <th>المبلغ</th>
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
        // Helper functions للحصول على URLs من routes
        const routes = {
            show: (id) => "{{ route('admin.expenses.show', ':id') }}".replace(':id', id),
            edit: (id) => "{{ route('admin.expenses.edit', ':id') }}".replace(':id', id)
        };

        $(function () {
            $('#expenses-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.expenses.datatable') }}',
                language: {
                    emptyTable:  'لا توجد مصروفات',
                    processing:  'جاري التحميل…',
                    lengthMenu:  'عرض _MENU_ مصروف',
                    info:        'عرض _START_ إلى _END_ من _TOTAL_',
                    search:      'بحث:',
                    paginate: {first:'الأول', last:'الأخير', next:'التالي', previous:'السابق'},
                },
                columns: [
                    {data: 'id'},
                    {data: 'expense_date'},
                    {data: 'title'},
                    {data: 'branch_name'},
                    {data: 'formatted_amount', className: 'fw-bold text-danger'},
                    {
                        data: 'id',
                        searchable: false,
                        orderable: false,
                        render: function (id) {
                            return '<div class="d-flex gap-1">'
                                + '<a class="btn btn-sm btn-outline-info" href="' + routes.show(id) + '"><i class="ti ti-eye"></i></a>'
                                + '<a class="btn btn-sm btn-outline-primary" href="' + routes.edit(id) + '"><i class="ti ti-pencil"></i></a>'
                                + '</div>';
                        }
                    }
                ]
            });
        });
    </script>
@endsection

