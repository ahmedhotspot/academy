@extends('admin.layouts.master')

@section('title', 'المتابعة التعليمية اليومية')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
@endsection

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'المتابعة التعليمية اليومية',
                    'breadcrumbs' => $breadcrumbs,
                    'actions'     => $actions,
                ])

                @include('admin.partials.alerts')

                {{-- بطاقات الملخص ── --}}
                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="flex-shrink-0 bg-primary bg-opacity-10 rounded p-3">
                                    <i class="ti ti-list-check fs-3 text-primary"></i>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted small">إجمالي سجلات المتابعة</p>
                                    <h4 class="mb-0 fw-bold">{{ $reportSummary['total'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="flex-shrink-0 bg-success bg-opacity-10 rounded p-3">
                                    <i class="ti ti-user-check fs-3 text-success"></i>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted small">ملتزم</p>
                                    <h4 class="mb-0 fw-bold text-success">{{ $reportSummary['committed'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="flex-shrink-0 bg-warning bg-opacity-10 rounded p-3">
                                    <i class="ti ti-user-exclamation fs-3 text-warning"></i>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted small">متأخر</p>
                                    <h4 class="mb-0 fw-bold text-warning">{{ $reportSummary['late'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="flex-shrink-0 bg-info bg-opacity-10 rounded p-3">
                                    <i class="ti ti-star fs-3 text-info"></i>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted small">إتقان ممتاز</p>
                                    <h4 class="mb-0 fw-bold text-info">{{ $reportSummary['excellent'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── فلاتر البحث ── --}}
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
                                <label class="form-label small text-muted">تاريخ المتابعة</label>
                                <input id="filter_progress_date" type="date" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small text-muted">حالة الالتزام</label>
                                <select id="filter_commitment_status" class="form-select form-select-sm">
                                    <option value="">الكل</option>
                                    <option value="ملتزم">ملتزم</option>
                                    <option value="متأخر">متأخر</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small text-muted">مستوى الإتقان</label>
                                <select id="filter_mastery_level" class="form-select form-select-sm">
                                    <option value="">الكل</option>
                                    @foreach(['ممتاز','جيد جداً','جيد','مقبول','ضعيف'] as $lvl)
                                        <option value="{{ $lvl }}">{{ $lvl }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button id="apply_filters" type="button" class="btn btn-primary btn-sm w-100">
                                    <i class="ti ti-search me-1"></i> تطبيق
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── جدول البيانات ── --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-semibold"><i class="ti ti-notebook me-1"></i> سجلات المتابعة التعليمية</h6>
                        <span class="badge bg-primary rounded-pill">بيانات آجاكس</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="progress-logs-table"
                                   class="table table-hover align-middle mb-0 w-100">
                                <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>الطالب</th>
                                    <th>الحلقة</th>
                                    <th>التاريخ</th>
                                    <th>الحفظ</th>
                                    <th>المراجعة</th>
                                    <th>التجويد</th>
                                    <th>الإتقان</th>
                                    <th>الالتزام</th>
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
            const table = $('#progress-logs-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.student-progress-logs.datatable') }}',
                    data: function (d) {
                        d.group_id           = $('#filter_group_id').val();
                        d.progress_date      = $('#filter_progress_date').val();
                        d.commitment_status  = $('#filter_commitment_status').val();
                        d.mastery_level      = $('#filter_mastery_level').val();
                    }
                },
                language: {
                    emptyTable:  'لا توجد سجلات متابعة حتى الآن',
                    processing:  'جاري التحميل…',
                    lengthMenu:  'عرض _MENU_ سجل',
                    info:        'عرض _START_ إلى _END_ من _TOTAL_ سجل',
                    infoEmpty:   'لا توجد سجلات',
                    search:      'بحث:',
                    paginate: {first:'الأول', last:'الأخير', next:'التالي', previous:'السابق'},
                },
                order: [[3, 'desc']],
                columns: [
                    {data: 'id', width: '50px'},
                    {data: 'student_name'},
                    {data: 'group_name'},
                    {data: 'progress_date'},
                    {data: 'memorization_amount'},
                    {data: 'revision_amount'},
                    {
                        data: 'tajweed_evaluation',
                        render: function (val, t, row) {
                            return '<span class="badge ' + row.tajweed_badge + '">' + val + '</span>';
                        }
                    },
                    {
                        data: 'mastery_level',
                        render: function (val, t, row) {
                            return '<span class="badge ' + row.mastery_badge + '">' + val + '</span>';
                        }
                    },
                    {
                        data: 'commitment_status',
                        render: function (val, t, row) {
                            return '<span class="badge ' + row.commitment_badge + '">' + val + '</span>';
                        }
                    },
                    {
                        data: 'id',
                        searchable: false,
                        orderable:  false,
                        render: function (id, t, row) {
                            const showUrl   = '{{ route('admin.student-progress-logs.show', ['student' => '__SID__']) }}'.replace('__SID__', row.student_id);
                            const editUrl   = '{{ route('admin.student-progress-logs.edit', ['studentProgressLog' => '__ID__']) }}'.replace('__ID__', id);
                            const deleteUrl = '{{ route('admin.student-progress-logs.destroy', ['studentProgressLog' => '__ID__']) }}'.replace('__ID__', id);

                            return '<div class="d-flex gap-1 flex-nowrap">'
                                + '<a class="btn btn-sm btn-outline-info" href="' + showUrl + '" title="سجل الطالب"><i class="ti ti-eye"></i></a>'
                                + '<a class="btn btn-sm btn-outline-primary" href="' + editUrl + '" title="تعديل"><i class="ti ti-pencil"></i></a>'
                                + '<form method="POST" action="' + deleteUrl + '" onsubmit="return confirm(\'هل تريد حذف هذا السجل؟\')" class="d-inline">'
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

            // إعادة التحميل عند الضغط على Enter في أي فلتر
            $('#filter_group_id, #filter_progress_date, #filter_commitment_status, #filter_mastery_level')
                .on('keypress change', function (e) {
                    if (e.type === 'change' || e.which === 13) table.ajax.reload();
                });
        });
    </script>
@endsection

