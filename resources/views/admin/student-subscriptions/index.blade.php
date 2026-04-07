@extends('admin.layouts.master')

@section('title', 'إدارة اشتراكات الطلاب')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
@endsection

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'إدارة اشتراكات الطلاب',
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
                                    <p class="mb-0 text-muted small">إجمالي الاشتراكات</p>
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
                                    <p class="mb-0 text-muted small">نشط</p>
                                    <h4 class="mb-0 fw-bold text-success">{{ $reportSummary['active'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="flex-shrink-0 bg-warning bg-opacity-10 rounded p-3">
                                    <i class="ti ti-alert-circle fs-3 text-warning"></i>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted small">متأخر</p>
                                    <h4 class="mb-0 fw-bold text-warning">{{ $reportSummary['overdueStudents'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="flex-shrink-0 bg-info bg-opacity-10 rounded p-3">
                                    <i class="ti ti-checkbox fs-3 text-info"></i>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted small">مكتمل</p>
                                    <h4 class="mb-0 fw-bold text-info">{{ $reportSummary['complete'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- قائمة الطلاب المتأخرين (DataTable Ajax) --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-warning bg-opacity-10 border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-semibold text-warning-emphasis">
                            <i class="ti ti-alert-triangle me-1"></i>
                            ⚠️ تنبيه: طلاب متأخرون
                        </h6>
                        <span class="badge bg-warning text-dark rounded-pill">{{ $reportSummary['overdueStudents'] }} طالب</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="overdue-subscriptions-table" class="table table-sm table-hover align-middle mb-0 w-100">
                                <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>الطالب</th>
                                    <th>رقم الهاتف</th>
                                    <th>الخطة</th>
                                    <th>المبلغ المتبقي</th>
                                    <th>الحالة</th>
                                    <th>العمليات</th>
                                </tr>
                                </thead>
                            </table>
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
                                <label class="form-label small text-muted">حالة الاشتراك</label>
                                <select id="filter_status" class="form-select form-select-sm">
                                    <option value="">كل الحالات</option>
                                    <option value="نشط">نشط — لم يتجاوز تاريخ الاستحقاق</option>
                                    <option value="متأخر">متأخر — تجاوز التاريخ ولديه متبقي</option>
                                    <option value="منتهي">منتهي — تجاوز تاريخ الاستحقاق</option>
                                    <option value="مكتمل">مكتمل — تم السداد بالكامل</option>
                                    <option value="موقوف">موقوف — تم التجديد أو الإيقاف</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted">خطة الرسوم</label>
                                <select id="filter_fee_plan_id" class="form-select form-select-sm">
                                    <option value="">كل الخطط</option>
                                    @foreach($feePlanOptions as $fId => $fName)
                                        <option value="{{ $fId }}">{{ $fName }}</option>
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
                        <h6 class="mb-0 fw-semibold"><i class="ti ti-receipt me-1"></i> الاشتراكات</h6>
                        <span class="badge bg-primary rounded-pill">بيانات آجاكس</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="subscriptions-table"
                                   class="table table-hover align-middle mb-0 w-100">
                                <thead class="table-light">
                             <tr>
                                     <th>#</th>
                                     <th>الطالب</th>
                                     <th>الخطة / الدورة</th>
                                     <th>المبلغ</th>
                                     <th>المدفوع</th>
                                     <th>المتبقي</th>
                                      <th>تاريخ البداية</th>
                                      <th>تاريخ الاستحقاق</th>
                                      <th>موعد الباقي</th>
                                      <th>التقدم</th>
                                      <th>حالة الطالب</th>
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
            const table = $('#subscriptions-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.student-subscriptions.datatable') }}',
                    data: function (d) {
                        d.status      = $('#filter_status').val();
                        d.fee_plan_id = $('#filter_fee_plan_id').val();
                    }
                },
                language: {
                    emptyTable:  'لا توجد اشتراكات حتى الآن',
                    processing:  'جاري التحميل…',
                    lengthMenu:  'عرض _MENU_ اشتراك',
                    info:        'عرض _START_ إلى _END_ من _TOTAL_ اشتراك',
                    infoEmpty:   'لا توجد اشتراكات',
                    search:      'بحث:',
                    paginate: {first:'الأول', last:'الأخير', next:'التالي', previous:'السابق'},
                },
                order: [[0, 'desc']],
                columns: [
                    {data: 'id', width: '50px'},
                    {data: 'student_name'},
                    {
                        data: 'fee_plan_name',
                        render: function (val, t, row) {
                            let cycle = row.payment_cycle ? '<br><span class="badge bg-info text-dark small">' + row.payment_cycle + '</span>' : '';
                            return val + cycle;
                        }
                    },
                    {data: 'formatted_amount'},
                    {data: 'formatted_paid'},
                    {
                        data: 'formatted_remaining',
                        render: function (val, t, row) {
                            let cls = row.remaining_amount > 0 ? 'text-danger fw-semibold' : 'text-success';
                            return '<span class="' + cls + '">' + val + '</span>';
                        }
                    },
                    {
                        data: 'start_date',
                        render: function (val) { return val ? '<span class="text-muted small">' + val + '</span>' : '-'; }
                    },
                    {
                        data: 'due_date',
                        render: function (val, t, row) {
                            if (!val) return '-';
                            let cls = row.is_expired ? 'text-danger fw-semibold' : 'text-dark';
                            let expired = row.is_expired ? ' <span class="badge bg-danger">منتهي</span>' : '';
                            return '<span class="' + cls + ' small">' + val + '</span>' + expired;
                        }
                    },
                    {
                        data: 'remaining_due_date',
                        render: function (val, t, row) {
                            if (!val) return '-';
                            let days = row.days_until_due;
                            let badge = '';
                            if (days !== null && days <= 2 && days >= 0 && row.remaining_amount > 0) {
                                badge = ' <span class="badge bg-warning text-dark">قريباً</span>';
                            }
                            return '<span class="small">' + val + '</span>' + badge;
                        }
                    },
                    {
                        data: 'payment_progress',
                        render: function (val) {
                            const color = val >= 100 ? 'success' : (val >= 50 ? 'warning' : 'danger');
                            return '<div class="progress" style="height:18px;min-width:80px"><div class="progress-bar bg-' + color + '" style="width:' + val + '%">' + val + '%</div></div>';
                        }
                    },
                    {
                        data: 'student_status',
                        render: function (val) {
                            const isActive = val === 'active';
                            const label    = isActive ? 'نشط' : 'غير نشط';
                            const cls      = isActive ? 'bg-success' : 'bg-secondary';
                            return '<span class="badge ' + cls + '">' + label + '</span>';
                        }
                    },
                    {
                        data: 'id',
                        searchable: false,
                        orderable:  false,
                        render: function (id, t, row) {
                            const showUrl    = '{{ route('admin.student-subscriptions.show', ['studentSubscription' => '__ID__']) }}'.replace('__ID__', id);
                            const editUrl    = '{{ route('admin.student-subscriptions.edit', ['studentSubscription' => '__ID__']) }}'.replace('__ID__', id);
                            const deleteUrl  = '{{ route('admin.student-subscriptions.destroy', ['studentSubscription' => '__ID__']) }}'.replace('__ID__', id);
                            const renewUrl   = row.renewal_url;
                            const csrfToken  = '{{ csrf_token() }}';

                            let renewBtn = '';
                            @can('student-subscriptions.create')
                            if (row.student_status === 'active') {
                                renewBtn = '<form method="POST" action="' + renewUrl + '" onsubmit="return confirm(\'تجديد الاشتراك؟ سيتم إنشاء اشتراك جديد.\')" class="d-inline">'
                                    + '<input type="hidden" name="_token" value="' + csrfToken + '">'
                                    + '<button type="submit" class="btn btn-sm btn-outline-success" title="تجديد الاشتراك"><i class="ti ti-refresh"></i></button>'
                                    + '</form>';
                            }
                            @endcan

                            return '<div class="d-flex gap-1 flex-nowrap">'
                                + '<a class="btn btn-sm btn-outline-info" href="' + showUrl + '" title="عرض"><i class="ti ti-eye"></i></a>'
                                + '<a class="btn btn-sm btn-outline-primary" href="' + editUrl + '" title="تعديل"><i class="ti ti-pencil"></i></a>'
                                + renewBtn
                                + '<form method="POST" action="' + deleteUrl + '" onsubmit="return confirm(\'هل تريد حذف هذا الاشتراك؟\')" class="d-inline">'
                                + '<input type="hidden" name="_token" value="' + csrfToken + '">'
                                + '<input type="hidden" name="_method" value="DELETE">'
                                + '<button type="submit" class="btn btn-sm btn-outline-danger" title="حذف"><i class="ti ti-trash"></i></button>'
                                + '</form></div>';
                        }
                    }
                ]
            });

            $('#overdue-subscriptions-table').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                paging: true,
                info: true,
                ajax: {
                    url: '{{ route('admin.student-subscriptions.overdue-datatable') }}'
                },
                language: {
                    emptyTable: 'لا يوجد طلاب متأخرون حاليًا',
                    processing: 'جاري التحميل…',
                    lengthMenu: 'عرض _MENU_ سجل',
                    info: 'عرض _START_ إلى _END_ من _TOTAL_ سجل',
                    infoEmpty: 'لا توجد بيانات',
                    search: 'بحث:',
                    paginate: {first:'الأول', last:'الأخير', next:'التالي', previous:'السابق'},
                },
                order: [[4, 'desc']],
                columns: [
                    {data: 'id', width: '55px'},
                    {data: 'student_name'},
                    {
                        data: 'student_phone',
                        render: function (val) {
                            return '<span class="badge bg-light text-dark border">' + (val || '-') + '</span>';
                        }
                    },
                    {data: 'fee_plan_name'},
                    {
                        data: 'formatted_remaining',
                        render: function (val) {
                            return '<span class="text-danger fw-semibold">' + val + '</span>';
                        }
                    },
                    {
                        data: 'status',
                        render: function (val, _t, row) {
                            return '<span class="badge ' + row.status_badge + '">' + val + '</span>';
                        }
                    },
                    {
                        data: 'id',
                        searchable: false,
                        orderable: false,
                        render: function (id) {
                            const showUrl = '{{ route('admin.student-subscriptions.show', ['studentSubscription' => '__ID__']) }}'.replace('__ID__', id);
                            const editUrl = '{{ route('admin.student-subscriptions.edit', ['studentSubscription' => '__ID__']) }}'.replace('__ID__', id);
                            return '<div class="d-flex gap-1 flex-nowrap">'
                                + '<a class="btn btn-sm btn-outline-info" href="' + showUrl + '" title="عرض"><i class="ti ti-eye"></i></a>'
                                + '<a class="btn btn-sm btn-outline-primary" href="' + editUrl + '" title="تعديل"><i class="ti ti-pencil"></i></a>'
                                + '</div>';
                        }
                    }
                ]
            });

            $('#apply_filters').on('click', function () {
                table.ajax.reload();
            });

            $('#filter_status, #filter_fee_plan_id').on('change', function () {
                table.ajax.reload();
            });
        });
    </script>
@endsection

