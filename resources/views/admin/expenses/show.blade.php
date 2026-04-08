@extends('admin.layouts.master')

@section('title', 'تفاصيل المصروف')

@section('css')
    <style>
        .expense-profile-table th,
        .expense-profile-table td {
            text-align: right !important;
        }
    </style>
@endsection

@section('content')
    @php
        $expenseInfo = $profile['expense'] ?? [];
        $stats = $profile['stats'] ?? [];
    @endphp

    <div class="page-content-wrapper">
        <div class="content-container">
            <div class="page-content">

                @include('admin.partials.page-header', [
                    'title'       => 'تفاصيل المصروف',
                    'breadcrumbs' => $breadcrumbs,
                ])

                @include('admin.partials.alerts')

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div>
                                <p class="text-muted small mb-1">تفاصيل المصروف</p>
                                <h4 class="fw-bold mb-1">{{ $expenseInfo['title'] ?? $expense->title }}</h4>
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="badge bg-light text-dark border">الفرع: {{ $expenseInfo['branch'] ?? ($expense->branch?->name ?? 'عام') }}</span>
                                    <span class="badge bg-light text-dark border">التاريخ: {{ $expenseInfo['date'] ?? $expense->formatted_date }}</span>
                                    <span class="badge bg-danger">{{ $expenseInfo['amount'] ?? $expense->formatted_amount }}</span>
                                </div>
                            </div>

                            <div class="d-flex gap-2 flex-wrap align-items-start">
                                @can('expenses.update')
                                    <a href="{{ route('admin.expenses.edit', $expense) }}" class="btn btn-primary">
                                        <i class="ti ti-pencil me-1"></i> تعديل
                                    </a>
                                @endcan
                                @can('expenses.delete')
                                    <form method="POST" action="{{ route('admin.expenses.destroy', $expense) }}"
                                          onsubmit="return confirm('حذف المصروف؟');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="ti ti-trash me-1"></i> حذف
                                        </button>
                                    </form>
                                @endcan
                                <a href="{{ route('admin.expenses.index') }}" class="btn btn-light">رجوع</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">مصروفات الشهر</p>
                                <h5 class="fw-bold text-danger mb-0">{{ $stats['month_amount'] ?? '0.00 ج' }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">عدد مصروفات الشهر</p>
                                <h5 class="fw-bold text-primary mb-0">{{ $stats['month_count'] ?? 0 }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">إجمالي مصروفات الجهة</p>
                                <h5 class="fw-bold text-warning mb-0">{{ $stats['all_amount'] ?? '0.00 ج' }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">إجمالي عدد المصروفات</p>
                                <h5 class="fw-bold text-info mb-0">{{ $stats['all_count'] ?? 0 }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-xl-7">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-history me-1"></i> أحدث المصروفات المرتبطة</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 text-end expense-profile-table">
                                    <thead class="table-light">
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>البند</th>
                                        <th>المبلغ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($profile['recent_expenses'] ?? [] as $item)
                                        <tr class="{{ $item['is_current'] ? 'table-primary' : '' }}">
                                            <td>{{ $item['date'] }}</td>
                                            <td class="fw-semibold">{{ $item['title'] }}</td>
                                            <td class="text-danger fw-semibold">{{ $item['amount'] }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center text-muted py-3">لا توجد مصروفات مرتبطة</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-5">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-semibold"><i class="ti ti-chart-pie me-1"></i> توزيع مصروفات الشهر حسب البنود</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 text-end expense-profile-table">
                                    <thead class="table-light">
                                    <tr>
                                        <th>البند</th>
                                        <th>العدد</th>
                                        <th>الإجمالي</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($profile['titles_breakdown'] ?? [] as $row)
                                        <tr>
                                            <td class="fw-semibold">{{ $row['title'] }}</td>
                                            <td>{{ $row['count'] }}</td>
                                            <td class="text-danger fw-semibold">{{ $row['total'] }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center text-muted py-3">لا توجد بيانات توزيع</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                @if(!empty($expenseInfo['notes']) && $expenseInfo['notes'] !== '-')
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-semibold"><i class="ti ti-note me-1"></i> ملاحظات المصروف</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $expenseInfo['notes'] }}</p>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection

