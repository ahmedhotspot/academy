@extends('admin.layouts.master')

@section('title', 'تقرير المصروفات')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    <style>
        #expenses-table th,
        #expenses-table td {
            text-align: right !important;
        }
    </style>
@endsection

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'تقرير مصروفات التشغيل',
                    'breadcrumbs' => $breadcrumbs,
                ])

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">تاريخ البداية</label>
                                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">تاريخ النهاية</label>
                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-4 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-primary"><i class="ti ti-search me-1"></i> بحث</button>
                                <a href="{{ route('admin.reports.expenses') }}" class="btn btn-secondary"><i class="ti ti-refresh me-1"></i> إعادة تعيين</a>
                                <a href="{{ route('admin.reports.expenses.pdf', request()->query()) }}" class="btn btn-danger"><i class="ti ti-file-download me-1"></i> PDF</a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body"><p class="text-muted small">إجمالي المصروفات</p><h4 class="fw-bold">{{ $report['total'] }}</h4></div></div></div>
                    <div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body"><p class="text-muted small">الإجمالي</p><h4 class="fw-bold text-danger">{{ number_format($report['amount'], 2) }} ج</h4></div></div></div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-semibold"><i class="ti ti-coin me-1"></i> تفاصيل المصروفات</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="expenses-table" class="table table-hover mb-0 w-100">
                                <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>التاريخ</th>
                                    <th>البيان</th>
                                    <th>الفرع</th>
                                    <th>المبلغ</th>
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
            $('#expenses-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.expenses.datatable') }}',
                    data: function (d) {
                        d.start_date = '{{ request('start_date') }}';
                        d.end_date = '{{ request('end_date') }}';
                    }
                },
                language: {
                    emptyTable: 'لا توجد بيانات',
                    processing: 'جاري التحميل...',
                    search: 'بحث:',
                    paginate: {first:'الأول', last:'الأخير', next:'التالي', previous:'السابق'}
                },
                columnDefs: [
                    {
                        targets: '_all',
                        className: 'text-end'
                    }
                ],
                columns: [
                    {data: 'id'},
                    {data: 'date'},
                    {data: 'title'},
                    {data: 'branch'},
                    {data: 'amount'}
                ]
            });
        });
    </script>
@endsection
