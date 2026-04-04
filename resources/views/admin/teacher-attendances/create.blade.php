@extends('admin.layouts.master')

@section('title', 'إدارة حضور وغياب المعلمين - إضافة')

@section('content')
    @php
        $statusStyles = [
            'حاضر' => 'success',
            'غائب' => 'danger',
            'متأخر' => 'warning',
            'بعذر' => 'info',
        ];

        $entries = old('entries', collect($dailySheet['rows'])->map(fn ($row) => [
            'teacher_id' => $row['teacher_id'],
            'status' => $row['status'],
            'notes' => $row['notes'],
        ])->all());
    @endphp

    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">
                @include('admin.partials.page-header', [
                    'title' => 'تسجيل حضور المعلمين',
                    'breadcrumbs' => $breadcrumbs,
                ])

                @include('admin.partials.alerts')

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
                            <form method="GET" action="{{ route('admin.teacher-attendances.create') }}" class="row g-3 align-items-end flex-grow-1">
                                <div class="col-lg-4 col-md-6">
                                    <label class="form-label fw-semibold">تاريخ كشف الحضور</label>
                                    <input type="date" name="attendance_date" class="form-control"
                                           value="{{ old('attendance_date', $dailySheet['attendance_date']) }}">
                                </div>
                                <div class="col-lg-3 col-md-6 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="ti ti-calendar-search me-1"></i> تحميل كشف اليوم
                                    </button>
                                </div>
                            </form>

                            <div class="alert alert-info mb-0 py-2 px-3">
                                عند الحفظ سيتم إضافة السجلات كما هي في الكشف الحالي، ويمكنك مراجعتها لاحقًا من سجل كل معلم.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100"><div class="card-body"><p class="mb-1 text-muted">عدد المعلمين</p><h5 class="mb-0">{{ $dailySheet['summary']['teachers_count'] }}</h5></div></div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100"><div class="card-body"><p class="mb-1 text-muted">مسجل مسبقًا</p><h5 class="mb-0 text-info">{{ $dailySheet['summary']['recorded_count'] }}</h5></div></div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100"><div class="card-body"><p class="mb-1 text-muted">غير مسجل بعد</p><h5 class="mb-0 text-warning">{{ $dailySheet['summary']['pending_count'] }}</h5></div></div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100"><div class="card-body"><p class="mb-1 text-muted">تاريخ التسجيل</p><h5 class="mb-0">{{ $dailySheet['attendance_date'] }}</h5></div></div>
                    </div>
                </div>

                <form action="{{ route('admin.teacher-attendances.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="attendance_date" value="{{ old('attendance_date', $dailySheet['attendance_date']) }}">

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom d-flex flex-column flex-lg-row justify-content-between gap-3">
                            <div>
                                <h6 class="mb-1 fw-semibold">كشف حضور المعلمين</h6>
                                <p class="mb-0 text-muted small">استخدم البحث والأزرار السريعة لتسجيل يوم كامل في أقل وقت ممكن.</p>
                            </div>
                            <div class="d-flex flex-column flex-md-row gap-2">
                                <input type="text" id="teacher-search" class="form-control" placeholder="ابحث باسم المعلم أو الفرع...">
                                <div class="btn-group" role="group" aria-label="bulk-status-actions">
                                    @foreach($statusStyles as $status => $style)
                                        <button type="button" class="btn btn-outline-{{ $style }} bulk-status-btn" data-bulk-status="{{ $status }}">{{ $status }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th style="width: 28%;">المعلم</th>
                                        <th style="width: 16%;">الفرع</th>
                                        <th style="width: 36%;">الحالة</th>
                                        <th style="width: 14%;">ملاحظات</th>
                                        <th style="width: 6%;">الحالة السابقة</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($dailySheet['rows'] as $index => $row)
                                        @php
                                            $currentEntry = $entries[$index] ?? ['teacher_id' => $row['teacher_id'], 'status' => $row['status'], 'notes' => $row['notes']];
                                            $currentStatus = $currentEntry['status'] ?? 'حاضر';
                                        @endphp
                                        <tr class="attendance-row" data-search="{{ mb_strtolower($row['teacher_name'] . ' ' . $row['branch_name']) }}">
                                            <td>
                                                <input type="hidden" name="entries[{{ $index }}][teacher_id]" value="{{ $row['teacher_id'] }}">
                                                <div class="fw-semibold">{{ $row['teacher_name'] }}</div>
                                                <div class="text-muted small">رقم المعلم: #{{ $row['teacher_id'] }}</div>
                                            </td>
                                            <td>{{ $row['branch_name'] }}</td>
                                            <td>
                                                <input type="hidden" name="entries[{{ $index }}][status]" value="{{ $currentStatus }}" class="status-input">
                                                <div class="btn-group flex-wrap status-group" role="group">
                                                    @foreach($statusStyles as $status => $style)
                                                        <button
                                                            type="button"
                                                            class="btn btn-sm status-btn {{ $currentStatus === $status ? 'btn-' . $style : 'btn-outline-' . $style }}"
                                                            data-status="{{ $status }}"
                                                        >
                                                            {{ $status }}
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" name="entries[{{ $index }}][notes]" class="form-control form-control-sm"
                                                       value="{{ $currentEntry['notes'] ?? '' }}" placeholder="اختياري">
                                            </td>
                                            <td>
                                                @if($row['is_recorded'])
                                                    <span class="badge bg-info-subtle text-info border">مسجل</span>
                                                @else
                                                    <span class="badge bg-light text-muted border">جديد</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">لا يوجد معلمون لعرضهم حالياً.</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top d-flex gap-2 justify-content-end">
                            <a href="{{ route('admin.teacher-attendances.index') }}" class="btn btn-light">رجوع</a>
                            <button type="submit" class="btn btn-primary">حفظ كشف اليوم</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        (function () {
            const styleMap = {
                'حاضر': 'success',
                'غائب': 'danger',
                'متأخر': 'warning',
                'بعذر': 'info',
            };

            function paintGroup(group, selectedStatus) {
                group.querySelectorAll('.status-btn').forEach((button) => {
                    const status = button.dataset.status;
                    const style = styleMap[status] || 'secondary';
                    button.className = 'btn btn-sm status-btn ' + (status === selectedStatus ? 'btn-' + style : 'btn-outline-' + style);
                });
            }

            document.querySelectorAll('.status-group').forEach((group) => {
                const input = group.closest('td').querySelector('.status-input');
                paintGroup(group, input.value);

                group.addEventListener('click', function (event) {
                    const button = event.target.closest('.status-btn');

                    if (!button) {
                        return;
                    }

                    input.value = button.dataset.status;
                    paintGroup(group, input.value);
                });
            });

            document.querySelectorAll('.bulk-status-btn').forEach((button) => {
                button.addEventListener('click', function () {
                    const status = this.dataset.bulkStatus;

                    document.querySelectorAll('.attendance-row').forEach((row) => {
                        if (row.style.display === 'none') {
                            return;
                        }

                        const input = row.querySelector('.status-input');
                        const group = row.querySelector('.status-group');
                        input.value = status;
                        paintGroup(group, status);
                    });
                });
            });

            const searchInput = document.getElementById('teacher-search');

            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    const keyword = this.value.trim().toLowerCase();

                    document.querySelectorAll('.attendance-row').forEach((row) => {
                        const haystack = row.dataset.search || '';
                        row.style.display = keyword === '' || haystack.includes(keyword) ? '' : 'none';
                    });
                });
            }
        })();
    </script>
@endsection

