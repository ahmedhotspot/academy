@extends('admin.layouts.master')

@section('title', 'إدارة طلاب تحفيظ القرآن الكريم')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <style>
        :root {
            --q-green:  #1B5E20;
            --q-mid:    #2E7D32;
            --q-light:  #43A047;
        }
        .students-banner {
            background: linear-gradient(135deg, var(--q-green) 0%, var(--q-mid) 60%, #388E3C 100%);
            border-radius: 16px; color: #fff; position: relative; overflow: hidden;
        }
        .students-banner::after {
            content: '﷽';
            position: absolute; left: -10px; top: -10px;
            font-size: 8rem; opacity: .06; color: #fff; line-height: 1;
            font-family: 'Cairo', sans-serif;
        }
        .stat-card { border:none; border-radius:14px; overflow:hidden; transition:transform .2s,box-shadow .2s; }
        .stat-card:hover { transform:translateY(-4px); box-shadow:0 10px 28px rgba(0,0,0,.12)!important; }
        .stat-icon { width:54px;height:54px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.5rem; }
        .filter-bar { background:linear-gradient(135deg,#f8fff8,#f0faf0); border:1px solid #c8e6c9; border-radius:12px; }
        #students-table thead th { background:var(--q-green);color:#fff;font-weight:600;border:none;white-space:nowrap;padding:12px 14px; }
        .btn-tbl { padding:4px 9px;border-radius:8px;font-size:.8rem;transition:filter .15s; }
        .btn-tbl:hover { filter:brightness(.88); }
        .badge-active   { background:linear-gradient(135deg,#1B5E20,#43A047);color:#fff;padding:5px 12px;border-radius:20px;font-size:.78rem; }
        .badge-inactive { background:linear-gradient(135deg,#757575,#9E9E9E);color:#fff;padding:5px 12px;border-radius:20px;font-size:.78rem; }
        .student-avatar { width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--q-green),var(--q-light));color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.9rem;flex-shrink:0; }
        .row-num { width:30px;height:30px;border-radius:50%;background:#e8f5e9;color:var(--q-green);display:inline-flex;align-items:center;justify-content:center;font-weight:700;font-size:.8rem; }
        .dataTables_wrapper .dataTables_paginate .page-item.active .page-link { background:var(--q-green)!important;border-color:var(--q-green)!important; }
        .dataTables_wrapper .dataTables_paginate .page-link { color:var(--q-green); }
        .dataTables_wrapper .dataTables_filter input { border-color:#c8e6c9!important; border-radius:8px!important; }
        .dataTables_wrapper .dataTables_filter input:focus { box-shadow:0 0 0 3px rgba(27,94,32,.15)!important; }
    </style>
@endsection

@section('content')
<div class="page-content-wrapper">
    <div class="content-container">
        <div class="page-content">

            @include('admin.partials.page-header', ['title'=>'إدارة الطلاب','breadcrumbs'=>$breadcrumbs,'actions'=>$actions])
            @include('admin.partials.alerts')

            {{-- ══ بانر القرآن ══ --}}
            <div class="students-banner p-4 mb-4 shadow">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h4 class="fw-bold mb-1"><i class="ti ti-books me-2"></i>طلاب أكاديمية تحفيظ القرآن الكريم</h4>
                    </div>
                </div>
            </div>

            {{-- ══ إحصائيات سريعة ══ --}}
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="card stat-card shadow-sm h-100">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="stat-icon" style="background:#e8f5e9;"><i class="ti ti-users" style="color:var(--q-green);"></i></div>
                            <div>
                                <div class="text-muted small mb-1">إجمالي الطلاب</div>
                                <div class="fs-3 fw-bold" style="color:var(--q-green);">{{ $stats['total'] ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card stat-card shadow-sm h-100">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="stat-icon" style="background:#e3f2fd;"><i class="ti ti-user-check" style="color:#1565C0;"></i></div>
                            <div>
                                <div class="text-muted small mb-1">الطلاب النشطون</div>
                                <div class="fs-3 fw-bold" style="color:#1565C0;">{{ $stats['active'] ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card stat-card shadow-sm h-100">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="stat-icon" style="background:#fce4ec;"><i class="ti ti-user-pause" style="color:#c62828;"></i></div>
                            <div>
                                <div class="text-muted small mb-1">غير النشطين</div>
                                <div class="fs-3 fw-bold" style="color:#c62828;">{{ $stats['inactive'] ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card stat-card shadow-sm h-100">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="stat-icon" style="background:#fff8e1;"><i class="ti ti-chart-pie-2" style="color:#e65100;"></i></div>
                            <div>
                                <div class="text-muted small mb-1">نسبة النشاط</div>
                                @php $actRate = ($stats['total']??0)>0 ? round((($stats['active']??0)/($stats['total']??1))*100) : 0; @endphp
                                <div class="fs-3 fw-bold" style="color:#e65100;">{{ $actRate }}%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══ شريط الفلاتر ══ --}}
            <div class="filter-bar p-3 mb-4">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ti ti-adjustments-horizontal" style="color:var(--q-green);"></i>
                        <span class="fw-semibold small" style="color:var(--q-green);">تصفية النتائج:</span>
                    </div>
                    <select id="filter-status" class="form-select form-select-sm" style="min-width:160px;">
                        <option value="">جميع الحالات</option>
                        <option value="active">نشط</option>
                        <option value="inactive">غير نشط</option>
                    </select>
                    <select id="filter-branch" class="form-select form-select-sm" style="min-width:190px;">
                        @if(auth()->user()?->isSuperAdmin())
                            <option value="">جميع الفروع</option>
                        @endif
                        @foreach($branchOptions as $bid => $bname)
                            <option value="{{ $bid }}">{{ $bname }}</option>
                        @endforeach
                    </select>
                    <button id="reset-filters" class="btn btn-outline-secondary btn-sm">
                        <i class="ti ti-rotate me-1"></i>إعادة تعيين
                    </button>
                </div>
            </div>

            {{-- ══ بطاقة الجدول ══ --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header border-0 d-flex align-items-center justify-content-between py-3 px-4"
                     style="background:linear-gradient(90deg,var(--q-green),#388E3C);">
                    <h6 class="mb-0 fw-bold text-white">
                        <i class="ti ti-list-details me-2"></i>قائمة الطلاب المسجلين
                    </h6>
                    <span class="badge bg-white px-3 py-2" style="color:var(--q-green);">
                        <i class="ti ti-users me-1"></i>{{ $stats['total'] ?? 0 }} طالب
                    </span>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table id="students-table" class="table table-hover align-middle w-100 mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>اسم الطالب</th>
                                    <th>العمر</th>
                                    <th>الجنسية</th>
                                    <th>رقم الهوية</th>
                                    <th>الهاتف</th>
                                    <th>الواتساب</th>
                                    <th>الفرع</th>
                                    <th>الحالة</th>
                                    <th style="width:130px;">الإجراءات</th>
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
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function () {

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    const table = $('#students-table').DataTable({
        processing : true,
        serverSide : true,
        ajax: {
            url : '{{ route('admin.students.datatable') }}',
            data: function (d) {
                d.filter_status    = $('#filter-status').val();
                d.filter_branch_id = $('#filter-branch').val();
            }
        },
        language: {
            sProcessing   : '<div class="d-flex align-items-center justify-content-center gap-2 py-2 text-success fw-semibold"><div class="spinner-border spinner-border-sm" role="status"></div> جاري تحميل البيانات...</div>',
            sLengthMenu   : 'عرض _MENU_ سجلات',
            sZeroRecords  : '<div class="text-center py-5"><i class="ti ti-search-off" style="font-size:3rem;color:#ccc;"></i><br><span class="text-muted mt-2 d-block">لا توجد نتائج مطابقة</span></div>',
            sEmptyTable   : '<div class="text-center py-5"><i class="ti ti-users-off" style="font-size:3rem;color:#ccc;"></i><br><span class="text-muted mt-2 d-block">لا يوجد طلاب مسجلون حتى الآن</span></div>',
            sInfo         : 'عرض <strong>_START_</strong> إلى <strong>_END_</strong> من <strong>_TOTAL_</strong> طالب',
            sInfoEmpty    : 'لا توجد سجلات',
            sInfoFiltered : '(تصفية من _MAX_ إجمالي)',
            sSearch       : '',
            sSearchPlaceholder: 'ابحث بالاسم أو الهاتف أو الهوية...',
            sLoadingRecords: 'جاري التحميل...',
            sInfoThousands : ',',
            oPaginate: { sFirst:'«', sLast:'»', sNext:'‹', sPrevious:'›' },
            oAria: { sSortAscending:': تصاعدي', sSortDescending:': تنازلي' }
        },
        columns: [
            {
                data:'id', orderable:false, searchable:false,
                render: function(id, type, row, meta) {
                    return '<span class="row-num">' + (meta.row + 1) + '</span>';
                }
            },
            {
                data:'full_name',
                render: function(name) {
                    const i = name ? name.charAt(0) : '?';
                    return '<div class="d-flex align-items-center gap-2">'
                         + '<div class="student-avatar">' + i + '</div>'
                         + '<span class="fw-semibold">' + (name||'-') + '</span></div>';
                }
            },
            {
                data:'age',
                render: function(age) {
                    return age ? '<span class="badge bg-light text-dark border">' + age + ' سنة</span>' : '<span class="text-muted">-</span>';
                }
            },
            { data:'nationality', render:function(v){return v||'<span class="text-muted">-</span>';} },
            { data:'identity_number', render:function(v){return (v&&v!=='-')?v:'<span class="text-muted">-</span>';} },
            {
                data:'phone',
                render: function(p) {
                    return p ? '<a href="tel:'+p+'" class="text-decoration-none text-dark"><i class="ti ti-phone me-1 text-success"></i>'+p+'</a>' : '<span class="text-muted">-</span>';
                }
            },
            {
                data:'whatsapp',
                render: function(w) {
                    if(!w||w==='-') return '<span class="text-muted">-</span>';
                    return '<a href="https://wa.me/'+w.replace(/\D/g,'')+'" target="_blank" class="text-decoration-none" style="color:#25D366;"><i class="ti ti-brand-whatsapp me-1"></i>'+w+'</a>';
                }
            },
            {
                data:'branch',
                render: function(b) {
                    return (b&&b!=='-') ? '<span class="badge border" style="background:#e8f5e9;color:#1B5E20;border-color:#c8e6c9!important;"><i class="ti ti-building me-1"></i>'+b+'</span>' : '<span class="text-muted">-</span>';
                }
            },
            {
                data:'status',
                render: function(s, type, row) {
                    return row.status_badge==='bg-success'
                        ? '<span class="badge-active"><i class="ti ti-circle-check me-1"></i>'+s+'</span>'
                        : '<span class="badge-inactive"><i class="ti ti-circle-x me-1"></i>'+s+'</span>';
                }
            },
            {
                data:'id', searchable:false, orderable:false,
                render: function(id) {
                    const su = '{{ route('admin.students.show',   ['student'=>'__ID__']) }}'.replace('__ID__',id);
                    const eu = '{{ route('admin.students.edit',   ['student'=>'__ID__']) }}'.replace('__ID__',id);
                    const du = '{{ route('admin.students.destroy',['student'=>'__ID__']) }}'.replace('__ID__',id);
                    return '<div class="d-flex gap-1 flex-nowrap">'
                         + '<a class="btn btn-tbl" style="background:#e3f2fd;color:#1565C0;border:1px solid #bbdefb;" href="'+su+'" title="عرض الملف"><i class="ti ti-eye"></i></a>'
                         + '<a class="btn btn-tbl" style="background:#e8f5e9;color:#1B5E20;border:1px solid #c8e6c9;" href="'+eu+'" title="تعديل"><i class="ti ti-pencil"></i></a>'
                         + '<button class="btn btn-tbl btn-del" style="background:#fce4ec;color:#c62828;border:1px solid #f8bbd0;" data-url="'+du+'" title="حذف"><i class="ti ti-trash"></i></button>'
                         + '</div>';
                }
            }
        ],
        order     : [[0, 'desc']],
        pageLength: 15,
        lengthMenu: [[10,15,25,50,100],[10,15,25,50,100]],
        dom: '<"row align-items-center mb-3"<"col-sm-4"l><"col-sm-8"f>>rt<"row align-items-center mt-3"<"col-sm-6"i><"col-sm-6"p>>'
    });

    $('#filter-status, #filter-branch').on('change', function() { table.ajax.reload(); });
    $('#reset-filters').on('click', function() {
        $('#filter-status, #filter-branch').val('');
        table.ajax.reload();
    });

    $(document).on('click', '.btn-del', function() {
        const url = $(this).data('url');
        if (!confirm('⚠️ تأكيد الحذف\n\nهل أنت متأكد من حذف هذا الطالب؟\nلا يمكن التراجع عن هذا الإجراء.')) return;
        $.post(url, {_method:'DELETE'}, function() {
            table.ajax.reload(null, false);
        }).fail(function() {
            alert('❌ حدث خطأ أثناء التنفيذ. يرجى المحاولة مرة أخرى.');
        });
    });
});
</script>
@endsection

