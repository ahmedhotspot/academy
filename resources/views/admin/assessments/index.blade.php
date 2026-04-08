@extends('admin.layouts.master')

@section('title', 'نظام الاختبارات')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    <style>
        #assessments-table thead th,
        #assessments-table tbody td {
            text-align: right;
        }

        #assessments-table {
            direction: rtl;
        }

        #assessments-table_wrapper {
            direction: rtl;
            text-align: right;
        }

        #assessments-table_wrapper .dataTables_filter,
        #assessments-table_wrapper .dataTables_length,
        #assessments-table_wrapper .dataTables_info {
            text-align: right;
        }
    </style>
@endsection

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'نظام الاختبارات',
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
                                    <p class="mb-0 text-muted small">إجمالي الاختبارات</p>
                                    <h4 class="mb-0 fw-bold">{{ $reportSummary['total'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="flex-shrink-0 bg-info bg-opacity-10 rounded p-3">
                                    <i class="ti ti-calendar-week fs-3 text-info"></i>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted small">اختبارات أسبوعية</p>
                                    <h4 class="mb-0 fw-bold text-info">{{ $reportSummary['weekly'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="flex-shrink-0 bg-success bg-opacity-10 rounded p-3">
                                    <i class="ti ti-calendar-month fs-3 text-success"></i>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted small">اختبارات شهرية</p>
                                    <h4 class="mb-0 fw-bold text-success">{{ $reportSummary['monthly'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="flex-shrink-0 bg-warning bg-opacity-10 rounded p-3">
                                    <i class="ti ti-certificate fs-3 text-warning"></i>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted small">اختبارات ختم الجزء</p>
                                    <h4 class="mb-0 fw-bold text-warning">{{ $reportSummary['complete'] }}</h4>
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
                            <div class="col-md-3">
                                <label class="form-label small text-muted">الحلقة</label>
                                <select id="filter_group_id" class="form-select form-select-sm">
                                    <option value="">كل الحلقات</option>
                                    @foreach($groupOptions as $gId => $gName)
                                        <option value="{{ $gId }}">{{ $gName }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small text-muted">نوع الاختبار</label>
                                <select id="filter_type" class="form-select form-select-sm">
                                    <option value="">كل الأنواع</option>
                                    @foreach($assessmentTypes as $t)
                                        <option value="{{ $t }}">{{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small text-muted">التاريخ</label>
                                <input id="filter_assessment_date" type="date" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
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
                        <h6 class="mb-0 fw-semibold"><i class="ti ti-notebook me-1"></i> سجل الاختبارات</h6>
                        <span class="badge bg-primary rounded-pill">بيانات آجاكس</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="assessments-table"
                                   class="table table-hover align-middle mb-0 w-100">
                                <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>الطالب</th>
                                    <th>الحلقة</th>
                                    <th>النوع</th>
                                    <th>التاريخ</th>
                                    <th>الحفظ</th>
                                    <th>التجويد</th>
                                    <th>التدبر</th>
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
            const table = $('#assessments-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.assessments.datatable') }}',
                    data: function (d) {
                        d.group_id        = $('#filter_group_id').val();
                        d.type            = $('#filter_type').val();
                        d.assessment_date = $('#filter_assessment_date').val();
                    }
                },
                language: {
                    emptyTable:  'لا توجد اختبارات حتى الآن',
                    processing:  'جاري التحميل…',
                    lengthMenu:  'عرض _MENU_ سجل',
                    info:        'عرض _START_ إلى _END_ من _TOTAL_ سجل',
                    infoEmpty:   'لا توجد سجلات',
                    search:      'بحث:',
                    paginate: {first:'الأول', last:'الأخير', next:'التالي', previous:'السابق'},
                },
                order: [[4, 'desc']],
                columns: [
                    {data: 'id', width: '50px'},
                    {data: 'student_name'},
                    {data: 'group_name'},
                    {data: 'type_label'},
                    {data: 'assessment_date'},
                    {
                        data: 'memorization_result',
                        render: function (val, t, row) {
                            if (val === '-') return '-';
                            return '<span class="badge ' + row.memorization_badge + '">' + val + '</span>';
                        }
                    },
                    {
                        data: 'tajweed_result',
                        render: function (val, t, row) {
                            if (val === '-') return '-';
                            return '<span class="badge ' + row.tajweed_badge + '">' + val + '</span>';
                        }
                    },
                    {
                        data: 'average_score',
                        render: function (val, t, row) {
                            if (val === '-') return '-';
                            return '<span class="badge ' + row.average_badge + ' fw-bold">' + val + '</span>';
                        }
                    },
                    {
                        data: 'id',
                        searchable: false,
                        orderable:  false,
                        render: function (id, t, row) {
                            const showUrl = '{{ route('admin.assessments.show', ['student' => '__SID__']) }}'.replace('__SID__', row.student_id);
                            const editUrl = '{{ route('admin.assessments.edit', ['assessment' => '__ID__']) }}'.replace('__ID__', id);
                            const deleteUrl = '{{ route('admin.assessments.destroy', ['assessment' => '__ID__']) }}'.replace('__ID__', id);

                            return '<div class="d-flex gap-1 flex-nowrap">'
                                + '<a class="btn btn-sm btn-outline-info" href="' + showUrl + '" title="سجل الطالب"><i class="ti ti-eye"></i></a>'
                                + '<a class="btn btn-sm btn-outline-primary" href="' + editUrl + '" title="تعديل"><i class="ti ti-pencil"></i></a>'
                                + '<form method="POST" action="' + deleteUrl + '" onsubmit="return confirm(\'هل تريد حذف هذا الاختبار؟\')" class="d-inline">'
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

            $('#filter_group_id, #filter_type, #filter_assessment_date')
                .on('change', function () { table.ajax.reload(); });
        });
    </script>
@endsection

