@extends('admin.layouts.master')

@section('title', 'إدارة المدفوعات وإيصالات القبض')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    <style>
        #payments-table th,
        #payments-table td {
            text-align: right !important;
        }
    </style>
@endsection

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'إدارة المدفوعات وإيصالات القبض',
                    'breadcrumbs' => $breadcrumbs,
                    'actions'     => $actions,
                ])

                @include('admin.partials.alerts')

                {{-- بطاقات الملخص --}}
                <div class="row g-3 mb-4">
                    <div class="col-xl-4 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="flex-shrink-0 bg-primary bg-opacity-10 rounded p-3">
                                    <i class="ti ti-receipt fs-3 text-primary"></i>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted small">إجمالي الدفعات</p>
                                    <h4 class="mb-0 fw-bold">{{ $reportSummary['total'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="flex-shrink-0 bg-success bg-opacity-10 rounded p-3">
                                    <i class="ti ti-coin fs-3 text-success"></i>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted small">الإجمالي المدفوع</p>
                                    <h4 class="mb-0 fw-bold text-success">{{ number_format($reportSummary['totalAmount'], 2) }} ج</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="flex-shrink-0 bg-info bg-opacity-10 rounded p-3">
                                    <i class="ti ti-calculator fs-3 text-info"></i>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted small">متوسط الدفعة</p>
                                    <h4 class="mb-0 fw-bold text-info">{{ number_format($reportSummary['averagePayment'], 2) }} ج</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- جدول البيانات --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-semibold"><i class="ti ti-receipt-2 me-1"></i> سجل المدفوعات وإيصالات القبض</h6>
                        <span class="badge bg-primary rounded-pill">بيانات آجاكس</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="payments-table"
                                   class="table table-hover align-middle mb-0 w-100">
                                <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>الطالب</th>
                                    <th>رقم الإيصال</th>
                                    <th>التاريخ</th>
                                    <th>المبلغ</th>
                                    <th>الملاحظات</th>
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
            const table = $('#payments-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.payments.datatable') }}',
                    data: function (d) {}
                },
                language: {
                    emptyTable:  'لا توجد دفعات حتى الآن',
                    processing:  'جاري التحميل…',
                    lengthMenu:  'عرض _MENU_ دفعة',
                    info:        'عرض _START_ إلى _END_ من _TOTAL_ دفعة',
                    infoEmpty:   'لا توجد دفعات',
                    search:      'بحث:',
                    paginate: {first:'الأول', last:'الأخير', next:'التالي', previous:'السابق'},
                },
                columnDefs: [
                    {
                        targets: '_all',
                        className: 'text-end'
                    }
                ],
                order: [[3, 'desc']],
                columns: [
                    {data: 'id', width: '50px'},
                    {data: 'student_name'},
                    {data: 'receipt_formatted'},
                    {data: 'payment_date'},
                    {data: 'formatted_amount'},
                    {data: 'notes'},
                    {
                        data: 'id',
                        searchable: false,
                        orderable:  false,
                        render: function (id, t, row) {
                            const showUrl   = '{{ route('admin.payments.show', ['payment' => '__ID__']) }}'.replace('__ID__', id);
                            const editUrl   = '{{ route('admin.payments.edit', ['payment' => '__ID__']) }}'.replace('__ID__', id);
                            const deleteUrl = '{{ route('admin.payments.destroy', ['payment' => '__ID__']) }}'.replace('__ID__', id);

                            return '<div class="d-flex gap-1 flex-nowrap">'
                                + '<a class="btn btn-sm btn-outline-info" href="' + showUrl + '" title="عرض"><i class="ti ti-eye"></i></a>'
                                + '<a class="btn btn-sm btn-outline-primary" href="' + editUrl + '" title="تعديل"><i class="ti ti-pencil"></i></a>'
                                + '<form method="POST" action="' + deleteUrl + '" onsubmit="return confirm(\'هل تريد حذف هذه الدفعة؟\')" class="d-inline">'
                                + '<input type="hidden" name="_token" value="{{ csrf_token() }}">'
                                + '<input type="hidden" name="_method" value="DELETE">'
                                + '<button type="submit" class="btn btn-sm btn-outline-danger" title="حذف"><i class="ti ti-trash"></i></button>'
                                + '</form></div>';
                        }
                    }
                ]
            });
        });
    </script>
@endsection

