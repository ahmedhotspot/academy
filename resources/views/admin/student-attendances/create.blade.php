@extends('admin.layouts.master')

@section('title', 'إدارة حضور وغياب الطلاب - إضافة')

@section('content')
    @php
        $statusStyles = [
            'حاضر' => 'success',
            'غائب' => 'danger',
            'منقول' => 'info',
        ];

        $entries = old('entries', collect($dailySheet['rows'])->map(fn ($row) => [
            'student_id' => $row['student_id'],
            'status' => $row['status'],
            'notes' => $row['notes'],
        ])->all());
    @endphp

    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">
                @include('admin.partials.page-header', [
                    'title' => 'تسجيل حضور الطلاب',
                    'breadcrumbs' => $breadcrumbs,
                ])

                @include('admin.partials.alerts')

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.student-attendances.create') }}" class="row g-3 align-items-end">
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label fw-semibold">تاريخ كشف الحضور</label>
                                <input type="date" name="attendance_date" class="form-control" value="{{ old('attendance_date', $dailySheet['attendance_date']) }}">
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <button type="submit" class="btn btn-primary w-100"><i class="ti ti-calendar-search me-1"></i> تحميل كشف اليوم</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-xl-4 col-md-6"><div class="card border-0 shadow-sm h-100"><div class="card-body"><p class="mb-1 text-muted">عدد الطلاب</p><h5 class="mb-0">{{ $dailySheet['summary']['students_count'] }}</h5></div></div></div>
                    <div class="col-xl-4 col-md-6"><div class="card border-0 shadow-sm h-100"><div class="card-body"><p class="mb-1 text-muted">مسجل مسبقًا</p><h5 class="mb-0 text-info">{{ $dailySheet['summary']['recorded_count'] }}</h5></div></div></div>
                    <div class="col-xl-4 col-md-6"><div class="card border-0 shadow-sm h-100"><div class="card-body"><p class="mb-1 text-muted">غير مسجل بعد</p><h5 class="mb-0 text-warning">{{ $dailySheet['summary']['pending_count'] }}</h5></div></div></div>
                </div>

                <form action="{{ route('admin.student-attendances.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="attendance_date" value="{{ old('attendance_date', $dailySheet['attendance_date']) }}">

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom d-flex flex-column flex-lg-row justify-content-between gap-3">
                            <h6 class="mb-0 fw-semibold">كشف حضور الطلاب</h6>
                            <input type="text" id="student-search" class="form-control" style="max-width:320px" placeholder="ابحث باسم الطالب أو الفرع...">
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th style="width: 30%">الطالب</th>
                                        <th style="width: 18%">الفرع</th>
                                        <th style="width: 36%">الحالة</th>
                                        <th style="width: 16%">ملاحظات</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($dailySheet['rows'] as $index => $row)
                                        @php
                                            $currentEntry = $entries[$index] ?? ['student_id' => $row['student_id'], 'status' => $row['status'], 'notes' => $row['notes']];
                                            $currentStatus = $currentEntry['status'] ?? 'حاضر';
                                        @endphp
                                        <tr class="attendance-row" data-search="{{ mb_strtolower($row['student_name'] . ' ' . $row['branch_name']) }}">
                                            <td>
                                                <input type="hidden" name="entries[{{ $index }}][student_id]" value="{{ $row['student_id'] }}">
                                                <div class="fw-semibold">{{ $row['student_name'] }}</div>
                                                <div class="text-muted small">رقم الطالب: #{{ $row['student_id'] }}</div>
                                            </td>
                                            <td>{{ $row['branch_name'] }}</td>
                                            <td>
                                                <input type="hidden" name="entries[{{ $index }}][status]" value="{{ $currentStatus }}" class="status-input">
                                                <div class="btn-group flex-wrap status-group" role="group">
                                                    @foreach($statusStyles as $status => $style)
                                                        <button type="button" class="btn btn-sm status-btn {{ $currentStatus === $status ? 'btn-' . $style : 'btn-outline-' . $style }}" data-status="{{ $status }}">{{ $status }}</button>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" name="entries[{{ $index }}][notes]" class="form-control form-control-sm" value="{{ $currentEntry['notes'] ?? '' }}" placeholder="اختياري">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted py-4">لا يوجد طلاب لعرضهم حالياً.</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top d-flex gap-2 justify-content-end">
                            <a href="{{ route('admin.student-attendances.index') }}" class="btn btn-light">رجوع</a>
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
                'منقول': 'info'
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

            const searchInput = document.getElementById('student-search');
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

