<?php

namespace App\Services\Admin;

use App\Models\Expense;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExpenseManagementService extends BaseService
{
    public function datatable(Request $request): array
    {
        $draw   = (int) $request->input('draw', 1);
        $start  = max((int) $request->input('start', 0), 0);
        $length = max((int) $request->input('length', 10), 1);
        $search = trim((string) data_get($request->input('search'), 'value', ''));

        $baseQuery = Expense::query()->with('branch');

        if ($request->filled('branch_id')) {
            $baseQuery->where('branch_id', $request->input('branch_id'));
        }

        $recordsTotal = Expense::query()->count();

        if ($search !== '') {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%")
                    ->orWhereHas('branch', fn ($b) => $b->where('name', 'like', "%{$search}%"));
            });
        }

        $recordsFiltered = (clone $baseQuery)->count();

        $rows = $baseQuery
            ->orderByDesc('expense_date')
            ->orderByDesc('id')
            ->skip($start)
            ->take($length)
            ->get();

        $data = $rows->map(function (Expense $expense) {
            return [
                'id'              => $expense->id,
                'expense_date'    => $expense->formatted_date,
                'title'           => $expense->title,
                'branch_name'     => $expense->branch?->name ?? 'عام',
                'amount'          => $expense->amount,
                'formatted_amount'=> $expense->formatted_amount,
            ];
        })->values()->all();

        return [
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ];
    }

    public function reportSummary(array $filters = []): array
    {
        $query = Expense::query();

        if (! empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        $total = (clone $query)->count();
        $totalAmount = (clone $query)->sum('amount');

        return compact('total', 'totalAmount');
    }

    public function getExpenseProfile(Expense $expense): array
    {
        $expense->load('branch');

        $date = $expense->expense_date ? Carbon::parse($expense->expense_date) : Carbon::now();
        $monthStart = $date->copy()->startOfMonth()->toDateString();
        $monthEnd = $date->copy()->endOfMonth()->toDateString();

        $branchId = $expense->branch_id;

        $baseQuery = Expense::query();

        if ($branchId) {
            $baseQuery->where('branch_id', $branchId);
        }

        $monthQuery = (clone $baseQuery)->whereBetween('expense_date', [$monthStart, $monthEnd]);

        $monthCount = (clone $monthQuery)->count();
        $monthAmount = (float) (clone $monthQuery)->sum('amount');

        $allCount = (clone $baseQuery)->count();
        $allAmount = (float) (clone $baseQuery)->sum('amount');

        $recentExpenses = (clone $baseQuery)
            ->latest('expense_date')
            ->latest('id')
            ->limit(10)
            ->get()
            ->map(fn (Expense $item) => [
                'id' => $item->id,
                'date' => $item->formatted_date,
                'title' => $item->title,
                'amount' => $item->formatted_amount,
                'is_current' => (int) $item->id === (int) $expense->id,
            ])
            ->values()
            ->all();

        $titlesBreakdown = (clone $monthQuery)
            ->get(['title', 'amount'])
            ->groupBy('title')
            ->map(fn ($rows, $title) => [
                'title' => $title,
                'count' => $rows->count(),
                'total' => number_format((float) $rows->sum('amount'), 2) . ' ر.س',
            ])
            ->values()
            ->all();

        return [
            'expense' => [
                'id' => $expense->id,
                'title' => $expense->title,
                'date' => $expense->formatted_date,
                'amount' => $expense->formatted_amount,
                'notes' => $expense->notes ?: '-',
                'branch' => $expense->branch?->name ?? 'عام',
                'created_at' => optional($expense->created_at)->format('Y-m-d H:i'),
            ],
            'stats' => [
                'month_count' => $monthCount,
                'month_amount' => number_format($monthAmount, 2) . ' ر.س',
                'all_count' => $allCount,
                'all_amount' => number_format($allAmount, 2) . ' ر.س',
            ],
            'recent_expenses' => $recentExpenses,
            'titles_breakdown' => $titlesBreakdown,
            'generated_at' => Carbon::now()->format('Y-m-d H:i'),
        ];
    }
}

