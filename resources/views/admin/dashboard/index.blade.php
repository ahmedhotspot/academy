@extends('admin.layouts.master')

@section('title', 'لوحة التحكم الرئيسية')

@section('content')
    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'لوحة التحكم الرئيسية',
                    'description' => 'نظرة سريعة شاملة على الأداء التشغيلي والتعليمي والمالي للأكاديمية.',
                    'breadcrumbs' => [
                        ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                        ['title' => 'لوحة التحكم'],
                    ],
                    'showToolbar' => false,
                ])

                @include('admin.partials.alerts')

                {{-- Hero Header --}}
                <div class="card border-0 shadow-sm mb-4 dashboard-hero">
                    <div class="card-body p-4 p-lg-5">
                        <div class="row g-4 align-items-center">
                            <div class="col-lg-7">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="hero-icon-box">
                                        <i class="ti ti-layout-dashboard"></i>
                                    </div>
                                    <div>
                                        <h4 class="fw-bold text-white mb-2">{{ $hero['greeting'] }}</h4>
                                        <h2 class="fw-bolder text-white mb-2">{{ $hero['user_name'] }}</h2>
                                        <p class="text-white-50 mb-1">
                                            <i class="ti ti-calendar-event me-1"></i>
                                            {{ $hero['date_text'] }}
                                        </p>
                                        <p class="text-white-50 mb-0">
                                            <i class="ti ti-clock me-1"></i>
                                            <span id="live-clock">{{ $hero['time_text'] }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-5">
                                <div class="d-flex flex-wrap justify-content-lg-end gap-2">
                                    @foreach($quickActions as $action)
                                        @can($action['permission'])
                                            <a href="{{ $action['url'] }}" class="btn {{ $action['class'] }} btn-sm hero-action-btn">
                                                <i class="{{ $action['icon'] }} me-1"></i>
                                                {{ $action['title'] }}
                                            </a>
                                        @endcan
                                    @endforeach
                                </div>
                                <div class="hero-side-box mt-3">
                                    <p class="mb-1 text-white-50 small">الإشعارات غير المقروءة</p>
                                    <h5 class="mb-0 text-white fw-bold">{{ $unreadNotifications->count() }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Stats Cards --}}
                <div class="row g-3 mb-4">
                    @foreach($statsCards as $card)
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="card border-0 shadow-sm h-100 dashboard-stat-card {{ $card['bg'] }}">
                                <div class="card-body d-flex align-items-center justify-content-between gap-3">
                                    <div>
                                        <p class="mb-1 text-muted small fw-semibold">{{ $card['title'] }}</p>
                                        <h4 class="fw-bold mb-1">{{ $card['value'] }}</h4>
                                        <p class="mb-0 text-muted small">{{ $card['hint'] }}</p>
                                    </div>
                                    <div class="dashboard-stat-icon">
                                        <i class="{{ $card['icon'] }}"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Charts Row --}}
                <div class="row g-3 mb-4">
                    <div class="col-xl-5">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-1 fw-semibold">إحصائيات الطلاب حسب الحالة</h6>
                                <p class="mb-0 text-muted small">توزيع حالة الطلاب المسجلين حاليًا.</p>
                            </div>
                            <div class="card-body">
                                <div class="position-relative" style="height:280px;">
                                    <canvas id="studentsStatusChart"></canvas>
                                    <div class="chart-center-label">
                                        <span>الإجمالي</span>
                                        <strong>{{ $charts['studentsByStatus']['total'] }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-7">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-1 fw-semibold">التحصيل مقابل المصروفات الشهرية</h6>
                                <p class="mb-0 text-muted small">مقارنة آخر 6 أشهر بين التحصيل والمصروفات.</p>
                            </div>
                            <div class="card-body">
                                <div style="height:280px;">
                                    <canvas id="financialChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bottom Blocks --}}
                <div class="row g-3">
                    <div class="col-xl-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                                <h6 class="mb-0 fw-semibold">أحدث الطلاب المسجلين</h6>
                                @can('students.view')
                                    <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                                @endcan
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    @forelse($recentStudents as $student)
                                        <a href="{{ route('admin.students.show', $student->id) }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between">
                                            <div>
                                                <p class="mb-0 fw-semibold">{{ $student->full_name }}</p>
                                                <small class="text-muted">{{ $student->created_at?->diffForHumans() }}</small>
                                            </div>
                                            <span class="badge bg-light text-dark">{{ $student->status }}</span>
                                        </a>
                                    @empty
                                        <div class="index-empty-state">
                                            <i class="ti ti-users"></i>
                                            لا توجد تسجيلات حديثة
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                                <h6 class="mb-0 fw-semibold">آخر الدفعات</h6>
                                @can('payments.view')
                                    <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-outline-success">عرض الكل</a>
                                @endcan
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover mb-0 align-middle">
                                        <thead class="table-light">
                                        <tr>
                                            <th>الطالب</th>
                                            <th>المبلغ</th>
                                            <th>التاريخ</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($latestPayments as $payment)
                                            <tr>
                                                <td class="small fw-semibold">{{ $payment->student?->full_name ?? '-' }}</td>
                                                <td class="small text-success fw-bold">{{ $payment->formatted_amount }}</td>
                                                <td class="small text-muted">{{ $payment->formatted_payment_date }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-3">لا توجد دفعات حديثة</td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold">تنبيهات مهمة</h6>
                            </div>
                            <div class="card-body">
                                @forelse($importantAlerts as $alert)
                                    <div class="d-flex align-items-start gap-2 mb-3">
                                        <span class="badge bg-{{ $alert['color'] }} p-2"><i class="{{ $alert['icon'] }}"></i></span>
                                        <div>
                                            <p class="mb-0 fw-semibold small">{{ $alert['title'] }}</p>
                                            <p class="mb-0 text-muted small">{{ $alert['message'] }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="index-empty-state p-0">
                                        <i class="ti ti-checks"></i>
                                        لا توجد تنبيهات حالية
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Recent Activities --}}
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-semibold">آخر الأنشطة</h6>
                        <span class="badge bg-light text-dark">{{ count($recentActivities) }} نشاط</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            @forelse($recentActivities as $activity)
                                <div class="col-md-6 col-xl-4">
                                    <div class="border rounded p-3 h-100 bg-light-subtle">
                                        <div class="d-flex align-items-start gap-2">
                                            <span class="badge bg-{{ $activity['color'] }} p-2"><i class="{{ $activity['icon'] }}"></i></span>
                                            <div>
                                                <p class="mb-1 fw-semibold small">{{ $activity['title'] }}</p>
                                                <p class="mb-1 text-muted small">{{ $activity['description'] }}</p>
                                                <small class="text-muted">{{ $activity['time'] }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="index-empty-state">
                                        <i class="ti ti-history"></i>
                                        لا توجد أنشطة حديثة
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .dashboard-hero {
            background: linear-gradient(135deg, #111827 0%, #155fc7 40%, #374151 100%);
            border-radius: 1rem;
            overflow: hidden;
        }

        .hero-icon-box {
            width: 60px;
            height: 60px;
            border-radius: 0.9rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
            font-size: 1.8rem;
        }

        .hero-side-box {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 0.75rem;
            padding: 0.75rem;
        }

        .hero-action-btn {
            border-radius: 0.6rem;
        }

        .dashboard-stat-card {
            border-radius: 0.85rem;
        }

        .dashboard-stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 0.7rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            box-shadow: 0 0.125rem 0.4rem rgba(0, 0, 0, 0.08);
            color: #334155;
            font-size: 1.25rem;
        }

        .soft-primary { background-color: #eef2ff; }
        .soft-success { background-color: #ecfdf3; }
        .soft-info { background-color: #ecfeff; }
        .soft-warning { background-color: #fffbeb; }
        .soft-danger { background-color: #fff1f2; }
        .soft-indigo { background-color: #eef2ff; }
        .soft-secondary { background-color: #f8fafc; }

        .chart-center-label {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            pointer-events: none;
        }

        .chart-center-label span {
            display: block;
            color: #6b7280;
            font-size: 0.75rem;
        }

        .chart-center-label strong {
            font-size: 1.15rem;
            color: #111827;
        }
    </style>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const studentsCtx = document.getElementById('studentsStatusChart');
            if (studentsCtx) {
                new Chart(studentsCtx, {
                    type: 'doughnut',
                    data: {
                        labels: @json($charts['studentsByStatus']['labels']),
                        datasets: [{
                            data: @json($charts['studentsByStatus']['data']),
                            backgroundColor: @json($charts['studentsByStatus']['colors']),
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    font: { family: 'Tajawal, Arial, sans-serif' }
                                }
                            }
                        }
                    }
                });
            }

            const financialCtx = document.getElementById('financialChart');
            if (financialCtx) {
                new Chart(financialCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($charts['financialByMonth']['labels']),
                        datasets: [
                            {
                                label: 'التحصيل',
                                data: @json($charts['financialByMonth']['collections']),
                                backgroundColor: '#16a34a',
                                borderRadius: 6,
                                maxBarThickness: 28,
                            },
                            {
                                label: 'المصروفات',
                                data: @json($charts['financialByMonth']['expenses']),
                                backgroundColor: '#ef4444',
                                borderRadius: 6,
                                maxBarThickness: 28,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function (value) {
                                        return value + ' ر.س';
                                    }
                                },
                                grid: {
                                    color: 'rgba(15, 23, 42, 0.08)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    boxWidth: 12,
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    font: { family: 'Tajawal, Arial, sans-serif' }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>

    {{-- Real-Time Clock --}}
    <script>
        (function () {
            function updateClock() {
                const now = new Date();
                let hours = now.getHours();
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                const period = hours >= 12 ? 'م' : 'ص';
                hours = hours % 12 || 12;
                const hoursStr = String(hours).padStart(2, '0');
                const el = document.getElementById('live-clock');
                if (el) {
                    el.textContent = hoursStr + ':' + minutes + ':' + seconds + ' ' + period;
                }
            }
            updateClock();
            setInterval(updateClock, 1000);
        })();
    </script>
@endsection

