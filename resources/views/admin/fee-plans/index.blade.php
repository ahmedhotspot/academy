@extends('admin.layouts.master')

@section('title', 'إدارة خطط الرسوم')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
@endsection

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'إدارة خطط الرسوم والاشتراكات',
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
                                    <p class="mb-0 text-muted small">إجمالي الخطط</p>
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
                                    <p class="mb-0 text-muted small">خطط نشطة</p>
                                    <h4 class="mb-0 fw-bold text-success">{{ $reportSummary['active'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="flex-shrink-0 bg-secondary bg-opacity-10 rounded p-3">
                                    <i class="ti ti-circle-x fs-3 text-secondary"></i>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted small">خطط معطلة</p>
                                    <h4 class="mb-0 fw-bold">{{ $reportSummary['inactive'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="flex-shrink-0 bg-info bg-opacity-10 rounded p-3">
                                    <i class="ti ti-discount fs-3 text-info"></i>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted small">بها خصم أخوات</p>
                                    <h4 class="mb-0 fw-bold text-info">{{ $reportSummary['withDiscount'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- فلاتر البحث --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-semibold"><i class="ti ti-filter me-1"></i> فلاتر البحث</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label small text-muted">دورة الدفع</label>
                                <select id="filter_payment_cycle" class="form-select form-select-sm">
                                    <option value="">كل الدورات</option>
                                    @foreach($paymentCycles as $cycle)
                                        <option value="{{ $cycle }}">{{ $cycle }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted">الحالة</label>
                                <select id="filter_status" class="form-select form-select-sm">
                                    <option value="">كل الحالات</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}">
                                            {{ $status === 'active' ? 'نشط' : 'غير نشط' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button id="apply_filters" type="button" class="btn btn-primary btn-sm w-100">
                                    <i class="ti ti-search me-1"></i> تطبيق
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- جدول البيانات --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-semibold"><i class="ti ti-receipt me-1"></i> خطط الرسوم والاشتراكات</h6>
                        <span class="badge bg-primary rounded-pill">بيانات آجاكس</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="fee-plans-table"
                                   class="table table-hover align-middle mb-0 w-100">
                                <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>اسم الخطة</th>
                                    <th>دورة الدفع</th>
                                    <th>المبلغ</th>
                                    <th>خصم الأخوات</th>
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
            const table = $('#fee-plans-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.fee-plans.datatable') }}',
                    data: function (d) {
                        d.payment_cycle = $('#filter_payment_cycle').val();
                        d.status        = $('#filter_status').val();
                    }
                },
                language: {
                    emptyTable:  'لا توجد خطط رسوم حتى الآن',
                    processing:  'جاري التحميل…',
                    lengthMenu:  'عرض _MENU_ خطة',
                    info:        'عرض _START_ إلى _END_ من _TOTAL_ خطة',
                    infoEmpty:   'لا توجد خطط',
                    search:      'بحث:',
                    paginate: {first:'الأول', last:'الأخير', next:'التالي', previous:'السابق'},
                },
                order: [[0, 'desc']],
                columns: [
                    {data: 'id', width: '50px'},
                    {data: 'name'},
                    {data: 'payment_cycle_label'},
                    {data: 'formatted_amount'},
                    {
                        data: 'discount_label',
                        render: function (val, t, row) {
                            return '<span class="badge ' + row.discount_badge + '">' + val + '</span>';
                        }
                    },
                    {
                        data: 'status_label',
                        render: function (val, t, row) {
                            return '<span class="badge ' + row.status_badge + '">' + val + '</span>';
                        }
                    },
                    {
                        data: 'id',
                        searchable: false,
                        orderable:  false,
                        render: function (id, t, row) {
                            const showUrl   = '{{ route('admin.fee-plans.show', ['feePlan' => '__ID__']) }}'.replace('__ID__', id);
                            const editUrl   = '{{ route('admin.fee-plans.edit', ['feePlan' => '__ID__']) }}'.replace('__ID__', id);
                            const deleteUrl = '{{ route('admin.fee-plans.destroy', ['feePlan' => '__ID__']) }}'.replace('__ID__', id);

                            return '<div class="d-flex gap-1 flex-nowrap">'
                                + '<a class="btn btn-sm btn-outline-info" href="' + showUrl + '" title="عرض"><i class="ti ti-eye"></i></a>'
                                + '<a class="btn btn-sm btn-outline-primary" href="' + editUrl + '" title="تعديل"><i class="ti ti-pencil"></i></a>'
                                + '<form method="POST" action="' + deleteUrl + '" onsubmit="return confirm(\'هل تريد حذف هذه الخطة؟\')" class="d-inline">'
                                + '<input type="hidden" name="_token" value="{{ csrf_token() }}">'
                                + '<input type="hidden" name="_method" value="DELETE">'
                                + '<button type="submit" class="btn btn-sm btn-outline-danger" title="حذف"><i class="ti ti-trash"></i></button>'
                                + '</form></div>';
                        }
                    }
                ]
            });

            $('#apply_filters').on('click', function () {
                table.ajax.reload();
            });

            $('#filter_payment_cycle, #filter_status').on('change', function () {
                table.ajax.reload();
            });
        });
    </script>
@endsection

