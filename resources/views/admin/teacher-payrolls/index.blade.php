@extends('admin.layouts.master')

@section('title', 'إدارة مستحقات المعلمين')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
@endsection

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'إدارة مستحقات المعلمين',
                    'breadcrumbs' => $breadcrumbs,
                    'actions'     => $actions,
                ])

                @include('admin.partials.alerts')

                {{-- بطاقات الملخص --}}
                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="flex-shrink-0 bg-primary bg-opacity-10 rounded p-3">
                                    <i class="ti ti-list-check fs-3 text-primary"></i>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted small">إجمالي الحسابات</p>
                                    <h4 class="mb-0 fw-bold">{{ $reportSummary['total'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="flex-shrink-0 bg-success bg-opacity-10 rounded p-3">
                                    <i class="ti ti-circle-check fs-3 text-success"></i>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted small">مصروفة</p>
                                    <h4 class="mb-0 fw-bold text-success">{{ $reportSummary['processed'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="flex-shrink-0 bg-warning bg-opacity-10 rounded p-3">
                                    <i class="ti ti-clock fs-3 text-warning"></i>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted small">قيد الانتظار</p>
                                    <h4 class="mb-0 fw-bold text-warning">{{ $reportSummary['pending'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="flex-shrink-0 bg-info bg-opacity-10 rounded p-3">
                                    <i class="ti ti-coin fs-3 text-info"></i>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted small">الإجمالي المستحق</p>
                                    <h4 class="mb-0 fw-bold">{{ number_format($reportSummary['totalSalaries'], 2) }} ر.س</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- جدول البيانات --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-semibold"><i class="ti ti-wallet me-1"></i> مستحقات المعلمين</h6>
                        <span class="badge bg-primary rounded-pill">بيانات آجاكس</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="payrolls-table"
                                   class="table table-hover align-middle mb-0 w-100">
                                <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>المعلم</th>
                                    <th>الشهر</th>
                                    <th>الراتب</th>
                                    <th>الاستقطاع</th>
                                    <th>الجزاء</th>
                                    <th>المكافأة</th>
                                    <th>الصافي</th>
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
            const table = $('#payrolls-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.teacher-payrolls.datatable') }}',
                    data: function (d) {}
                },
                language: {
                    emptyTable:  'لا توجد حسابات حتى الآن',
                    processing:  'جاري التحميل…',
                    lengthMenu:  'عرض _MENU_ حساب',
                    info:        'عرض _START_ إلى _END_ من _TOTAL_ حساب',
                    infoEmpty:   'لا توجد حسابات',
                    search:      'بحث:',
                    paginate: {first:'الأول', last:'الأخير', next:'التالي', previous:'السابق'},
                },
                order: [[2, 'desc']],
                columns: [
                    {data: 'id', width: '50px'},
                    {data: 'teacher_name'},
                    {data: 'month_year'},
                    {data: 'formatted_base'},
                    {data: 'deduction'},
                    {data: 'penalty'},
                    {data: 'bonus'},
                    {data: 'formatted_final', className: 'fw-bold text-success'},
                    {
                        data: 'status',
                        render: function (val, t, row) {
                            return '<span class="badge ' + row.status_badge + '">' + val + '</span>';
                        }
                    },
                    {
                        data: 'id',
                        searchable: false,
                        orderable:  false,
                        render: function (id, t, row) {
                            const showUrl = '{{ route('admin.teacher-payrolls.show', ['teacherPayroll' => '__ID__']) }}'.replace('__ID__', id);
                            const editUrl = '{{ route('admin.teacher-payrolls.edit', ['teacherPayroll' => '__ID__']) }}'.replace('__ID__', id);

                            return '<div class="d-flex gap-1 flex-nowrap">'
                                + '<a class="btn btn-sm btn-outline-info" href="' + showUrl + '" title="عرض"><i class="ti ti-eye"></i></a>'
                                + '<a class="btn btn-sm btn-outline-primary" href="' + editUrl + '" title="تعديل"><i class="ti ti-pencil"></i></a>'
                                + '</div>';
                        }
                    }
                ]
            });
        });
    </script>
@endsection

